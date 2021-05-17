<?php
/**
 * Ajax "msginit" route, for initializing new translation files
 */
class Loco_ajax_MsginitController extends Loco_ajax_common_BundleController {

    /**
     * @return Loco_Locale
     */
    private function getLocale(){
        if( $this->get('use-selector') ){
            $tag = $this->get('select-locale');
        }
        else {
            $tag = $this->get('custom-locale');
        }
        $locale = Loco_Locale::parse($tag);
        if( ! $locale->isValid() ){
            throw new Loco_error_LocaleException('Invalid locale');
        }
        return $locale;
    }



    /**
     * {@inheritdoc}
     */
    public function render(){

        $post = $this->validate();
        $bundle = $this->getBundle();
        $project = $this->getProject( $bundle );
        $domain = (string) $project->getDomain();
        $locale = $this->getLocale();
        $suffix = (string) $locale;
        
        // The front end posts a template path, so we must replace the actual locale code
        $base = loco_constant('WP_CONTENT_DIR');
        $path = $post->path[ $post['select-path'] ];
        // The request_filesystem_credentials function will try to access the "path" field later
        $_POST['path'] = $path;

        $pofile = new Loco_fs_LocaleFile( $path );
        if( $suffix !== $pofile->getSuffix() ){
            $pofile = $pofile->cloneLocale( $locale );
            if( $suffix !== $pofile->getSuffix() ){
                throw new Loco_error_Exception('Failed to suffix file path with locale code');
            }
        }

        // target PO should not exist yet
        $pofile->normalize( $base );
        $api = new Loco_api_WordPressFileSystem;
        $api->authorizeCreate( $pofile );
        
        // Target MO probably doesn't exist, but we don't want to overwrite it without asking
        $mofile = $pofile->cloneExtension('mo');
        if( $mofile->exists() ){
            throw new Loco_error_Exception( __('MO file exists for this language already. Delete it first','loco-translate') );
        }

        /*/ Same for JSON file, but WordPress >= only 5
        $jsfile = function_exists('wp_set_script_translations') ? $pofile->cloneExtension('json') : null;
        if( $jsfile && $jsfile->exists() ){
            throw new Loco_error_Exception( __('JSON file exists for this language already. Delete it first','loco-translate') );
        }*/
        
        // Permit forcing of any parsable file as strings template
        $source = (string) $post->source;
        $compile = false;
        if( '' !== $source ){
            $potfile = new Loco_fs_LocaleFile( $source );
            $potfile->normalize( $base );
            $data = Loco_gettext_Data::load($potfile);
            // Remove target strings when copying PO 
            if( $post->strip ){
                $data->strip();
            }
            // else we're copying target translations from given file
            else {
                $compile = true;
            }
        }
        // else parse POT file if project defines one that exists
        else {
            $potfile = $project->getPot();
            if( $potfile->exists() ){ 
                $data = Loco_gettext_Data::load($potfile);
            }
            // else extract directly from source code, assuming domain passed though from front end
            else {
                $extr = new Loco_gettext_Extraction( $bundle );
                $data = $extr->addProject($project)->includeMeta()->getTemplate($domain);
                $potfile = null;
            }
        }

        // Let template define Project-Id-Version, else set header to current project name
        $headers = array();
        $vers = $data->getHeaders()->{'Project-Id-Version'};
        if( ! $vers || 'PACKAGE VERSION' === $vers ){
            $headers['Project-Id-Version'] = $project->getName();
        }
        // relative path from bundle root to the template/source this file was created from
        if( $potfile && $post->link ){
            $headers['X-Loco-Template'] = $potfile->getRelativePath( $bundle->getDirectoryPath() );
            // legacy behaviour was to sync source AND target strings in the absence of the following
            if( $post->strip ){
                $headers['X-Loco-Template-Mode'] = 'POT';
            }
            // without strip argument we need to remember the source PO is effectively a fallback locale
            else {
                $fallback = $potfile instanceof Loco_fs_LocaleFile ? $potfile->getLocale() : $locale;
                $headers['X-Loco-Fallback'] = (string) $fallback;
                $headers['X-Loco-Template-Mode'] = 'PO';
            }
        }
        
        // finalize PO data ready to write to new file
        $locale->ensureName( new Loco_api_WordPressTranslations );
        $data->localize( $locale, $headers );
        
        // compile all files in this set when copying target translation
        $compiler = new Loco_gettext_Compiler($pofile);
        if( $compile ){
            $compiler->writeAll($data,$project);
        }
        // empty translations don't require compiled files, but adding MO for completeness.
        else {
            $compiler->writePo($data);
            $data->clear();
            $compiler->writeMo($data);
        }

        // return debugging information, used in tests.
        $this->set('debug',new Loco_mvc_ViewParams( array(
            'poname' => $pofile->basename(),
            'source' => $potfile ? $potfile->basename() : '',
        ) ) );

        // push recent items on file creation
        Loco_data_RecentItems::get()->pushBundle($bundle)->persist();
        
        // front end will redirect to the editor
        $type = strtolower( $this->get('type') );
        $this->set( 'redirect', Loco_mvc_AdminRouter::generate( sprintf('%s-file-edit',$type), array (
            'path' => $pofile->getRelativePath($base),
            'bundle' => $bundle->getHandle(),
            'domain' => $project->getId(),
        ) ) );
        
        return parent::render();
    }

}
<?php
/**
 * Private message action class
 */
class AE_Search extends AE_Base
{
    public static $instance;

    /**
     * getInstance method
     *
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * The constructor
     *
     * @param void
     * @return void
     * @since 1.0
     * @author Tambh
     */
    public function __construct()
    {
    }
    public  function ae_parse_search_terms( $terms ) {
        $strtolower = function_exists( 'mb_strtolower' ) ? 'mb_strtolower' : 'strtolower';
        $checked = array();

        $stopwords = $this->ae_get_search_stopwords();
        foreach ( $terms as $term ) {
            // keep before/after spaces when term is for exact match
            if ( preg_match( '/^".+"$/', $term ) )
                $term = trim( $term, "\"'" );
            else
                $term = trim( $term, "\"' " );

            // Avoid single A-Z.
            if ( ! $term || ( 1 === strlen( $term ) && preg_match( '/^[a-z]$/i', $term ) ) )
                continue;

            if ( in_array( call_user_func( $strtolower, $term ), $stopwords, true ) )
                continue;

            $checked[] = $term;
        }

        return $checked;
    }

    /**
     * Retrieve stopwords used when parsing search terms.
     *
     * @since 3.7.0
     *
     * @return array Stopwords.
     */
    function ae_get_search_stopwords() {
        /* translators: This is a comma-separated list of very common words that should be excluded from a search,
         * like a, an, and the. These are usually called "stopwords". You should not simply translate these individual
         * words into your language. Instead, look for and provide commonly accepted stopwords in your language.
         */
        $words = explode( ',', _x( 'about,an,are,as,at,be,by,com,for,from,how,in,is,it,of,on,or,that,the,this,to,was,what,when,where,who,will,with,www',
            'Comma-separated list of search stopwords in your language' ) );

        $stopwords = array();
        foreach( $words as $word ) {
            $word = trim( $word, "\r\n\t " );
            if ( $word )
                $stopwords[] = $word;
        }

        /**
         * Filter stopwords used when parsing search terms.
         *
         * @since 3.7.0
         *
         * @param array $stopwords Stopwords.
         */
        return $stopwords;
    }
    /**
     * Generate SQL for the WHERE clause based on passed search terms.
     *
     * @since 3.7.0
     *
     * @param string $q search variable
     * @return string WHERE clause.
     */
    public  function ae_parse_search( $q ) {

        $a = array();
        // added slashes screw with quote grouping when done early, so done later
        $q = stripslashes( $q );
        // there are no line breaks in <input /> fields
        $q = str_replace( array( "\r", "\n" ), '', $q );
        if ( preg_match_all( '/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $q, $matches ) ) {
            $a['search_terms'] = $this->ae_parse_search_terms( $matches[0] );
            // if the search string has only short terms or stopwords, or is 10+ terms long, match it as sentence
            if ( empty( $a['search_terms'] ) || count( $a['search_terms'] ) > 9 )
                $a['search_terms'] = array( $q );
        } else {
            $a['search_terms'] = array( $q );
        }
        return $a['search_terms'];
    }
}
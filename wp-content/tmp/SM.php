<?php
	/**
	 * Twenty Nineteen functions and definitions
	 *
	 * @link       https://developer.wordpress.org/themes/basics/theme-functions/
	 *
	 * @package    WordPress
	 * @subpackage Twenty_Nineteen
	 * @since      1.0.0
	 */

	/**
	 * Twenty Nineteen only works in WordPress 4.7 or later.
	 */
	if ( version_compare( $GLOBALS[ 'wp_version' ], '4.7', '<' ) ) {
		require get_template_directory() . '/inc/back-compat.php';

		return;
	}

	if ( ! function_exists( 'twentynineteen_setup' ) ) :
		/**
		 * Sets up theme defaults and registers support for various WordPress features.
		 *
		 * Note that this function is hooked into the after_setup_theme hook, which
		 * runs before the init hook. The init hook is too late for some features, such
		 * as indicating support for post thumbnails.
		 */
		function twentynineteen_setup() {
			/*
			 * Make theme available for translation.
			 * Translations can be filed in the /languages/ directory.
			 * If you're building a theme based on Twenty Nineteen, use a find and replace
			 * to change 'twentynineteen' to the name of your theme in all the template files.
			 */
			load_theme_textdomain( 'twentynineteen', get_template_directory() . '/languages' );

			// Add default posts and comments RSS feed links to head.
			add_theme_support( 'automatic-feed-links' );

			/*
			 * Let WordPress manage the document title.
			 * By adding theme support, we declare that this theme does not use a
			 * hard-coded <title> tag in the document head, and expect WordPress to
			 * provide it for us.
			 */
			add_theme_support( 'title-tag' );

			/*
			 * Enable support for Post Thumbnails on posts and pages.
			 *
			 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
			 */
			add_theme_support( 'post-thumbnails' );
			//set_post_thumbnail_size( 1568, 9999 );

			// This theme uses wp_nav_menu() in two locations.
			register_nav_menus( [
					'menu-1' => __( 'Primary', 'twentynineteen' ),
					'footer' => __( 'Footer Menu', 'twentynineteen' ),
					'social' => __( 'Social Links Menu', 'twentynineteen' ),
				] );

			/*
			 * Switch default core markup for search form, comment form, and comments
			 * to output valid HTML5.
			 */
			add_theme_support( 'html5', [
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
				] );

			/**
			 * Add support for core custom logo.
			 *
			 * @link https://codex.wordpress.org/Theme_Logo
			 */
			add_theme_support( 'custom-logo', [
					'height'      => 190,
					'width'       => 190,
					'flex-width'  => false,
					'flex-height' => false,
				] );

			// Add theme support for selective refresh for widgets.
			add_theme_support( 'customize-selective-refresh-widgets' );

			// Add support for Block Styles.
			add_theme_support( 'wp-block-styles' );

			// Add support for full and wide align images.
			add_theme_support( 'align-wide' );

			// Add support for editor styles.
			add_theme_support( 'editor-styles' );

			// Enqueue editor styles.
			add_editor_style( 'style-editor.css' );

			// Add custom editor font sizes.
			add_theme_support( 'editor-font-sizes', [
					[
						'name'      => __( 'Small', 'twentynineteen' ),
						'shortName' => __( 'S', 'twentynineteen' ),
						'size'      => 19.5,
						'slug'      => 'small',
					],
					[
						'name'      => __( 'Normal', 'twentynineteen' ),
						'shortName' => __( 'M', 'twentynineteen' ),
						'size'      => 22,
						'slug'      => 'normal',
					],
					[
						'name'      => __( 'Large', 'twentynineteen' ),
						'shortName' => __( 'L', 'twentynineteen' ),
						'size'      => 36.5,
						'slug'      => 'large',
					],
					[
						'name'      => __( 'Huge', 'twentynineteen' ),
						'shortName' => __( 'XL', 'twentynineteen' ),
						'size'      => 49.5,
						'slug'      => 'huge',
					],
				] );

			// Editor color palette.
			add_theme_support( 'editor-color-palette', [
					[
						'name'  => __( 'Primary', 'twentynineteen' ),
						'slug'  => 'primary',
						'color' => twentynineteen_hsl_hex( 'default' === get_theme_mod( 'primary_color' ) ? 199 : get_theme_mod( 'primary_color_hue', 199 ), 100, 33 ),
					],
					[
						'name'  => __( 'Secondary', 'twentynineteen' ),
						'slug'  => 'secondary',
						'color' => twentynineteen_hsl_hex( 'default' === get_theme_mod( 'primary_color' ) ? 199 : get_theme_mod( 'primary_color_hue', 199 ), 100, 23 ),
					],
					[
						'name'  => __( 'Dark Gray', 'twentynineteen' ),
						'slug'  => 'dark-gray',
						'color' => '#111',
					],
					[
						'name'  => __( 'Light Gray', 'twentynineteen' ),
						'slug'  => 'light-gray',
						'color' => '#767676',
					],
					[
						'name'  => __( 'White', 'twentynineteen' ),
						'slug'  => 'white',
						'color' => '#FFF',
					],
				] );

			// Add support for responsive embedded content.
			add_theme_support( 'responsive-embeds' );
		}
	endif;
	add_action( 'after_setup_theme', 'twentynineteen_setup' );

	/**
	 * Register widget area.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
	 */
	function twentynineteen_widgets_init() {

		register_sidebar( [
				'name'          => __( 'Footer', 'twentynineteen' ),
				'id'            => 'sidebar-1',
				'description'   => __( 'Add widgets here to appear in your footer.', 'twentynineteen' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			] );

	}

	add_action( 'widgets_init', 'twentynineteen_widgets_init' );

	/**
	 * Set the content width in pixels, based on the theme's design and stylesheet.
	 *
	 * Priority 0 to make it available to lower priority callbacks.
	 *
	 * @global int $content_width Content width.
	 */
	function twentynineteen_content_width() {
		// This variable is intended to be overruled from themes.
		// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
		$GLOBALS[ 'content_width' ] = apply_filters( 'twentynineteen_content_width', 640 );
	}

	add_action( 'after_setup_theme', 'twentynineteen_content_width', 0 );

	/**
	 * Enqueue scripts and styles.
	 */
	function twentynineteen_scripts() {
		wp_enqueue_style( 'twentynineteen-style', get_stylesheet_uri(), [], wp_get_theme()->get( 'Version' ) );

		wp_style_add_data( 'twentynineteen-style', 'rtl', 'replace' );

		if ( has_nav_menu( 'menu-1' ) ) {
			wp_enqueue_script( 'twentynineteen-priority-menu', get_theme_file_uri( '/js/priority-menu.js' ), [], '1.1', true );
			wp_enqueue_script( 'twentynineteen-touch-navigation', get_theme_file_uri( '/js/touch-keyboard-navigation.js' ), [], '1.1', true );
		}

		wp_enqueue_style( 'twentynineteen-print-style', get_template_directory_uri() . '/print.css', [], wp_get_theme()->get( 'Version' ), 'print' );

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}

	add_action( 'wp_enqueue_scripts', 'twentynineteen_scripts' );

	/**
	 * Fix skip link focus in IE11.
	 *
	 * This does not enqueue the script because it is tiny and because it is only for IE11,
	 * thus it does not warrant having an entire dedicated blocking script being loaded.
	 *
	 * @link https://git.io/vWdr2
	 */
	function twentynineteen_skip_link_focus_fix() {
		// The following is minified via `terser --compress --mangle -- js/skip-link-focus-fix.js`.
		?>
		<script>
            /(trident|msie)/i.test(navigator.userAgent) && document.getElementById && window.addEventListener && window.addEventListener("hashchange", function () {
                var t, e = location.hash.substring(1);
                /^[A-z0-9_-]+$/.test(e) && (t = document.getElementById(e)) && (/^(?:a|select|input|button|textarea)$/i.test(t.tagName) || (t.tabIndex = -1), t.focus())
            }, !1);
		</script>
		<?php
	}

	add_action( 'wp_print_footer_scripts', 'twentynineteen_skip_link_focus_fix' );

	/**
	 * Enqueue supplemental block editor styles.
	 */
	function twentynineteen_editor_customizer_styles() {

		wp_enqueue_style( 'twentynineteen-editor-customizer-styles', get_theme_file_uri( '/style-editor-customizer.css' ), false, '1.1', 'all' );

		if ( 'custom' === get_theme_mod( 'primary_color' ) ) {
			// Include color patterns.
			require_once get_parent_theme_file_path( '/inc/color-patterns.php' );
			wp_add_inline_style( 'twentynineteen-editor-customizer-styles', twentynineteen_custom_colors_css() );
		}
	}

	add_action( 'enqueue_block_editor_assets', 'twentynineteen_editor_customizer_styles' );

	/**
	 * Display custom color CSS in customizer and on frontend.
	 */
	function twentynineteen_colors_css_wrap() {

		// Only include custom colors in customizer or frontend.
		if ( ( ! is_customize_preview() && 'default' === get_theme_mod( 'primary_color', 'default' ) ) || is_admin() ) {
			return;
		}

		require_once get_parent_theme_file_path( '/inc/color-patterns.php' );

		$primary_color = 199;
		if ( 'default' !== get_theme_mod( 'primary_color', 'default' ) ) {
			$primary_color = get_theme_mod( 'primary_color_hue', 199 );
		}
		?>

		<style type="text/css"
		       id="custom-theme-colors" <?php echo is_customize_preview() ? 'data-hue="' . absint( $primary_color ) . '"' : ''; ?>>
			<?php echo twentynineteen_custom_colors_css(); ?>
		</style>
		<?php
	}

	add_action( 'wp_head', 'twentynineteen_colors_css_wrap' );

	/**
	 * SVG Icons class.
	 */
	require get_template_directory() . '/classes/class-twentynineteen-svg-icons.php';

	/**
	 * Custom Comment Walker template.
	 */
	require get_template_directory() . '/classes/class-twentynineteen-walker-comment.php';

	/**
	 * Enhance the theme by hooking into WordPress.
	 */
	require get_template_directory() . '/inc/template-functions.php';

	/**
	 * SVG Icons related functions.
	 */
	require get_template_directory() . '/inc/icon-functions.php';

	/**
	 * Custom template tags for the theme.
	 */
	require get_template_directory() . '/inc/template-tags.php';

	/**
	 * Customizer additions.
	 */
	require get_template_directory() . '/inc/customizer.php';


	//добавляем форму поиска
	add_filter( 'get_search_form', 'my_search_form' );
	function my_search_form( $form ) {
		$form = '<div id="ft-searching"><button id="ft-searching-close"></button><form method="get" id="ft-searching-form" action="' . home_url( '/' ) . '" ><input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="ваш запрос (от 4-х знаков)" /><label id="ft-searching-form-submit" for="ft-searching-form-submit-go" class="ft-searching-form-disabled"><input type="submit" id="ft-searching-form-submit-go" value="" style="display:none;" disabled="disabled" /></label></form></div>';

		return $form;
	}

	;


	//Удаляем category из УРЛа категорий
	add_filter( 'category_link', function( $a ) {
		return str_replace( 'category/', '', $a );
	}, 99 );


	/**
	 * Функция для вывода записей по произвольному полю содержащему числовое значение.
	 *
	 * Пример вызова:
	 *     kama_get_most_viewed( "num=5 &key=views &cache=1 &format={a}{title}{/a} - {date:j.M.Y} ({views})
	 *     ({comments})" );
	 *
	 * @param string $args   {
	 *
	 * @type int     $num    (10)    Количество постов.
	 * @type string  $key    (views) Ключ произвольного поля, по значениям которого будет проходить выборка.
	 * @type string  $order  (DESC)  Порядок вывода записей. Чтобы вывести сначала менее просматириваемые устанавливаем
	 *       order=1
	 * @type string  $format ('')   Формат выводимых ссылок. По дефолту такой: ({a}{title}{/a}).
	 *                                   Можно использовать, например, такой:
	 *                                   {date:j.M.Y} - {a}{title}{/a} ({views}, {comments}).
	 * @type int     $days   (0)     Число последних дней, записи которых нужно вывести
	 *                                   по количеству просмотров. Если указать год (2011,2010),
	 *                                   то будут отбираться популярные записи за этот год.
	 * @type int     $cache  (0)     Использовать кэш или нет.  Варианты 1 - кэширование включено, 0 - выключено (по
	 *       дефолту).
	 * @type string  $echo   (1)     Выводить на экран или нет. Варианты 1 - выводить (по дефолту), 0 - вернуть для
	 *       обработки (return).
	 * }
	 *
	 * @return bool|int|mixed|string
	 *
	 * @ver 1.0
	 */


	/* Подсчет количества посещений страниц
	---------------------------------------------------------- */
	add_action( 'the_post', 'kama_postviews' );
	function kama_postviews() {
		global $user_ID, $post;
		/* ------------ Настройки -------------- */
		$meta_key     = 'views';  // Ключ мета поля, куда будет записываться количество просмотров.
		$who_count    = 0;            // Чьи посещения считать? 0 - Всех. 1 - Только гостей. 2 - Только зарегистрированных пользователей.
		$exclude_bots = 0;            // Исключить ботов, роботов, пауков и прочую нечесть :)? 0 - нет, пусть тоже считаются. 1 - да, исключить из подсчета.


		if ( is_singular() ) {
			$id = (int) $post->ID;
			static $post_views = false;
			if ( $post_views ) {
				return true;
			} // чтобы 1 раз за поток
			$post_views   = (int) get_post_meta( $id, $meta_key, true );
			$should_count = false;
			switch ( (int) $who_count ) {
				case 0:
					$should_count = true;
					break;
				case 1:
					if ( (int) $user_ID == 0 ) {
						$should_count = true;
					}
					break;
				case 2:
					if ( (int) $user_ID > 0 ) {
						$should_count = true;
					}
					break;
			}
			if ( (int) $exclude_bots == 1 && $should_count ) {
				$useragent = $_SERVER[ 'HTTP_USER_AGENT' ];
				$notbot    = "Mozilla|Opera"; //Chrome|Safari|Firefox|Netscape - все равны Mozilla
				$bot       = "Bot/|robot|Slurp/|yahoo"; //Яндекс иногда как Mozilla представляется
				if ( ! preg_match( "/$notbot/i", $useragent ) || preg_match( "!$bot!i", $useragent ) ) {
					$should_count = false;
				}
			}

			if ( $should_count ) {
				if ( ! update_post_meta( $id, $meta_key, ( $post_views + 1 ) ) ) {
					add_post_meta( $id, $meta_key, 1, true );
				}
			}
		}

		return true;
	}


	/*function kama_get_most_viewed( $args = '' ){
		global $wpdb, $post;

		$count_p = 1;

		parse_str( $args, $i );
		$num    = isset( $i['num'] )    ? (int) $i['num'] : 10;
		$key    = isset( $i['key'] )    ? sanitize_text_field($i['key']) : 'views';
		$order  = isset( $i['order'] )  ? 'ASC' : 'DESC';
		$days   = isset( $i['days'] )   ? (int) $i['days'] : 2020;
		$format = isset( $i['format'] ) ? stripslashes( $i['format'] ) : '';
		$cache  = isset( $i['cache'] );
		$echo   = isset( $i['echo'] )   ? (int) $i['echo'] : 1;
		if( $cache ){
			$cache_key = (string) md5( __FUNCTION__ . serialize( $args ) );
			//получаем и отдаем кеш если он есть
			if( $cache_out = wp_cache_get( $cache_key ) ){
				if( $echo )
					return print( $cache_out );
				else
					return $cache_out;
			}
		}
		if( $days ){
			$AND_days = "AND post_date > CURDATE() - INTERVAL $days DAY";
			if( strlen( $days ) == 4 ){
				$AND_days = "AND YEAR(post_date)=" . $days;
			}
		}
		$sql = "SELECT p.ID, p.post_title, p.post_date, p.guid, p.comment_count, (pm.meta_value+0) AS views
		FROM $wpdb->posts p
			LEFT JOIN $wpdb->postmeta pm ON (pm.post_id = p.ID)
		WHERE pm.meta_key = '$key' $AND_days
			AND p.post_type = 'post'
			AND p.post_status = 'publish'
		ORDER BY views $order LIMIT $num";
		$results = $wpdb->get_results( $sql );
		if( ! $results ){
			return false;
		}
		$out = $x = '';
		preg_match( '!{date:(.*?)}!', $format, $date_m );
		foreach( $results as $pst ){
			$x = ( $x == 'li1' ) ? 'li2' : 'li1';
			if( $pst->ID == $post->ID )
				$x .= ' current-item';
			$Title    = $pst->post_title;

			//$a1       = '<a href="' . get_permalink( $pst->ID ) . "\" title=\"{$pst->views} просмотров: $Title\">";

			$post_id = $pst->ID;
			$home_popular_image = get_post_meta($post_id, 'middle_image', true);
			$home_popular_image_min = get_post_meta($post_id, 'small_image', true);

			$a1       = '<a href="' . get_permalink( $pst->ID ) . '" ><img data-src="' . $home_popular_image . '" /><span class="title">' . $Title . '</span><span class="color-background"></span>';

			$a2       = '</a>';
			//$a2       = '</a>';
			$comments = $pst->comment_count;
			$views    = $pst->views;
			if( $format ){
				$date    = apply_filters( 'the_time', mysql2date( $date_m[ 1 ], $pst->post_date ) );
				$Sformat = str_replace( $date_m[ 0 ], $date, $format );
				$Sformat = str_replace( [ '{a}', '{title}', '{/a}', '{comments}', '{views}' ], [ $a1, $Title, $a2, $comments, $views, ], $Sformat );
			} else {
				//$Sformat = $a1 . $Title . $a2;
				$Sformat = $a1 . $a2;
				//$out .= "<li class=\"$x\">$Sformat</li>";
				$out .= "$Sformat";
				if (($count_p == 4) and (is_front_page())) {
					$out.="<div id=\"ya_2\"><div id=\"yandex_direct_R-A-001\"></div><div id=\"yandex_direct_R-A-002\"></div></div>";
				}
				$count_p++;
			}
		}
		if( $cache ) { wp_cache_add( $cache_key, $out ); }
		if( $echo )	{ echo $out; } else	return $out;
	}*/
	function kama_get_most_viewed( $args = '' ) {
		global $wpdb, $post;

		parse_str( $args, $i );

		$num    = isset( $i[ 'num' ] ) ? preg_replace( '/[^0-9,\s]/', '', $i[ 'num' ] ) : 10; // 20,10 | 10
		$key    = isset( $i[ 'key' ] ) ? sanitize_text_field( $i[ 'key' ] ) : 'views';
		$order  = isset( $i[ 'order' ] ) && in_array( strtoupper( $i[ 'order' ] ), [ 'ASC', 1 ] ) ? 'ASC' : 'DESC';
		$days   = isset( $i[ 'days' ] ) ? (int) $i[ 'days' ] : 0;
		$format = isset( $i[ 'format' ] ) ? stripslashes( $i[ 'format' ] ) : '';
		$cache  = isset( $i[ 'cache' ] );
		$echo   = isset( $i[ 'echo' ] ) ? (int) $i[ 'echo' ] : 1;
		$return = isset( $i[ 'return' ] ) ? $i[ 'return' ] : 'string';

		if ( $cache ) {
			$cache_key = (string) md5( __FUNCTION__ . serialize( $args ) );

			//получаем и отдаем кеш если он есть
			if ( $cache_out = wp_cache_get( $cache_key ) ) {
				if ( $echo ) {
					return print( $cache_out );
				} else {
					return $cache_out;
				}
			}
		}

		if ( $days ) {
			$AND_days = "AND post_date > CURDATE() - INTERVAL $days DAY";
			if ( strlen( $days ) == 4 ) {
				$AND_days = "AND YEAR(post_date)=$days";
			}
		}

		$esc_key = esc_sql( $key );

		$sql = "SELECT *, (pm.meta_value+0) AS views
	FROM $wpdb->posts p
		LEFT JOIN $wpdb->postmeta pm ON (pm.post_id = p.ID)
	WHERE pm.meta_key = '$esc_key' $AND_days
		AND p.post_type = 'post'
		AND p.post_status = 'publish'
	ORDER BY views $order LIMIT $num";

		$posts = $wpdb->get_results( $sql );
		if ( ! $posts ) {
			return false;
		}

		if ( 'array' === $return ) {
			return $posts;
		}

		$out = $x = '';
		preg_match( '!{date:(.*?)}!', $format, $date_m );

		foreach ( $posts as $pst ) {

			$x = ( $x == 'li1' ) ? 'li2' : 'li1';

			if ( $pst->ID == $post->ID ) {
				$x .= ' current-item';
			}

			//$Title    = '<span>' . $pst->post_title . '</span>';
			$Title = $pst->post_title;
			//$a1       = '<a href="' . get_permalink( $pst->ID ) . "\" title=\"{$pst->views} просмотров: $Title\">";
			$liked    = get_post_meta( $pst->ID, "likes", true );
			$disliked = get_post_meta( $pst->ID, "dislikes", true );
			$estimed  = [ $liked, $disliked ];
			$viewed   = get_post_meta( $pst->ID, "views", true );
			$commeted = get_comments_number( $pst->ID );

			$preface     = get_post_meta( $pst->ID, "preface", true );
			$preface_min = mb_strimwidth( $preface, 0, 150, '...' );
			$a1          = '<a href="' . get_permalink( $pst->ID ) . '" class="ft-popular-item"><div class="ft-popular-item-monitor"><span class="views" title="Просмотров: ' . $viewed . '">' . $viewed . '</span><span class="score" title="Оценка: ' . array_sum( $estimed ) . '">' . array_sum( $estimed ) . '</span><span class="comment" title="Комментариев: ' . $commeted . '">' . $commeted . '</span></div><img class="ft-popular-item-img" data-src="' . get_post_meta( $pst->ID, "middle_image", true ) . '" alt="' . $Title . '" title="Интересное в дорогу | ' . $Title . '" /><span class="ft-popular-item-title">' . $Title . '</span><p class="ft-popular-item-p">' . $preface_min . '</p>';
			$a2          = '</a>';
			$comments    = $pst->comment_count;
			$views       = $pst->views;

			if ( $format ) {

				$date    = apply_filters( 'the_time', mysql2date( $date_m[ 1 ], $pst->post_date ) );
				$Sformat = str_replace( $date_m[ 0 ], $date, $format );
				$Sformat = str_replace( [ '{a}', '{title}', '{/a}', '{comments}', '{views}' ], [
					$a1,
					$Title,
					$a2,
					$comments,
					$views,
				], $Sformat );
			} else //$Sformat =  $a1 . $Title . $a2;
			{
				$Sformat = $a1 . $a2;
			}

			//$out .= "<li class=\"$x\">$Sformat</li>";
			$out .= "$Sformat";
		}

		if ( $cache ) {
			wp_cache_add( $cache_key, $out );
		}

		if ( $echo ) {
			echo $out;
		} else {
			return $out;
		}
	}


	function wpp_upd_post_data_for_map( $post_id ) {
		$vals = [];
		$n    = 1;
		/**
		 * Массив с автонабором имен полей
		 */
		while ( $n <= 10 ) {
			$val_t = get_option( 'top' . $n );
			$val_m = get_option( 'news' . $n );

			if ( ! empty( $val_m ) ) {
				$vals[] = (int) $val_m;
			}

			if ( ! empty( $val_t ) ) {
				$vals[] = (int) $val_t;
			}

			$n ++;
		}

		if ( in_array( (int) $post_id, $vals ) ) {
			wp_update_post( wp_slash( [ 'ID' => get_option( 'page_on_front' ) ] ) );
		}


	}

	add_action( 'save_post', 'wpp_upd_post_data_for_map' );
	add_action( 'wp_delete_post', 'wpp_upd_post_data_for_map' );
	add_action( 'wp_publish_post', 'wpp_upd_post_data_for_map' );

	function wpp_updated_mode_date() {

		$keys = [];
		$n    = 1;

		/**
		 * Массив с автонабором имен полей
		 */
		while ( $n <= 10 ) {
			$keys[] = 'top' . $n;
			$keys[] = 'news' . $n;
			$n ++;
		}

		/**
		 * Тут ID домашней страницы,
		 * если она задана не через настройки,
		 * то можно просто прописать ее ID
		 */
		$home_page_ID = get_option( 'page_on_front' );

		$customizer_data = json_decode( wp_unslash( $_REQUEST[ 'customized' ] ), true );

		file_put_contents( '../log.txt', $_REQUEST[ 'customized' ] );

		foreach ( $customizer_data as $data_key => $data_val ) {

			if ( in_array( $data_key, $keys ) ) {

				wp_update_post( wp_slash( [ 'ID' => $home_page_ID ] ) );

				break;
			}

		}

	}

	add_action( 'customize_save', 'wpp_updated_mode_date' );


	add_action( 'save_post', 'sitemaps' );
	add_action( 'wp_delete_post', 'sitemaps' );
	add_action( 'wp_publish_post', 'sitemaps' );


	function sitemaps() {

		global $wpdb;

		$home_page_ID = get_option( 'page_on_front' );
		$out          = '';

		$wrap = <<<WRAP
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	%s
</urlset>
WRAP;


		$item = <<<ITEM
<url>
	<loc>%s</loc>
	<lastmod>%s</lastmod>
	<priority>%s</priority>
</url>
ITEM;


		//Главная
		$out .= sprintf( $item, get_home_url(), get_the_modified_date( 'Y-m-d H:i:s', $home_page_ID ), '1.0' );

		//Cтраницы
		$pages = get_pages( [
			'post_type'   => 'page',
			'post_status' => 'publish',
		] );

		foreach ( $pages as $post ) {
			setup_postdata( $post );

			if ( (int) $home_page_ID === (int) $post->ID ) {
				continue;
			}
			$out .= sprintf( $item, urldecode( get_the_permalink( $post->ID ) ), get_the_modified_date( 'Y-m-d H:i:s', $post->ID ), '0.9' );
		}
		wp_reset_postdata();

		//Категории
		$all_categories = get_categories( [ 'hide_empty' => 0 ] );

		foreach ( $all_categories as $single_cat ) {
			$out .= sprintf( $item, urldecode( get_category_link( $single_cat->term_id ) ), get_metadata( 'term', $single_cat->term_id, 'txseo_seo_modified_date', 1 ), '0.9' );
		}


		//Посты
		$_posts = $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}posts` WHERE `post_type`='post' AND `post_status`='publish'" );
		foreach ( $_posts as $key => $row ) {
			$post_id    = $row->ID;
			$post_title = $row->post_name;
			$bread      = '';
			$sep        = '/';
			$terms      = get_the_terms( $post_id, 'category' );
			if ( ! empty( $terms ) ) {
				$bread .= urldecode( $terms[ 0 ]->slug );
				$bread .= '' . $sep . '';
				$bread .= $post_title;
			}

			if ( ! empty( $bread ) ) {
				$bread = strtr( $bread, " ", "_" );
				$bread = mb_strtolower( $bread );
				$out   .= sprintf( $item, home_url( '/' . $bread ), $row->post_modified_gmt, '0.8' );
			}
		}

		$map = sprintf( $wrap, $out );

		//Пишем в файл sitemaps.xml
		file_put_contents( '../sitemap.xml', $map );

		$out = 'User-agent: *
Disallow: /a*
Disallow: /b*
Disallow: /c*
Disallow: /d*
Disallow: /e*
Disallow: /f*
Disallow: /g*
Disallow: /h*
Disallow: /i*
Disallow: /j*
Disallow: /k*
Disallow: /l*
Disallow: /m*
Disallow: /n*
Disallow: /o*
Disallow: /p*
Disallow: /q*
Disallow: /r*
Disallow: /s*
Disallow: /t*
Disallow: /u*
Disallow: /v*
Disallow: /w*
Disallow: /x*
Disallow: /y*
Disallow: /z*
Disallow: /?*
Disallow: /.*
Disallow: //*
Disallow: /-*
Disallow: /[*
Disallow: /]*
Disallow: /_*
Disallow: /=*
Disallow: /&*
Disallow: /%*
Disallow: /;*
Disallow: /:*
Disallow: /#*
Disallow: /@*
Disallow: /~*
Disallow: /0*
Disallow: /1*
Disallow: /2*
Disallow: /3*
Disallow: /4*
Disallow: /5*
Disallow: /6*
Disallow: /7*
Disallow: /8*
Disallow: /9*
Allow: /robots.txt$
Allow: /sitemap.xml$
Allow: /$';
		$out .= "\r\n";

		//Категории
		$all_categories = get_categories( 'hide_empty=0' );

		foreach ( $all_categories as $single_cat ) {

			$cat_slug = $single_cat->slug;
			$out      .= "Allow: /" . $cat_slug . "/$\r\n";
		}

		//Посты
		foreach ( $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'posts where (post_type = "page" or post_type = "post") and post_status = "publish"' ) as $key => $row ) {
			$post_id    = $row->ID;
			$post_title = $row->post_name;
			$bread      = '';
			$sep        = '/';
			$terms      = get_the_terms( $post_id, 'category' );
			if ( is_array( $terms ) && $terms !== [] ) {
				$bread .= ( $terms[ 0 ]->slug );
				$bread .= '' . $sep . '';
				$bread .= urlencode( $post_title );
			}
			if ( $bread != '' ) {
				$bread = strtr( $bread, " ", "_" );
				$bread = mb_strtolower( $bread );
				$out   .= "Allow: /" . $bread . "/$\r\n";
			}
		}

		//Пишем в файл robots.txt
		file_put_contents( '../robots.txt', $out );
	}


	/*
	function wpp_updated_mode_date() {

			$keys = [];
			$n    = 1;

			/**
			 * Массив с автонабором имен полей
			 */
	/*		while ( $n <= 10 ) {
				$keys[] = 'top' . $n;
				$keys[] = 'news' . $n;
				$n ++;
			}

			/**
			 * Тут ID домашней страницы,
			 * если она задана не через настройки,
			 * то можно просто прописать ее ID
			 */
	/*		$home_page_ID = get_option( 'page_on_front' );

			$customizer_data = json_decode( wp_unslash( $_REQUEST[ 'customized' ] ), true );

			foreach ( $customizer_data as $data_key => $data_val ) {

				if ( in_array( $data_key, $keys ) ) {

					wp_update_post( wp_slash( [ 'ID' => $home_page_ID ] ) );
					break;
				}

			}

		}

		add_action( 'customize_save', 'wpp_updated_mode_date' );


	add_action( 'save_post', 'sitemaps');
	add_action( 'wp_delete_post', 'sitemaps');
	add_action( 'wp_publish_post', 'sitemaps');

	function sitemaps (){
		global $wpdb, $priority;
		$out = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$out .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

	/*   //Главная страница
		работает корректно
		$main_url = $wpdb->get_var('SELECT option_value FROM '.$wpdb->prefix.'options  where option_name="siteurl"');
		$modified_main_url = file_exists(dirname(__FILE__) . '/home.php') ? date("Y-m-d H:i:s", filectime(dirname(__FILE__) . '/home.php')) : '';
		$out .= '<url>
					<loc>'.$main_url.'</loc>
					<lastmod>'.$modified_main_url.'</lastmod>
					<priority>1.0</priority>
				</url>';
	  */

	//Главная
	/*    $out .= '<url>
				<loc>https://funtrip.me</loc>
				<lastmod>2021-06-02 00:39:03</lastmod>
				<priority>1.0</priority>
			</url>';
		$pages = get_pages([
			'post_type'   => 'page',
			'post_status' => 'publish',
		]);
		foreach ( $pages as $post ) {
			setup_postdata( $post );
			$out .= '<url>
				<loc>'.urldecode( get_the_permalink( $post->ID ) ).'</loc>
				<lastmod>'.get_the_modified_date('Y-d-m', $post->ID ).'</lastmod>
				<priority>0.9</priority>
			 </url>';
		}
		wp_reset_postdata();

		//Категории
		$all_categories = get_categories('hide_empty=0');
		$category_link_array = array();
		foreach( $all_categories as $single_cat ){
			$out .= '<url>
				<loc>'.urldecode(get_category_link($single_cat->term_id)).'</loc>
				<lastmod>2021-06-01 00:20:05</lastmod>

				<lastmod>'.get_metadata('term',$single_cat->term_id,'txseo_seo_modified_date',1).'</lastmod>
				<priority>0.9</priority>
			</url>';
		}

		//Посты
		foreach( $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'posts where (post_type = "page" or post_type = "post") and post_status = "publish"') as $key => $row)
		{
		$post_id = $row->ID;
		// $post_title = $row->post_title;
		$post_title = $row->post_name;
		$bread = '';
		$sep = '/';
		$terms = get_the_terms( $post_id, 'category' );
			if ( is_array( $terms ) && $terms !== array() ) {
				$bread .=  urldecode ($terms[0]->slug);
				$bread .= '' . $sep . '';
				$bread .= $post_title ;
			}
			if ($bread !=''){
				$bread = strtr($bread, " ", "_");
				$bread = mb_strtolower($bread);
				$out .= '<url>
					<loc>https://funtrip.me/'.$bread.'/</loc>
					<lastmod>'.$row->post_modified_gmt.'</lastmod>
					<priority>0.8</priority>
				</url>';
			}
		}
		$out .= '</urlset>';

		//Пишем в файл sitemaps.xml
		$fp = fopen('../sitemap.xml', 'w' );
		if (fwrite($fp, $out )){ }
		fclose( $fp );

		$out = 'User-agent: *
	Disallow: /a*
	Disallow: /b*
	Disallow: /c*
	Disallow: /d*
	Disallow: /e*
	Disallow: /f*
	Disallow: /g*
	Disallow: /h*
	Disallow: /i*
	Disallow: /j*
	Disallow: /k*
	Disallow: /l*
	Disallow: /m*
	Disallow: /n*
	Disallow: /o*
	Disallow: /p*
	Disallow: /q*
	Disallow: /r*
	Disallow: /s*
	Disallow: /t*
	Disallow: /u*
	Disallow: /v*
	Disallow: /w*
	Disallow: /x*
	Disallow: /y*
	Disallow: /z*
	Disallow: /?*
	Disallow: /.*
	Disallow: //*
	Disallow: /-*
	Disallow: /[*
	Disallow: /]*
	Disallow: /_*
	Disallow: /=*
	Disallow: /&*
	Disallow: /%*
	Disallow: /;*
	Disallow: /:*
	Disallow: /#*
	Disallow: /@*
	Disallow: /~*
	Disallow: /0*
	Disallow: /1*
	Disallow: /2*
	Disallow: /3*
	Disallow: /4*
	Disallow: /5*
	Disallow: /6*
	Disallow: /7*
	Disallow: /8*
	Disallow: /9*
	Allow: /robots.txt$
	Allow: /sitemap.xml$
	Allow: /$';
		$out.=$r."\r\n".$r;

		//Категории
		$all_categories = get_categories('hide_empty=0');
		$category_link_array = array();
		foreach( $all_categories as $single_cat ){
			//$out.= $r."Allow: ".get_category_link($single_cat->term_id)."$\r\n".$r;
			$cat_slug = $single_cat->slug;
			$out.= $r."Allow: /".$cat_slug."/$\r\n".$r;
		}

		//Посты
		foreach( $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'posts where (post_type = "page" or post_type = "post") and post_status = "publish"') as $key => $row)
		{
		$post_id = $row->ID;
		$post_title = $row->post_name;
		$bread = '';
		$sep = '/';
		$terms = get_the_terms( $post_id, 'category' );
			if ( is_array( $terms ) && $terms !== array() ){
				$bread .=  ($terms[0]->slug);
				$bread .= '' . $sep . '';
				$bread .= urlencode ($post_title);
			}
			if ($bread !=''){
				$bread = strtr($bread, " ", "_");
				$bread = mb_strtolower($bread);
				$out.= $r."Allow: /".$bread."/$\r\n".$r;
			}
		}
		//Пишем в файл robots.txt
		$fp = fopen('../robots.txt', 'w' );
		if (fwrite($fp, iconv('UTF-8', 'Windows-1251', $out) )){ }
		fclose( $fp );
	}
	*/


	// Хлебные крошки
	function breadcrumbs( $separator = '', $home = 'Главная' ) {
		$url_mas     = explode( '/', parse_url( $_SERVER[ 'REQUEST_URI' ], PHP_URL_PATH ) );
		$path        = array_filter( explode( '/', parse_url( $_SERVER[ 'REQUEST_URI' ], PHP_URL_PATH ) ) );
		$base_url    = ( $_SERVER[ 'HTTPS' ] ? 'https' : 'http' ) . '://' . $_SERVER[ 'HTTP_HOST' ] . '/';
		$breadcrumbs = [ "<li itemscope itemtype=\"https://schema.org/ListItem\" itemprop=\"itemListElement\"><a itemprop=\"item\" class=\"ft-first-breadcrumbs-link\" href=\"$base_url\"><span itemprop=\"name\">$home</span></a><span itemprop=\"position\" content=\"1\"></span></li>" ];
		$count_mas   = count( $url_mas );
		if ( $count_mas > 2 ) {
			$last = end( array_keys( $path ) );
			foreach ( $path as $x => $crumb ) {
				$count_i = 2;
				$title   = ucwords( str_replace( [ '.php', '_' ], [ '', ' ' ], $crumb ) );
				$title   = urldecode( $title );
				//if( $x != $last ){
				$breadcrumbs[] = '<li itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><a itemprop="item" class="ft-first-breadcrumbs-link" href="' . $base_url . $crumb . '/"><span itemprop="name">' . $title . '</span></a><span itemprop="position" content="' . $count_i . '"></span></li>';
				/*} else {
					$breadcrumbs[] = '<li itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><a itemprop="item" class="ft-first-breadcrumbs-link" href="'.$base_url.$crumb.'"><span itemprop="name">'.$title.'</span></a><span itemprop="position" content="'.$count_i.'"></span></li>';
					//$breadcrumbs[] = $title;
				}*/
			}

			return implode( $separator, $breadcrumbs );
			$count_i ++;
		}
	}

	//Отключаем проверку оновления движка/тем/плагинов
	remove_action( 'load-update-core.php', 'wp_update_themes' );
	add_filter( 'pre_site_transient_update_themes', create_function( '$a', "return null;" ) );
	wp_clear_scheduled_hook( 'wp_update_themes' );
	remove_action( 'load-update-core.php', 'wp_update_plugins' );
	add_filter( 'pre_site_transient_update_plugins', create_function( '$a', "return null;" ) );
	wp_clear_scheduled_hook( 'wp_update_plugins' );
	add_filter( 'pre_site_transient_update_core', create_function( '$a', "return null;" ) );
	wp_clear_scheduled_hook( 'wp_version_check' );


	//Удаляем произвольные поля
	add_action( 'do_meta_boxes', 'remove_default_custom_fields_meta_box', 1, 3 );
	function remove_default_custom_fields_meta_box( $post_type, $context, $post ) {
		remove_meta_box( 'postcustom', [ 'post', 'page' ], 'normal', 'high' );
	}

	//Дополнительные поля для СТРАНИЦ
	add_action( 'add_meta_boxes', 'my_extra_fields', 1 );

	function my_extra_fields() {
		add_meta_box( 'my_extra_fields', 'Дополнительные поля', 'extra_fields_box_func', [
			'post',
			'page'
		], 'normal', 'high' );
	}

	function extra_fields_box_func( $post ) { ?>
		<style>
			#custom-additional-field {
				padding: 30px 0 0 0;
			}

			#custom-additional-field label {
				float: left;
				display: block;
				width: 140px;
				margin: 2px 10px 20px 0;
				line-height: 30px;
				text-align: right;
			}

			#custom-additional-field input, #custom-additional-field textarea {
				float: left;
				display: block;
				width: calc(100% - 155px);
				margin: 0 0 20px 0;
				line-height: 30px;
			}
		</style>
		<div id="custom-additional-field">
			<label>Author: </label><input type="text" name="extra[page_author]"
			                              value="<?php echo get_post_meta( $post->ID, 'page_author', true ); ?>">
			<label>Title: </label><input type="text" name="extra[page_meta_title]"
			                             value="<?php echo get_post_meta( $post->ID, 'page_meta_title', true ); ?>">
			<label>Description: </label><input type="text" name="extra[page_meta_description]"
			                                   value="<?php echo get_post_meta( $post->ID, 'page_meta_description', true ); ?>">
			<label>Preface: </label><input type="text" name="extra[page_preface]"
			                               value="<?php echo get_post_meta( $post->ID, 'page_preface', true ); ?>">
			<label>Image: </label><input type="text" name="extra[page_image]"
			                             value="<?php echo get_post_meta( $post->ID, 'page_image', true ); ?>">
			<label>Image (title): </label><input type="text" name="extra[page_image_title]"
			                                     value="<?php echo get_post_meta( $post->ID, 'page_image_title', true ); ?>">
			<label>Image (alt): </label><input type="text" name="extra[page_image_alt]"
			                                   value="<?php echo get_post_meta( $post->ID, 'page_image_alt', true ); ?>">
			<label>Anchor for category: </label><input type="text" name="extra[page_anchor]"
			                                           value="<?php echo get_post_meta( $post->ID, 'page_anchor', true ); ?>">
			<label>First H2: </label><input type="text" name="extra[article_first_h2]"
			                                value="<?php echo get_post_meta( $post->ID, 'article_first_h2', true ); ?>">
			<label>First description: </label><input type="text" name="extra[article_first_description]"
			                                         value="<?php echo get_post_meta( $post->ID, 'article_first_description', true ); ?>">
			<label>1 link anchor: </label><input type="text" name="extra[article_anchor_link_1]"
			                                     value="<?php echo get_post_meta( $post->ID, 'article_anchor_link_1', true ); ?>">
			<label>1 name anchor: </label><input type="text" name="extra[article_anchor_name_1]"
			                                     value="<?php echo get_post_meta( $post->ID, 'article_anchor_name_1', true ); ?>">
			<label>2 link anchor: </label><input type="text" name="extra[article_anchor_link_2]"
			                                     value="<?php echo get_post_meta( $post->ID, 'article_anchor_link_2', true ); ?>">
			<label>2 name anchor: </label><input type="text" name="extra[article_anchor_name_2]"
			                                     value="<?php echo get_post_meta( $post->ID, 'article_anchor_name_2', true ); ?>">
			<label>3 link anchor: </label><input type="text" name="extra[article_anchor_link_3]"
			                                     value="<?php echo get_post_meta( $post->ID, 'article_anchor_link_3', true ); ?>">
			<label>3 name anchor: </label><input type="text" name="extra[article_anchor_name_3]"
			                                     value="<?php echo get_post_meta( $post->ID, 'article_anchor_name_3', true ); ?>">
			<label>4 link anchor: </label><input type="text" name="extra[article_anchor_link_4]"
			                                     value="<?php echo get_post_meta( $post->ID, 'article_anchor_link_4', true ); ?>">
			<label>4 name anchor: </label><input type="text" name="extra[article_anchor_name_4]"
			                                     value="<?php echo get_post_meta( $post->ID, 'article_anchor_name_4', true ); ?>">
			<label>5 link anchor: </label><input type="text" name="extra[article_anchor_link_5]"
			                                     value="<?php echo get_post_meta( $post->ID, 'article_anchor_link_5', true ); ?>">
			<label>5 name anchor: </label><input type="text" name="extra[article_anchor_name_5]"
			                                     value="<?php echo get_post_meta( $post->ID, 'article_anchor_name_5', true ); ?>">
			<label>6 link anchor: </label><input type="text" name="extra[article_anchor_link_6]"
			                                     value="<?php echo get_post_meta( $post->ID, 'article_anchor_link_6', true ); ?>">
			<label>6 name anchor: </label><input type="text" name="extra[article_anchor_name_6]"
			                                     value="<?php echo get_post_meta( $post->ID, 'article_anchor_name_6', true ); ?>">
			<label>7 link anchor: </label><input type="text" name="extra[article_anchor_link_7]"
			                                     value="<?php echo get_post_meta( $post->ID, 'article_anchor_link_7', true ); ?>">
			<label>7 name anchor: </label><input type="text" name="extra[article_anchor_name_7]"
			                                     value="<?php echo get_post_meta( $post->ID, 'article_anchor_name_7', true ); ?>">
			<label>8 link anchor: </label><input type="text" name="extra[article_anchor_link_8]"
			                                     value="<?php echo get_post_meta( $post->ID, 'article_anchor_link_8', true ); ?>">
			<label>8 name anchor: </label><input type="text" name="extra[article_anchor_name_8]"
			                                     value="<?php echo get_post_meta( $post->ID, 'article_anchor_name_8', true ); ?>">
			<label>9 link anchor: </label><input type="text" name="extra[article_anchor_link_9]"
			                                     value="<?php echo get_post_meta( $post->ID, 'article_anchor_link_9', true ); ?>">
			<label>9 name anchor: </label><input type="text" name="extra[article_anchor_name_9]"
			                                     value="<?php echo get_post_meta( $post->ID, 'article_anchor_name_9', true ); ?>">
			<label>10 link anchor: </label><input type="text" name="extra[article_anchor_link_10]"
			                                      value="<?php echo get_post_meta( $post->ID, 'article_anchor_link_10', true ); ?>">
			<label>10 name anchor: </label><input type="text" name="extra[article_anchor_name_10]"
			                                      value="<?php echo get_post_meta( $post->ID, 'article_anchor_name_10', true ); ?>">
			<label>Cite: </label><input type="text" name="extra[cite]"
			                            value="<?php echo get_post_meta( $post->ID, 'cite', true ); ?>">
			<label>Cite author: </label><input type="text" name="extra[cite_author]"
			                                   value="<?php echo get_post_meta( $post->ID, 'cite_author', true ); ?>">
			<input type="hidden" name="extra_fields_nonce" value="<?php echo wp_create_nonce( __FILE__ ); ?>">
			<div style="clear:both;"></div>
		</div>
	<?php }

	add_action( 'save_post', 'extra_fields_update', 0 );
	function extra_fields_update( $post_id ) {
		if ( ! isset( $_POST[ 'extra_fields_nonce' ] ) || ! wp_verify_nonce( $_POST[ 'extra_fields_nonce' ], __FILE__ ) ) {
			return false;
		} // проверка
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}
		if ( ! isset( $_POST[ 'extra' ] ) ) {
			return false;
		}
		$_POST[ 'extra' ] = array_map( 'trim', $_POST[ 'extra' ] );
		foreach ( $_POST[ 'extra' ] as $key => $value ) {
			if ( empty( $value ) ) {
				delete_post_meta( $post_id, $key );
				continue;
			}
			update_post_meta( $post_id, $key, $value );
		}

		return $post_id;
	}

	//Дополнительные поля для РУБРИК
	class trueTaxonomyMetaBox{
		private $opt;
		private $prefix;

		function __construct( $option ) {
			$this->opt    = (object) $option;
			$this->prefix = $this->opt->id . '_';
			foreach ( $this->opt->taxonomy as $taxonomy ) {
				add_action( $taxonomy . '_edit_form_fields', [ &$this, 'fill' ], 10, 2 );
			}
			global $wpdb;
			if ( ! isset( $wpdb->termmeta ) ) {
				$wpdb->termmeta = $wpdb->prefix . 'termmeta';
			}
			add_action( 'edit_term', [ &$this, 'save' ], 10, 1 );
		}

		function fill( $term, $taxonomy ) {
			foreach ( $this->opt->args as $param ) {
				$def        = [ 'id' => '', 'title' => '', 'type' => '', 'desc' => '', 'std' => '', 'args' => [] ];
				$param      = (object) array_merge( $def, $param );
				$meta_key   = $this->prefix . $param->id;
				$meta_value = get_metadata( 'term', $term->term_id, $meta_key, true ) ?: $param->std;
				echo '<tr class ="form-field">';
				echo '<th scope="row"><label for="' . $meta_key . '">' . $param->title . '</label></th>';
				echo '<td>';
				if ( $param->type == 'wp_editor' ) {
					wp_editor( $meta_value, $meta_key, [
						'wpautop'       => 1,
						'media_buttons' => false,
						'textarea_name' => $meta_key, //нужно указывать!
						'textarea_rows' => 10,
						'teeny'         => 0,
						'dfw'           => 0,
						'tinymce'       => 1,
						'quicktags'     => 1
					] );
				} elseif ( $param->type == 'select' ) {
					echo '<select name="' . $meta_key . '" id="' . $meta_key . '"><option value="">...</option>';
					foreach ( $param->args as $val => $name ) {
						echo '<option value="' . $val . '" ' . selected( $meta_value, $val, 0 ) . '>' . $name . '</option>';
					}
					echo '</select>';
					if ( $param->desc ) {
						echo '<p class="description">' . $param->desc . '</p>';
					}
				} elseif ( $param->type == 'checkbox' ) {
					echo '<label><input type="hidden" name="' . $meta_key . '" value=""><input name="' . $meta_key . '" type="' . $param->type . '" id="' . $meta_key . '" ' . checked( $meta_value, 'on', 0 ) . '>' . $param->desc . '</label>';
				} elseif ( $param->type == 'textarea' ) {
					echo '<textarea name="' . $meta_key . '" type="' . $param->type . '" id="' . $meta_key . '" value="' . $meta_value . '" class="large-text">' . esc_html( $meta_value ) . '</textarea>';
					if ( $param->desc ) {
						echo '<p class="description">' . $param->desc . '</p>';
					}
				} else {
					echo '<input name="' . $meta_key . '" type="' . $param->type . '" id="' . $meta_key . '" value="' . $meta_value . '" class="regular-text">';
					if ( $param->desc ) {
						echo '<p class="description">' . $param->desc . '</p>';
					}
				}
				echo '</td>';
				echo '</tr>';
			}
		}

		function save( $term_id ) {
			foreach ( $this->opt->args as $field ) {
				$meta_key = $this->prefix . $field[ 'id' ];
				if ( ! isset( $_POST[ $meta_key ] ) ) {
					continue;
				}
				if ( $meta_value = trim( $_POST[ $meta_key ] ) ) {
					update_metadata( 'term', $term_id, $meta_key, $meta_value, '' );
				} else {
					delete_metadata( 'term', $term_id, $meta_key, '', false );
				}
			}
		}
	}

	add_action( 'init', 'register_additional_term_fields' );
	function register_additional_term_fields() {
		new trueTaxonomyMetaBox( [
			'id'       => 'txseo',
			// id играет роль префикса названий полей
			'taxonomy' => [ 'category', 'post_tag' ],
			// названия таксономий, для которых нужно добавить ниже перечисленные поля
			'args'     => [
				[
					'id'    => 'seo_title',
					'title' => 'Meta-title',
					'type'  => 'text',
					'desc'  => '',
					'std'   => '',
				],
				[
					'id'    => 'seo_description',
					'title' => 'Meta-description',
					'type'  => 'text',
					'desc'  => '',
					'std'   => '',
				],
				[
					'id'    => 'seo_h1',
					'title' => 'Заголовок H1',
					'type'  => 'text',
					'desc'  => '',
					'std'   => '',
				],
				[
					'id'    => 'seo_thesis',
					'title' => 'Тезис',
					'type'  => 'text',
					'desc'  => '',
					'std'   => '',
				],
				[
					'id'    => 'seo_h2',
					'title' => 'Заголовок h2',
					'type'  => 'text',
					'desc'  => '',
					'std'   => '',
				],
				[
					'id'    => 'seo_image',
					'title' => 'Фотография',
					'type'  => 'text',
					'desc'  => '',
					'std'   => '',
				],
				[
					'id'    => 'seo_image_title',
					'title' => 'Photo title',
					'type'  => 'text',
					'desc'  => '',
					'std'   => '',
				],
				[
					'id'    => 'seo_image_alt',
					'title' => 'Photo alt',
					'type'  => 'text',
					'desc'  => '',
					'std'   => '',
				],
				[
					'id'    => 'seo_date',
					'title' => 'Date',
					'type'  => 'text',
					'desc'  => '',
					'std'   => '',
				],
				[
					'id'    => 'seo_modified_date',
					'title' => 'Modified',
					'type'  => 'text',
					'desc'  => '',
					'std'   => '',
				]
			]
		] );
	}

	remove_filter( 'term_description', 'wpautop' );


	/*
	//дерево комментариев
	function walk_comments_recursive($post_id, $parent_id = 0){
		$args = array(
			'post_id' => $post_id,
			'parent'  => $parent_id,
			'status'  => 'approve',
			'type'    => ''
		);
		$result = "";
		if( $comments = get_comments( $args ) ){
			if ($parent_id != 0) $result .= '<div class="children">';
			else $result .= '<div id="comment">';
			foreach( $comments as $comment ){
				$result .= '<div class="parent-comment" id="comment-'.$comment->comment_ID.'">';
				$result .= '<div class="comment">';
				$result .= '<span class="avatar"></span>';
				$result .= '<p>'.$comment->comment_content.'</p>';
				$result .= '<span class="data-comment">'.$comment->comment_author.' '.$comment->comment_date.'</span>';
				$result .= '<button class="reply-comment" data-reply="'.$comment->comment_ID.'">Ответить</button>';
				$result .= '</div>';
				$result .= walk_comments_recursive($post_id, $comment->comment_ID);
				$result .= '</div>';
				if ($parent_id == 0) $result .= '';
			}
			$result .= '</div>';
		}
		return $result;
	}
	*/


	function top_customize_register( $wp_customize ) {
		$wp_customize->add_section( 'top_section', [
				'title'       => 'TOP главной',
				'capability'  => 'edit_theme_options',
				'description' => ''
			] );

		$wp_customize->add_setting( 'top1', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'top1_control', [
				'type'     => 'text',
				'label'    => "ID 1-го TOP",
				'section'  => 'top_section',
				'settings' => 'top1'
			] );

		$wp_customize->add_setting( 'top2', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'top2_control', [
				'type'     => 'text',
				'label'    => "ID 2-го TOP",
				'section'  => 'top_section',
				'settings' => 'top2'
			] );

		$wp_customize->add_setting( 'top3', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'top3_control', [
				'type'     => 'text',
				'label'    => "ID 3-го TOP",
				'section'  => 'top_section',
				'settings' => 'top3'
			] );

		$wp_customize->add_setting( 'top4', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'top4_control', [
				'type'     => 'text',
				'label'    => "ID 4-го TOP",
				'section'  => 'top_section',
				'settings' => 'top4'
			] );

		$wp_customize->add_setting( 'top5', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'top5_control', [
				'type'     => 'text',
				'label'    => "ID 5-го TOP",
				'section'  => 'top_section',
				'settings' => 'top5'
			] );

		$wp_customize->add_setting( 'top6', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'top6_control', [
				'type'     => 'text',
				'label'    => "ID 6-го TOP",
				'section'  => 'top_section',
				'settings' => 'top6'
			] );

		$wp_customize->add_setting( 'top7', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'top7_control', [
				'type'     => 'text',
				'label'    => "ID 7-го TOP",
				'section'  => 'top_section',
				'settings' => 'top7'
			] );

		$wp_customize->add_setting( 'top8', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'top8_control', [
				'type'     => 'text',
				'label'    => "ID 8-го TOP",
				'section'  => 'top_section',
				'settings' => 'top8'
			] );

		$wp_customize->add_setting( 'top9', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'top9_control', [
				'type'     => 'text',
				'label'    => "ID 9-го TOP",
				'section'  => 'top_section',
				'settings' => 'top9'
			] );

		$wp_customize->add_setting( 'top10', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'top10_control', [
				'type'     => 'text',
				'label'    => "ID 10-го TOP",
				'section'  => 'top_section',
				'settings' => 'top10'
			] );
	}

	add_action( 'customize_register', 'top_customize_register' );


	function news_customize_register( $wp_customize ) {
		$wp_customize->add_section( 'news_section', [
				'title'       => 'NEWS главной',
				'capability'  => 'edit_theme_options',
				'description' => ''
			] );

		$wp_customize->add_setting( 'news1', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'news1_control', [
				'type'     => 'text',
				'label'    => "ID 1-ой NEWS",
				'section'  => 'news_section',
				'settings' => 'news1'
			] );

		$wp_customize->add_setting( 'news2', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'news2_control', [
				'type'     => 'text',
				'label'    => "ID 2-ой NEWS",
				'section'  => 'news_section',
				'settings' => 'news2'
			] );

		$wp_customize->add_setting( 'news3', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'news3_control', [
				'type'     => 'text',
				'label'    => "ID 3-ей NEWS",
				'section'  => 'news_section',
				'settings' => 'news3'
			] );

		$wp_customize->add_setting( 'news4', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'news4_control', [
				'type'     => 'text',
				'label'    => "ID 4-ой NEWS",
				'section'  => 'news_section',
				'settings' => 'news4'
			] );

		$wp_customize->add_setting( 'news5', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'news5_control', [
				'type'     => 'text',
				'label'    => "ID 5-ой NEWS",
				'section'  => 'news_section',
				'settings' => 'news5'
			] );

		$wp_customize->add_setting( 'news6', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'news6_control', [
				'type'     => 'text',
				'label'    => "ID 6-ой NEWS",
				'section'  => 'news_section',
				'settings' => 'news6'
			] );

		$wp_customize->add_setting( 'news7', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'news7_control', [
				'type'     => 'text',
				'label'    => "ID 7-ой NEWS",
				'section'  => 'news_section',
				'settings' => 'news7'
			] );

		$wp_customize->add_setting( 'news8', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'news8_control', [
				'type'     => 'text',
				'label'    => "ID 8-ой NEWS",
				'section'  => 'news_section',
				'settings' => 'news8'
			] );

		$wp_customize->add_setting( 'news9', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'news9_control', [
				'type'     => 'text',
				'label'    => "ID 9-ой NEWS",
				'section'  => 'news_section',
				'settings' => 'news9'
			] );

		$wp_customize->add_setting( 'news10', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'news10_control', [
				'type'     => 'text',
				'label'    => "ID 10-ой NEWS",
				'section'  => 'news_section',
				'settings' => 'news10'
			] );
	}

	add_action( 'customize_register', 'news_customize_register' );


	function aside_customize_register( $wp_customize ) {
		$wp_customize->add_section( 'aside_section', [
				'title'       => 'ASIDE',
				'capability'  => 'edit_theme_options',
				'description' => ''
			] );

		$wp_customize->add_setting( 'aside1', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'aside1_control', [
				'type'     => 'text',
				'label'    => "ID 1-го ASIDE",
				'section'  => 'aside_section',
				'settings' => 'aside1'
			] );

		$wp_customize->add_setting( 'aside2', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'aside2_control', [
				'type'     => 'text',
				'label'    => "ID 2-го ASIDE",
				'section'  => 'aside_section',
				'settings' => 'aside2'
			] );

		$wp_customize->add_setting( 'aside3', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'aside3_control', [
				'type'     => 'text',
				'label'    => "ID 3-го ASIDE",
				'section'  => 'aside_section',
				'settings' => 'aside3'
			] );

		$wp_customize->add_setting( 'aside4', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'aside4_control', [
				'type'     => 'text',
				'label'    => "ID 4-го ASIDE",
				'section'  => 'aside_section',
				'settings' => 'aside4'
			] );

		$wp_customize->add_setting( 'aside5', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'aside5_control', [
				'type'     => 'text',
				'label'    => "ID 5-го ASIDE",
				'section'  => 'aside_section',
				'settings' => 'aside5'
			] );

		$wp_customize->add_setting( 'aside6', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'aside6_control', [
				'type'     => 'text',
				'label'    => "ID 6-го ASIDE",
				'section'  => 'aside_section',
				'settings' => 'aside6'
			] );

		$wp_customize->add_setting( 'aside7', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'aside7_control', [
				'type'     => 'text',
				'label'    => "ID 7-го ASIDE",
				'section'  => 'aside_section',
				'settings' => 'aside7'
			] );

		$wp_customize->add_setting( 'aside8', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'aside8_control', [
				'type'     => 'text',
				'label'    => "ID 8-го ASIDE",
				'section'  => 'aside_section',
				'settings' => 'aside8'
			] );

		$wp_customize->add_setting( 'aside9', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'aside9_control', [
				'type'     => 'text',
				'label'    => "ID 9-го ASIDE",
				'section'  => 'aside_section',
				'settings' => 'aside9'
			] );

		$wp_customize->add_setting( 'aside10', [
				'default' => '',
				'type'    => 'option'
			] );
		$wp_customize->add_control( 'aside10_control', [
				'type'     => 'text',
				'label'    => "ID 10-го ASIDE",
				'section'  => 'aside_section',
				'settings' => 'aside10'
			] );
	}

	add_action( 'customize_register', 'aside_customize_register' );


	// подсчет просмотров
	add_action( 'wp_head', 'custom_postviews' );
	function custom_postviews() {
		$meta_key     = 'views'; // Ключ мета поля, куда будет записываться количество просмотров.
		$who_count    = 0; // Чьи посещения считать? 0 - Всех. 1 - Только гостей. 2 - Только зарегистрированных пользователей.
		$exclude_bots = 1; // Исключить ботов, роботов, пауков и прочую нечесть :)? 0 - нет, пусть тоже считаются. 1 - да, исключить из подсчета.
		global $user_ID, $post;
		if ( is_singular() ) {
			$id = (int) $post->ID;
			static $post_views = false;
			if ( $post_views ) {
				return true;
			}
			$post_views   = (int) get_post_meta( $id, $meta_key, true );
			$should_count = false;
			switch ( (int) $who_count ) {
				case 0:
					$should_count = true;
					break;
				case 1:
					if ( (int) $user_ID == 0 ) {
						$should_count = true;
					}
					break;
				case 2:
					if ( (int) $user_ID > 0 ) {
						$should_count = true;
					}
					break;
			}
			if ( (int) $exclude_bots == 1 && $should_count ) {
				$useragent = $_SERVER[ 'HTTP_USER_AGENT' ];
				$notbot    = "Mozilla|Opera";
				$bot       = "Bot/|robot|Slurp/|yahoo";
				if ( ! preg_match( "/$notbot/i", $useragent ) || preg_match( "!$bot!i", $useragent ) ) {
					$should_count = false;
				}
			}

			if ( $should_count ) {
				if ( ! update_post_meta( $id, $meta_key, ( $post_views + 1 ) ) ) {
					add_post_meta( $id, $meta_key, 1, true );
				}
			}
		}

		return true;
	}

	/*
	/* подсчет и вывод лайков */
	//---- Добавляем кнопки выше содержимого записи
	/*function ip_post_likes($content) {
		if(is_singular('post')) {
			ob_start(); ?>
			<ul class="likes">
				<li class="likes__item likes__item--like">
					<a href="<?php echo add_query_arg('post_action', 'like'); ?>">Like (<?php echo ip_get_like_count('likes') ?>)</a>
				</li>
				<li class="likes__item likes__item--dislike">
					<a href="<?php echo add_query_arg('post_action', 'dislike'); ?>">Dislike (<?php echo ip_get_like_count('dislikes') ?>)</a>
				</li>
			</ul><?php
			$output = ob_get_clean();
			return $content . $output ;
		}else{
			return $content;
		}
	}
	add_filter('the_content', 'ip_post_likes');*/
	/*function ip_post_likes() {
		if(is_singular('post')) {
			ob_start(); ?>
			<ul>
			   <li>
					<a href="<?php echo add_query_arg('post_action', 'like'); ?>">Like (<?php echo ip_get_like_count('likes') ?>)</a>
				</li>
				<li>
					<a href="<?php echo add_query_arg('post_action', 'dislike'); ?>">Dislike (<?php echo ip_get_like_count('dislikes') ?>)</a>
				</li>
			</ul><?php
			$output = ob_get_clean();
			return $output;
		}
	}

	//---- Получаем количество лайков и дизлайков
	function ip_get_like_count($type = 'likes') {
		$current_count = get_post_meta(get_the_id(), $type, true);
		return ($current_count ? $current_count : 0);
	}
	//---- Обрабатываем лайки и дизлайки
	function ip_process_like() {
		$processed_like = false;
		$redirect = false;
		// Проверяем, это лайк или дизлайк
		if(is_singular('post')) {
			if(isset($_GET['post_action'])) {
				if($_GET['post_action'] == 'like') {
					// Лайк
					$like_count = get_post_meta(get_the_id(), 'likes', true);
					if($like_count) {
						$like_count = $like_count + 1;
					}else {
						$like_count = 1;
					}
					$processed_like = update_post_meta(get_the_id(), 'likes', $like_count);
				}elseif($_GET['post_action'] == 'dislike') {
						// Дизлайк
					$dislike_count = get_post_meta(get_the_id(), 'dislikes', true);

					if($dislike_count) {
						$dislike_count = $dislike_count + 1;
					}else {
						$dislike_count = 1;
					}
					$processed_like = update_post_meta(get_the_id(), 'dislikes', $dislike_count);
				}
				if($processed_like) {
					$redirect = get_the_permalink();
				}
			}
		}
		//Редирект
		if($redirect) {
			wp_redirect($redirect);
			die;
		}
	}
	add_action('template_redirect', 'ip_process_like');
	*/

	//рандомный выбор видеофона
	function getRandomFile( $files_path = __DIR__ . '/home/domnasutki/domains/funtrip.me/public_html/media' ) {
		$last_files = filter_input( INPUT_COOKIE, 'last_file' );
		$scandir    = array_diff( scandir( $files_path ), [ '.', '..' ] );
		$files      = array_diff( $scandir, explode( ',', $last_files ) );
		if ( ! $files ) {
			if ( count( $scandir ) > 0 ) {
				$last_files = '';
				setcookie( 'last_file', '' );
				$files = $scandir;
			} else {
				return null;
			}
		}
		shuffle( $files );
		$last_files .= $files[ 0 ] . ',';
		setcookie( 'last_file', $last_files );

		return $files_path . '/' . $files[ 0 ];
	}

	$result        = getRandomFile( '/home/domnasutki/domains/funtrip.me/public_html/media' );
	$result_domain = substr( $result, 54 );
	//$media = str_replace('.mp4','',$result_domain);
	$GLOBALS[ 'media' ] = str_replace( '.mp4', '', $result_domain );


	/*  корректировка url для пагинации в категориях  */
	function codernote_request( $query_string ) {
		if ( isset( $query_string[ 'page' ] ) ) {
			if ( '' != $query_string[ 'page' ] ) {
				if ( isset( $query_string[ 'name' ] ) ) {
					unset( $query_string[ 'name' ] );
				}
			}
		}

		return $query_string;
	}

	add_filter( 'request', 'codernote_request' );
	add_action( 'pre_get_posts', 'codernote_pre_get_posts' );
	function codernote_pre_get_posts( $query ) {
		if ( $query->is_main_query() && ! $query->is_feed() && ! is_admin() ) {
			$query->set( 'paged', str_replace( '/', '', get_query_var( 'page' ) ) );
		}
	}


	//load more категорий
	function true_load_posts() {
		$args                  = unserialize( stripslashes( $_POST[ 'query' ] ) );
		$args[ 'paged' ]       = $_POST[ 'page' ] + 1;
		$args[ 'post_status' ] = 'publish';
		$q                     = new WP_Query( $args );
		$i                     = 1;
		if ( $q->have_posts() ):
			while ( $q->have_posts() ): $q->the_post();
				$postID       = get_the_ID();
				$comment      = get_comments_number( $postID );
				$full_preface = get_post_meta( $postID, 'page_preface', true );
				$min_preface  = mb_strimwidth( $full_preface, 0, 220, '...' );
				$total        = wp__get_data( 'vote-total', $postID );
				$rating       = wp__get_data( 'vote-rating', $postID );
				$abs          = round( $rating / $total, 1 );
				if ( ( $total == 0 ) || ( $rating == 0 ) ) {
					$abs = 4.2;
				};
				if ( $abs - floor( $abs ) != 0 ) {
					$abs_round = round( $abs, 0 );
				} else {
					$abs_round = $abs;
				}; ?>
				<article itemscope itemtype="https://schema.org/Article" class="ft-item ft-item-load-more">
					<h3 itemprop="headline"><?php the_title(); ?></h3>
					<div class="ft-item-content">
						<img itemprop="image" content="<?php echo get_post_meta( $postID, 'page_image', true ); ?>"
						     data-src="<?php echo get_post_meta( $postID, 'page_image', true ); ?>"
						     title="<?php echo get_post_meta( $postID, 'page_image_title', true ); ?>"
						     alt="<?php echo get_post_meta( $postID, 'page_image_alt', true ); ?>"/>
						<a itemprop="url mainEntityOfPage" class="ft-item-content-link"
						   href="<?php echo the_permalink(); ?>">Читать</a>
						<p itemprop="description"><?php echo $min_preface; ?></p>
						<div itemscope itemtype="https://schema.org/InteractionCounter" itemprop="interactionStatistic"
						     class="ft-item-content-monitor">
                        <span itemprop="interactionType" content="https://schema.org/ViewAction"
                              class="ft-item-content-monitor-views"
                              title="Всего просмотров: <?php echo get_post_meta( $postID, 'views', true ); ?>">
                            <span
	                            itemprop="userInteractionCount"><?php echo get_post_meta( $postID, 'views', true ); ?></span>
                        </span>
							<span itemprop="interactionType" content="https://schema.org/AssessAction"
							      class="ft-item-content-monitor-rating"
							      title="Текущий рейтинг: <?php echo $abs_round; ?>">
                            <span itemprop="userInteractionCount"
                                  content="<?php echo $abs_round; ?>"><?php echo $abs_round; ?></span>
                        </span>
							<span itemprop="interactionType" content="https://schema.org/CommentAction"
							      class="ft-item-content-monitor-comment"
							      title="Опубликовано комментариев: <?php echo $comment; ?>">
                            <span itemprop="userInteractionCount"><?php echo $comment; ?></span>
                        </span>
						</div>
						<details>
							<summary><span class="ft-item-content-details-open">&#10247;</span></summary>
							<div class="ft-item-content-details-case">
								<div class="ft-item-content-details-case-clause">Автор: <span
										itemprop="author"><?php echo get_post_meta( $postID, 'page_author', true ); ?></span>
								</div>
								<div class="ft-item-content-details-case-clause">Создано: <span itemprop="datePublished"
								                                                                content="<?php echo get_the_date( 'c', $postID ); ?>"><?php echo get_the_date( 'd.m.Y', $postID ); ?></span>
								</div>
								<div class="ft-item-content-details-case-clause">Изменено: <span itemprop="dateModified"
								                                                                 content="<?php echo get_the_modified_date( 'c', $postID ); ?>"><?php echo get_the_modified_date( 'd.m.Y', $postID ); ?></span>
								</div>
								<div itemscope itemtype="https://schema.org/Organization" itemprop="publisher">
									<span itemprop="name">FunTrip</span>
									<img itemprop="logo" data-src="/favicon.png"
									     content="https://funtrip.me/favicon.png" title="FunTrip | Логотип"
									     alt="FunTrip | Логотип"/>
								</div>
							</div>
						</details>
					</div>
					<div style="clear:both;"></div>
				</article>
				<?php

				if ( $i == 5 ) {
					?>
					<div id="ft-quiz-11" class="ft-quiz"></div>
				<?php };
				/*if($i==2){?>
					<div id="ft-quiz-12" class="ft-quiz"></div>
				<?php };*/

				$i ++;
			endwhile;
			get_template_part( 'template-parts/aside', 'page' );
		endif;
		wp_reset_postdata();
		die();
	}

	add_action( 'wp_ajax_loadmore', 'true_load_posts' );
	add_action( 'wp_ajax_nopriv_loadmore', 'true_load_posts' );


	//рейтинг
	if ( isset( $_GET[ 'do' ] ) && $_GET[ 'do' ] == 'ajax' ) {
		if ( isset( $_POST[ 'num' ] ) ) {
			if ( ( isset( $_POST[ 'id' ] ) && is_numeric( $_POST[ 'id' ] ) ) && in_array( (int) $_POST[ 'num' ], [
					1,
					2,
					3,
					4,
					5
				] ) ) {
				$id  = $_POST[ 'id' ];
				$num = $_POST[ 'num' ];
				if ( ! $_COOKIE[ "vote-post-" . $id ] ) {
					wp__set_data( 'vote-total', $id, (int) wp__get_data( 'vote-total', $id ) + 1 );
					wp__set_data( 'vote-rating', $id, (int) wp__get_data( 'vote-rating', $id ) + $num );
					$total  = wp__get_data( 'vote-total', $id );
					$rating = wp__get_data( 'vote-rating', $id );
					if ( $total == 0 ) {
						$total = 1;
					}
					// echo ($rating/($total*5))*100;
					echo round( $rating / $total, 1 );
				} else {
					echo 'limit';
				}
				die();
			}
		}
		die();
	}
	function rating( $ID, $voted = true ) {
		if ( $voted ) {
			$disable_class = isset( $_COOKIE[ "vote-post-" . $ID ] ) ? ' ft-rating-disabled' : '';
		} else {
			$disable_class = ' ft-rating-disabled';
		}
		$title  = get_the_title( $ID );
		$total  = wp__get_data( 'vote-total', $ID );
		$rating = wp__get_data( 'vote-rating', $ID );
		if ( $total == 0 ) {
			$pr = 86;
		} else {
			$pr = $rating / $total / 5 * 100;
		}
		//$abs = round($rating/$total, 1);
		$abs = $total > 0 ? round( $rating / $total, 1 ) : 0;
		if ( $abs - floor( $abs ) != 0 ) {
			$abs_round = round( $abs, 0 );
		} else {
			$abs_round = $abs;
		};
		echo '<div itemscope itemtype="https://schema.org/InteractionCounter" itemprop="interactionStatistic" class="ft-rating" data-id="' . $ID . '" data-total="' . $total . '" data-rating="' . $rating . '">
    <span class="ft-rating-title">Помогите стать лучше!<br>Оставьте свою оценку.</span><div class="ft-rating-star"><ol><li></li><li></li><li></li><li></li><li></li></ol><span class="fr-rating-star-mask" style="width: ' . $pr . '%;"><span></span><span></span><span></span><span></span><span></span></span></div><div itemprop="interactionType" content="https://schema.org/AssessAction" class="ft-rating-total" title="Текущий рейтинг ' . $abs_round . ' из 5"><span itemprop="userInteractionCount" content="' . $abs_round . '">' . $abs_round . '</span><span> из 5</span></div><div style="clear:both;"></div><span class="ft-rating-thanks" style="display:none;">Спасибо !</span></div>';
	}

	function wp__set_data( $name, $postID, $value ) {
		$count_key = $name;
		$count     = get_post_meta( $postID, $count_key, true );
		if ( $count == '' ) {
			$count = 0;
			delete_post_meta( $postID, $count_key );
			add_post_meta( $postID, $count_key, '0' );
		} else {
			update_post_meta( $postID, $count_key, $value );
		}
	}

	function wp__get_data( $name, $postID ) {
		$count_key = $name;
		$count     = get_post_meta( $postID, $count_key, true );
		if ( $count == '' ) {
			delete_post_meta( $postID, $count_key );
			add_post_meta( $postID, $count_key, '0' );

			return "0";
		}

		return $count . '';
	}


	//пересохранение даты обновления рубрики
	add_action( 'edited_category', 'update_cat_f' );
	function update_cat_f( $category_id ) {
		update_term_meta( $category_id, 'txseo_seo_modified_date', date( 'Y-m-d' ) );
	}

	add_action( 'pre_post_update', 'update_post_f' );
	function update_post_f( $post_id ) {
		$cats = wp_get_post_categories( $post_id );
		foreach ( $cats as $cat ) :
			update_term_meta( $cat, 'txseo_seo_modified_date', date( 'Y-m-d' ) );
		endforeach;
	}
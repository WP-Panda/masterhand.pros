<?php
	/**
	 * Created by PhpStorm.
	 * User: WP_PANDA
	 * Date: 17.04.2019
	 * Time: 10:49
	 */
	if ( !defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}

	class WPP_Pf_Breadcrumbs{
		/**
		 * Breadcrumb trail.
		 *
		 * @var array
		 */
		private static $crumbs = [];
		private static $args;

		public static function init() {
			if ( class_exists( 'Woocommerce' ) ) :
				remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
				add_action( 'woocommerce_before_main_content', __CLASS__ . '::breadcrumbs_string', 20 );
			endif;

			add_action( 'wpp_breadcrumbs', __CLASS__ . '::breadcrumbs_string' );
			#add_action( 'wp_head', __CLASS__ . '::breadcrumbs_json_ld' );
		}

		/**
		 * Вывод разметки json-ld
		 */
		public static function breadcrumbs_json_ld() {
			$out = self::bread_array();
			echo $out[ 'bread_json' ];
		}

		/**
		 * Вывод Навигационной цепочки
		 */
		public static function breadcrumbs_string() {

			$out = self::bread_array();
			$html = apply_filters( 'wpp_pf_breadcrumb_wrap', '%s' );

			printf( $html, $out[ 'bread_string' ] );
		}


		/**
		 * Формирование навигационной цепочки
		 *
		 * @return array
		 */
		public static function bread_array() {


			$args = [
				'delimiter' => '',
				'home_text' => __( 'Home', 'wpp-fr' ),
				'error_404' => __( 'Error 404', 'wpp-fr' ),
				'author'    => 'Архив автора: ',
				'year'      => 'Год XXX',
				'month'     => 'Месяц XXX',
				'markup'    => 'microdata',
				'nofollow'  => false,
				'show_home' => false,
				'type'      => 'list'
				// list|plane
			];


			self::$args = apply_filters( 'wpp_fr_breadcrumb_setting', $args );

			////if ( ! empty( self::$args[ 'home' ] ) ) {
			self::add_crumb( self::$args[ 'home_text' ], apply_filters( 'wpp_breadcrumb_home_url', home_url() ) );
			////}


			$breadcrumb = self::generate();

			$args = (object)self::$args;
			$wrap_tag = $args->type === 'list' ? 'ul' : 'div';
			$item_tag = $args->type === 'list' ? 'li' : 'span';

			$classes = apply_filters( 'wpp_fr_breadcrumbs_classes', [
				'wrap_class' => 'wpp_breadcrumbs',
				'item_class' => 'wpp_br_item',
				'link_class' => 'wpp_br_link'
			] );


			// Массив разметок
			switch ( $args->markup ):
				case 'rdf.data-vocabulary.org': //RDF - он вроде как устарел, но пусть будет
					$mark = (object)[
						'wrap_before' => sprintf( '<%s class="%s" prefix="v: http://rdf.data-vocabulary.org/#">', $wrap_tag, $classes[ 'wrap_class' ] ),
						'wrap_after'  => sprintf( '</%s>', $wrap_tag ),
						'link_wrap'   => '<' . $item_tag . ' class="' . $classes[ 'item_class' ] . '" typeof="v:Breadcrumb"><a class="' . $classes[ 'link_class' ] . '" ' . ( $args->nofollow ? 'rel="nofollow" ' : '' ) . 'href="%s" rel="v:url" property="v:title">%s</a></' . $item_tag . '>'
					];
					break;
				case  'microdata' : //mikroformat
					$mark = (object)[
						'wrap_before' => sprintf( '<%s class="%s" itemscope itemtype="http://schema.org/BreadcrumbList">', $wrap_tag, $classes[ 'wrap_class' ] ),
						'wrap_after'  => sprintf( '</%s>', $wrap_tag ),
						'link_wrap'   => '<' . $item_tag . ' class="' . $classes[ 'item_class' ] . '" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a class="' . $classes[ 'link_class' ] . '" ' . ( $args->nofollow ? 'rel="nofollow" ' : '' ) . 'href="%s" itemprop="item"><span itemprop="name">%s</span><meta itemprop="position" content="%s" /></a></' . $item_tag . '>'
					];
					break;
				case  'rdfa': //RDFa
					$mark = (object)[
						'wrap_before' => sprintf( '<%s class="%s" vocab="http://schema.org/" typeof="BreadcrumbList">', $wrap_tag, $classes[ 'wrap_class' ] ),
						'wrap_after'  => sprintf( '</%s>', $wrap_tag ),
						'link_wrap'   => '<' . $item_tag . ' class="' . $classes[ 'item_class' ] . '" property="itemListElement" typeof="ListItem"><a class="' . $classes[ 'link_class' ] . '" ' . ( $args->nofollow ? 'rel="nofollow" ' : '' ) . 'property="item" typeof="WebPage" href="%s"><span property="name">%s</span><meta property="position" content="%s"></a></' . $item_tag . '>'
					];
					break;
				case 'json-ld': //json-ld
				default:
					$mark = (object)[
						'wrap_before' => sprintf( '<%s class="%s">', $classes[ 'wrap_class' ] ),
						'wrap_after'  => sprintf( '</%s>', $wrap_tag ),
						'link_wrap'   => '<' . $item_tag . ' class="' . $classes[ 'item_class' ] . '"><a class="' . $classes[ 'link_class' ] . '" ' . ( $args->nofollow ? 'rel="nofollow" ' : '' ) . 'href="%s">%s</a></' . $item_tag . '>'
					];
			endswitch;


			$out = $mark->wrap_before;
			$json = '';

			if ( 'json-ld' === $args->markup ) {
				$json = [
					'@context' => 'http://schema.org',
					'@type'    => 'BreadcrumbList',
				];
			}

			$count = count( $breadcrumb );
			$n = 1;
			foreach ( $breadcrumb as $key => $crumb ) {


				$out .= sprintf( $mark->link_wrap, esc_url( $crumb[ 1 ] ), esc_html( $crumb[ 0 ] ), $n );

				if ( 'json-ld' === $args->markup ) {
					$json[ 'itemListElement' ][] = [
						'@type'    => 'ListItem',
						'position' => $n,
						'item'     => [
							'@id'  => esc_url( $crumb[ 1 ] ),
							'name' => esc_html( $crumb[ 0 ] )
						]
					];
				}

				if ( $n !== $count ) {
					$out .= $args->delimiter;
				}

				$n++;
			}


			$out .= $mark->wrap_after;

			if ( !empty( $json ) ) {
				$json = sprintf( '<script type = "application/ld+json" >%s</script>', json_encode( $json ) );
			}


			return [
				'bread_string' => $out,
				'bread_json'   => $json
			];


		}


		/**
		 * Добавляет одну ссылку.
		 *
		 * @param string $name Анкор
		 * @param string $link Ссылка
		 */
		public static function add_crumb( $name, $link = null ) {
			// костыльдля не добавлять дубликаты, при вызове экземпляра несколько раз
			//  надо если json разметка, подумать как избавиться от этого по другому

			if ( !in_array( $link, self::$crumbs, true ) ) {
				self::$crumbs[ $link ] = [
					wp_strip_all_tags( $name ),
					$link,
				];
			}
		}

		/**
		 * Очистка Крошек.
		 */
		public function reset() {
			self::$crumbs = [];
		}


		/**
		 * Получение крошек.
		 *
		 * @return array
		 */
		public static function get_breadcrumb() {
			return apply_filters( 'wpp_get_breadcrumb', self::$crumbs, __CLASS__ );
		}

		/**
		 * Генерация крошек.
		 *
		 * @return array of breadcrumbs
		 */
		public static function generate() {

			/**
			 * Условные теги
			 */
			$conditionals = [
				'is_home',
				'is_front_page',
				'is_404',
				'is_attachment',
				'is_single',
				'is_page',
				'is_post_type_archive',
				'is_category',
				'is_tag',
				'is_author',
				'is_date',
				'is_tax',
			];

			/**
			 * Если включен WooCommerce
			 */
			if ( class_exists( 'Woocommerce' ) ) {
				$conditionals[] = 'is_product_category';
				$conditionals[] = 'is_product_tag';
				$conditionals[] = 'is_shop';
			}


			//if ( ( ! is_front_page() && ! ( is_post_type_archive() && absint( get_option( 'page_on_front' ) ) === wc_get_page_id( 'shop' ) ) ) || is_paged() ) {

			//if ( ( ! is_front_page() && ! is_post_type_archive() ) || is_paged() ) {

			if ( empty( self::$args[ 'show_home' ] ) && ( is_front_page() || is_home() ) ) {
				return [];
			}

			foreach ( $conditionals as $conditional ) {
				if ( call_user_func( $conditional ) ) {
					#wpp_dump($conditional);
					call_user_func( __CLASS__ . '::' . $conditional );
					break;
				}
			}

			self::search_trail();
			self::paged_trail();

			return self::get_breadcrumb();
			//}

			//return array();
		}


		/**
		 * Добавление магазина в хлебневе крошки.
		 */
		private static function prepend_shop_page() {
			$shop_page_id = wc_get_page_id( 'shop' );
			#wpp_dump( 'ffffffffffffffffffffffff' );
			if ( class_exists( 'Woocommerce' ) ) :

				$permalinks = wc_get_permalink_structure();
				$shop_page_id = wc_get_page_id( 'shop' );
				$shop_page = get_post( $shop_page_id );

				// If permalinks contain the shop page in the URI prepend the breadcrumb with shop
				if ( $shop_page_id && $shop_page && isset( $permalinks[ 'product_base' ] ) && strstr( $permalinks[ 'product_base' ], '/' . $shop_page->post_name ) && get_option( 'page_on_front' ) != $shop_page_id ) {

					self::add_crumb( get_the_title( $shop_page ), get_permalink( $shop_page ) );
				}
			endif;
		}

		/**
		 * Главная страница.
		 */
		private static function is_home() {
			self::add_crumb( self::$args[ 'home_text' ], apply_filters( 'wpp_breadcrumb_home_url', home_url() ) );
		}

		/**
		 * 404.
		 */
		private static function is_404() {
			self::add_crumb( self::$args[ 'error_404' ] );
		}

		/**
		 * Вложение.
		 */
		private static function is_attachment() {
			global $post;
			self::is_single( $post->post_parent, get_permalink( $post->post_parent ) );
			self::add_crumb( get_the_title(), get_permalink() );
		}

		/**
		 * Единичная запись
		 *
		 * @param int    $post_id
		 * @param string $permalink
		 */
		private static function is_single( $post_id = 0, $permalink = null ) {

			if ( !$post_id ) {

				global $post;

			} else {

				$post = get_post( $post_id );


			}

			if ( !$permalink ) {
				$permalink = get_permalink( $post->ID, false );
			}

			if ( 'product' === get_post_type( $post ) && class_exists( 'Woocommerce' ) ) {
				self::prepend_shop_page();
				if ( $terms = wc_get_product_terms( $post->ID, 'product_cat', [
					'orderby' => 'parent',
					'order'   => 'DESC'
				] ) ) {
					$main_term = apply_filters( 'wpp_breadcrumb_main_term', $terms[ 0 ], $terms );
					self::term_ancestors( $main_term->term_id, 'product_cat' );
					self::add_crumb( $main_term->name, get_term_link( $main_term ) );
				}

			} elseif ( 'post' !== get_post_type( $post ) ) {

				$post_type_name = get_post_type( $post );

				#архив типа записей
				$post_type_archive = apply_filters( "wpp_pf_breadcrumbs_{$post_type_name}_archive", [
					'name' => get_post_type_object( $post_type_name ),
					'link' => get_post_type_archive_link( $post_type_name )
				] );

				if ( false !== $post_type_archive[ 'link' ] ) :
					self::add_crumb( $post_type_archive[ 'name' ], $post_type_archive[ 'link' ] );
				endif;

				#термин таксономии записи
				$taxes = get_object_taxonomies( $post, 'names' );
				$terms = get_the_terms( $post->ID, $taxes[ 0 ] );

				$post_term = apply_filters( "wpp_pf_breadcrumbs_{$taxes[0]}_{$terms[0]->term_id}_archive", [
					'name' => $terms[ 0 ]->name,
					'link' => get_term_link( (int)$terms[ 0 ]->term_id, $taxes[ 0 ] )
				] );

				self::add_crumb( $post_term[ 'name' ], $post_term[ 'link' ] );

				#родительский пост
				if ( $post->post_parent ) {
					$parent_crumbs = [];
					$parent_id = $post->post_parent;
					while ( $parent_id ) {
						$page = get_post( $parent_id );
						$parent_id = $page->post_parent;
						$parent_crumbs[] = [
							get_the_title( $page->ID ),
							get_permalink( $page->ID )
						];
					}
					$parent_crumbs = array_reverse( $parent_crumbs );
					foreach ( $parent_crumbs as $crumb ) {
						self::add_crumb( $crumb[ 0 ], $crumb[ 1 ] );
					}
				}

			} else {

				$cat = current( get_the_category( $post ) );
				if ( $cat ) {
					self::term_ancestors( $cat->term_id, 'post_category' );
					self::add_crumb( $cat->name, get_term_link( $cat ) );
				}
			}

			self::add_crumb( get_the_title( $post ), $permalink );
		}


		/**
		 * Страница
		 */
		private static function is_page() {
			global $post;
			if ( $post->post_parent ) {
				$parent_crumbs = [];
				$parent_id = $post->post_parent;
				while ( $parent_id ) {
					$page = get_post( $parent_id );
					$parent_id = $page->post_parent;
					$parent_crumbs[] = [
						get_the_title( $page->ID ),
						get_permalink( $page->ID )
					];
				}
				$parent_crumbs = array_reverse( $parent_crumbs );
				foreach ( $parent_crumbs as $crumb ) {
					self::add_crumb( $crumb[ 0 ], $crumb[ 1 ] );
				}
			}
			self::add_crumb( get_the_title(), get_permalink() );
			self::endpoint_trail();
		}

		/**
		 * Категория продуктов
		 */
		private static function is_product_category() {
			if ( class_exists( 'Woocommerce' ) ) :
				$current_term = $GLOBALS[ 'wp_query' ]->get_queried_object();
				self::prepend_shop_page();
				self::term_ancestors( $current_term->term_id, 'product_cat' );
				self::add_crumb( $current_term->name );
			endif;
		}

		/**
		 * Метка продуктов
		 */
		private static function is_product_tag() {
			if ( class_exists( 'Woocommerce' ) ) :
				$current_term = $GLOBALS[ 'wp_query' ]->get_queried_object();
				self::prepend_shop_page();
				self::add_crumb( sprintf( __( 'Products tagged &ldquo;%s&rdquo;', 'wpp-fr' ), $current_term->name ) );
			endif;
		}

		/**
		 * Страница магащина
		 */
		private static function is_shop() {

			if ( class_exists( 'Woocommerce' ) ) :
				if ( get_option( 'page_on_front' ) == wc_get_page_id( 'shop' ) ) {
					return;
				}
				$_name = wc_get_page_id( 'shop' ) ? get_the_title( wc_get_page_id( 'shop' ) ) : '';
				if ( !$_name ) {
					$product_post_type = get_post_type_object( 'product' );
					$_name = $product_post_type->labels->singular_name;
				}
				self::add_crumb( $_name, get_post_type_archive_link( 'product' ) );
			endif;
		}

		/**
		 * АРхив типа записей
		 */
		private static function is_post_type_archive() {
			$type = get_post_type();
			$post_type = get_post_type_object( $type );
			if ( $post_type ) {
				if ( $type === 'product' && class_exists( 'Woocommerce' ) ) {
					if ( get_option( 'page_on_front' ) == wc_get_page_id( 'shop' ) ) {
						return;
					}
					$_name = wc_get_page_id( 'shop' ) ? get_the_title( wc_get_page_id( 'shop' ) ) : '';
					if ( !$_name ) {
						$product_post_type = get_post_type_object( 'product' );
						$_name = $product_post_type->labels->singular_name;
					}
					self::add_crumb( $_name, get_post_type_archive_link( 'product' ) );
				} else {
					self::add_crumb( $post_type->labels->singular_name, get_post_type_archive_link( get_post_type() ) );
				}
			}
		}

		/**
		 * Категория
		 */
		private static function is_category() {
			$this_category = get_category( $GLOBALS[ 'wp_query' ]->get_queried_object() );
			if ( 0 != $this_category->parent ) {
				self::term_ancestors( $this_category->parent, 'post_category' );
				self::add_crumb( $this_category->name, get_category_link( $this_category->term_id ) );
			}
			self::add_crumb( single_cat_title( '', false ), get_category_link( $this_category->term_id ) );
		}

		/**
		 * Метка
		 */
		private static function is_tag() {
			$queried_object = $GLOBALS[ 'wp_query' ]->get_queried_object();
			self::add_crumb( sprintf( __( 'Posts tagged &ldquo;%s&rdquo;', 'wpp-fr' ), single_tag_title( '', false ) ), get_tag_link( $queried_object->term_id ) );
		}

		/**
		 * Архив по дате.
		 */
		private static function is_date() {
			if ( is_year() || is_month() || is_day() ) {
				self::add_crumb( str_replace( 'XXX', get_the_time( 'Y' ), self::$args[ 'year' ] ), get_year_link( get_the_time( 'Y' ) ) );
			}
			if ( is_month() || is_day() ) {
				self::add_crumb( str_replace( 'XXX', get_the_time( 'F' ), self::$args[ 'month' ] ), get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) );
			}
			if ( is_day() ) {
				self::add_crumb( get_the_time( 'd' ) );
			}
		}

		/**
		 * Таксономия
		 */
		private static function is_tax() {

			$this_term = $GLOBALS[ 'wp_query' ]->get_queried_object();
			$taxonomy = get_taxonomy( $this_term->taxonomy );
			$tax_archive = apply_filters( "wpp_pf_breadcrumbs_{$this_term->taxonomy}_archive", [
				'name' => $taxonomy->labels->name,
				'link' => null
			] );

			if ( class_exists( 'Woocommerce' ) ) {
				if ( get_option( 'page_on_front' ) == wc_get_page_id( 'shop' ) ) {
					return;
				}
				$_name = wc_get_page_id( 'shop' ) ? get_the_title( wc_get_page_id( 'shop' ) ) : '';
				if ( !$_name ) {
					$product_post_type = get_post_type_object( 'product' );
					$_name = $product_post_type->labels->singular_name;
				}
				self::add_crumb( $_name, get_post_type_archive_link( 'product' ) );
			} else {
				self::add_crumb( $tax_archive[ 'name' ], $tax_archive[ 'link' ] );
			}

			if ( 0 != $this_term->parent ) {
				self::term_ancestors( $this_term->term_id, $this_term->taxonomy );
			}
			self::add_crumb( single_term_title( '', false ), get_term_link( $this_term->term_id, $this_term->taxonomy ) );
		}

		/**
		 * Архив автора
		 */
		private static function is_author() {
			global $author;
			$userdata = get_userdata( $author );
			self::add_crumb( sprintf( '%s %s', self::$args[ 'author' ], $userdata->display_name ) );
		}

		/**
		 * Термин
		 *
		 * @param string $taxonomy
		 */
		private static function term_ancestors( $term_id, $taxonomy ) {
			$ancestors = get_ancestors( $term_id, $taxonomy );
			$ancestors = array_reverse( $ancestors );
			foreach ( $ancestors as $ancestor ) {
				$ancestor = get_term( $ancestor, $taxonomy );
				if ( !is_wp_error( $ancestor ) && $ancestor ) {
					self::add_crumb( $ancestor->name, get_term_link( $ancestor ) );
				}
			}
		}

		/**
		 * Endpoints.
		 */
		private static function endpoint_trail() {
			/*// Is an endpoint showing?
			if ( is_wc_endpoint_url() && ( $endpoint = WC()->query->get_current_endpoint() ) && ( $endpoint_title = WC()->query->get_endpoint_title( $endpoint ) ) ) {
				$this->add_crumb( $endpoint_title );
			}*/
		}

		/**
		 * Add a breadcrumb for search results.
		 */
		private static function search_trail() {
			if ( is_search() ) {
				self::add_crumb( sprintf( __( 'Search results for &ldquo;%s&rdquo;', 'wpp-fr' ), get_search_query() ), remove_query_arg( 'paged' ) );
			}
		}

		/**
		 * Add a breadcrumb for pagination.
		 */
		private static function paged_trail() {
			if ( get_query_var( 'paged' ) ) {
				self::add_crumb( sprintf( __( 'Page %d', 'woocommerce' ), get_query_var( 'wpp-fr' ) ) );
			}
		}
	}

	WPP_Pf_Breadcrumbs::init();
	if ( !function_exists( 'wpp_pf_breadcrumbs' ) ) :
		/**
		 * вызов хлебных крошек
		 */
		function wpp_pf_breadcrumbs() {
			do_action( 'wpp_breadcrumbs' );
		}

	endif;
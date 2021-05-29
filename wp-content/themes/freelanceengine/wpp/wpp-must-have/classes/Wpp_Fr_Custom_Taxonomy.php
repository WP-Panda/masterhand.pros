<?php
	/**
	 * Class Wpp_Pf_Custom_Taxonomy
	 * Регистрация таксономий
	 */
	if ( ! class_exists( 'Wpp_Fr_Custom_Taxonomy' ) ) :

		class Wpp_Fr_Custom_Taxonomy{

			private static $post_types = [];
			private static $taxonomies = [];

			public static function init() {

				add_action( 'init', [ __CLASS__, 'custom_post_type' ], 10 );
				add_action( 'wp_loaded', [ __CLASS__, 'unregister_post_types' ], 10 );

				add_filter( 'post_updated_messages', [ __CLASS__, 'update_messages' ], 10 );
				add_action( 'contextual_help', [ __CLASS__, 'help_text' ], 10, 3 );
				add_filter( 'enter_title_here', [ __CLASS__, 'title' ] );

				#if ( class_exists( 'WPP_Tax_Term_Img' ) ) {
					add_filter( 'wpp_tax_imgs_targets', [ __CLASS__, 'img_for_taxes' ], 50 );
				#}
			}

			/**
			 * типы записей
			 *
			 * @return array
			 */
			private static function post_types() {
				return apply_filters( 'wpp_fr_register_post_types', self::$post_types );
			}

			/**
			 * Лэйблы для кастомного типа записи
			 *
			 * @param $single
			 * @param $plural
			 *
			 * @return array
			 */
			private static function post_types_labels( $single, $plural ) {
				return [
					'name'                  => sprintf( _x( '%s', 'Post Type General Name', 'wpp-fr' ), $single ),
					'singular_name'         => sprintf( _x( '%s', 'Post Type Singular Name', 'wpp-fr' ), $single ),
					'menu_name'             => sprintf( __( '%s', 'wpp-fr' ), $plural ),
					'name_admin_bar'        => sprintf( __( '%s', 'wpp-fr' ), $single ),
					'archives'              => sprintf( __( '%s Archives', 'wpp-fr' ), $plural ),
					'attributes'            => sprintf( __( '%s Attributes', 'wpp-fr' ), $single ),
					'parent_item_colon'     => sprintf( __( 'Parent %s:', 'wpp-fr' ), $single ),
					'all_items'             => sprintf( __( 'All %s', 'wpp-fr' ), $plural ),
					'add_new_item'          => sprintf( __( 'Add New %s', 'wpp-fr' ), $single ),
					'add_new'               => sprintf( __( 'Add New %s', 'wpp-fr' ), $single ),
					'new_item'              => sprintf( __( 'New %s', 'wpp-fr' ), $single ),
					'edit_item'             => sprintf( __( 'Edit %s', 'wpp-fr' ), $single ),
					'update_item'           => sprintf( __( 'Update %s', 'wpp-fr' ), $single ),
					'view_item'             => sprintf( __( 'View %s', 'wpp-fr' ), $single ),
					'view_items'            => sprintf( __( 'View %s', 'wpp-fr' ), $plural ),
					'search_items'          => sprintf( __( 'Search %s', 'wpp-fr' ), $single ),
					'not_found'             => __( 'Not found', 'wpp-fr' ),
					'not_found_in_trash'    => __( 'Not found in Trash', 'wpp-fr' ),
					'featured_image'        => __( 'Featured Image', 'wpp-fr' ),
					'set_featured_image'    => __( 'Set featured image', 'wpp-fr' ),
					'remove_featured_image' => __( 'Remove featured image', 'wpp-fr' ),
					'use_featured_image'    => __( 'Use as featured image', 'wpp-fr' ),
					'insert_into_item'      => sprintf( __( 'Insert into %s', 'wpp-fr' ), $single ),
					'uploaded_to_this_item' => sprintf( __( 'Uploaded to this %s', 'wpp-fr' ), $single ),
					'items_list'            => sprintf( __( '%s list', 'wpp-fr' ), $plural ),
					'items_list_navigation' => sprintf( __( '%s list navigation', 'wpp-fr' ), $plural ),
					'filter_items_list'     => sprintf( __( 'Filter %s list', 'wpp-fr' ), $plural ),
				];
			}

			/**
			 * Массив лэйблов для русской записис
			 *
			 * @param $nominative     string - именительный падеж кто? что?
			 * @param $genitive       string - родительный кого? чего?
			 * @param $dative         string - дательный кого? что?
			 * @param $instrumental   string - творительный кем? чем?
			 * @param $plural         string - множественное
			 * @param $plurals        string - множественное 2
			 *
			 * @return array
			 */
			private static function post_types_ru_labels( $nominative, $genitive, $dative, $instrumental, $plural, $plurals ) {
				return [
					'name'                  => sprintf( _x( '%s', 'Post Type General Name', 'wpp-fr' ), $nominative ),
					'singular_name'         => sprintf( _x( '%s', 'Post Type Singular Name', 'wpp-fr' ), $nominative ),
					'menu_name'             => sprintf( __( '%s', 'wpp-fr' ), $plural ),
					'name_admin_bar'        => sprintf( __( '%s', 'wpp-fr' ), $nominative ),
					'archives'              => sprintf( __( 'Архив %s', 'wpp-fr' ), $plurals ),
					'attributes'            => sprintf( __( 'Атрибуты %s', 'wpp-fr' ), $instrumental ),
					'parent_item_colon'     => sprintf( __( 'Родительская %s:', 'wpp-fr' ), $nominative ),
					'all_items'             => sprintf( __( 'Все %s', 'wpp-fr' ), $plural ),
					'add_new_item'          => sprintf( __( 'Добавить %s', 'wpp-fr' ), $genitive ),
					'add_new'               => sprintf( __( 'Добавить %s', 'wpp-fr' ), $genitive ),
					'new_item'              => sprintf( __( 'Новая %s', 'wpp-fr' ), $nominative ),
					'edit_item'             => sprintf( __( 'Рдактировать %s', 'wpp-fr' ), $genitive ),
					'update_item'           => sprintf( __( 'Обновить %s', 'wpp-fr' ), $genitive ),
					'view_item'             => sprintf( __( 'Просмотреть %s', 'wpp-fr' ), $genitive ),
					'view_items'            => sprintf( __( 'Просмотреть %s', 'wpp-fr' ), $plural ),
					'search_items'          => sprintf( __( 'Найти %s', 'wpp-fr' ), $genitive ),
					'not_found'             => __( 'Не найдено', 'wpp-fr' ),
					'not_found_in_trash'    => __( 'Не найдено в корзине', 'wpp-fr' ),
					'featured_image'        => __( 'Миниатюра', 'wpp-fr' ),
					'set_featured_image'    => __( 'Установить миниатюру', 'wpp-fr' ),
					'remove_featured_image' => __( 'Удалить миниатюру', 'wpp-fr' ),
					'use_featured_image'    => __( 'Использовать как миниатюру', 'wpp-fr' ),
					'insert_into_item'      => sprintf( __( 'Добавить к %s', 'wpp-fr' ), $dative ),
					'uploaded_to_this_item' => sprintf( __( 'Загрузить для %s', 'wpp-fr' ), $instrumental ),
					'items_list'            => sprintf( __( 'Список %s', 'wpp-fr' ), $plurals ),
					'items_list_navigation' => sprintf( __( 'Навигация по списку %s', 'wpp-fr' ), $plurals ),
					'filter_items_list'     => sprintf( __( 'Фильтровать список %s', 'wpp-fr' ), $plurals ),
				];
			}

			/**
			 * Массив по умолчанию для типа постов
			 *
			 * @return array
			 */
			private static function post_types_default_array() {
				return [
					'public'          => true,
					'show_ui'         => true,
					# Нужно ли включать тип записи в REST API без этого не будет работать гутенберг
					'show_in_rest'    => true,
					'capability_type' => 'post',
					'hierarchical'    => false,
					'supports'        => [
						'title',
						'editor',
						'author',
						'thumbnail',
						'excerpt',
						'trackbacks',
						'custom-fields',
						'comments',
						'revisions',
						'page-attributes',
						'post-formats'
					],
					'has_archive'     => true,
					'rewrite'         => true,
					'query_var'       => true,
					'description'     => ''
				];
			}

			/**
			 * Лэйблы для таксономии
			 *
			 * @param $single
			 * @param $plural
			 *
			 * @return array
			 */
			private static function taxonomy_labels( $single, $plural ) {
				return [
					'name'              => sprintf( __( '%s', 'wpp-fr' ), $plural ),
					'singular_name'     => sprintf( __( '%s', 'wpp-fr' ), $single ),
					'search_items'      => sprintf( __( 'Search %s', 'wpp-fr' ), $plural ),
					'all_items'         => sprintf( __( 'All %s', 'wpp-fr' ), $plural ),
					'view_item '        => sprintf( __( 'View %s', 'wpp-fr' ), $single ),
					'parent_item'       => sprintf( __( 'Parent %s', 'wpp-fr' ), $single ),
					'parent_item_colon' => sprintf( __( 'Parent %s:', 'wpp-fr' ), $single ),
					'edit_item'         => sprintf( __( 'Edit %s', 'wpp-fr' ), $single ),
					'update_item'       => sprintf( __( 'Update %s', 'wpp-fr' ), $single ),
					'add_new_item'      => sprintf( __( 'Add New %s', 'wpp-fr' ), $single ),
					'new_item_name'     => sprintf( __( 'New %s Name', 'wpp-fr' ), $single ),
					'menu_name'         => sprintf( __( '%s', 'wpp-fr' ), $plural )
				];
			}

			/**
			 * Лэйблы для таксономии русские
			 *
			 * @param $single
			 * @param $plural
			 *
			 * @return array
			 */
			private static function taxonomy_ru_labels( $nominative, $genitive, $plural ) {
				return [
					'name'              => sprintf( __( '%s', 'wpp-fr' ), $plural ),
					'singular_name'     => sprintf( __( '%s', 'wpp-fr' ), $nominative ),
					'search_items'      => sprintf( __( 'Ннайти %s', 'wpp-fr' ), $genitive ),
					'all_items'         => sprintf( __( 'Все %s', 'wpp-fr' ), $plural ),
					'view_item'         => sprintf( __( 'Посмотреть %s', 'wpp-fr' ), $genitive ),
					'parent_item'       => sprintf( __( 'Родительский %s', 'wpp-fr' ), $nominative ),
					'parent_item_colon' => sprintf( __( 'Родительский %s:', 'wpp-fr' ), $nominative ),
					'edit_item'         => sprintf( __( 'Редактировать %s', 'wpp-fr' ), $genitive ),
					'update_item'       => sprintf( __( 'Обновить %s', 'wpp-fr' ), $genitive ),
					'add_new_item'      => sprintf( __( 'Добавить %s', 'wpp-fr' ), $genitive ),
					'new_item_name'     => sprintf( __( 'Имя %s', 'wpp-fr' ), $genitive ),
					'menu_name'         => sprintf( __( '%s', 'wpp-fr' ), $plural )
				];
			}

			private static function taxonomy_default_array() {
				return [
					'public'            => true,
					'hierarchical'      => true,
					'rewrite'           => true,
					'show_admin_column' => true,
					'show_in_rest'      => true, // добавить в REST API
				];
			}

			/**
			 * таксономии
			 *
			 * @return array
			 */
			private static function taxonomies() {
				return apply_filters( 'wpp_fr_register_taxonomies', self::$taxonomies );
			}

			/**
			 * Registred Post Types
			 */
			public static function custom_post_type() {

				if ( ! empty( self::post_types() ) ) :
					#wpp_d_log(self::post_types());
					foreach ( self::post_types() as $post_type => $args ) :

						if ( ! empty( $args[ 'cir' ] ) ) {
							/**
							 * @param $nominative     string - именительный падеж кто? что?
							 * @param $genitive       string - родительный кого? чего?
							 * @param $dative         string - дательный кого? что?
							 * @param $instrumental   string - творительный кем? чем?
							 * @param $plural         string - множественное
							 * @param $plurals        string - множественное 2
							 */
							$args[ 'labels' ] = self::post_types_ru_labels( $args[ 'single' ], $args[ 'genitive' ], $args[ 'dative' ], $args[ 'instrumental' ], $args[ 'plural' ], $args[ 'plurals' ] );
						} else {
							$args[ 'labels' ] = self::post_types_labels( $args[ 'single' ], $args[ 'plural' ] );
						}
						$args[ 'label' ] = $args[ 'single' ];

						$args = array_merge( self::post_types_default_array(), $args );

						#wpp_d_log($args);
						/**
						 * фильтр для конкретного типра записи
						 */
						register_post_type( $post_type, apply_filters( "wpp_fr_register_{$post_type}_post_types", $args ) );

					endforeach;
				endif;

				if ( ! empty( self::taxonomies() ) ) :
					foreach ( self::taxonomies() as $taxonomy => $args ) :

						if ( ! empty( $args[ 'cir' ] ) ) {
							$args[ 'labels' ] = self::taxonomy_ru_labels( $args[ 'single' ], $args[ 'genitive' ], $args[ 'plural' ] );
						} else {
							$args[ 'labels' ] = self::taxonomy_labels( $args[ 'single' ], $args[ 'plural' ] );
						}
						/**
						 * фильтр для конкретной таксономии
						 */
						$args = array_merge( self::taxonomy_default_array(), $args );
						register_taxonomy( $taxonomy, $args[ 'post_types' ], apply_filters( "wpp_fr_register_{$taxonomy}_taxonomy", $args ) );

					endforeach;
				endif;

			}

			/**
			 * Изменение/добавление смообщений при обновлении/созданнии записи
			 *
			 * @param $messages
			 *
			 * @return mixed
			 */
			public static function update_messages( $messages ) {

				if ( ! empty( self::post_types() ) ) :
					foreach ( self::post_types() as $post_type => $args ) :

						if ( empty( $args[ 'messages' ] ) ) {
							continue;
						}

						$messages[ $post_type ] = $args[ 'messages' ];

					endforeach;
				endif;

				return $messages;
			}

			/**
			 * Раздел помощь для типа записей
			 *
			 * @param $contextual_help
			 * @param $screen_id
			 * @param $screen
			 *
			 * @return mixed
			 */
			public static function help_text( $contextual_help, $screen_id, $screen ) {

				if ( ! empty( self::post_types() ) ) :

					foreach ( self::post_types() as $post_type => $args ) :

						if ( $screen_id !== $post_type && $screen_id !== 'edit-' . $post_type ) {
							continue;
						}

						if ( ( ! empty( $args[ 'helpers' ][ 'post-screen' ][ 'tabs' ] ) && $screen_id === $post_type ) || ( ! empty( $args[ 'helpers' ][ 'edit-screen' ][ 'tabs' ] ) && $screen_id === 'edit-' . $post_type ) ) {

							$pref = $screen_id === $post_type ? 'post-screen' : 'edit-screen';

							foreach ( $args[ 'helpers' ][ $pref ][ 'tabs' ] as $key => $data ) {
								$screen->add_help_tab( [
									'id'      => $key,
									'title'   => $data[ 'title' ],
									'content' => $data[ 'text' ]
								] );
							}

							if ( ! empty( $args[ 'helpers' ][ $pref ][ 'aside' ] ) ) {
								$screen->set_help_sidebar( $args[ 'helpers' ][ $pref ][ 'aside' ] );
							}

						}

					endforeach;
				endif;

				return $contextual_help;
			}


			/**
			 * Плэйсхолдер в поле ввода заголовка
			 *
			 * @param $title
			 *
			 * @return mixed
			 */
			public static function title( $title ) {
				$posts = self::post_types();

				$screen = get_current_screen();

				if ( ! empty( $posts ) ) /*&& ! empty( $posts[ $screen->post_type ][ 'enter-title' ] ) )*/ {
					foreach ( $posts as $post_type => $args ) {

						if ( $screen->post_type !== $post_type ) {
							continue;
						}

						if ( ! empty( $args[ 'enter_title' ] ) ) {
							$title = $args[ 'enter_title' ];
						}


					}
				}

				return $title;
			}

			/**
			 * Unregistred post Types
			 */
			public static function unregister_post_types() {
				$default_unregistred = [];
				$unregistred         = apply_filters( 'wpp_fr_unregistred_post_types', $default_unregistred );
				if ( ! empty( $unregistred ) ) :
					foreach ( $unregistred as $one_type ):
						unregister_post_type( $one_type );
					endforeach;
				endif;
			}

			/**
			 * Добаление миниатюры к таксономии нужен класс WPP_Tax_Term_Img
			 *
			 * @param $args
			 *
			 * @return array|mixed
			 */
			public static function img_for_taxes( $args ) {

				if ( ! empty( self::taxonomies() ) ) :

					foreach ( self::taxonomies() as $taxonomy => $args ) :
						#wpp_d_log($taxonomy);
						if ( ! empty( $args[ 'img' ] ) && $args[ 'img' ] === true ) {
							$args[] = $taxonomy;
						}
					endforeach;
				endif;

				return $args;
			}

		}

	endif;

	Wpp_Fr_Custom_Taxonomy::init();
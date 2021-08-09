# WPP Post Gallery - v2.0.0

Галлерея изображений для любых типов записей Работает как в Classic Editor так и с Gutenberg

 *По умолчанию никаких метабоксов не создается, для активации метабокса надо использовать фильтр* **'wpp_post_gallery_types'**
 
 ```php
 function wpp_post_gallery_meta_boxes( $post_types ) {

		$post_types[] = 'post';

		return $post_types;

	}

	add_filter( 'wpp_post_gallery_types', 'wpp_post_gallery_meta_boxes' );
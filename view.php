<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php'); //загружаем окружение wordpress, файл wp-load.php лежит в корне структуры каталогов WordPress
if (function_exists('get_post_custom')) { //проверяем загрузилось ли оно
  global $wpdb;
  global $OFFSET; //получение настроек часового пояса
  $nowtime = gmdate('Y-m-d', time() + 3600*$OFFSET); //генерация текущей даты
  $post_id = intval($_GET['views_id']); //инициализируем переменную с id поста
        if($post_id > 0) {
                $post_views = get_post_custom($post_id); //получаем Custom Fields
                $post_views_t = intval($post_views['views'][0]);
                /* пытаемся обновить значения просмотров, если не получается, то создаем такое поле */
                if(!update_post_meta($post_id, 'views', ($post_views_t+1))) {
                        add_post_meta($post_id, 'views', 1, true);
                }
                $today_date = $post_views['tdate'][0];
                $today_views = intval($post_views['tviews'][0]);
                if(!$today_date) {
                  add_post_meta($post_id, 'tdate', $nowtime, true);
                }
                /* проверяем текущую дату, если совпадает, то обновляем, если нет, то копируем в yviews и обнуляем */
                if($today_date == $nowtime) {
                  if(!update_post_meta($post_id, 'tviews', ($today_views+1))) {
                          add_post_meta($post_id, 'tviews', 1, true);
                        }
                } else {
                  if(!update_post_meta($post_id, 'yviews', $today_views)) {
                          add_post_meta($post_id, 'yviews', $today_views, true);
                        }
                        update_post_meta($post_id, 'tviews', 1);
                        update_post_meta($post_id, 'tdate', $nowtime);
                }
        }
}
?>
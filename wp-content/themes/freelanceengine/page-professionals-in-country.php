<?php
/**
 * Template Name: Page professionals in country
 * The main template file
 *
 * This is the most generic template file in a WordPress theme and one
 * of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query,
 * e.g., it puts together the home page when no home.php file exists.
 *
 * @link       http://codex.wordpress.org/Template_Hierarchy
 *
 * @package    WordPress
 * @subpackage FreelanceEngine
 * @since      FreelanceEngine 1.0
 */

get_header();

if ( get_field( 'country-for-page' ) ) {
	$getc = get_field( 'country-for-page' );
} else {
	$getc = '';
}
?>

<?php if ( ( get_field( 'btitle' ) ) || ( get_field( 'btext' ) ) ) { ?>
    <section class="prof-bn-wp" style="background:url(<?php the_field( 'bimg' ); ?>) top center no-repeat;">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12">
                    <h1><?php the_field( 'btitle' ); ?></h1>
                    <div class="b_text"><?php the_field( 'btext' ); ?></div>
                    <a class="fre-btn fre-btn primary-bg-color"
                       href="<?php echo et_get_page_link( 'register' ); ?>"><?php _e( 'Get Started', ET_DOMAIN ) ?></a>
                </div>
            </div>
        </div>
    </section>
<?php } ?>

    <section class="profs-wp">
        <div class="container">
            <h2 class="title_start t1"><?php the_title(); ?></h2>
			<?php include 'dbConfig.php';
			// заменить это
			$querynew = $db->query( "SELECT `country_id` FROM `_countries` WHERE `title_en` = '$getc' " );
			$rowCount = $querynew->num_rows;
			if ( $rowCount > 0 ) {
				$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
				while ( $row = $querynew->fetch_assoc() ) {
					$cid        = $row['country_id'];
					$query_args = [
						'post_type'      => PROFILE,
						'post_status'    => 'publish',
						'posts_per_page' => 8,
						'paged'          => $paged,
						'meta_query'     => [
							[
								'key'     => 'country',
								'value'   => $cid,
								'compare' => '='
							]
						]
					];
				}
			} else {
				echo '<div>' . __( 'There is no professionals yet', ET_DOMAIN ) . '</div>';
			}
			// заменить это end
			// на это
			//        global $wpdb;
			//        $cid = $wpdb->get_var("SELECT `id` FROM `wp_location_countries` WHERE `name` = '$getc' ");
			//        if (!empty($cid)) {
			//            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
			//            $query_args = array(
			//                'post_type' => PROFILE,
			//                'post_status' => 'publish',
			//                'posts_per_page' => 8,
			//                'paged' => $paged,
			//                'meta_query' => array(
			//                    array(
			//                        'key' => 'country',
			//                        'value' => $cid,
			//                        'compare' => '='
			//                    )
			//                )
			//            ) ;
			//        }  else {
			//            echo '<div>'.__( 'There is no professionals yet', ET_DOMAIN ).'</div>';
			//        }
			// на это end
			?>

			<?
			if ( $getc != '' ) {
				$loop = new WP_Query( $query_args );
				global $ae_post_factory, $post;
				$post_object = $ae_post_factory->get( PROFILE );
				?>


                <div class="row">
					<?php
					if ( $loop->have_posts() ) {
						$postdata = [];

						foreach ( $loop->posts as $key => $value ) {
							$post       = $value;
							$convert    = $post_object->convert( $post );
							$postdata[] = $convert;
							$hou_rate   = (int) $convert->hour_rate; // from 1.8.5
							?>
                            <div class="col-lg-6 col-md-12">
                                <div class="fre-freelancer-wrap">
                                    <a class="free-avatar"
                                       href="<?php echo get_author_posts_url( $convert->post_author ); ?>">
										<?php echo $convert->et_avatar; ?>
                                    </a>
                                    <h6>
                                        <a href="<?php echo get_author_posts_url( $convert->post_author ); ?>"><?php the_author_meta( 'display_name', $convert->post_author ); ?></a>
                                    </h6>
                                    <p class="secondary-color"><?php echo $convert->et_professional_title; ?></p>
                                    <div class="free-rating rate-it"
                                         data-score="<?php echo $convert->rating_score; ?>"></div>
									<?php if ( $hou_rate > 0 ) { ?>
                                        <div class="free-hourly-rate">
											<?php printf( __( '%s/hr', ET_DOMAIN ), "<span>" . fre_price_format( $convert->hour_rate ) . "</span>" ); ?>
                                        </div>
									<?php } ?>
                                    <div class="free-experience">
                                        <span><?php echo $convert->experience; ?></span>
                                        <span><?php echo $convert->project_worked; ?></span>
                                    </div>
                                    <div class="free-skill">
										<?php
										if ( isset( $convert->tax_input['skill'] ) && $convert->tax_input['skill'] ) {
											$skills = $convert->tax_input['skill'];
											for ( $i = 0; $i <= 2; $i ++ ) {
												if ( isset( $skills[ $i ] ) ) {
													echo '<span class="fre-label"><a href="' . get_post_type_archive_link( PROFILE ) . '?skill_profile=' . $skills[ $i ]->slug . '">' . $skills[ $i ]->name . '</a></span>';
												}
											}
										}
										?>
                                    </div>
                                </div>
                            </div>

						<?php }
					} else {
						echo '<div class="col-sm-12"><br>' . __( 'There is no professionals yet', ET_DOMAIN ) . '</div>';
					} ?>
                </div>

				<?php echo '<div class="fre-paginations paginations-wrapper">';
				ae_pagination( $loop, get_query_var( 'paged' ) );
				echo '</div>';
			}
			?>

        </div>
    </section>

    <section class="top-skill">
        <div class="container">
            <div class="row">
				<?php
				if ( $getc != '' ) {

					$args = [
						'taxonomy'   => 'skill',
						'hide_empty' => 1,
					];
					global $md_array;
					$terms = get_terms( $args );
					//перебираем все категории способностей
					foreach ( $terms as $term ) {

						$tid = $term->term_id;

						//ищем посты если есть в категории и данной стране
						$query_args2 = [
							'post_type'   => PROFILE,
							'post_status' => 'publish',
							'tax_query'   => [
								[
									'taxonomy' => 'skill',
									'field'    => 'id',
									'terms'    => $tid
								]
							],
							'meta_query'  => [
								[
									'key'     => 'country',
									'value'   => $cid,
									'compare' => '='
								]
							]
						];


						$query3 = new WP_Query( $query_args2 );
						//если количество постов из категории больше 0 то добавляем в массив
						$tcount = $query3->found_posts;
						if ( $tcount > 0 ) {
							$md_array[] = [
								'id'    => $tcount,
								'count' => $term->term_id
							];

						}
					}

					if ( $md_array != '' ) { ?>
                        <span class="col-sm-12 col-xs-12 title_start t2">Top skills in <?php echo $getc; ?></span>
						<?php
						//сортируем массив по количеству статей в найденых категорий - от наибольших к меньшим
						arsort( $md_array );
						//обрезаем массив чтоб выводить не более 16 категорий
						$output = array_slice( $md_array, 0, 16 );
						//выводим на екран названия категорий
						foreach ( $output as $key ) {
							foreach ( $key as $key2 => $val ) {

								if ( $key2 == 'count' ) {
									$t = get_term( $val, 'skill' );
									echo '<div class="col-sm-3 col-xs-6"><a href="' . get_post_type_archive_link( PROFILE ) . '?skill_profile=' . $t->slug . '&country=' . $cid . '">' . $t->name . '</a></div>';

								}

							}
						}
					}

				} ?>
            </div>
        </div>
    </section>


    <section class="top-state">
        <div class="container">
            <div class="row">

				<?php
				if ( $getc != '' ) {
					global $md_array3;
					$querynew3 = $db->query( "SELECT `region_id` FROM `_regions` WHERE `country_id` = '$cid' " );
					$rowCount2 = $querynew3->num_rows;
					if ( $rowCount2 > 0 ) {

						while ( $row = $querynew3->fetch_assoc() ) {
							$stid        = $row['region_id'];
							$query_args3 = [
								'post_type'   => PROFILE,
								'post_status' => 'publish',
								'meta_query'  => [
									'relation' => 'AND',
									[
										'key'     => 'country',
										'value'   => $cid,
										'compare' => '='
									],
									[
										'key'     => 'state',
										'value'   => $stid,
										'compare' => '='
									]
								]
							];

							$query4  = new WP_Query( $query_args3 );
							$tcount2 = $query4->found_posts;
							if ( $tcount2 > 0 ) {
								$md_array3[] = [
									'id'    => $tcount2,
									'count' => $stid
								];
							}
						}
					}


					if ( $md_array3 ) { ?>
                        <span class="col-sm-12 col-xs-12 title_start t2">Browse <?php echo $getc; ?> professionals by state </span>
						<?php
						arsort( $md_array3 );
						$output2 = array_slice( $md_array3, 0, 16 );
						foreach ( $output2 as $key ) {
							foreach ( $key as $key2 => $val ) {

								$querynew  = $db->query( "SELECT `title_en` FROM `_regions` WHERE `region_id` = '$val' " );
								$rowStates = $querynew->num_rows;
								if ( $rowStates > 0 ) {
									while ( $row = $querynew->fetch_assoc() ) {
										echo '<div class="col-sm-3 col-xs-6"><a href="' . get_post_type_archive_link( PROFILE ) . '?country=' . $cid . '&state=' . $val . '">' . $row['title_en'] . '</a></div>';
									}
								}

							}
						}
					}
				} ?>
            </div>
        </div>
    </section>

    <section class="top-city">
        <div class="container">
            <div class="row">

				<?php /*
      global $md_array4;
      $querynew4 = $db->query("SELECT `city_id` FROM `_cities` WHERE `country_id` = '$cid' ");       $rowCount3 = $querynew4->num_rows;
           if($rowCount2 > 0) {

                while($row = $querynew4->fetch_assoc()) {
                    $stid2 = $row['city_id'];
                    $query_args4 = array(
                            'post_type' => PROFILE ,
                            'post_status' => 'publish' ,
                            'meta_query' =>  array(
                                array(
                                    'key'   => 'city',
                                    'value'   => $stid2,
                                    'compare' => '='
                               )
                           )
                        );

                    $query5 = new WP_Query( $query_args4);
                    $tcount3 = $query5->found_posts;
                    if ($tcount3 > 0) {
                          $md_array4[] =  array (
                                  'id' => $tcount3,
                                  'count' => $stid2
                                );
                        }
                }
            }


             if ($md_array4) {?>
                <span class="col-sm-12 col-xs-12 title_start t2">Top cities in <?php echo $getc;?></span>
               <?php
                arsort($md_array4);
                $output3 = array_slice($md_array4, 0, 16);
                foreach ($output3 as $key) {
                     foreach ($key as $key3 => $val) {

                        $querynew = $db->query("SELECT `title_en`, `region_id`, `country_id` FROM `_cities` WHERE `city_id` = '$val' ");
                        $rowCities = $querynew->num_rows;
                           if($rowCities > 0) {
                                while($row = $querynew->fetch_assoc()) {
                                     echo '<div class="col-sm-3 col-xs-6"><a href="'.get_post_type_archive_link( PROFILE ).'?country='.$row['country_id'].'&state='.$row['region_id'].'&city='.$val.'">' . $row['title_en'] . '</a></div>';
                                }
                            }

                    }
                }
            }  */ ?>

                <!-- пока сайт не раскрутят отображать ето, а потом раскоментить код  выше а етот удалить -->
				<?php if ( ( $getc != '' ) && ( $loop->have_posts() ) ) { ?>
                    <span class="col-sm-12 col-xs-12 title_start t2">Top cities in <?php echo $getc; ?></span>

					<?php if ( $cid == 19 ) { ?>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=19&state=4002131&city=699">Sydney</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=19&state=4000210&city=980">Melbourne</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=19&state=4001529&city=823">Brisbane</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=19&state=3977392&city=9620">Perth</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=19&state=3977604&city=8350">Adelaide</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=19&state=4002131&city=1802">Wollongong</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=19&state=4000210&city=1713470">Geelong</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=19&state=4001529&city=14514">Townsville</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=19&state=4001529&city=21972">Cairns</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=19&state=3977860&city=14179">Darwin</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=19&state=4001529&city=1517117">Toowoomba</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=19&state=4000210&city=6218">Ballarat</a>
                        </div>
					<?php } ?>


					<?php if ( $cid == 9 ) { ?>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=9&state=5060716&city=378">New
                                York City</a></div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=9&state=5090053&city=5331">Los
                                Angeles</a></div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=9&state=5029761&city=1017">Chicago</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=9&state=5007584&city=8496">Houston</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=9&state=5135821&city=1708303">Philadelphia</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=9&state=5102686&city=1896">Phoenix</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=9&state=4922758&city=4924641">San
                                Antonio</a></div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=9&state=5090053&city=1517925">San
                                Diego</a></div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=9&state=5007584&city=319">Dallas</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=9&state=5090053&city=1705355">San
                                Jose</a></div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=9&state=5007584&city=1068">Austin</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=9&state=5032525&city=1707488">Indianapolis</a>
                        </div>
					<?php } ?>


					<?php if ( $cid == 142 ) { ?>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=142&state=4904941&city=1442">Wellington</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=142&state=4904941&city=1941">Auckland</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=142&state=4007798&city=14174">Hamilton</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=142&state=4009929&city=8478">Christchurch</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=142&state=4904941&city=8790">Tauranga</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=142&state=4904941&city=1518927">Dunedin</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=142&state=4904941&city=4008908">Nelson</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=142&state=5007584&city=5134022">Rotorua</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=142&state=4010056&city=4008894">New
                                Plymouth</a></div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=142&state=4904941&city=4009476">Invercargill</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=142&state=4010056&city=5133838">Whangarei</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=142&state=4904941&city=4008914">Napier</a>
                        </div>
					<?php } ?>


					<?php if ( $cid == 49 ) { ?>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=49&state=5145349&city=2057">Birmingham</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=49&state=5145349&city=4241365">Bradford</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=49&state=4231395&city=1201">Glasgow</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=49&state=5145349&city=4235265">Leeds</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=49&state=5145349&city=2702">Liverpool</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=49&state=5145349&city=584">Manchester</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=49&state=5145349&city=20905">Sheffield</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=49&state=5145349&city=8183">Wakefield</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=49&state=4229320&city=21568">Swansea</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=49&state=5145349&city=4230296">Sunderland</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=49&state=5145349&city=4110">Southampton</a>
                        </div>
					<?php } ?>

					<?php if ( $cid == 10 ) { ?>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=10&state=5164658&city=1914789">Montreal</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=10&state=1953862&city=1475">Calgary</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=10&state=1711248&city=1944918">Ottawa</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=10&state=1953862&city=775">Edmonton</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=10&state=1711248&city=6436">Mississauga</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=10&state=5126967&city=8954">Winnipeg</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=10&state=5122981&city=1960291">Vancouver</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=10&state=1711248&city=1413">Brampton</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=10&state=1711248&city=2208972">Hamilton</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=10&state=5122981&city=5129932">Surrey</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=10&state=5164658&city=1710744">Laval</a>
                        </div>
                        <div class="col-sm-3 col-xs-6"><a
                                    href="<?php echo get_post_type_archive_link( PROFILE ); ?>?country=10&state=5127854&city=2203643">Halifax</a>
                        </div>
					<?php } ?>

				<?php } ?>

                <!-- the end -->


            </div>
        </div>
    </section>
<?php
get_footer();
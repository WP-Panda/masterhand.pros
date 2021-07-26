<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

function wpp_action_liat_page() { ?>

    <div class="wrap">

        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>


        <div class="wpp-container">
            <h2>Import Company</h2>

            <div class="options">
                <p>
                    <label>Download the list of companies in CSV format delimiter - semicolon.</label>
                    <!-- label>Загрузите список компаний в формате CSV разделитель - точка с запятой.</label -->
                </p>
                <form id="wpp_company_import_form">
                    <input type="file" name="wpp_company_import_file" id="wpp_company_import_file" value=""/>
                </form>
                <p>
                    <i>If the id column is not empty and there is a company with this id it will be changed,
                        otherwise a new company will be created, the id column will not be taken into account.</i>
                    <!-- Если столбец ID будет не пустым и существует компания с таким ID она будет изменена, в противном случае будет создана новая компания, столбец ID не будет учитываться. -->
                </p>
            </div>

            <div class="wpp-error-block"></div>
            <div class="wpp-ok-block"></div>
        </div>


        <div class="wpp-container">
            <h2>Export Company</h2>

            <div class="options">
                <p>
                    <label>Сompany list export</label>
                </p>
            </div>


			<?php
			$dir = wp_upload_dir();

			$wrte_file_preff = $dir['basedir'] . '/wpp/company/export';
			$files           = scandir( $wrte_file_preff, SCANDIR_SORT_DESCENDING );
			if ( ! empty( $files ) ) {
				$newest_file = $dir['baseurl'] . '/wpp/company/export/' . $files[0];
				printf( '<div class="latest-export"><a href="%s">%s</a></div>', $newest_file, __( 'Download the latest export', 'wpp' ) );
			}
			?>


            <button class="button button-primary" id="wpp-export-company">Start Export</button>
            <div class="wpp-export-block"></div>
        </div>

    </div>


    <div class="wpp-loader"></div>
    <script>
        jQuery(function ($) {

            $(document).on('change', '#wpp_company_import_file', function (e) {
                e.preventDefault();

                $('.wpp-ok-block,.wpp-error-block').hide();
                $('.wpp-loader').show();

                var formData = new FormData(document.getElementById("wpp_company_import_form"));

                formData.append('action', 'wpp_c_upload_file');

                $.ajax({
                    url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (response) {

                        if (response.success) {
                            $('.wpp-ok-block').html(response.data.msg).show()
                        } else {
                            $('.wpp-error-block').html(response.data.msg).show()
                        }

                        $('#wpp_company_import_file').val('');
                        $('.wpp-loader').hide();

                    }
                });


            });


            $(document).on('click', '#wpp-export-company', function (e) {
                e.preventDefault();

                $('.wpp-ok-block,.wpp-error-block').hide();
                $('.wpp-loader').show();

                var $data = {
                    action: 'wpp_export_company_upload',
                }

                $.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', $data, function (response) {

                    if (response.success) {
                        $('.wpp-export-block').html(response.data.msg).show()
                    }

                    $('.wpp-loader,.latest-export').hide();
                });
            });

        });
    </script>

<?php }
<?php
/**
 * Class ME_Sampledata
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ME_Sampledata
 * 
 * ME Html install sampledata field
 *
 * @since 1.0
 * @package Admin/Options
 * @category Class
 *
 * @version 1.0
 */
class ME_Sampledata {
    function __construct( $args, $options ) {
    }
    function render() {
        wp_nonce_field('marketengine-setup');
        $is_added_sample_data  = get_option('me-added-sample-data')
    ?>
        <h3><?php _e("Sample Data", "enginethemes"); ?></h3>
        <form id="add-sample-data" <?php if($is_added_sample_data) echo 'style="display:none;"' ?>>
            <div class="me-setup-sample">
                <p><?php _e("You can add some sample data to grasp some clearer ideas of how your marketplace will look like.<br>Some sample listings will be generated in each of your categories, together with a few users &amp; orders to demonstrate the checkout flows.<br>", "enginethemes"); ?></p>
                <p><?php _e("You will be able to remove those samples with another click later.", "enginethemes"); ?></p>
                <label class="me-setup-data-btn" id="me-add-sample-data" for="me-setup-sample-data">
                    <span id="me-setup-sample-data"><?php _e("ADD SAMPLE DATA", "enginethemes"); ?></span>
                </label>
            </div>
        </form>
        <div class="me-setup-overlay">
            <div class="me-setup-overlay-container"></div>
            <div class="me-setup-overlay-loading">
                <div class="s1">
                    <div class="s b sb1"></div>
                    <div class="s b sb2"></div>
                    <div class="s b sb3"></div>
                    <div class="s b sb4"></div>
                </div>
                <div class="s2">
                    <div class="s b sb5"></div>
                    <div class="s b sb6"></div>
                    <div class="s b sb7"></div>
                    <div class="s b sb8"></div>
                </div>
                <div class="bigcon">
                  <!-- <div class="big b"></div> -->
                </div>
            </div>
        </div>

        <script type="text/javascript">
            (function($) {
                $(document).ready(function() {
                    $('#me-add-sample-data').on('click', function(event) {
                        var $target = $(event.currentTarget);
                        var count = 1;
                        $target.parents('.me-section-content').addClass('me-setup-section-loading');
                        for (var i = 1; i <= 12; i++) {
                            $.ajax({
                                type: 'post',
                                url: me_globals.ajaxurl,
                                data: {
                                    action: 'me-add-sample-data',
                                    number : i,
                                    _wpnonce: $('#_wpnonce').val()
                                },
                                beforeSend: function() {
                                    
                                    
                                },
                                success: function(res, xhr) {
                                    count ++;
                                    if(count == i) {
                                        $('#add-sample-data').hide();
                                        $('#remove-sample-data').show();
                                    }
                                    
                                }
                            });
                        };
                        setTimeout(function(){
                            $('#add-sample-data').hide();
                            $('#remove-sample-data').show();
                            $target.parents('.me-section-content').removeClass('me-setup-section-loading');
                        }, 45000);
                    });
                });
            })(jQuery)
        </script>
        <form id="remove-sample-data" <?php if(!$is_added_sample_data) echo 'style="display:none;"' ?>>
            <div class="me-setup-sample-finish">
                <p><?php _e("Few users, orders and some sample listings have already been generated in each of your categories.", "enginethemes"); ?></p>
                <p><?php _e("You will be able to remove those samples with another click later.", "enginethemes"); ?></p>
                <label class="me-setup-data-btn" id="me-add-sample-data" for="me-remove-sample-data">
                    <span id="me-remove-sample-data"><?php _e("REMOVE SAMPLE DATA", "enginethemes"); ?></span>
                </label>
            </div>
        </form>
        <script type="text/javascript">
            (function($) {
                $(document).ready(function() {
                    $('#me-remove-sample-data').on('click', function(event) {
                        var $target = $(event.currentTarget);
                        var count = 1;
                        
                        $.ajax({
                            type: 'post',
                            url: me_globals.ajaxurl,
                            data: {
                                action: 'me-remove-sample-data',
                                _wpnonce: $('#_wpnonce').val()
                            },
                            beforeSend: function() {
                                
                                $target.parents('.me-section-content').addClass('me-setup-section-loading');
                            },
                            success: function(res, xhr) {
                                $('#remove-sample-data').hide();
                                $('#add-sample-data').show();
                                $target.parents('.me-section-content').removeClass('me-setup-section-loading');
                            }
                        });
                    });
                });
            })(jQuery)
        </script>
    <?php 
    }
}

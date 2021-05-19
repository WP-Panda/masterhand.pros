<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'STI_Admin_Page_Premium' ) ) :

    /**
     * Class for plugin admin ajax hooks
     */
    class STI_Admin_Page_Premium {

        /*
         * Constructor
         */
        public function __construct() {
            
            $this->generate_content();

        }

        /*
         * Generate options fields
         */
        private function generate_content() {

            echo '<div class="buy-premium">';
                echo '<a href="' . admin_url( 'admin.php?page=sti-options-pricing' ) . '">';
                    echo '<span class="desc">' . __( 'Upgrade to the', 'share-this-image' ) . '<b> ' . __( 'Premium plugin version', 'share-this-image' ) . '</b><br>' . __( 'to have all available features!', 'share-this-image' ) . '</span>';
                    echo '</a>';
            echo '</div>';

            echo '<div class="features">';

                echo '<h3>' . __( 'Premium Features', 'share-this-image' ) . '</h3>';

                echo '<div class="features-item">';
                    echo '<div class="column">';
                        echo '<h4 class="title">';
                            echo __( 'Advanced Content Customization', 'share-this-image' );
                        echo '</h4>';
                        echo '<p class="desc">';
                            echo __( 'Set what title, description and URL must be used when sharing images. Set sources for this content and change their priority.', 'share-this-image' );
                            echo '<br><a href="https://share-this-image.com/guide/content-sources/?utm_source=plugin&utm_medium=premium-tab&utm_campaign=sti-pro-plugin" target="_blank">' . __( 'Learn more', 'share-this-image' ) . '</a>';
                        echo '</p>';
                    echo '</div>';
                    echo '<div class="column">';
                        echo '<div class="img">';
                            echo '<img alt="" src="' . STI_URL . '/assets/images/feature3.png' . '" />';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';

                echo '<div class="features-item">';
                    echo '<div class="column">';
                        echo '<h4 class="title">';
                            echo __( 'Content Variables', 'share-this-image' );
                        echo '</h4>';
                        echo '<p class="desc">';
                            echo __( "It is possible to use set of variables to customize shared content. Use all of them or just some. Also possible to use simple logic operators like 'if' and 'not if' to check availability of variables for currently sharing image.", 'share-this-image' );
                            echo '<br><a href="https://share-this-image.com/guide/content-variables/?utm_source=plugin&utm_medium=premium-tab&utm_campaign=sti-pro-plugin" target="_blank">' . __( 'Learn more', 'share-this-image' ) . '</a>';
                        echo '</p>';
                    echo '</div>';
                    echo '<div class="column">';
                        echo '<div class="img">';
                            echo '<img alt="" src="' . STI_URL . '/assets/images/feature7.png' . '" />';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';

                echo '<div class="features-item">';
                    echo '<div class="column">';
                        echo '<h4 class="title">';
                            echo __( 'Styling options', 'share-this-image' );
                        echo '</h4>';
                        echo '<p class="desc">';
                            echo __( "Choose from one of predefined styles for sharing buttons. Align sharing buttons to the left or right side of the image. Choose from vertical or horizontal orientation. Change buttons offsets. Change the looks of mobile sharing buttons.", 'share-this-image' );
                            echo '<br><a href="https://share-this-image.com/guide/buttons-styling/?utm_source=plugin&utm_medium=premium-tab&utm_campaign=sti-pro-plugin" target="_blank">' . __( 'Learn more', 'share-this-image' ) . '</a>';
                        echo '</p>';
                    echo '</div>';
                    echo '<div class="column">';
                        echo '<div class="img">';
                            echo '<img alt="" src="' . STI_URL . '/assets/images/feature6.png' . '" />';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';

                echo '<div class="features-item">';
                    echo '<div class="column">';
                        echo '<h4 class="title">';
                            echo __( 'New Buttons', 'share-this-image' );
                        echo '</h4>';
                        echo '<p class="desc">';
                            echo __( "Link, email, embed and download buttons.", 'share-this-image' );
                            echo '<br>';
                            echo __( "Copy link to image button, send email button to send image via email with custom content, embed code button to copy and paste image embed code and download button that give your users option to download images in just one click.", 'share-this-image' );
                            echo '<br><a href="https://share-this-image.com/guide/link-and-email-sharing/?utm_source=plugin&utm_medium=premium-tab&utm_campaign=sti-pro-plugin" target="_blank">' . __( 'Learn more', 'share-this-image' ) . '</a>';
                        echo '</p>';
                    echo '</div>';
                    echo '<div class="column">';
                        echo '<div class="img">';
                            echo '<img alt="" src="' . STI_URL . '/assets/images/feature1.png' . '" />';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';

                 echo '<div class="features-item">';
                    echo '<div class="column">';
                        echo '<h4 class="title">';
                            echo __( 'New Buttons Positions', 'share-this-image' );
                        echo '</h4>';
                        echo '<p class="desc">';
                            echo __( "Choose total from 4 sharing buttons positions: on image, on image (hover), before image, after image.", 'share-this-image' );
                            echo '<br><a href="https://share-this-image.com/guide/buttons-positions/?utm_source=plugin&utm_medium=premium-tab&utm_campaign=sti-pro-plugin" target="_blank">' . __( 'Learn more', 'share-this-image' ) . '</a>';
                        echo '</p>';
                    echo '</div>';
                    echo '<div class="column">';
                        echo '<div class="img">';
                            echo '<img alt="" src="' . STI_URL . '/assets/images/feature8.png' . '" />';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';

                echo '<div class="features-item">';
                    echo '<div class="column">';
                        echo '<h4 class="title">';
                            echo __( 'Auto-scroll', 'share-this-image' );
                        echo '</h4>';
                        echo '<p class="desc">';
                            echo __( "Auto-scroll visitors that click on shared image in social network to the exact location of this image on website.", 'share-this-image' );
                            echo '<br><a href="https://share-this-image.com/guide/auto-scroll/?utm_source=plugin&utm_medium=premium-tab&utm_campaign=sti-pro-plugin" target="_blank">' . __( 'Learn more', 'share-this-image' ) . '</a>';
                        echo '</p>';
                    echo '</div>';
                    echo '<div class="column">';
                        echo '<div class="img">';
                            echo '<img alt="" src="' . STI_URL . '/assets/images/feature4.png' . '" />';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';

                echo '<div class="features-item">';
                    echo '<div class="column">';
                        echo '<h4 class="title">';
                            echo __( 'Exclude options', 'share-this-image' );
                        echo '</h4>';
                        echo '<p class="desc">';
                            echo __( "With help of plugin settings page it is possible to exclude all images from certain pages from sharing. Or exclude just single images with help if 'Black list' selector.", 'share-this-image' );
                            echo '<br><a href="https://share-this-image.com/guide/exclude-options/?utm_source=plugin&utm_medium=premium-tab&utm_campaign=sti-pro-plugin" target="_blank">' . __( 'Learn more', 'share-this-image' ) . '</a>';
                        echo '</p>';
                    echo '</div>';
                    echo '<div class="column">';
                        echo '<div class="img">';
                            echo '<img alt="" src="' . STI_URL . '/assets/images/feature2.png' . '" />';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';

                echo '<div class="features-item">';
                    echo '<div class="column">';
                        echo '<h4 class="title">';
                            echo __( 'Priority Support', 'share-this-image' );
                        echo '</h4>';
                        echo '<p class="desc">';
                            echo __( "You will benefit of our full support for any issues you have with this plugin.", 'share-this-image' );
                        echo '</p>';
                    echo '</div>';
                    echo '<div class="column">';
                        echo '<div class="img">';
                            echo '<img alt="" src="' . STI_URL . '/assets/images/feature5.png' . '" />';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';

            echo '</div>';

            echo '<div class="faq">';

            echo '<h3>' . __( 'Frequently Asked Questions', 'share-this-image' ) . '</h3>';

            echo '<div class="faq-item">';
                echo '<h4 class="question">';
                    echo __( 'Can I cancel my account at any time?', 'share-this-image' );
                echo '</h4>';
                echo '<div class="answer">';
                    echo __( 'Yes, if you ever decide that Share This Image isn\'t the best plugin for your business, simply cancel your account from your Account panel. You\'ll still be able to use the plugin without updates or support.', 'share-this-image' );
                echo '</div>';
            echo '</div>';

            echo '<div class="faq-item">';
                echo '<h4 class="question">';
                    echo __( 'Do you offer refunds?', 'share-this-image' );
                echo '</h4>';
                echo '<div class="answer">';
                    echo __( "If you're not completely happy with your purchase and we're unable to resolve the issue, let us know and we'll refund the full purchase price.", 'share-this-image' );
                    echo '<br>';
                    echo __( 'Refunds can be processed within 30 days of the original purchase.', 'share-this-image' );
                echo '</div>';
            echo '</div>';

            echo '<div class="faq-item">';
                echo '<h4 class="question">';
                    echo __( 'Do I get updates for the premium plugin?', 'share-this-image' );
                echo '</h4>';
                echo '<div class="answer">';
                    echo __( 'Yes! Automatic updates for premium plugin available during 1 year after the purchase.', 'share-this-image' );
                echo '</div>';
            echo '</div>';

            echo '<div class="faq-item">';
                echo '<h4 class="question">';
                    echo __( 'Can I change my plan later on?', 'share-this-image' );
                echo '</h4>';
                echo '<div class="answer">';
                    echo __( 'Absolutely! You can upgrade or downgrade your plan at any time.', 'share-this-image' );
                echo '</div>';
            echo '</div>';

            echo '<div class="faq-item">';
                echo '<h4 class="question">';
                    echo __( 'What payment methods do you accept?', 'share-this-image' );
                echo '</h4>';
                echo '<div class="answer">';
                    echo __( 'We support major credit and debit cards, PayPal, and a variety of other mainstream payment methods, so there’s plenty to pick from.', 'share-this-image' );
                echo '</div>';
            echo '</div>';

            echo '<div class="faq-item">';
                echo '<h4 class="question">';
                    echo __( 'Do you offer support if I need help?', 'share-this-image' );
                echo '</h4>';
                echo '<div class="answer">';
                    echo __( 'Yes! You will benefit of our full support for any issues you have with this plugin.', 'share-this-image' );
                echo '</div>';
            echo '</div>';

            echo '<div class="faq-item">';
                echo '<h4 class="question">';
                    echo __( 'I have other pre-sale questions, can you help?', 'share-this-image' );
                echo '</h4>';
                echo '<div class="answer">';
                    echo __( 'Yes! You can ask us any question through our', 'share-this-image' ) . ' <a href="https://share-this-image.com/contact/?utm_source=plugin&utm_medium=premium-tab&utm_campaign=sti-pro-plugin" target="_blank">' . __( 'contact form.', 'share-this-image' ) . '</a>';
                echo '</div>';
            echo '</div>';

            echo '</div>';

            echo '<div class="buy-premium">';
                echo '<a href="' . admin_url( 'admin.php?page=sti-options-pricing' ) . '">';
                    echo '<span class="desc">' . __( 'Upgrade to the', 'share-this-image' ) . '<b> ' . __( 'Premium plugin version', 'share-this-image' ) . '</b><br>' . __( 'to have all available features!', 'share-this-image' ) . '</span>';
                echo '</a>';
            echo '</div>';

        }
        
    }

endif;

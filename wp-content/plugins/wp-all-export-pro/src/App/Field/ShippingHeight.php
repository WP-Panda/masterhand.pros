<?php

namespace Wpae\App\Field;


class ShippingHeight extends Field
{
    const SECTION = 'shipping';

    public function getValue($snippetData)
    {
        $shippingData = $this->feed->getSectionFeedData(self::SECTION);

        if($shippingData['dimensions'] == 'useWooCommerceProductValues') {

            $currentUnit = get_option('woocommerce_dimension_unit');
            $toUnit = $shippingData['convertTo'];

            $product = $_product = wc_get_product($this->entry->ID);

            if($currentUnit !== $toUnit) {

                $shippingHeight = $product->get_height();
                if(is_numeric($shippingHeight)) {
                    $height = wc_get_dimension($shippingHeight, $currentUnit, $toUnit);
                } else {
                    $height = $shippingHeight;
                }

            } else {
                $height = $product->get_height();
            }


            return $height. ' '.$toUnit;

        } else {
            return $this->replaceSnippetsInValue($shippingData['dimensionsCV'], $snippetData);
        }
    }

    public function getFieldName()
    {
        return 'shipping_height';
    }
}
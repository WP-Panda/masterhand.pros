<?php

namespace Wpae\App\Field;


class IsBundle extends Field
{
    public function getValue($snippetData)
    {
        $defaultValue = 'no';
        return apply_filters('pmxe_is_bundle', $defaultValue, $this->entry, \XmlExportEngine::$exportID);
    }
    
    public function getFieldName()
    {
        return 'is_bundle';
    }
}
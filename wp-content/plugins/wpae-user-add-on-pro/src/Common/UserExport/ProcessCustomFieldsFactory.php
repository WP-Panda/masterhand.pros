<?php

namespace Pmue\Common\UserExport;


use Pmue\Pro\UserExport\ProcessCustomFields as ProProcessCustomFields;

class ProcessCustomFieldsFactory
{
    /*
     * @return ProProcessCustomFields | ProcessCustomFields
     */
    public function create()
    {
        if(PMUE_EDITION == 'paid') {
            return new ProProcessCustomFields();
        } else {
            return new ProcessCustomFields();
        }
    }

}
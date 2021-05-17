<?php

namespace Pmue\Common\CombineFields;


use Pmue\Pro\CombineFields as ProCombineFields;


class CombineFieldsFactory
{
    public function create() {
        if(PMUE_EDITION == 'paid') {
            return new ProCombineFields();
        } else {
            return new CombineFields();
        }
    }
}
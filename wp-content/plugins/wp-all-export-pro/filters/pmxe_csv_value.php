<?php

function pmxe_pmxe_csv_value($value)
{
    return preg_replace("/^[=\+\-\@](?=[^a-zA-Z]*[a-zA-Z])/", "'$0", $value);
}

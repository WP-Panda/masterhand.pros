<?php 
/** Fenom template '_head.tpl' compiled at 2020-05-15 12:08:15 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  lang="en" xml:lang="en">
<head>
    <link href="<?php
/* _head.tpl:4: {$PATH_INC} */
 echo $var["PATH_INC"]; ?>/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="<?php
/* _head.tpl:5: {$PATH_INC} */
 echo $var["PATH_INC"]; ?>/css/jquery-confirm.min.css" rel="stylesheet"/>
    <link href="<?php
/* _head.tpl:6: {$PATH_INC} */
 echo $var["PATH_INC"]; ?>/css/fontawesome/all.min.css" rel="stylesheet"/>
    <link href="<?php
/* _head.tpl:7: {$PATH_INC} */
 echo $var["PATH_INC"]; ?>/css/select2.min.css" rel="stylesheet"/>
    <link href="<?php
/* _head.tpl:8: {$PATH_INC} */
 echo $var["PATH_INC"]; ?>/css/module.css" rel="stylesheet"/>
    <script type="text/javascript" src="<?php
/* _head.tpl:9: {$PATH_INC} */
 echo $var["PATH_INC"]; ?>/js/jquery.min.js" ></script>
    <script type="text/javascript" src="<?php
/* _head.tpl:10: {$PATH_INC} */
 echo $var["PATH_INC"]; ?>/js/bootstrap.min.js" ></script>
    <script type="text/javascript" src="<?php
/* _head.tpl:11: {$PATH_INC} */
 echo $var["PATH_INC"]; ?>/js/jquery-confirm.min.js" ></script>
    <script type="text/javascript" src="<?php
/* _head.tpl:12: {$PATH_INC} */
 echo $var["PATH_INC"]; ?>/js/select2.full.min.js" ></script>

    <script type="text/javascript" src="<?php
/* _head.tpl:14: {$PATH_INC} */
 echo $var["PATH_INC"]; ?>/js/module.js" ></script>
    <script type="text/javascript">
        function postForm(action){ document.module.action.value=action;document.module.submit(); }
    </script>
    <?php
/* _head.tpl:18: {$default_set} */
 echo $var["default_set"]; ?>
</head>
<body>
<div class="blockLoader"><div class="showLoad" onclick="mod.hideLoad()"></div></div>
<div class="body-alerts"></div>
<form name="module" method="POST"><input name="action" type="hidden" value="" /></form>
<div class="col-xs-12"><div class="text-info text-center">Skills & Endorsements v<?php
/* _head.tpl:24: {$VERSION} */
 echo $var["VERSION"]; ?></div></div><?php
}, array(
	'options' => 128,
	'provider' => false,
	'name' => '_head.tpl',
	'base_name' => '_head.tpl',
	'time' => 1588253053,
	'depends' => array (
  0 => 
  array (
    '_head.tpl' => 1588253053,
  ),
),
	'macros' => array(),

        ));

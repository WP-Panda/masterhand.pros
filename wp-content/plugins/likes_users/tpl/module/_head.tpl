<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  lang="en" xml:lang="en">
<head>
    <link href="{$PATH_INC_OTHER}/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="{$PATH_INC_OTHER}/css/jquery-confirm.min.css" rel="stylesheet"/>
    <link href="{$PATH_INC_OTHER}/css/fontawesome/all.min.css" rel="stylesheet"/>
    <link href="{$PATH_INC}/css/module.css" rel="stylesheet"/>
    <script type="text/javascript" src="{$PATH_INC_OTHER}/js/jquery.min.js" ></script>
    <script type="text/javascript" src="{$PATH_INC_OTHER}/js/bootstrap.min.js" ></script>
    <script type="text/javascript" src="{$PATH_INC_OTHER}/js/jquery-confirm.min.js" ></script>
    {*
    <link href="{$PATH_INC}/module/js/fancybox/helpers/jquery.fancybox-buttons.css" rel="stylesheet"/>
    <link href="{$PATH_INC}/module/js/fancybox/jquery.fancybox.css" rel="stylesheet"/>

    <script type="text/javascript" src="{$PATH_INC}/module/js/fancybox/jquery.fancybox.js" ></script>
    <script type="text/javascript" src="{$PATH_INC}/module/js/fancybox/jquery.fancybox.pack.js" ></script>
    <script type="text/javascript" src="{$PATH_INC}/module/js/fancybox/helpers/jquery.fancybox-buttons.js" ></script>
    *}
    <script type="text/javascript" src="{$PATH_INC}/js/Chart.min.js" ></script>
    <script type="text/javascript" src="{$PATH_INC}/js/module.js" ></script>
    <script type="text/javascript">
        function postForm(action){ document.module.action.value=action;document.module.submit(); }
    </script>
    {$default_set}
</head>
<body>
<div class="blockLoader"><div class="showLoad" onclick="mod.hideLoad()"></div></div>
<div class="body-alerts"></div>
<form name="module" method="POST"><input name="action" type="hidden" value="" /></form>
<div class="col-xs-12"><div class="text-info text-center">Likes of Users v{$VERSION}</div></div>
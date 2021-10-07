<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  lang="en" xml:lang="en">
<head>
    <link href="{$PATH_INC}/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="{$PATH_INC}/css/jquery-confirm.min.css" rel="stylesheet"/>
    <link href="{$PATH_INC}/css/bootstrap-switch.min.css" rel="stylesheet"/>
    <link href="{$PATH_INC}/css/fontawesome/all.min.css" rel="stylesheet"/>
    <link href="{$PATH_INC}/css/select2.min.css" rel="stylesheet"/>
    {*<link href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" rel="stylesheet">*}
    <link href="{$PATH_INC}/css/module.css" rel="stylesheet"/>
    <script type="text/javascript" src="{$PATH_INC}/js/jquery.min.js" ></script>
    <script type="text/javascript" src="{$PATH_INC}/js/bootstrap-switch.js" ></script>
    <script type="text/javascript" src="{$PATH_INC}/js/bootstrap.min.js" ></script>
    <script type="text/javascript" src="{$PATH_INC}/js/jquery-confirm.min.js" ></script>
    <script type="text/javascript" src="{$PATH_INC}/js/select2.full.min.js" ></script>
    {*
    <link href="{$PATH_INC}/module/js/fancybox/helpers/jquery.fancybox-buttons.css" rel="stylesheet"/>
    <link href="{$PATH_INC}/module/js/fancybox/jquery.fancybox.css" rel="stylesheet"/>

    <script type="text/javascript" src="{$PATH_INC}/module/js/fancybox/jquery.fancybox.js" ></script>
    <script type="text/javascript" src="{$PATH_INC}/module/js/fancybox/jquery.fancybox.pack.js" ></script>
    <script type="text/javascript" src="{$PATH_INC}/module/js/fancybox/helpers/jquery.fancybox-buttons.js" ></script>
    *}
    <script type="text/javascript" src="{$PATH_INC}/js/module.js" ></script>
    <script type="text/javascript">
        function postForm(action){ document.module.action.value=action;document.module.submit(); }
        $(document).ready(function(){
            $("[name='new_review_publish']").bootstrapSwitch({
                animate: false,
                size: 'small',
                offColor: 'danger',
                onColor: 'success',
                offText: 'No',
                onText: 'Yes'
            });
            $("[name='send_notice']").bootstrapSwitch({
                animate: false,
                size: 'small',
                offColor: 'danger',
                onColor: 'success',
                offText: 'No',
                onText: 'Yes'
            });
        });
    </script>
    {$default_set}
</head>
<body>
<div class="blockLoader"><div class="showLoad" onclick="mod.hideLoad()"></div></div>
<div class="body-alerts"></div>
<form name="module" method="POST"><input name="action" type="hidden" value="" /></form>
<div class="col-xs-12"><div class="text-info text-center">Reviews Module v{$VERSION}</div></div>
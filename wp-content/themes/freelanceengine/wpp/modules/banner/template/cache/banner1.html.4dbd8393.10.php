<?php 
/** Fenom template 'banner1.html' compiled at 2020-06-18 06:18:19 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?><style type="text/css">
    @import url('https://fonts.googleapis.com/css?family=Montserrat:700,600');

    .bn-1 * {
        outline: none !important;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
    }

    .bn-1 .wrapper {
        width: 1200px;
        font-family: 'Montserrat',
            sans-serif;
        font-style: normal;
        font-weight: 600;
        margin: auto;
        background: #F4F4F4;
    }

    .bn-1 {
        margin: 0;
    }

    .bn-1 .header {
        text-align: center;
        position: relative;
        padding-top: 32px;
        padding-bottom: 20px;
        width: 1200px;
        background: #2C33C1;
    }

    .bn-1 .masterhand {
        margin: 10px 0;
        width: 582px;
        height: 40px;
    }

    .bn-1 .low-cost p {
        margin: 15px 0px;
        font-size: 36px;
        line-height: 46px;
        letter-spacing: 0.05em;
        color: #FFFFFF;
    }

    .bn-1 .body {
        padding: 0 120px;
        overflow: hidden;
        position: relative;
        top: -60px;
        margin-bottom: -40px;
    }

    .bn-1 .avatar {
        float: left;
        margin: 0 0 28px;
        width: 200px;
        height: 200px;
        background-size: cover !important;
    }

    .bn-1 .name {
        margin-left: 42px;
        margin-top: 105px;
        float: left;
        overflow: hidden;
        font-weight: bold;
        font-size: 42px;
        line-height: 51px;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }

    .bn-1 .spisok {
        padding: 0 5px;
        letter-spacing: 0.02em;
        clear: both;
        margin: 0;
        overflow: hidden;
    }

    .bn-1 .spisok li {
        padding-left: 10px;
        text-align: left;
        color: #2C33C1;
        margin: 0 0 20px;
        line-height: 1.2;
        list-style: none;
        position: relative;
        width: 49%;
        float: left;
    }

    .bn-1 .spisok li:last-child {
        margin: 0;
    }

    .bn-1 .spisok li:before {
        content: '';
        width: 7px;
        height: 7px;
        border-radius: 50%;
        -webkit-border-radius: 50%;
        -moz-border-radius: 50%;
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        margin: auto;
        background: #2C33C1;
    }

    .bn-1 .spisok li span {
        margin-left: 8px;
        font-size: 28px;
        color: black;
    }

    .bn-1 .foot-text p {
        text-align: center;
        font-size: 26px;
        line-height: 150%;
        margin: 0 0 10px;
        letter-spacing: 0.02em;
        color: #2C33C1;
    }

    .bn-1 .foot {
        width: 521px;
        margin: 0 auto;
        background: #2C33C1;
        border-radius: 50px 50px 0px 0px;
        -webkit-border-radius: 50px 50px 0px 0px;
        -moz-border-radius: 50px 50px 0px 0px;
    }

    .bn-1 .number {
        padding-top: 20px;
        padding-bottom: 20px;
        text-align: center;
        font-size: 32px;
        line-height: 1;
        letter-spacing: 0.05em;
        color: #FFFFFF;
        margin: 0;
    }

</style>


<div class="bn-1">
    <div class="wrapper banner1">
        <div class="header" style="background: #2C33C1 url(<?php
/* banner1.html:154: {$path_inc} */
 echo $var["path_inc"]; ?>/img/header_bg.png) 0 0 no-repeat;">
            <img class="masterhand" src="<?php
/* banner1.html:155: {$path_inc} */
 echo $var["path_inc"]; ?>/img/Logo.svg">
            <div class="low-cost">
                <p>Low-Cost Service Deals</p>
            </div>
        </div>
        <div class="body">
            <div class="name_wp">
                <div class="avatar" style="background: url(<?php
/* banner1.html:162: {$avatar_url} */
 echo $var["avatar_url"]; ?>) center no-repeat;"></div>
                <div class="name">
                    <span><?php
/* banner1.html:164: {$display_name} */
 echo $var["display_name"]; ?></span>
                </div>
            </div>
            <ul class="spisok">
                <?php  if(!empty($var["category"]) && (is_array($var["category"]) || $var["category"] instanceof \Traversable)) {
  foreach($var["category"] as $var["item"]) { ?>
                <li><span><?php
/* banner1.html:169: {$item} */
 echo $var["item"]; ?></span></li>
                <?php
/* banner1.html:170: {/foreach} */
   } } ?>
            </ul>
        </div>
        <div class="footer">
            <div class="foot-text">
                <p>Join us with reference code:</p>
            </div>
            <div class="foot">
                <p class="number"><?php
/* banner1.html:178: {$refer_code} */
 echo $var["refer_code"]; ?></p>
            </div>
        </div>
    </div>
</div>
<?php
}, array(
	'options' => 128,
	'provider' => false,
	'name' => 'banner1.html',
	'base_name' => 'banner1.html',
	'time' => 1588253052,
	'depends' => array (
  0 => 
  array (
    'banner1.html' => 1588253052,
  ),
),
	'macros' => array(),

        ));

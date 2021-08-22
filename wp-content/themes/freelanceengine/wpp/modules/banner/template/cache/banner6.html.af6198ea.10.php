<?php 
/** Fenom template 'banner6.html' compiled at 2020-05-02 18:58:28 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?>    <style type="text/css">
        @import url('https://fonts.googleapis.com/css?family=Montserrat:700,600');

        .bn6 * {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        .bn6 {
            margin: 0;
            font-family: 'Montserrat',
                sans-serif;
            font-style: normal;
            font-weight: 600;
        }

        .bn6_wp {
            text-align: center;
            padding: 25px 0 5px;
            margin: 0 auto;
            width: 1200px;
            background-size: 1200px 640px!important;
        }

        .bn6_logo img {
            width: 582px;
            height: 40px;
            margin: auto;
        }

        .bn6_inner {
            position: relative;
            text-align: center;
            margin: 70px auto 0;
            width: 750px;
            background-size: 600px !important;
            padding: 15px 0 61px;
        }

        .bn6_inner-user {
            position: absolute;
            top: -60px;
            left: 0;
            right: 0;
            margin: 0 auto -70px;
            width: 180px;
            height: 180px;
            position: relative;
            z-index: 1;
            -webkit-border-radius: 50%;
            -moz-border-radius: 50%;
            border-radius: 50%;
            overflow: hidden;
            border: 15px solid rgba(255, 255, 255, 0.2);
        }

        .bn6_inner-userimg {
            position: absolute;
            top: 0;
            left: 0;
            z-index: 0;
            width: 100%;
            height: 100%;
            background-size: cover !important;
        }

        .bn6_inner-info {
            padding: 18px 0 15px;
            background: #EAE833;
            width: 445px;
            margin: 0 auto 47px;
        }

        .bn6_inner-t {
            display: block;
            padding: 0 15px;
            font-weight: bold;
            font-size: 32px;
            line-height: 44px;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            color: #191919;
        }

        .bn6_inner-txt {
            font-weight: 600;
            font-size: 28px;
            letter-spacing: 0.02em;
            color: #191919;
            opacity: 0.7;
            margin: 5px 0 0;
        }

        .bn6_btm-txt {
            font-weight: 600;
            font-size: 26px;
            letter-spacing: 0.02em;
            color: #191919;
            margin: 17px 0;
            line-height: 1;
        }

        .bn6_btm-code {
            width: 400px;
            margin: 0 auto 25px;
            padding: 20px 0;
            line-height: 1;
            font-weight: 600;
            font-size: 28px;
            line-height: 39px;
            letter-spacing: 0.05em;
            color: #191919;
            background: #FF88A2;
            -webkit-border-radius: 53px;
            -moz-border-radius: 53px;
            border-radius: 53px;
        }

        .bn6_inner-price {
            position: absolute;
            left: 85%;
            top: 60%;
            padding: 10px;
            width: 190px;
            height: 190px;
            font-weight: 600;
            font-size: 26px;
            line-height: 32px;
            text-align: center;
            letter-spacing: 0.02em;
            color: #191919;
            background-size: cover !important;
            -webkit-transform: rotate(15deg);
            -moz-transform: rotate(15deg);
            transform: rotate(15deg);
        }

        .bn6_inner-price p {
            margin: 45px 10px 0;
        }

    </style>


    <div class="bn6">
        <div class="bn6_wp" style="background: url(<?php
/* banner6.html:147: {$path_inc} */
 echo $var["path_inc"]; ?>/img/grad_bg.png) center no-repeat;">
            <div class="bn6_logo"><img src="<?php
/* banner6.html:148: {$path_inc} */
 echo $var["path_inc"]; ?>/img/Logo2.svg"></div>
            <div class="bn6_inner" style="background: url(<?php
/* banner6.html:149: {$path_inc} */
 echo $var["path_inc"]; ?>/img/round.png) bottom center no-repeat;">
                <div class="bn6_inner-user">
                    <div class="bn6_inner-userimg" style="background: url(<?php
/* banner6.html:151: {$avatar_url} */
 echo $var["avatar_url"]; ?>) center no-repeat;"></div>
                </div>
                <div class="bn6_inner-info">
                    <span class="bn6_inner-t"><?php
/* banner6.html:154: {$display_name} */
 echo $var["display_name"]; ?></span>
                    <p class="bn6_inner-txt">Invites You</p>
                </div>
                <div class="bn6_btm">
                    <div class="bn6_btm-txt">Join us with reference code:</div>
                    <div class="bn6_btm-code"><?php
/* banner6.html:159: {$refer_code} */
 echo $var["refer_code"]; ?></div>
                </div>
                <div class="bn6_inner-price" style="background: url(<?php
/* banner6.html:161: {$path_inc} */
 echo $var["path_inc"]; ?>/img/star.svg) center no-repeat;">
                    <p>Low-Cost Service Deals</p>
                </div>
            </div>
        </div>
    </div>
<?php
}, array(
	'options' => 128,
	'provider' => false,
	'name' => 'banner6.html',
	'base_name' => 'banner6.html',
	'time' => 1588253052,
	'depends' => array (
  0 => 
  array (
    'banner6.html' => 1588253052,
  ),
),
	'macros' => array(),

        ));

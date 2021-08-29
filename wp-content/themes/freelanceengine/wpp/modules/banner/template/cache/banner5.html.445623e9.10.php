<?php 
/** Fenom template 'banner5.html' compiled at 2020-05-02 18:58:27 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?>    <style type="text/css">
        @import url('https://fonts.googleapis.com/css?family=Montserrat:700,600');

        .bn5 * {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        .bn5 {
            margin: 0;
            font-family: 'Montserrat',
                sans-serif;
            font-style: normal;
            font-weight: 600;
        }

        .bn5_wp {
            padding: 55px 0 0;
            margin: 0 auto;
            width: 1200px;
            background-size: cover !important;
        }

        .bn5_logo {
            text-align: center;
            padding: 30px 0;
        }

        .bn5_logo img {
            width: 582px;
            height: 40px;
        }

        .bn5_inner {
            position: relative;
            margin: 20px auto 0;
            width: 877px;
        }

        .bn5_inner-user {
            float: left;
            width: 255px;
            height: 255px;
            position: relative;
            z-index: 1;
            -webkit-border-radius: 50%;
            -moz-border-radius: 50%;
            border-radius: 50%;
            overflow: hidden;
            border: 15px solid rgba(255, 255, 255, 0.2);
        }

        .bn5_inner-userimg {
            position: absolute;
            top: 0;
            left: 0;
            z-index: 0;
            width: 100%;
            height: 100%;
            background-size: cover !important;
        }


        .bn5_inner-info {
            padding: 45px 0 0 55px;
            float: left;
            overflow: hidden;
            width: 620px;
        }

        .bn5_inner-t {
            display: block;
            font-weight: bold;
            font-size: 58px;
            line-height: 76px;
            letter-spacing: 0.02em;
            text-align: left;
            text-transform: uppercase;
            color: #fff;
        }

        .bn5_inner-txt {
            font-weight: 600;
            font-size: 42px;
            letter-spacing: 0.02em;
            color: #fff;
            opacity: 0.7;
            text-align: left;
            margin: 28px 0 0;
        }

        .bn5_btm-txt {
            font-weight: 600;
            font-size: 30px;
            letter-spacing: 0.02em;
            color: #fff;
            margin: 17px 0;
            line-height: 1;
        }

        .bn5_btm {
            text-align: center;
            width: 670px;
            padding: 17px 75px;
            clear: both;
            margin: 0 auto;
        }

        .bn5_btm-code {
            width: 510px;
            margin: 30px auto 0;
            padding: 25px 0;
            line-height: 1;
            font-weight: 600;
            font-size: 38px;
            line-height: 46px;
            letter-spacing: 0.05em;
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
            -webkit-border-radius: 53px;
            -moz-border-radius: 53px;
            border-radius: 53px;
        }

        .bn5_inner-price {
            position: absolute;
            left: -12%;
            top: 64%;
            padding: 10px;
            width: 150px;
            height: 150px;
            font-weight: 600;
            font-size: 20px;
            line-height: 24px;
            text-align: center;
            letter-spacing: 0.02em;
            color: #fff;
            background-size: cover !important;
            -webkit-transform: rotate(-15deg);
            -moz-transform: rotate(-15deg);
            transform: rotate(-15deg);
        }

        .bn5_inner-price p {
            margin: 35px 0 0;
        }

    </style>


    <div class="bn5">
        <div class="bn5_wp" style="background: url(<?php
/* banner5.html:153: {$path_inc} */
 echo $var["path_inc"]; ?>/img/circles2.png) top right no-repeat;">
            <div class="bn5_logo"><img src="<?php
/* banner5.html:154: {$path_inc} */
 echo $var["path_inc"]; ?>/img/Logo2.svg"></div>
            <div class="bn5_inner">
                <div class="bn5_inner-user">
                    <div class="bn5_inner-userimg" style="background: url(<?php
/* banner5.html:157: {$avatar_url} */
 echo $var["avatar_url"]; ?>) center no-repeat;"></div>
                </div>
                <div class="bn5_inner-info">
                    <span class="bn5_inner-t"><?php
/* banner5.html:160: {$display_name} */
 echo $var["display_name"]; ?></span>
                    <p class="bn5_inner-txt">Invites You</p>
                </div>
                <div class="bn5_btm">
                    <div class="bn5_btm-txt">Join us with reference code:</div>
                    <div class="bn5_btm-code"><?php
/* banner5.html:165: {$refer_code} */
 echo $var["refer_code"]; ?></div>
                </div>
                <div class="bn5_inner-price" style="background: url(<?php
/* banner5.html:167: {$path_inc} */
 echo $var["path_inc"]; ?>/img/star2.svg) center no-repeat;">
                    <p>Low-Cost Service Deals</p>
                </div>
            </div>
        </div>
    </div>
<?php
}, array(
	'options' => 128,
	'provider' => false,
	'name' => 'banner5.html',
	'base_name' => 'banner5.html',
	'time' => 1588253052,
	'depends' => array (
  0 => 
  array (
    'banner5.html' => 1588253052,
  ),
),
	'macros' => array(),

        ));
<?php 
/** Fenom template 'banner4.html' compiled at 2020-05-02 18:58:27 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?> <style type="text/css">
        @import url('https://fonts.googleapis.com/css?family=Montserrat:700,600');

        .bn4 * {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        .bn4 {
            margin: 0;
            font-family: 'Montserrat',
                sans-serif;
            font-style: normal;
            font-weight: 600;
        }

        .bn4_wp {
            padding: 55px 0 0;
            margin: 0 auto;
            width: 1200px;
            background-size: 475px !important;
        }

        .bn4_logo {
            padding: 0 60px;
            text-align: left;
        }

        .bn4_logo img {
            width: 582px;
            height: 40px;
            margin: 0;
        }

        .bn4_inner {
            position: relative;
            margin: 10px 0 0;
            width: 1050px;
            background-size: cover !important;
            padding: 130px 60px 30px;
        }

        .bn4_inner-user {
            float: left;
            width: 187px;
            height: 187px;
            position: relative;
            z-index: 1;
            -webkit-border-radius: 50%;
            -moz-border-radius: 50%;
            border-radius: 50%;
            overflow: hidden;
            border: 15px solid rgba(255, 255, 255, 0.2);
        }

        .bn4_inner-userimg {
            position: absolute;
            top: 0;
            left: 0;
            z-index: 0;
            width: 100%;
            height: 100%;
            background-size: cover !important;
        }

        .bn4_inner-info {
            padding: 40px 40px 0px;
            float: left;
        }

        .bn4_inner-t {
            display: block;
            font-weight: bold;
            max-width: 400px;
            font-size: 42px;
            text-align: left;
            line-height: 51px;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            color: #191919;
        }

        .bn4_inner-txt {
            font-weight: 600;
            font-size: 34px;
            letter-spacing: 0.02em;
            color: #191919;
            opacity: 0.7;
            margin: 22px 0 0;
            text-align: left;
        }

        .bn4_btm-txt {
            font-weight: 600;
            font-size: 24px;
            letter-spacing: 0.02em;
            color: #2C33C1;
            margin: 17px 0;
            line-height: 1;
        }

        .bn4_btm {
            text-align: center;
            width: 610px;
            padding: 24px 75px 0;
            clear: both;
        }

        .bn4_btm-code {
            width: 460px;
            margin: 30px 0 0;
            padding: 25px 0;
            line-height: 1;
            font-weight: 600;
            font-size: 32px;
            line-height: 39px;
            letter-spacing: 0.05em;
            color: #fff;
            background: #2C33C1;
            -webkit-border-radius: 53px;
            -moz-border-radius: 53px;
            border-radius: 53px;
        }

        .bn4_inner-price {
            position: absolute;
            left: 80%;
            top: 60%;
            padding: 10px;
            width: 210px;
            height: 210px;
            font-weight: 600;
            font-size: 30px;
            line-height: 37px;
            text-align: center;
            letter-spacing: 0.02em;
            color: #fff;
            background-size: cover !important;
            -webkit-transform: rotate(15deg);
            -moz-transform: rotate(15deg);
            transform: rotate(15deg);
        }

        .bn4_inner-price p {
            margin: 45px 0 0;
        }

    </style>

  <div class="bn4">
      <div class="bn4_wp" style="background: #2C33C1 url(<?php
/* banner4.html:152: {$path_inc} */
 echo $var["path_inc"]; ?>/img/circles.png) top right no-repeat;">
          <div class="bn4_logo"><img src="<?php
/* banner4.html:153: {$path_inc} */
 echo $var["path_inc"]; ?>/img/Logo2.svg"></div>
          <div class="bn4_inner" style="background: url(<?php
/* banner4.html:154: {$path_inc} */
 echo $var["path_inc"]; ?>/img/planet.png) top left no-repeat;">
              <div class="bn4_inner-user">
                  <div class="bn4_inner-userimg" style="background: url(<?php
/* banner4.html:156: {$avatar_url} */
 echo $var["avatar_url"]; ?>) center no-repeat;"></div>
              </div>
              <div class="bn4_inner-info">
                  <span class="bn4_inner-t"><?php
/* banner4.html:159: {$display_name} */
 echo $var["display_name"]; ?></span>
                  <p class="bn4_inner-txt">Invites You</p>
              </div>
              <div class="bn4_btm">
                  <div class="bn4_btm-txt">Join us with reference code:</div>
                  <div class="bn4_btm-code"><?php
/* banner4.html:164: {$refer_code} */
 echo $var["refer_code"]; ?></div>
              </div>
              <div class="bn4_inner-price" style="background: url(<?php
/* banner4.html:166: {$path_inc} */
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
	'name' => 'banner4.html',
	'base_name' => 'banner4.html',
	'time' => 1588253052,
	'depends' => array (
  0 => 
  array (
    'banner4.html' => 1588253052,
  ),
),
	'macros' => array(),

        ));

<?php 
/** Fenom template 'poster3.html' compiled at 2020-04-30 22:56:17 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?><!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1 ,user-scalable=no">
    <title>Poster3 | MasterHand Pro</title>
    <style type="text/css">
        @font-face {
            font-family: "DinProCond";
            src: url("<?php
/* poster3.html:11: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/fonts/DINPro-Cond.eot#")format("eot"),
            url("<?php
/* poster3.html:12: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/fonts/DINPro-Cond.woff") format("woff"),
            url("<?php
/* poster3.html:13: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/fonts/DINPro-Cond.ttf") format("truetype");
            font-style: normal;
            font-weight: normal;
        }

        @font-face {
            font-family: "DinProCond-Bold";
            src: url("<?php
/* poster3.html:20: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/fonts/DINPro-CondBold.eot#")format("eot"),
            url("<?php
/* poster3.html:21: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/fonts/DINPro-CondBold.woff") format("woff"),
            url("<?php
/* poster3.html:22: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/fonts/DINPro-CondBold.ttf") format("truetype");
            font-style: normal;
            font-weight: normal;
        }

        :after,
        :before {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        body {
            font-size: 14px;
            line-height: 1.42857143;
            margin: 0;
            font-weight: 500;
            font-family: 'Arial', sans-serif;
            color: #878787;
            background: #f9f9f9;
        }

        * {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        .row {
            margin-right: -15px;
            margin-left: -15px;
        }

        .row:before {
            display: table;
            content: " ";
        }

        .row:after {
            display: table;
            content: " ";
            clear: both;
        }

        .col-xs-2,
        .col-sm-2,
        .col-sm-3,
        .col-xs-3,
        .col-sm-6,
        .col-xs-6,
        .col-sm-5,
        .col-sm-7,
        .col-sm-12,
        .col-xs-12,
        .col-xs-5,
        .col-xs-7 {
            float: left;
            position: relative;
            min-height: 1px;
            padding-right: 15px;
            padding-left: 15px;
        }

        .col-xs-2,
        .col-sm-2 {
            width: 16.66666667%;
        }

        .col-sm-3,
        .col-xs-3 {
            width: 25%;
        }

        .col-sm-5,
        .col-xs-5 {
            width: 41.66666667%;
        }

        .col-sm-7,
        .col-xs-7 {
            width: 58.33333333%;
        }

        .col-sm-6,
        .col-xs-6 {
            width: 50%;
        }

        .col-sm-12,
        .col-xs-12 {
            width: 100%;
        }

        .poster {
            font-family: 'Arial', sans-serif;
            background: #fff;
            margin: auto;
            overflow: hidden
        }

        .poster.poster3 {
            width: 595px
        }

        .poster>.row {
            margin: 0
        }

        .poster_right {
            padding: 0 0 0 20px;
        }

        .poster_right .poster_category {
            padding: 10px 0 0px 25px;
            width: 60%;
            height: 100px;
            margin-bottom: 71px;
            overflow: hidden;
        }

        .poster_right .poster_category:before {
            content: '';
            position: absolute;
            width: 1px;
            height: 100%;
            top: -18%;
            left: 5px;
            background: #A1D9F8
        }

        .poster_right .poster_category li {
            color: #fff;
            font-size: 12px
        }

        .poster_exp {
            background: #EF7C00;
            margin-top: 60px;
            padding: 10px 10px 10px 25%;
            text-align: right;
            position: absolute;
            bottom: 0;
            right: 0;
            width: 75%;
            z-index: -1
        }

        .poster_exp .poster_t {
            padding: 2px 0
        }

        .poster_exp .poster_txt {
            font-size: 22px;
            color: #fff
        }

        .poster .pull-right {
            float: right
        }

        .poster_author {
            margin: 0 -30px 0 -15px;
            position: relative;
            z-index: 5;
            padding: 55px 0 0 0
        }

        .poster_author .circle {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%
        }

        .poster_author .circle:before,
        .poster_author .circle:after {
            content: '';
            position: absolute;
            z-index: -1
        }

        .poster_author .circle:before {
            width: 92px;
            height: 92px;
            background: #EF7C00;
            top: -15px;
            left: 30px;
            z-index: 1;
            -webkit-border-radius: 50%;
            -moz-border-radius: 50%;
            border-radius: 50%
        }

        .poster_author .circle:after {
            top: 0;
            left: 0;
            border: 120px solid transparent;
            border-bottom: 120px solid #B1B2B3;
            border-left: 120px solid #B1B2B3
        }

        .poster_t {
            font-size: 18px;
            font-weight: bold;
            color: #1A1A18;
            margin: 12px 0 6px;
            line-height: 1;
            text-align: right;
            text-decoration: underline;
            text-transform: uppercase
        }

        .poster_name {
            color: #1A1A18;
            font-size: 24px;
            text-transform: uppercase;
            margin: 0 0 -145px;
            line-height: 1.2;
            font-weight: 600;
            position: relative;
            z-index: 5;
            top: -70px;
            padding: 15px 0 15px 25px
        }

        .poster_name:before {
            content: '';
            position: absolute;
            z-index: -1;
            top: 0;
            right: 0;
            width: 150%;
            height: 100%;
            background: #ECEEEF;
            transform: skew(-45deg);
            -webkit-transform: skew(-45deg);
            -moz-transform: skew(-45deg);
            -o-transform: skew(-45deg);
            -ms-transform: skew(-45deg)
        }

        .poster_txt {
            font-size: 16px;
            color: #1A1A18;
            position: relative;
            z-index: 0
        }

        .poster_txt-about {
            font-size: 13px;
            line-height: 1;
            text-align: justify;
            color: #878786
        }

        .poster_txt-more {
            color: #A1D9F8;
            font-size: 16px;
            line-height: 1.15;
            letter-spacing: -0.09em;
            padding: 30px 0 25px;
            text-align: right;
            position: relative;
            z-index: 0
        }

        .poster_txt-more:before {
            content: '';
            position: absolute;
            z-index: -1;
            top: 0;
            right: -50%;
            width: 170%;
            height: 100%;
            background: #064497;
            transform: skew(-45deg);
            -webkit-transform: skew(-45deg);
            -moz-transform: skew(-45deg);
            -o-transform: skew(-45deg);
            -ms-transform: skew(-45deg)
        }

        .poster_txt-more:after {
            content: '';
            position: absolute;
            z-index: -1;
            top: -30px;
            right: -25%;
            width: 80%;
            height: 30px;
            background: #B1B2B3;
            transform: skew(-45deg);
            -webkit-transform: skew(-45deg);
            -moz-transform: skew(-45deg);
            -o-transform: skew(-45deg);
            -ms-transform: skew(-45deg)
        }

        .poster_hire {
            color: #fff;
            color: #1A1A18;
            font-size: 15px;
            text-transform: uppercase;
            line-height: 1.2;
            font-weight: 600;
            margin: -5px 0 0;
            letter-spacing: -0.03em;
            text-align: right;
        }

        .poster_hire span {
            color: #064497
        }

        .poster_main-t {
            font-size: 39px;
            height: 68px;
            text-transform: uppercase;
            line-height: 1.2;
            margin: 10px 0 5px;
            padding: 20px 0 0;
            font-weight: 600;
            letter-spacing: -0.015em;
            color: #1A1A18;
            text-align: right;
            font-family: "DinProCond-Bold"
        }

        .poster_main-t span.yellow {
            color: #fff;
            font-weight: 400;
            padding: 0 5px;
            background: #EF7C00;
            font-family: "DinProCond"
        }

        .poster_img {
            position: relative;
            z-index: 0;
            width: 290px;
            max-width: 100%;
            height: 290px
        }

        .poster_img .img {
            width: 240px;
            max-width: 100%;
            height: 240px;
            overflow: hidden;
            float: right;
            position: relative;
            z-index: 1;
            background-size: cover !important;
            -webkit-border-radius: 50%;
            -moz-border-radius: 50%;
            border-radius: 50%
        }

        .poster_img:after {
            content: '';
            position: absolute;
            z-index: -1;
            width: 100%;
            height: 100%;
            border: 30px solid #ECECED;
            -webkit-border-radius: 50%;
            -moz-border-radius: 50%;
            border-radius: 50%;
            top: -25px;
            left: 25px;
            z-index: 0
        }

        .poster_img:before {
            content: '';
            position: absolute;
            border: 130px solid transparent;
            border-top: 130px solid #fff;
            border-right: 130px solid #fff;
            top: -30px;
            left: -33%;
            z-index: 1
        }

        .poster_category li {
            font-size: 17px;
            color: #6F6F6E;
            letter-spacing: -0.025em;
            line-height: 1.2;
            text-transform: none;
            font-weight: 400
        }

        .poster ul {
            padding: 0;
            margin: 0
        }

        .poster ul li {
            list-style: none
        }

        .poster_info {
            margin: 0;
            clear: both;
            position: relative;
            z-index: 0
        }

        .poster_info:before,
        .poster_info:after {
            content: '';
            position: absolute;
            z-index: -1
        }

        .poster_info:before {
            width: 100%;
            height: 40px;
            background: #EF7C00;
            top: 0;
            left: 0;
            z-index: 0
        }

        .poster_info:after {
            top: 0;
            left: -12px;
            z-index: 8;
            border: 50px solid transparent;
            border-left: 50px solid #fff;
            border-top: 50px solid #fff
        }

        .poster_info .poster_right {
            position: relative;
            z-index: 5;
            padding: 0 5px 0 10px
        }

        .poster_info .poster_right:before,
        .poster_info .poster_right:after {
            content: '';
            position: absolute;
            z-index: -1
        }

        .poster_info .poster_right:after {
            left: -356px;
            border: 180px solid transparent;
            border-bottom: 180px solid #B1B2B3;
            bottom: 29%;
            z-index: 9
        }

        .poster_info .poster_right:before {
            z-index: 0;
            top: 40px;
            right: 45%;
            width: 170%;
            height: 100%;
            background: #064497;
            transform: skew(-45deg);
            -webkit-transform: skew(-45deg);
            -moz-transform: skew(-45deg);
            -o-transform: skew(-45deg);
            -ms-transform: skew(-45deg)
        }

        .poster_skills {
            padding: 10px 10px 0 17px;
            background: #EF7C00;
            margin: 0 -15px
        }

        .poster_skills .poster_t {
            margin: 0;
            padding: 0
        }

        .poster_skills ul {
            padding: 0px 5px 0;
            overflow: hidden;
            min-height: 30px;
        }

        .poster_skills ul li {
            font-size: 12px;
            width: 33%;
            float: left;
            color: #fff;
            line-height: 1.2;
            padding: 0 0 0 5px;
            border-left: 1px solid #A1D9F8;
            margin: 0 0 15px
        }

        .poster_skills ul li:nth-child(3) {
            clear: both
        }

        .poster_register {
            margin: 0 -60px 0 -10px;
            padding: 52px 0 0;
            position: relative;
            z-index: 12
        }

        .poster_register:before,
        .poster_register:after {
            content: '';
            position: absolute;
            z-index: -1
        }

        .poster_register:before {
            content: '';
            position: absolute;
            top: 13px;
            z-index: 0;
            right: -60px;
            width: 405px;
            height: 100%;
            background: url(<?php
/* poster3.html:546: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/img/triang.svg) center no-repeat;
            background-size: cover !important
        }

        .poster_register:after {
            z-index: -1;
            top: 40px;
            left: -20px;
            width: 50%;
            height: 100%;
            background: #064497;
            transform: skew(-48deg);
            -webkit-transform: skew(-48deg);
            -moz-transform: skew(-48deg);
            -o-transform: skew(-48deg);
            -ms-transform: skew(-48deg)
        }

        .poster_register_t {
            font-size: 16px;
            color: #1A1A18;
            text-transform: uppercase;
            line-height: 1.2;
            text-align: center;
            letter-spacing: -0.025em;
            position: relative
        }

        .poster_register ol {
            margin: 0;
            padding: 50px 0 10px 18px;
            counter-reset: myCounter;
            overflow: hidden
        }

        .poster_register ol li {
            font-size: 9px;
            margin: 0 0 5px;
            width: 36%;
            display: block;
            float: left;
            color: #1A1A18;
            list-style: none;
            line-height: 1.2;
            padding: 0 0 0 5px;
            border-left: 1px solid #A1D9F8;
            position: relative
        }

        .poster_register ol li:first-child:before {
            right: 0;
            left: 0;
            margin: auto
        }

        .poster_register ol li:first-child:after {
            right: 0
        }

        .poster_register ol li:before {
            counter-increment: myCounter;
            content: counter(myCounter);
            color: #A1D9F8;
            position: absolute;
            top: -45px;
            left: 15%;
            display: block;
            font-size: 31px;
            -webkit-border-radius: 50%;
            -moz-border-radius: 50%;
            border-radius: 50%;
            border: 1px dashed #EF7C00;
            text-align: center;
            line-height: 38px;
            width: 38px;
            height: 38px
        }

        .poster_register ol li:after {
            content: '';
            position: absolute;
            right: 25px;
            transform: rotate(-90deg);
            -webkit-transform: rotate(-90deg);
            -moz-transform: rotate(-90deg);
            -o-transform: rotate(-90deg);
            -ms-transform: rotate(-90deg);
            top: -35px;
            background: url(<?php
/* poster3.html:634: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/img/reg-arrow2.svg) center no-repeat;
            display: inline-block;
            text-align: center;
            line-height: 1;
            width: 20px;
            height: 18px
        }

        .poster_register ol li:last-child {
            margin: 0;
            width: 28%
        }

        .poster_register ol li:last-child:before {
            left: -15%;
            right: auto
        }

        .poster_register ol li:last-child:after {
            display: none
        }

        .poster .poster_invite {
            padding: 10px 80px 0 0;
            margin: 0 0 0 -10px;
            position: relative;
            z-index: 0
        }

        .poster_invite:before {
            content: '';
            position: absolute;
            right: 0;
            top: 10px;
            height: 85%;
            width: 1px;
            background: #9C9D9D
        }

        .poster_invite li {
            font-size: 15px;
            color: #1A1A18;
            padding: 5px 0 5px 45px;
            position: relative;
            z-index: 0;
            line-height: 1.2;
            letter-spacing: -0.09em
        }

        .poster_invite img {
            width: auto;
            height: 26px;
            max-width: 25px;
            position: absolute;
            left: 0px;
            top: 0;
            bottom: 0;
            margin: auto
        }

        .poster_code {
            color: #9C9D9D;
            font-size: 15px;
            background: #ECECED;
            text-align: center;
            display: inline-block;
            padding: 5px 10px;
            min-width: 115px;
        }

        .poster_code-t {
            font-size: 16px;
            color: #878786;
            display: block;
            padding: 10px 0 5px;
            line-height: 1
        }

        .poster .no-pad {
            padding: 0
        }

        .poster .no-pad2 {
            padding: 0 0 0 15px;
        }

        .poster_scheme {
            padding: 12px 0;
            text-align: center;
        }

        .poster_scheme img {
            margin: 20px 0 0
        }

        .poster_web {
            margin: 20px 0 0 0
        }

        .poster_web span {
            text-transform: uppercase;
            color: #fff;
            background: #133D82;
            padding: 5px 10px;
            font-size: 10px
        }

    </style>
</head>

<body>
    <div class="fre-page-wrapper">

        <div class="poster3 poster">
            <div class="row">
                <div class="col-sm-6 col-xs-6">
                    <div class="poster_author">
                        <div class="poster_img">
                            <div class="circle"></div>
                            <div class="img" style="background:url(<?php
/* poster3.html:753: {$avatar_url} */
 echo $var["avatar_url"]; ?>) center no-repeat"></div>
                        </div>
                        <div class="poster_name"><?php
/* poster3.html:755: {$display_name} */
 echo $var["display_name"]; ?></div>
                    </div>
                </div>
                <div class="col-sm-6 col-xs-6">
                    <div class="poster_right">
                        <div class="poster_main-t"><span>MASTER</span>HAND <span class="yellow">PRO</span></div>
                        <div class="poster_hire"> HIRE REAL LOCAL <span>MASTER HAND</span></div>
                        <div class="poster_t">About me</div>
                        <div class="poster_txt-about"><?php
/* poster3.html:763: {$about} */
 echo $var["about"]; ?></div>
                    </div>
                </div>
                <div class="col-sm-12 col-xs-12">
                    <div class="col-sm-6 col-xs-6 pull-right poster_txt-more">
                        Save up to 70% on hiring
                        perfect local professional
                        on the website, it is Super
                        Fast and Easy.
                    </div>
                </div>
                <div class="row poster_info">
                    <div class="col-sm-6 col-xs-6">
                        <div class="poster_register">
                            <div class="poster_register_t">HOW TO<br> REGISTER<br> ON THE SITE</div>
                            <ol>
                                <li>Go to www.masterhand.pro</li>
                                <li>Sign up with reference code <?php
/* poster3.html:780: {$refer_code} */
 echo $var["refer_code"]; ?></li>
                                <li>Post your task and receive bids</li>
                            </ol>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-6 poster_right">
                        <div class="poster_t">SPECIALIZATION</div>
                        <div class="poster_txt">
                            <ul class="poster_category">
                                <?php  if(!empty($var["category"]) && (is_array($var["category"]) || $var["category"] instanceof \Traversable)) {
  foreach($var["category"] as $var["item"]) { ?>
                                <li><?php
/* poster3.html:790: {$item} */
 echo $var["item"]; ?></li>
                                <?php
/* poster3.html:791: {/foreach} */
   } } ?>
                            </ul>
                        </div>
                        <div class="poster_exp">
                            <div class="poster_t">WORK<br> EXPERIENCE</div>
                            <div class="poster_txt"><?php
/* poster3.html:796: {$experience} */
 echo $var["experience"]; ?> years</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-xs-12">
                    <div class="poster_skills">
                        <div class="poster_t">Skills</div>
                        <ul>
                            <?php  if(!empty($var["skills"]) && (is_array($var["skills"]) || $var["skills"] instanceof \Traversable)) {
  foreach($var["skills"] as $var["item"]) { ?>
                            <li><?php
/* poster3.html:805: {$item} */
 echo $var["item"]; ?></li>
                            <?php
/* poster3.html:806: {/foreach} */
   } } ?>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-12 col-xs-12">
                    <div class="col-sm-7 col-xs-7">
                        <ul class="poster_invite">
                            <li><img src="<?php
/* poster3.html:813: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/img/invite1-1.svg" alt="" />Post your task</li>
                            <li><img src="<?php
/* poster3.html:814: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/img/invite1-2.svg" alt="" />Invite me</li>
                            <li><img src="<?php
/* poster3.html:815: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/img/invite1-3.svg" alt="" />Get qualified proposal with bid price within minutes</li>
                        </ul>
                    </div>
                    <div class="col-sm-3 col-xs-3 no-pad2">
                        <div class="poster_web"><span>masterhand.pro</span></div>
                        <span class="poster_code-t">Reference code:</span>
                        <div class="poster_code"><?php
/* poster3.html:821: {$refer_code} */
 echo $var["refer_code"]; ?></div>
                    </div>
                    <div class="col-sm-2 col-xs-2 poster_scheme">
                        <img src="<?php
/* poster3.html:824: {$qr_code} */
 echo $var["qr_code"]; ?>" alt="" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<?php
}, array(
	'options' => 128,
	'provider' => false,
	'name' => 'poster3.html',
	'base_name' => 'poster3.html',
	'time' => 1588253060,
	'depends' => array (
  0 => 
  array (
    'poster3.html' => 1588253060,
  ),
),
	'macros' => array(),

        ));

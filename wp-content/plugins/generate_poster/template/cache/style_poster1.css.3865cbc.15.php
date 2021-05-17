<?php 
/** Fenom template 'style_poster1.css' compiled at 2020-07-20 18:32:28 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?>body {
    font-size: 14px;
    line-height: 1.42857143;
    margin: 0;
    font-weight: 500;
    font-family: 'Arial', sans-serif;
    color: #878787;
}

* {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}

.row {
    margin-right: -15px;
    margin-left: -15px;
    overflow: hidden;
}

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

.col-sm-5,
.col-xs-5 {
    width: 230px;
}

.col-sm-7,
.col-xs-7 {
    width: 335px;
}

.col-sm-12,
.col-xs-12 {
    clear: both;
}

.poster {
    font-family: 'Arial', sans-serif;
    background: #fff;
    margin: auto;
    overflow: hidden;
    width: 595px;
    position: relative;
    z-index: 0;
    background-image:url(<?php
/* style_poster1.css:58: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/img/poster_form.png);
    background-position:right top;
    background-repeat: no-repeat;
}

.poster>.row>.col-sm-12 {
    z-index: 9;
}

.poster_right {
    width: 230px;
    position: relative;
    z-index: 5;
    padding: 7px 0 0 17px
}

.poster_t {
    font-size: 18px;
    font-weight: bold;
    color: #FFCC00;
    margin: 15px 0 2px;
    text-transform: uppercase;
    text-decoration: underline
}

.poster_name {
    color: #1A1A18;
    font-size: 25px;
    text-transform: uppercase;
    margin-right: 25px;
    line-height: 1.2;
    font-weight: 600;
    height: 30px;
    margin-top:15px;
}

.poster_author {
    padding: 25px 0 140px 35px;
    background: url(<?php
/* style_poster1.css:96: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/img/circle.png) top left no-repeat;
    width: 360px;
}

.poster_txt {
    font-size: 16px;
    color: #fff;
    padding-right: 45px;
}

.poster_txt-about {
    font-size: 13px;
    line-height: 1;
    text-align: justify;
    color: #fff;
    padding-right: 45px;
    max-height: 130px;
    min-height: 50px;
}

.poster_txt-more {
    color: #1A1A18;
    font-size: 14px;
    margin-right: 35px;
    line-height: 1.2
}

.poster_hire {
    color: #565f64;
    font-size: 22px;
    line-height: 1.2;
    background: #FFCC00;
    letter-spacing: -0.03em;
    padding-top: 8px;
    padding-bottom: 8px;
    padding-left: 35px;
    margin-left: -10px;
    margin-right: -18px;
    margin-top: 10px;
}

.poster_main-t {
    color: #9C9D9D;
    font-size: 37px;
    line-height: 1.2;
    background: #565f64;
    letter-spacing: -0.015em;
    color: #fff;
    font-family: dinprocondbold;
    padding-top: 10px;
    padding-bottom: 10px;
    padding-left: 42px;
    margin-left: -14px;
    margin-right: -20px;
    margin-top: 10px;
    z-index: 5;
}

.poster_main-t-after {
    border-left: 20px solid #fff;
    z-index: 10;
    margin-top: -47px;
    margin-left: 230px;
    border-bottom: 25px solid rgb(86, 95, 100);
    border-right: 25px solid rgb(86, 95, 100);
    border-top: 25px solid rgb(86, 95, 100);
}

.poster_main-t span {
    color: #C5C6C6
}

.poster_main-t .yellow {
    color: #1A1A18;
    background: #FFCC00;
    font-family: dinprocond;
    border-color: #FFCC00;
    border-left:2px;
    border-right:2px;
    border-style:solid;
}

.poster_img {
    position: relative;
    z-index: 1;
    width: 280px;
    max-width: 100%;
    height: 280px;
    -webkit-border-radius: 50%;
    -moz-border-radius: 50%;
    border-radius: 50%;
    overflow: hidden;
    background-size: cover !important;
}

.poster_category {
    min-height: 50px;
    margin: 5px 0 0 !important
}

.poster_category li {
    font-size: 16px;
    color: #fff;
    letter-spacing: -0.025em;
    line-height: 1.2
}

.poster ul {
    padding: 0;
    margin: 0
}

.poster ul li {
    list-style: none
}

.poster_skills {
    padding-left: 17px;
    padding-right: 17px;
    height: 200px;
    background-color: #fff;
}

.poster_skills ul {
    padding: 15px 0 0;
    background: #fff;
}

.poster_skills ul li {
    font-size: 16px;
    color: #6F6F6E;
    line-height: 1.2;
    margin: 0 0 2px
}

.last-item {
    padding-bottom: 10px;
    border-bottom: 1px dashed #6F6F6E;
    margin: 0 0 10px;
}

.poster_register {
    border-bottom: 5px solid #fff;
    padding-bottom: 1px;
    margin-bottom: -15px;
    height: 263px;
    background-image:url(<?php
/* style_poster1.css:242: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/img/figure.svg);
    background-position:right top;
    background-repeat: no-repeat;
    background-image-resize: 6;
}

.poster_register_t {
    font-size: 23px;
    color: #1A1A18;
    line-height: 1.2;
    letter-spacing: -0.025em;

    width: 260px;
    background-color: #fff;
    padding-left: 34px;
    padding-top: 16px;
    padding-bottom: 9px;
}

.poster_register_img {
    float: left;
    width: 30px;
    padding-top: 17px;
    padding-left: 55px;
    padding-right: 15px;
}

.poster_ol {
    padding-top: 15px;
}

.poster_ol .poster_li {
    font-size: 17px;
    color: #1A1A18;
    list-style: none;
    line-height: 1.2;
    border-left: 1px solid #565f64;
    position: relative;
    padding-left: 12px;
    height: 40px;
    margin-bottom: 13px;
}

.poster_invite {
    padding-top: 15px;
}

.poster_invite .line {
    font-size: 12px;
    color: #1A1A18;
    vertical-align:super;
    height: 30px;
}

.poster_code {
    color: #9C9D9D;
    font-size: 15px;
    background-color: #ECECED;
    text-align: center;

    border-color: #fff;
    border-top:15px;
    border-bottom:15px;
    border-left:5px;
    border-right:5px;
    border-style:solid;

    width:200px;
}

.poster_text{
    width:100px;
    text-align: right
}

.reff_block{
    padding-top: 10px;
    vertical-align:baseline;
}

.poster_top1 {
    width: 370px;
    float:left;
}
.poster_top2 {
    width: 255px;
    overflow: hidden;
}<?php
}, array(
	'options' => 128,
	'provider' => false,
	'name' => 'style_poster1.css',
	'base_name' => 'style_poster1.css',
	'time' => 1588253060,
	'depends' => array (
  0 => 
  array (
    'style_poster1.css' => 1588253060,
  ),
),
	'macros' => array(),

        ));

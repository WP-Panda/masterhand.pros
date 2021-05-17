<?php 
/** Fenom template 'style_poster2.css' compiled at 2020-07-20 22:13:54 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?>body {
    font-size: 14px;
    line-height: 1.42857143;
    /*margin: 0;*/
    /*font-weight: 500;*/
    font-family: 'Arial', sans-serif;
    color: #878787;
    background: #f9f9f9;
}

.poster_author {
    float: left;
    padding-left: 30px;
    padding-right: 23px;
    padding-bottom: 10px;
    height: 240px;
    width: 270px;
}
.poster {
    font-family: 'Arial', sans-serif;
    background: #fff;
    margin: auto;
}

.poster.poster2 {
    width: 595px;
    padding-top: 30px;
    border: #ccc 1px solid;
    padding-bottom: 30px;
}

.poster_about {
    width: 240px;
    overflow: hidden;
    padding-right: 30px;
}

.poster_right {
    width: 250px;
    overflow: hidden;
}

.poster_right .poster_category li {
    font-size: 14px;
    color: #6F6F6E
}

.poster_t {
    font-size: 18px;
    font-weight: bold;
    color: #fff;
    background: #7DA4BD;
    padding-top: 3px;
    padding-left: 10px;
    padding-bottom: 3px;
    line-height: 1;
    border-right: 7px solid #EF7C00;
    text-transform: uppercase;
    margin-bottom: 12px;
}

.poster_title {
    font-size: 18px;
    font-weight: bold;
    color: #fff;
    background: #7DA4BD;
    margin-top: 13px;
    padding-left: 11px;
    border-right: 7px solid #EF7C00;
    text-transform: uppercase;
    margin-bottom: 10px;
    margin-right: 40px;
}

.poster_skills ul {
    padding-left: 0;
    font-size: 17px;
    color: #1A1A18;
    line-height: 1.2;
}

.poster_name {
    font-weight: bold;
    color: #1A1A18;
    font-size: 26px;
    text-transform: uppercase;
    line-height: 1.2;
}

.poster_txt {
    font-size: 16px;
    color: #1A1A18
}

.poster_txt-about {
    font-size: 13px;
    line-height: 1.1;
    text-align: justify;
    color: #878786;
}

.poster_txt-more {
    margin-top: 35px;
    color: #1A1A18;
    font-size: 16px;
    padding-top: 15px;
    line-height: 1.15;
    margin-bottom: 10px;
}

.poster_left {
    float: left;
    width: 288px;
    border-bottom: 3px solid #2F4389;
}

.poster_main-t {
    padding-top: 11px;
    float: left;
    padding-left: 33px;
    width: 285px;
    font-size: 37px;
    line-height: 1.2;
    background: #133D82;
    color: #fff;
    font-family: dinprocondbold;
    padding-bottom: 11px;
}

.poster_hire {
    margin-top: -67px;
    width: 220px;
    height: 51px;
    float: right;
    color: #fff;
    font-size: 22px;
    text-transform: uppercase;
    line-height: 1.2;
    text-align: center;
    padding-top: 8px;
    padding-bottom: 7px;
    padding-left: 16px;
    padding-right: 16px;
    background: #7DA4BD;
    border-right: 25px solid #133D82;
    font-weight: bold;
}

.poster_main-t .yellow {
    color: #fff;
    background: #EF7C00;
    font-family: dinprocond;
    border-color: #EF7C00;
    border-left: 10px;
    border-right: 10px;
    border-top: 5px;
    border-bottom: 5px;
    border-style: solid;

}

.poster_category {
    margin-top: 0;
    padding-left: 0;
    margin-bottom: 0;
}

.poster_category li {
    font-size: 16px;
    color: #1A1A18;
    line-height: 1.2
}

.poster ul li {
    list-style: none
}

.poster_info {
    margin-left: 30px;
}

.poster_bottom {
    background: #fff;
    z-index: 5
}

.poster_bottom {
    width: 536px;
    height: 1px;
    border-top: 1px solid #133D82;
    left: 0;
    top: 0;
    right: 0;
    margin: auto
}

.poster_skills {
    padding-top: 0;
    padding-bottom: 0;
    padding-left: 0;
    padding-right: 0;
    background: #fff url(<?php
/* style_poster2.css:202: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/img/poster_fon.png);
    height: 171px;
    width: 235px;
    background-size: contain;
    background-repeat: no-repeat;
    background-position: right top;
}

.poster_skills li {
    font-size: 16px;
    color: #1A1A18;
    line-height: 1.2;
}

.poster_register {
    margin-top: 10px;
}

.poster_register_t {
    font-size: 20px;
    color: #fff;
    text-transform: uppercase;
    padding-top: 8px;
    padding-bottom: 8px;
    line-height: 1.2;
    text-align: center;
    background: #7DA4BD;
}

.poster_register_img {
    float: left;
    width: 25px;
    padding-top: 14px;
    padding-left: 12px;
    padding-right: 10px;
}

.poster_ol {
    padding-top: 9px;
    padding-left: 47px;
    background: #133D82;
}

.poster_ol .poster_li {
    font-size: 15px;
    color: #A1D9F8;
    list-style: none;
    line-height: 1.2;
    border-left: 1px solid #A1D9F8;
    position: relative;
    padding-left: 10px;
    height: 40px;
    margin-bottom: 10px;
}

.poster_invite {
    padding-left: 0;
}

.poster_invite div {
    font-size: 14px;
    color: #1A1A18;
}

.PAINTING1 {
    background: url(<?php
/* style_poster2.css:267: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/img/invite1.png);
    background-repeat: no-repeat;
    background-position: left center;
    background-size: 10%;
}
.PAINTING2 {
    background: url(<?php
/* style_poster2.css:273: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/img/invite2.png);
    background-repeat: no-repeat;
    background-size: 10%;
    background-position: left center;
}
.PAINTING3 {
    background: url(<?php
/* style_poster2.css:279: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/img/invite3.png);
    background-repeat: no-repeat;
    background-size: 10%;
    background-position: left center;
}

.poster_invite img {
    width: auto;
    height: 50px;
    margin: auto
}

.poster_invite p {
    border-left-width: 1px;
    border-left-style: solid;
    border-left-color: black;
    margin-left: 35px;
    margin-top: 0;
    margin-bottom: 0;
    padding-left: 15px;
    padding-top: 5px;
    padding-bottom: 3px;
}

.row_footer{
    padding-top: 10px;
}

.poster_code {
    color: #9C9D9D;
    font-size: 15px;
    background-color: #ECECED;
    text-align: center;
    width: 150px;
    border-top: 25px solid #fff;
    border-bottom: 8px solid #fff;
    border-left: 5px solid #fff;
    border-right: 5px solid #fff;
}

.poster_text {
    width: 68px;
    text-align: right;
    line-height: 1;
    padding-top: 15px;
    padding-left: 25px;
}

.reff_block {
    overflow: hidden;
}

.poster_web {
    float: left;
    width: 215px;
    padding-top: 25px;
    padding-left: 30px;
}

.poster_web span {
    text-transform: uppercase;
    color: #fff;
    background-color: #133D82;
    font-size: 16px;
    border-top: 5px solid #133D82;
    border-bottom: 5px solid #133D82;
    border-left: 5px solid #133D82;
}

.poster_web .two {
    background-color: #EF7C00;
    border-top: 5px solid #EF7C00;
    border-bottom: 5px solid #EF7C00;
    border-left: 5px solid #EF7C00;
    border-right: 5px solid #EF7C00;
}<?php
}, array(
	'options' => 128,
	'provider' => false,
	'name' => 'style_poster2.css',
	'base_name' => 'style_poster2.css',
	'time' => 1588253060,
	'depends' => array (
  0 => 
  array (
    'style_poster2.css' => 1588253060,
  ),
),
	'macros' => array(),

        ));

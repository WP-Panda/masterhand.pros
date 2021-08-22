<?php 
/** Fenom template 'style_poster3.css' compiled at 2020-07-20 18:33:48 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?> body {
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

 .poster {
     font-family: 'Arial', sans-serif;
     background: #fff;
     margin-left: auto;
     margin-right: auto;
     overflow: hidden;
     width: 595px
 }

 .poster_right {
     padding-bottom: 20px;
     overflow: hidden;
     padding-right: 5px;
     padding-left: 5px;
 }

 .poster_right .poster_category {
     padding-top: 57px;
     padding-left: 20px;
     padding-bottom: 1px;
     width: 169px;
     margin-bottom: 65px;
     border-left: 1px solid #A1D9F8;
     margin-top: -45px;
     margin-left: 10px;
     font-size: 12px;
 }

 .poster_right .poster_category li {
     color: #fff;
 }

 .poster_exp {
     margin-top: -95px;
     padding-top: 10px;
     padding-right: 10px;
     text-align: right;
     width: 225px;
     float: right;
 }

 .poster_exp .poster_txt {
     font-size: 22px;
     color: #fff
 }

 .poster_exp .poster_t {
     margin-right: 0;
 }

 .poster_author {
     width: 350px;
     margin-bottom: -48px;
     float: left;
 }

 .poster_img {
     padding-top: 55px;
     margin-bottom: -20px;
     z-index: 0;
     position: relative;
     background: url(<?php
/* style_poster3.css:78: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/img/author.png) 0 30px no-repeat;
 }

 .img {
     width: 240px;
     height: 240px;
     margin-left: auto;
     margin-right: auto;
     overflow: hidden;
     background-size: cover !important;
     -webkit-border-radius: 50%;
     -moz-border-radius: 50%;
     border-radius: 50%
 }

 .poster_t {
     font-size: 18px;
     font-weight: bold;
     color: #1A1A18;
     margin-top: 10px;
     margin-bottom: 6px;
     margin-right: 8px;
     line-height: 1;
     text-align: right;
     text-decoration: underline;
     text-transform: uppercase;
 }

 .poster_name {
     color: #1A1A18;
     font-size: 21px;
     position: relative;
     z-index: 2;
     text-transform: uppercase;
     line-height: 19px;
     font-weight: 600;
     padding-left: 25px;
     padding-top: 15px;
     padding-bottom: 15px;
     background: url(<?php
/* style_poster3.css:117: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/img/authorname.png) top right no-repeat;
 }

 .poster_txt {
     font-size: 16px;
     color: #1A1A18;
     margin-left: -55px;
 }

 .poster_txt-about {
     font-size: 13px;
     line-height: 1;
     text-align: justify;
     color: #878786
 }

 .poster_txt-more {
     padding-top: 45px;
     padding-right: 14px;
     padding-bottom: 15px;
     text-align: right;
     margin-bottom: -35px;
     clear: both;
     background: url(<?php
/* style_poster3.css:140: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/img/sometext.png) top right no-repeat;
     margin-top: -45px;
 }

 .poster_txt-more-inner {
     width: 272px;
     color: #A1D9F8;
     font-size: 16px;
     line-height: 1.15;
 }

 .poster_hire {
     color: #1A1A18;
     font-size: 14px;
     text-transform: uppercase;
     line-height: 1.2;
     text-align: right;
 }

 .poster_hire span {
     color: #064497
 }

 .poster_main-t {
     font-size: 38px;
     text-transform: uppercase;
     line-height: 1.2;
     font-weight: 600;
     color: #1A1A18;
     text-align: right;
     font-family: dinprocondbold
 }

 .poster_main-t .yellow {
     color: #fff;
     font-weight: 400;
     padding: 0 5px;
     background: #EF7C00;
     font-family: dinprocond
 }


 .poster_category li {
     /*font-size: 17px;*/
     color: #6F6F6E;
     line-height: 1.1;
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
     padding-top: 40px;
     clear: both;
     background: url(<?php
/* style_poster3.css:203: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/img/mainblock.png) top left no-repeat;
 }

 .poster_info .poster_right {
     float: left;
     overflow: visible;
     padding: 0;
     width: 235px;
 }

 .poster_skills {
     clear: both;
     padding: 10px 10px 0 17px;
     background: #d87d1b;
 }

 .poster_skills .poster_t {
     margin: 0;
     padding: 0;
 }

 .poster_skills ul {
     padding: 0px 5px 0;
     overflow: hidden;
     min-height: 30px;
 }

 .poster_skills ul li {
     font-size: 12px;
     width: 25%;
     float: left;
     color: #fff;
     line-height: 1.2;
     padding: 0 0 0 5px;
     border-left: 1px solid #A1D9F8;
     margin: 0 0 15px
 }

 /*.poster_skills ul li:nth-child(3) */

 .poster_register {
     padding: 52px 0 0;
     width: 360px;
     text-align: center;
     float: left;
 }

 .poster_register .poster_register_t {
     font-size: 16px;
     color: #1A1A18;
     padding-bottom: 0;
     padding-left: 0;
     text-transform: uppercase;
     line-height: 1.2;
     text-align: center;
 }

 .poster_register ul {
     padding-bottom: 10px;
     padding-left: 25px;
     text-align: left;
     overflow: hidden;
 }

 .poster_register li {
     font-size: 9px;
     color: #1A1A18;
     float: left;
     width: 100px;
     overflow: hidden;
     list-style: none;
     line-height: 1.2;
     padding-left: 5px;
     border-left: 1px solid #A1D9F8;
 }

 .poster_bottom {
     padding-top: 10px;
 }

 .poster .poster_invite {
     padding-left: 20px;
     padding-right: 40px;
 }

 .poster_invite li {
     font-size: 15px;
     color: #1A1A18;
     padding-top: 5px;
     padding-bottom: 5px;
     line-height: 1.2;
     padding-left: 40px;
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
     padding-top: 10px;
     padding-bottom: 5px;
     line-height: 1;
     text-align: center;
 }

 .bottom_invite {
     width: 327px;
     float: left;
 }

 .poster .no-pad2 {
     float: left;
     width: 144px;
     padding-left: 25px;
     border-left: 1px solid #9c9c9c;
 }

 .poster_scheme {
     overflow: hidden;
     text-align: center;
     padding-top: 20px
 }

 .poster_web {
     margin-top: 10px
 }

 .poster_web div {
     text-transform: uppercase;
     color: #fff;
     background: #133D82;
     padding-top: 5px;
     text-align: center;
     padding-left: 10px;
     padding-bottom: 5px;
     padding-right: 10px;
     font-size: 10px
 }

 .bottom-code {
     overflow: hidden
 }
<?php
}, array(
	'options' => 128,
	'provider' => false,
	'name' => 'style_poster3.css',
	'base_name' => 'style_poster3.css',
	'time' => 1588253060,
	'depends' => array (
  0 => 
  array (
    'style_poster3.css' => 1588253060,
  ),
),
	'macros' => array(),

        ));

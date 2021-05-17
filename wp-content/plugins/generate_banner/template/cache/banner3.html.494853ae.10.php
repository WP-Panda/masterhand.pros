<?php 
/** Fenom template 'banner3.html' compiled at 2020-06-18 06:18:20 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?> <style type="text/css">
     @import url('https://fonts.googleapis.com/css?family=Montserrat:700,600');

     * {
         -webkit-box-sizing: border-box;
         -moz-box-sizing: border-box;
         box-sizing: border-box;
     }

     .bn-3 {
         margin: 0;
         font-family: 'Montserrat',
             sans-serif;
         font-style: normal;
         font-weight: 600;
     }

     .bn-3 .wrap {
         width: 1200px;
         margin: auto;
     }

     .bn-3 .header {
         overflow: hidden;
     }

     .bn-3 .avatar {
         margin: -75px 0 0 -60px;
         width: 420px;
         height: 410px;
         border: 23px solid #2C33C1;
         border-radius: 0% 50% 50% 50%;
         -webkit-border-radius: 0% 50% 50% 50%;
         -moz-border-radius: 0% 50% 50% 50%;
         background-size: cover !important;
         float: left;
     }

     .bn-3 .masterhand {
         float: left;
         padding: 55px 0 0 55px;
         overflow: hidden;
         text-align: left;
     }

     .bn-3 .master img {
         width: 582px;
         height: 40px;
         max-width: 90%;
     }

     .bn-3 .servis {
         font-size: 38px;
         line-height: 46px;
         letter-spacing: 0.02em;
         margin-top: 30px;
     }

     .bn-3 .name {
         font-style: normal;
         font-weight: bold;
         font-size: 50px;
         line-height: 61px;
         letter-spacing: 0.02em;
     }

     .bn-3 .body {
         background: #2C33C1;
     }

     .bn-3 .fon-text {
         padding: 20px 150px;
         margin: 26px 0 0;
         text-align: center;
         color: #fff;
         font-size: 28px;
         letter-spacing: 0.02em;
     }


     .bn-3 .cont {
         text-align: center;
         font-size: 32px;
         line-height: 150%;
         letter-spacing: 0.02em;
         color: #2C33C1;

     }

     .bn-3 .cont p {
         margin: 10px 0;
     }

     .bn-3 .foot {
         text-align: center;
         width: 458px;
         margin: 15px auto;
         background: #2C33C1;
         border-radius: 56px;
         -webkit-border-radius: 56px;
         -moz-border-radius: 56px;
     }

     .bn-3 .foot p {
         margin: 0;
         padding: 30px 0;
         font-size: 30px;
         line-height: 37px;
         letter-spacing: 0.05em;
         color: #FFFFFF;
     }

 </style>


 <div class="bn-3">
     <div class="wrap banner3">
         <div class="header">
             <div class="avatar" style="background:#C8C8C8 url(<?php
/* banner3.html:119: {$avatar_url} */
 echo $var["avatar_url"]; ?>) center no-repeat;">
             </div>
             <div class="masterhand">
                 <div class="master"><img src="<?php
/* banner3.html:122: {$path_inc} */
 echo $var["path_inc"]; ?>/img/Logo-black.svg"></div>
                 <p class="servis">Low-Cost Service Deals</p>
                 <p class="name"><?php
/* banner3.html:124: {$display_name} */
 echo $var["display_name"]; ?></p>
             </div>
         </div>
         <div class="body">
             <p class="fon-text">
                 <?php  if(!empty($var["category"]) && (is_array($var["category"]) || $var["category"] instanceof \Traversable)) {
  foreach($var["category"] as $var["item"]) { ?>
                 <?php
/* banner3.html:130: {$item} */
 echo $var["item"]; ?>;
                 <?php
/* banner3.html:131: {/foreach} */
   } } ?>
             </p>
         </div>
         <div class="footer">
             <div class="cont">
                 <p> Join us with reference code:</p>
             </div>
             <div class="foot">
                 <p><?php
/* banner3.html:139: {$refer_code} */
 echo $var["refer_code"]; ?></p>
             </div>
         </div>
     </div>
 </div>
<?php
}, array(
	'options' => 128,
	'provider' => false,
	'name' => 'banner3.html',
	'base_name' => 'banner3.html',
	'time' => 1588253052,
	'depends' => array (
  0 => 
  array (
    'banner3.html' => 1588253052,
  ),
),
	'macros' => array(),

        ));

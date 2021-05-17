<?php 
/** Fenom template 'banner2.html' compiled at 2020-06-18 06:18:20 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?> <style type="text/css">
     @import url('https://fonts.googleapis.com/css?family=Montserrat:700,600');

     .bn-2 {
         margin: 0;
         text-align: center;
         font-family: 'Montserrat',
             sans-serif;
         font-style: normal;
         font-weight: 600;
     }

     .bn-2 .wrapper {
         width: 1200px;
         background: #2C33C1;
         margin: auto;
         padding: 22px 0;
     }

     .bn-2 .body {
         width: 1100px;
         background: #F4F4F4;
         margin: 0 auto;
     }

     .bn-2 .logo {
         margin: 45px 0 0;
         width: 582px;
         height: 40px;
     }

     .bn-2 .rectangle {
         width: 100%;
         min-height: 150px;
         background: #FFFFFF;
         position: absolute;
         top: 0;
         left: -25px;
         width: 1150px;
         z-index: -1;
         height: 80%;
     }

     .bn-2 .rectangle:before {
         content: '';
         width: 0;
         height: 0;
         border-top: 25px solid #D0D0D0;
         border-right: 25px solid transparent;
         position: absolute;
         right: 0;
         top: 100%;
     }

     .bn-2 .rectangle:after {
         content: '';
         width: 0;
         height: 0;
         border-top: 25px solid #D0D0D0;
         border-left: 25px solid transparent;
         position: absolute;
         left: 0;
         top: 100%;
     }

     .bn-2 .low {
         margin: 20px 0 0;
         font-size: 30px;
         line-height: 30px;
         letter-spacing: 0.02em;
     }

     .bn-2 .face {
         width: 187px;
         height: 187px;
         border: 15px solid #fff;
         background-size: cover !important;
         -webkit-border-radius: 50%;
         -moz-border-radius: 50%;
         border-radius: 50%;
         overflow: hidden;
         float: left;
         margin-top: -70px;
         margin-left: 100px;
     }

     .bn-2 .group {
         position: relative;
         margin: 20px 0 0;
         z-index: 1;
         padding: 45px 0;
     }

     .bn-2 .group-text {
         font-weight: bold;
         font-size: 50px;
         line-height: 61px;
         letter-spacing: 0.02em;
         overflow: hidden;
         margin: 0;
         text-align: left;
         padding: 0 20px 0 40px;
     }

     .bn-2 .text {
         padding: 0 150px;
         margin: 66px 0 0;
         font-size: 28px;
         text-align: center;
         letter-spacing: 0.02em;
         color: rgba(0, 0, 0, 0.7);
     }

     .bn-2 .foot-text {
         margin: 30px 0 0;
         font-size: 30px;
         padding: 0 15px;
         letter-spacing: 0.02em;
         color: #2C33C1;
     }

     .bn-2 .rectangle-foot:before {
         content: '';
         width: 0;
         height: 0;
         border-top: 25px solid #D0D0D0;
         border-right: 25px solid transparent;
         position: absolute;
         bottom: 0;
         right: -25px;
     }

     .bn-2 .rectangle-foot:after {
         content: '';
         width: 0;
         height: 0;
         border-top: 25px solid #D0D0D0;
         border-left: 25px solid transparent;
         position: absolute;
         bottom: 0;
         left: -25px;
     }

     .bn-2 .rectangle-foot {
         width: 600px;
         background: #FFFFFF;
         position: relative;
         margin: 0 auto;
         border-radius: 50px 50px 0px 0px;
         bottom: -25px;
         padding: 25px 0;
     }

     .bn-2 .number {
         font-size: 35px;
         line-height: 1;
         letter-spacing: 0.05em;
         color: #2C33C1;
         margin: 0;
         text-align: center;
     }

 </style>


 <div class="bn-2">
     <div class="wrapper banner2">
         <div class="body">
             <img class="logo" src="<?php
/* banner2.html:169: {$path_inc} */
 echo $var["path_inc"]; ?>/img/Logo-black.svg">
             <p class="low">Low-Cost Service Deals</p>
             <div class="group">
                 <div class="rectangle"></div>
                 <div class="face" style="background:#fff url(<?php
/* banner2.html:173: {$avatar_url} */
 echo $var["avatar_url"]; ?>) 0 0 no-repeat;"></div>
                 <p class="group-text"><?php
/* banner2.html:174: {$display_name} */
 echo $var["display_name"]; ?></p>
             </div>
             <p class="text">
                 <?php  if(!empty($var["category"]) && (is_array($var["category"]) || $var["category"] instanceof \Traversable)) {
  foreach($var["category"] as $var["item"]) { ?>
                 <?php
/* banner2.html:178: {$item} */
 echo $var["item"]; ?>;
                 <?php
/* banner2.html:179: {/foreach} */
   } } ?>
             </p>
             <p class="foot-text">Join us with reference code:</p>
             <div class="foot">
                 <div class="rectangle-foot">
                     <p class="number"><?php
/* banner2.html:184: {$refer_code} */
 echo $var["refer_code"]; ?></p>
                 </div>
             </div>
         </div>
     </div>
 </div>
<?php
}, array(
	'options' => 128,
	'provider' => false,
	'name' => 'banner2.html',
	'base_name' => 'banner2.html',
	'time' => 1588253052,
	'depends' => array (
  0 => 
  array (
    'banner2.html' => 1588253052,
  ),
),
	'macros' => array(),

        ));

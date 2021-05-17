<?php 
/** Fenom template 'poster3.html' compiled at 2020-07-20 18:33:48 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?><!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1 ,user-scalable=no">
    <link rel="stylesheet" type="text/css" href="<?php
/* poster3.html:7: {$path_inc} */
 echo $var["path_inc"]; ?>/style_poster3.css">
</head>

<body>
<div class="fre-page-wrapper">
    <div class="poster">
        <div class="poster_author">
            <div class="poster_img">
                <div class="img" style="background:url(<?php
/* poster3.html:15: {$avatar_url} */
 echo $var["avatar_url"]; ?>) center no-repeat"></div>
            </div>
            <div class="poster_name"><?php
/* poster3.html:17: {$display_name} */
 echo $var["display_name"]; ?></div>
        </div>

        <div class="poster_right">
            <div class="poster_main-t"><span>MASTER</span>HAND <span class="yellow">PRO</span></div>
            <div class="poster_hire"> HIRE REAL LOCAL <span>MASTER HAND</span></div>
            <div class="poster_t">About me</div>
            <div class="poster_txt-about"><?php
/* poster3.html:24: {$about} */
 echo $var["about"]; ?></div>
        </div>


        <div class="poster_txt-more">
            <span class="poster_txt-more-inner">Save up to 70% on hiring perfect<br> local professional on the website,<br> it is Super Fast and Easy.</span>
        </div>

        <div class="poster_info">
            <div class="poster_register">
                <div class="poster_register_t">HOW TO<br> REGISTER<br> ON THE SITE</div>
                <img src="<?php
/* poster3.html:35: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/img/numbers3.png">
                <ul>
                    <li>Go to<br/> www.masterhand.pro</li>
                    <li>Sign up with reference<br/> code <?php
/* poster3.html:38: {$refer_code} */
 echo $var["refer_code"]; ?></li>
                    <li>Post your task and<br/> receive bids</li>
                </ul>
            </div>

            <div class="poster_right">
                <div class="poster_t">SPECIALIZATION</div>
                <div class="poster_txt">
                    <ul class="poster_category">
                        <?php  if(!empty($var["category"]) && (is_array($var["category"]) || $var["category"] instanceof \Traversable)) {
  foreach($var["category"] as $var["item"]) { ?>
                        <li><?php
/* poster3.html:48: {$item} */
 echo $var["item"]; ?></li>
                        <?php
/* poster3.html:49: {/foreach} */
   } } ?>
                    </ul>
                </div>
                <div class="poster_exp">
                    <div class="poster_t">WORK<br> EXPERIENCE</div>
                    <div class="poster_txt"><?php
/* poster3.html:54: {$experience} */
 echo $var["experience"]; ?> years</div>
                </div>
            </div>

            <div class="poster_skills">
                <div class="poster_t">Skills</div>
                <ul>
                    <?php  if(!empty($var["skills"]) && (is_array($var["skills"]) || $var["skills"] instanceof \Traversable)) {
  foreach($var["skills"] as $var["item"]) { ?>
                    <li><?php
/* poster3.html:62: {$item} */
 echo $var["item"]; ?></li>
                    <?php
/* poster3.html:63: {/foreach} */
   } } ?>
                </ul>
            </div>

            <div class="poster_bottom">
                <div class="bottom_invite">
                    <ul class="poster_invite">
                        <li style="background:url(<?php
/* poster3.html:70: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/img/invite3-1.png) center left no-repeat;">Post
                            your task
                        </li>
                        <li style="background:url(<?php
/* poster3.html:73: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/img/invite3-2.png) center left no-repeat;">Invite
                            me
                        </li>
                        <li style="background:url(<?php
/* poster3.html:76: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/img/invite3-3.png) center left no-repeat;">Get
                            qualified proposal with bid<br/> price within minutes
                        </li>
                    </ul>
                </div>
                <div class="bottom-code">
                    <div class="no-pad2">
                        <div class="poster_web">
                            <div>masterhand.pro</div>
                        </div>
                        <div class="poster_code-t">Reference code:</div>
                        <div class="poster_code"><?php
/* poster3.html:87: {$refer_code} */
 echo $var["refer_code"]; ?></div>
                    </div>
                    <div class="poster_scheme">
                        <img src="<?php
/* poster3.html:90: {$qr_code} */
 echo $var["qr_code"]; ?>" alt=""/>
                    </div>
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

<?php 
/** Fenom template 'poster2.html' compiled at 2020-07-20 22:13:55 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?><!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1 ,user-scalable=no">
</head>

<body>
<div class="fre-page-wrapper">
    <div class="poster2 poster">
        <div class="row">
            <div class="poster_author">
                <img src="<?php
/* poster2.html:14: {$avatar_url} */
 echo $var["avatar_url"]; ?>" alt="" height="270px" width="270px"/>
            </div>
            <div class="poster_about">
                <div class="poster_name"><?php
/* poster2.html:17: {$display_name} */
 echo $var["display_name"]; ?></div>
                <div class="poster_t">About me</div>
                <div class="poster_txt-about"><?php
/* poster2.html:19: {$about} */
 echo $var["about"]; ?></div>
            </div>
        </div>
        <div class="row">
            <div class="poster_main-t"><span>MASTER</span>HAND <span class="yellow">PRO</span></div>
            <div class="poster_hire"> HIRE REAL LOCAL<br> MASTER HAND</div>
        </div>
        <div class="poster_info">
            <div class="poster_left">
                <div class="poster_title">SPECIALIZATION</div>
                <ul class="poster_category">
                    <?php  if(!empty($var["category"]) && (is_array($var["category"]) || $var["category"] instanceof \Traversable)) {
  foreach($var["category"] as $var["item"]) { ?>
                    <li><?php
/* poster2.html:31: {$item} */
 echo $var["item"]; ?></li>
                    <?php
/* poster2.html:32: {/foreach} */
   } } ?>
                </ul>
                <div class="poster_title">WORK EXPERIENCE</div>
                <div class="poster_txt"><?php
/* poster2.html:35: {$experience} */
 echo $var["experience"]; ?> years</div>
                <div class="poster_title">Skills</div>
                <ul class="poster_skills">
                    <?php  if(!empty($var["skills"]) && (is_array($var["skills"]) || $var["skills"] instanceof \Traversable)) {
  foreach($var["skills"] as $var["item"]) { ?>
                    <li><?php
/* poster2.html:39: {$item} */
 echo $var["item"]; ?></li>
                    <?php
/* poster2.html:40: {/foreach} */
   } } ?>
                </ul>
            </div>
            <div class="poster_right">
                <div class="poster_txt-more">
                    Save up to 70% on hiring
                    perfect local professional
                    on the website, it is Super
                    Fast and Easy.
                </div>
                <div class="poster_invite">
                    <div class="PAINTING1"><p>Post your task</p></div>
                    <div class="PAINTING2"><p>Invite me</p></div>
                    <div class="PAINTING3"><p>Get qualified proposal with bid price within minutes</p></div>
                </div>
                <div class="poster_register">
                    <div class="poster_register_t">HOW TO REGISTER<br> ON THE SITE</div>
                    <div class="poster_register_img"><img src="<?php
/* poster2.html:57: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/img/123.png" alt=""/></div>
                    <div class="poster_ol">
                        <div class="poster_li">
                            Go to<br> www.masterhand.pro
                        </div>
                        <div class="poster_li">
                            Sign up with reference<br> code <?php
/* poster2.html:63: {$refer_code} */
 echo $var["refer_code"]; ?>
                        </div>
                        <div class="poster_li">
                            Post your task and<br> receive bids
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row_footer">
            <div class="poster_web"><span>www.</span><span class="two">masterhand.pro</span></div>
            <div class="reff_block">
                <table>
                    <tr>
                        <td class="poster_text">Reference code:</td>
                        <td class="poster_code"><?php
/* poster2.html:78: {$refer_code} */
 echo $var["refer_code"]; ?></td>
                        <td class="poster_img"><img src="<?php
/* poster2.html:79: {$qr_code} */
 echo $var["qr_code"]; ?>" alt=""/></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html><?php
}, array(
	'options' => 128,
	'provider' => false,
	'name' => 'poster2.html',
	'base_name' => 'poster2.html',
	'time' => 1588253060,
	'depends' => array (
  0 => 
  array (
    'poster2.html' => 1588253060,
  ),
),
	'macros' => array(),

        ));

<?php 
/** Fenom template 'poster1.html' compiled at 2020-07-20 18:32:28 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?><!DOCTYPE html>
<html lang="en-US">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1 ,user-scalable=no">
</head>
<body>
<div class="poster">
    <div class="poster-before"></div>
    <div class="row">
        <div class="poster_top1">
            <div class="poster_author">
                <div class="poster_img" style="background:url(<?php
/* poster1.html:12: {$avatar_url} */
 echo $var["avatar_url"]; ?>) center no-repeat"></div>
                <div class="poster_name"><?php
/* poster1.html:13: {$display_name} */
 echo $var["display_name"]; ?></div>
            </div>
            <div class="poster_register">
                <div class="poster_register_t">HOW TO REGISTER<br> ON THE SITE</div>
                <div class="poster_register_img"><img src="<?php
/* poster1.html:17: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/img/1-2-3.png" alt=""/></div>
                <div class="poster_ol">
                    <div class="poster_li">
                        Go to<br> www.masterhand.pro
                    </div>
                    <div class="poster_li">
                        Sign up with reference<br> code <?php
/* poster1.html:23: {$refer_code} */
 echo $var["refer_code"]; ?>
                    </div>
                    <div class="poster_li">
                        Post your task and<br> receive bids
                    </div>
                </div>
            </div>
        </div>
        <div class="poster_top2">
            <div class="poster_right">
                <div class="poster_t">About me</div>
                <div class="poster_txt-about"><?php
/* poster1.html:34: {$about} */
 echo $var["about"]; ?></div>
                <div class="poster_t">SPECIALIZATION</div>
                <div class="poster_txt">
                    <ul class="poster_category">
                        <?php  if(!empty($var["category"]) && (is_array($var["category"]) || $var["category"] instanceof \Traversable)) {
  foreach($var["category"] as $var["item"]) { ?>
                        <li><?php
/* poster1.html:39: {$item} */
 echo $var["item"]; ?></li>
                        <?php
/* poster1.html:40: {/foreach} */
   } } ?>
                    </ul>
                </div>
                <div class="poster_t">WORK EXPERIENCE</div>
                <div class="poster_txt"><?php
/* poster1.html:44: {$experience} */
 echo $var["experience"]; ?> years</div>
                <div class="poster_t">Skills</div>
            </div>
            <div class="poster_skills">
                <div class="poster_skills-before"></div>
                <ul>
                    <?php  if(!empty($var["skills"]) && (is_array($var["skills"]) || $var["skills"] instanceof \Traversable)) {
  foreach($var["skills"] as $var["item"]) { ?>
                    <li><?php
/* poster1.html:51: {$item} */
 echo $var["item"]; ?></li>
                    <?php
/* poster1.html:52: {/foreach} */
   } } ?>
                </ul>
                <div class="last-item"></div>
                <div class="poster_txt-more">
                    Save up to 70% on hiring
                    perfect local professional
                    on the website, it is Super
                    Fast and Easy.
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-xs-12">
            <div class="row">
                <div class="col-sm-7 col-xs-7">
                    <div class="poster_main-t">
                        <span>MASTER</span>HAND <span class="yellow">PRO</span>
                        <div class="poster_main-t-after"></div>
                    </div>
                </div>
                <div class="col-sm-5 col-xs-5">
                    <div class="poster_hire">
                        <b>HIRE REAL LOCAL<br> MASTER HAND</b>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-5 col-xs-5">
                    <div class="poster_invite">
                        <div class="line">
                            <span>
                                <img src="<?php
/* poster1.html:84: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/img/invite2.png" height="25px" alt=""/>
                                <span>Post your task</span>
                            </span>
                            <span>
                                <img src="<?php
/* poster1.html:88: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/img/invite1.png" height="25px" alt=""/>
                                <span>Invite me</span>
                            </span>
                        </div>
                        <div class="line">
                            <img src="<?php
/* poster1.html:93: {$path_inc} */
 echo $var["path_inc"]; ?>/assets/img/invite3.png" height="25px" alt=""/>
                            Get qualified proposal with bid price within minutes
                        </div>
                    </div>
                </div>
                <div class="col-sm-7 col-xs-7 reff_block">
                    <table>
                        <tr>
                            <td class="poster_text">Reference code:</td>
                            <td class="poster_code"><?php
/* poster1.html:102: {$refer_code} */
 echo $var["refer_code"]; ?></td>
                            <td><img src="<?php
/* poster1.html:103: {$qr_code} */
 echo $var["qr_code"]; ?>" alt=""/></td>
                        </tr>
                    </table>
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
	'name' => 'poster1.html',
	'base_name' => 'poster1.html',
	'time' => 1588253060,
	'depends' => array (
  0 => 
  array (
    'poster1.html' => 1588253060,
  ),
),
	'macros' => array(),

        ));

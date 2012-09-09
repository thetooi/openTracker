<?php

include("../../../../init.php");
session_start();

$str = "";
$length = 0;
for ($i = 0; $i < 6; $i++) {
    $str .= chr(rand(97, 122));
}

//md5 letters and saving them to session
$letters = md5($str);
$_SESSION['letters'] = $letters;

$imgW = 150;
$imgH = 60;
$image = imagecreatetruecolor($imgW, $imgH);

$image = imagecreatefrompng(PATH_APPLICATIONS . "user/captcha/captcha.png");

imagealphablending($image, true);
imagesavealpha($image, true);

$text_col = imagecolorallocate($image, rand(70, 90), rand(50, 70), rand(120, 140));


$font = PATH_APPLICATIONS . "user/captcha/times_new_yorker.ttf";

$font_size = $imgH / 2.2;
$angle = rand(-15, 15);
$box = imagettfbbox($font_size, $angle, $font, $str);
$x = (int) ($imgW - $box[4]) / 2;
$y = (int) ($imgH - $box[5]) / 2;
imagettftext($image, $font_size, $angle, $x, $y, $text_col, $font, $str);

header("Content-type: image/png");
imagepng($image);
imagedestroy($image);
?>
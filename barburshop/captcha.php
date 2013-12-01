<?php


session_start();
$string = strtoupper(substr(str_shuffle('acdhkw234569'), 0, 5));
$_SESSION['key'] = $string;
$image = imagecreatefromjpeg("images/box6.jpg");
for($i=1; $i<=rand(5, 8); $i++) {  // lines
	$lines = imagecolorallocate($image, rand(180, 200),rand(180, 210),rand(160, 200));
	imageline($image,rand(1, 90),rand(1, 35),rand(10, 150),rand(1, 40),$lines);
}
for ($i = 0; $i <= rand(250, 350); $i++) {  // points
$point_color = imagecolorallocate ($image, rand(0,255), rand(0,255), rand(0,255));
imagesetpixel($image, rand(1,128), rand(1,38), $point_color);
}
$angle = rand(-3, 3);
$x = rand(4, 44);
$y = rand(20, 30);
$color = imagecolorallocate($image, 100, 100, 100);
$font = 'images/REFSAN.TTF';
imagettftext($image, 16, $angle, $x, $y, $color, $font, $string);
header("Content-type: image/jpeg");
header('Cache-control: no-cache');
imagejpeg($image);
?> 
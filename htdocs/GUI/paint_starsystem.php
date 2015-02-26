<?php
header ( 'Content-Type: image/png' );
session_start ();
header ( "Cache-Control: no-cache, must-revalidate" );
header ( "Expires: Sat, 26 Jul 1997 05:00:00 GMT" );

$im = @imagecreatetruecolor ( 500, 500 ) or die ( 'Cannot Initialize new GD image stream' );

$white = Imagecolorallocate ( $im, 255, 255, 255 );
$grey = Imagecolorallocate ( $im, 150, 150, 150 );
$red = Imagecolorallocate ( $im, 255, 0, 0 );
$green = Imagecolorallocate ( $im, 0, 255, 0 );
$blue = Imagecolorallocate ( $im, 0, 0, 255 );

include ("dbconnect.php");

$starsystem_display = 1;
if (isset ( $_GET ['disp'] )) {
	$starsystem_display = htmlspecialchars ( $_GET ['disp'] );
}

$planets = mysqli_query ( 'SELECT id,pos_x,pos_y,vel_x,vel_y,type_id FROM game_planets WHERE (type_id = 1 OR type_id = 2) AND starsystem_id = ' . mysqli_real_escape_string ( $starsystem_display ) . ' ORDER BY type_id' );
while ( $planet = mysqli_fetch_object ( $planets ) ) {
	$mass = 0;
	if ($planet->type_id == 1) {
		$mass = 8;
	}
	if ($planet->type_id == 2) {
		$mass = 2;
	}
	
	$owner = mysqli_query ( 'SELECT player_id FROM game_planets WHERE id = ' . $planet->id );
	$owner = mysqli_fetch_array ( $owner );
	if ($owner ['player_id'] == $_SESSION ['gameuserid']) {
		ImageEllipse ( $im, 250 + $planet->pos_x / 7000e6 * 500, 250 - $planet->pos_y / 7000e6 * 500, 8 + $mass, 8 + $mass, $red );
	}
	
	ImageFilledEllipse ( $im, 250 + $planet->pos_x / 7000e6 * 500, 250 - $planet->pos_y / 7000e6 * 500, 2 + $mass, 2 + $mass, $white );
}

imagefilter ( $im, IMG_FILTER_GAUSSIAN_BLUR );

for($bg = 0; $bg < 100; $bg ++) {
	ImageFilledEllipse ( $im, mt_rand ( 0, 500 ), mt_rand ( 0, 500 ), 1, 1, $grey );
}

imagepng ( $im );
imagedestroy ( $im );
?>
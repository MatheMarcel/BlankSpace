<?php
include ("dbconnect.php");

$input = "";
$starsystem_id = 0;
$input = mysqli_query ( 'INSERT INTO game_starsystems (mass) VALUES (0)' );
$starsystem_id = mysqli_insert_id ();

$starsystem_mass = 0;

// Masse in kg
$star_mass = mt_rand ( 1, 100 ) * 1e29;
$starsystem_mass += $star_mass;
$input = mysqli_query ( 'INSERT INTO game_planets (starsystem_id, mass, type_id) VALUES (' . $starsystem_id . ',' . $star_mass . ',1)' );

$planets_count = mt_rand ( 1, 5 ) + mt_rand ( 2, 5 );
// $planets_count = 1; #zum testen nur einen planet erzeugen
for($i = 1; $i <= $planets_count; $i ++) {
	// Position in km (30 mio bis 3 mrd)
	$planet_pos_x = pow ( (- 1), mt_rand ( 0, 1 ) ) * mt_rand ( 30, 3000 ) * 1e6;
	$planet_pos_y = pow ( (- 1), mt_rand ( 0, 1 ) ) * mt_rand ( 30, 3000 ) * 1e6;
	// Abstand in km
	$planet_abs = sqrt ( $planet_pos_x * 1000 * $planet_pos_x * 1000 + $planet_pos_y * 1000 * $planet_pos_y * 1000 ) / 1000;
	
	// Masse in kg
	$planet_mass = (3000 * exp ( - 1 / 2 * (($planet_abs / 1e6 - 2500) / 1000) * (($planet_abs / 1e6 - 2500) / 1000) ) - 100) * 1e22;
	$starsystem_mass += $planet_mass;
	
	// Geschwindigkeit in km/h
	$planet_vel = sqrt ( 6.67384e-11 * $star_mass / ($planet_abs * 1000) ) / 1000 * 60 * 60; // *mt_rand(90,105)/100;
	$planet_vel_x = - $planet_pos_y / $planet_abs * $planet_vel;
	$planet_vel_y = $planet_pos_x / $planet_abs * $planet_vel;
	
	$input = mysqli_query ( 'INSERT INTO game_planets (starsystem_id, pos_x, pos_y, vel_x, vel_y, mass, type_id) VALUES (' . $starsystem_id . ', ' . $planet_pos_x . ',' . $planet_pos_y . ', ' . $planet_vel_x . ',' . $planet_vel_y . ',' . $planet_mass . ',2)' );
}

$input = mysqli_query ( 'UPDATE game_starsystems SET mass = ' . $starsystem_mass . ' WHERE id=' . $starsystem_id );
?>
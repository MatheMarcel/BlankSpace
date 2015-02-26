<?php
include_once ("dbconnect.php");

$del = mysqli_query ( 'DELETE FROM game_planets WHERE starsystem_id = 1' );

$starsystem_mass = 0;

// Masse in kg

// füge Sonne ein
$star_mass = 1.98892e30;
$starsystem_mass += $star_mass;
$input = mysqli_query ( 'INSERT INTO game_planets (starsystem_id, mass, type_id) VALUES (1,' . $star_mass . ',1)' );

// füge Erde ein
$starsystem_mass += 5.974e24;
$input = mysqli_query ( 'INSERT INTO game_planets (starsystem_id, pos_x, pos_y, vel_x, vel_y, mass, type_id) VALUES (1, 149600000, 0, 0,107233.2, 5.974e24, 2)' );

// füge Erdmond ein
$starsystem_mass += 7.349e22;
$input = mysqli_query ( 'INSERT INTO game_planets (starsystem_id, pos_x, pos_y, vel_x, vel_y, mass, type_id) VALUES (1, 149984400, 0, 0,110916, 7.349e22, 3)' );

$input = mysqli_query ( 'UPDATE game_starsystems SET mass = ' . $starsystem_mass . ' WHERE id=1' );
?>
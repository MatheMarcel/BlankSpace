<?php
include ("dbconnect.php");

$del1 = mysqli_query ( 'TRUNCATE TABLE game_starsystems' );
$del2 = mysqli_query ( 'TRUNCATE TABLE game_planets' );
?>
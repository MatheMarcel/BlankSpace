<?php
include_once ("dbconnect.php");
include_once ("includes/classes.inc");

// Für später: testen, dass diese Datei von cron im Commandlineinterface ausgeführt wird und nicht per Browser
if (PHP_SAPI === 'cli') {
}

$nextrunde = "";
$nextrunde = FALSE;

$lastround = "";
$lastround = mysqli_query ( $dblink, 'SELECT wert FROM game_master WHERE variable="LastRound"' );
$lastround = mysqli_fetch_array ( $lastround );
$nextround = "";
$nextround = mysqli_query ( $dblink, 'SELECT wert FROM game_master WHERE variable="NextRound"' );
$nextround = mysqli_fetch_array ( $nextround );
if (time () > $lastround ['wert'] + ($nextround ['wert'] * 60)) {
	$nextrunde = TRUE;
}

$abf1 = mysqli_query ( $dblink, 'SELECT count(*) as anza FROM game_players' );
$abf1 = mysqli_fetch_array ( $abf1 );
$abf2 = mysqli_query ( $dblink, 'SELECT sum(rundefertig) as summ FROM game_players' );
$abf2 = mysqli_fetch_array ( $abf2 );
if ($abf1 ['anza'] <= $abf2 ['summ'] + 1) {
	$nextrunde = TRUE;
}

if ($nextrunde) {
	$runde = "";
	$runde = mysqli_query ( $dblink, 'SELECT wert FROM game_master WHERE variable="Runde"' );
	$runde = mysqli_fetch_array ( $runde );
	
	$runde ['wert'] ++;
	$err = mysqli_query ( $dblink, 'UPDATE game_master SET wert="' . $runde ['wert'] . '" WHERE variable="Runde"' );
	$err = mysqli_query ( $dblink, 'UPDATE game_master SET wert="' . time () . '" WHERE variable="LastRound"' );
	$err = mysqli_query ( $dblink, 'INSERT INTO game_round (nummer,beginn) VALUES (' . $runde ['wert'] . ',' . time () . ')' );
	
	$abf = mysqli_query ( $dblink, 'UPDATE game_players SET rundefertig = "0"' );
	
	// move starsystem by 1 week
	// for ($ind = 0; $ind <= 24*7; $ind++){
	// include("move_starsystem.php");
	// }
	$syslist = getlist ( 'starsystems' );
	foreach ( $syslist as $row ) {
		$row->move ( 24 * 7 );
	}
	unset ( $syslist );
	
	include ("cron/calc_population.php");
	include ("cron/calc_researchers.php");
	include ("cron/calc_craftsmen.php");
	include ("cron/do_planet_mission.php");
}
?>
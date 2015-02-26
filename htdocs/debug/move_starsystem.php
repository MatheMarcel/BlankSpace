<?php

// WIRD NICHT MEHR BENUTZT !!!
// WIRD NICHT MEHR BENUTZT !!! siehe cronjob.php
// WIRD NICHT MEHR BENUTZT !!!
$abfr = mysqli_query ( 'SELECT id FROM game_starsystems' );
while ( $sys = mysqli_fetch_array ( $abfr ) ) {
	$starsystem_id = $sys ['id'];
	$planet_mysqli = mysqli_query ( 'SELECT id, pos_x, pos_y, vel_x, vel_y, mass FROM game_planets WHERE starsystem_id = ' . $starsystem_id );
	
	$planets_array = array ();
	$num_planets = 0;
	
	while ( $row = mysqli_fetch_array ( $planet_mysqli ) ) {
		$planets_array [$num_planets] = $row;
		$num_planets ++;
	}
	
	$planets_forces = array ();
	for($i = 0; $i < $num_planets; $i ++) {
		$planets_forces ['x'] [$i] = 0;
		$planets_forces ['y'] [$i] = 0;
	}
	for($i = 0; $i < $num_planets; $i ++) {
		for($j = $i + 1; $j < $num_planets; $j ++) {
			// Position in km (30 mio bis 3 mrd)
			// Masse in kg
			// Geschwindigkeit in km/h
			
			// Quadrat des Abstandes in m
			$sqdist = pow ( ($planets_array [$i] ['pos_x'] - $planets_array [$j] ['pos_x']) * 1000, 2 ) + pow ( ($planets_array [$i] ['pos_y'] - $planets_array [$j] ['pos_y']) * 1000, 2 );
			// Grav-kraft in N
			$force = 6.67384e-11 * $planets_array [$i] ['mass'] * $planets_array [$j] ['mass'] / $sqdist;
			
			if ($sqdist < 1000000) {
				echo "Crash!";
			}
			
			$force_diff_x = ($planets_array [$i] ['pos_x'] - $planets_array [$j] ['pos_x']) * 1000 / sqrt ( $sqdist ) * $force;
			$force_diff_y = ($planets_array [$i] ['pos_y'] - $planets_array [$j] ['pos_y']) * 1000 / sqrt ( $sqdist ) * $force;
			
			// if ($starsystem_id == 1){
			// echo 'Planet '.$i.' mit planet '.$j.' hat Kraft = '.$force_diff_x.','.$force_diff_y.'.<br/>';
			// }
			
			$planets_forces ['x'] [$i] -= $force_diff_x;
			$planets_forces ['y'] [$i] -= $force_diff_y;
			$planets_forces ['x'] [$j] += $force_diff_x;
			$planets_forces ['y'] [$j] += $force_diff_y;
		}
		
		// Geschwindigkeit in km/h
		$gfaktor = 3600;
		$planets_array [$i] ['vel_x'] += $gfaktor * $planets_forces ['x'] [$i] / $planets_array [$i] ['mass'] * 60 * 60 / 1000;
		$planets_array [$i] ['vel_y'] += $gfaktor * $planets_forces ['y'] [$i] / $planets_array [$i] ['mass'] * 60 * 60 / 1000;
		
		/*
		 * $sun_x = $planets_array[0]['pos_x'];
		 * $sun_y = $planets_array[0]['pos_y'];
		 * $vek_x = $sun_x - $planets_array[$i]['pos_x']; $vek_y = $sun_y - $planets_array[$i]['pos_y'];
		 * $skalar = ($vek_x * $planets_array[$i]['vel_x'] + $vek_y * $planets_array[$i]['vel_y']) / sqrt($vek_x*$vek_x + $vek_y*$vek_y) / sqrt($planets_array[$i]['vel_x']*$planets_array[$i]['vel_x'] + $planets_array[$i]['vel_y']*$planets_array[$i]['vel_y']);
		 *
		 * if ($skalar > 1e-1){
		 * $planets_array[$i]['vel_x'] *= 1.000001;
		 * $planets_array[$i]['vel_y'] *= 1.000001;
		 * }elseif($skalar < -1e-1){
		 * $planets_array[$i]['vel_x'] *= 0.999999;
		 * $planets_array[$i]['vel_y'] *= 0.999999;
		 * }
		 */
		
		$vfaktor = 1;
		$planets_array [$i] ['pos_x'] += $vfaktor * $planets_array [$i] ['vel_x'];
		$planets_array [$i] ['pos_y'] += $vfaktor * $planets_array [$i] ['vel_y'];
		
		$planet_mysqli = mysqli_query ( 'UPDATE game_planets SET pos_x = ' . $planets_array [$i] ['pos_x'] . ', pos_y = ' . $planets_array [$i] ['pos_y'] . ', vel_x = ' . $planets_array [$i] ['vel_x'] . ', vel_y = ' . $planets_array [$i] ['vel_y'] . ' WHERE id=' . $planets_array [$i] ['id'] );
	}
}
?>
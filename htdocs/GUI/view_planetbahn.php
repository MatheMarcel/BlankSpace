<?php
if ($_SESSION ['gameuserid'] != 0) {
	$id = htmlspecialchars ( $_GET ['id'] );
	$waszusehen = FALSE;
	$planet = new planet ( $id );
	if ($planet->player_id == $_SESSION ['gameuserid']) {
		$waszusehen = TRUE;
	}
	
	$waszusehen = TRUE;
	if ($waszusehen) {
		$system = new starsystem ( $planet->starsystem_id );
		$hn = $system->hamilton ();
		echo 'Hamilton: ' . $hn . '.<br/>';
		
		echo 'Bahnberechnung:<br/>';
		echo 'Planet: x=' . $planet->pos_x . ', y=' . $planet->pos_y . '.<br/>';
		$sun = $system->sun ();
		echo 'Sonne: x=' . $sun->pos_x . ', y=' . $sun->pos_y . '.<br/>';
		$r = sqrt ( pow ( $sun->pos_x - $planet->pos_x, 2 ) + pow ( $sun->pos_y - $planet->pos_y, 2 ) );
		echo 'Abstand Sonne-Planet: r=' . $r . 'km<br/>';
		$gamma = 6.67e-11 / 1000 / 1000 / 1000;
		$v1 = (sqrt ( $gamma * $sun->mass / $r ));
		echo 'Geschwindigkeit für Kreisbahn: v_1=' . $v1 . 'km/s<br/>';
		$v2 = sqrt ( 2 ) * $v1;
		echo 'Max. Geschwindigkeit für Ellipse: v_2=' . $v2 . 'km/s<br/><br/>';
		
		echo 'Richtung Tangente: x=' . $planet->vel_x . ', y=' . $planet->vel_y . '.<br/>';
		$wa = rad2deg ( atan2 ( $planet->vel_y, $planet->vel_x ) );
		if ($wa < 0) {
			$wa += 360;
		}
		echo 'Winkel der Tangente zu x-Achse: wa=' . $wa . '<br/>';
		
		$r_x = ($planet->pos_x - $sun->pos_x);
		$r_y = ($planet->pos_y - $sun->pos_y);
		echo 'Richtung Sonne-Planet: x=' . $r_x . ', y=' . $r_y . '.<br/>';
		$wb = rad2deg ( atan2 ( $r_y, $r_x ) );
		if ($wb < 0) {
			$wb += 360;
		}
		echo 'Winkel Sonne-Planet zu x-Achse: wb=' . $wb . '<br/><br/>';
		
		$u = $wa - 90 - $wb;
		$wc = ($wa + 90) + $u;
		if ($wc >= 360) {
			$wc -= 360;
		}
		echo 'Winkel Planet-2.Brennpunkt: wc=' . $wc . '<br/><br/>';
		
		$v = sqrt ( pow ( $planet->vel_x, 2 ) + pow ( $planet->vel_y, 2 ) ) / 60 / 60;
		echo 'Geschwindigkeit Planet: v=' . $v . 'km/s';
		if ($v < $v1) {
			echo ' <font color="#FF0000">keine Ellipse: fällt in Sonne!</font>';
		}
		if ($v > $v2) {
			echo ' <font color="#FF0000">keine Ellipse: fliegt davon!</font>';
		}
		echo '<br/>';
		$a = 1 / ((2 / ($r)) - (($v * $v) / ($gamma * $sun->mass)));
		echo 'Große Halbachse: a=' . $a . '<br/><br/>';
		
		$bz_x = cos ( deg2rad ( $wc ) ) * (2 * $a - $r) + $planet->pos_x;
		$bz_y = sin ( deg2rad ( $wc ) ) * (2 * $a - $r) + $planet->pos_y;
		echo '2. Brennpunkt: x=' . $bz_x . ', y=' . $bz_y . '<br/>';
		$wd = rad2deg ( atan2 ( ($bz_y - $sun->pos_y), ($bz_x - $sun->pos_x) ) );
		echo 'Winkel Sonne-2.Brennpunkt: ' . $wd . '<br/><br/>';
		
		$m_x = ($sun->pos_x + $bz_x) / 2;
		$m_y = ($sun->pos_y + $bz_y) / 2;
		;
		echo 'Mittelpunkt Ellipse: x=' . $m_x . ', y=' . $m_y . '<br/><br/>';
		
		$e = sqrt ( pow ( $m_x - $sun->pos_x, 2 ) + pow ( $m_y - $sun->pos_y, 2 ) );
		echo 'lin. Exzentrizität: e=' . $e . '<br/>';
		echo 'num. Exzentrizität: eps=' . ($e / $a) . ' (eps = 0 <-> Kreis, eps > 1 <-> Parabel)<br/>';
		$b = sqrt ( pow ( $a, 2 ) - pow ( $e, 2 ) );
		echo 'kleine Halbachse: b=' . $b . '<br/><br/>';
		
		echo 'Minimaler Sonnenabst: ' . ($a - $e) . '<br/>';
		echo 'Maximaler Sonnenabst: ' . ($a + $e) . '<br/><br/>';
		
		$l = 3 * pow ( ($a - $b) / ($a + $b), 2 );
		$u = pi () * ($a + $b) * (1 + ($l / (10 + sqrt ( 4 - $l ))));
		echo 'Bahnlänge: U=' . $u . 'km<br/>';
		
		$t = sqrt ( (pow ( $a, 3 ) * 4 * pi () * pi ()) / ($gamma * ($sun->mass + $planet->mass)) );
		echo 'Bahnperiode: T=' . $t . 's = ' . ($t / 60 / 60 / 24 / 365.4) . 'y = ' . ($t / 60 / 60 / 24 / 7) . 'Runden <br/><br/>';
		
		echo 'Grav-kraft zu anderen Planeten:<br/>';
		$liste = $system->planets ();
		foreach ( $liste as $row ) {
			if ($row->id != $planet->id) {
				echo 'Zu Planet ' . $row->id . ' ist F = ' . $row->get_forces ( $planet->id ) . '<br/>';
			}
		}
		echo '<br/>';
		unset ( $liste );
		
		$hill = $r * pow ( $planet->mass / 3 / $sun->mass, 1 / 3 );
		echo 'Halber Radius Hill-Sphäre: ' . $planet->hill_radius () . ' (darin Mond möglich)<br/><br/>';
		
		$dichte = 5500 * 1000000000;
		echo 'Masse des Planeten (kg): ' . $planet->mass . '<br/>';
		echo 'Dichte des Planeten (kg/km^3): ' . $dichte . '<br/>';
		
		$volumen = $planet->mass / $dichte;
		echo 'Volumen des Planeten (in km^3): ' . $volumen . '<br/><br/>';
		
		$radius = pow ( 3 * $volumen / 4 / pi (), 1 / 3 );
		echo 'Radius in km: ' . $radius . '<br/>';
		echo 'Oberfläche des Planeten (in km^2): ' . (4 * pi () * $radius * $radius) . '<br/><br/>';
		
		$roche = $radius * pow ( 2 * $sun->mass / $planet->mass, 1 / 3 );
		echo 'Radius Roche-Grenze (zur Sonne): ' . $planet->roche_radius () . '. (Planet näher wird von Sonne zerrissen)<br/>';
		
		echo 'Radius Roche-Grenze (für Monde mit gleicher Dichte): ' . ($planet->get_radius () * 2.423) . '.<br/>';
	} else {
		echo "Nix zu sehen";
	}
}
?>
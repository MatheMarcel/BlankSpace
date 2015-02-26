<?php
if ($player->id != 0) {
	echo '<table><tr><td>';
	// Anzeige des Starsystem-Picture
	if (isset ( $_GET ['disp'] )) {
		$starsystem = new starsystem ( htmlspecialchars ( $_GET ['disp'] ) );
	} else {
		$planet = new planet ( $player->homeplanet );
		$starsystem = new starsystem ( $planet->starsystem_id );
	}
	
	if ($starsystem->id == 1) {
		// 65546 = tag, 524288 = woche
		$starsystem->move_rk ( 524288 );
		unset ( $starsystem );
		$starsystem = new starsystem ( 1 );
	}
	
	echo 'Display <a href="./index.php?v=starsys&amp;disp=' . ($starsystem->id - 1) . '">&lt;</a> Starsystem <a href="./index.php?v=starsys&amp;disp=' . ($starsystem->id + 1) . '">&gt;</a><br/>';
	// echo '<form action="./index.php">';
	// echo '<input type="hidden" name="v" value="starsys"/>';
	// echo '<input type="hidden" name="disp" value="'.$starsystem->id.'"/>';
	// echo '<input type="image" name="c" src="./paint_starsystem.php?disp='.$starsystem->id.'"/>';
	// echo '</form>';
	include ("paint_starsystem2.php");
	echo '</td>';
	
	echo '<td>';
	// Tabelle von Planeten und Monden
	echo '<table><tr><th>Objekt</th><th>Typ</th><th>Besitzer</th><th>Bevölkerung<br/> (genutzt/total)</th></tr>';
	// $starsystem->move(24*7);
	$list = $starsystem->planets ();
	foreach ( $list as $row ) {
		// if($row->type_id==2){$row->create_moon();}
		echo '<tr><td align="left">' . $row->id . ' (' . round ( 250 + $row->pos_x / 7000e6 * 500 ) . "," . round ( 250 - $row->pos_y / 7000e6 * 500 ) . ')</td><td>' . $row->type_id . '</td><td>';
		if ($row->player_id == $player->id) {
			echo $player->name . '</td><td>' . $row->usedpop () . '/' . $row->population . '</td>';
			echo '</tr>';
		} else {
			if ($row->type_id == 1) {
				echo '-';
			} else {
				echo '?';
			}
			echo '</td></tr>';
		}
		foreach ( $row->list_moons () as $row2 ) {
			echo '<tr><td align="left"> + ' . $row2->id . ' (' . round ( 250 + $row2->pos_x / 7000e6 * 500 ) . "," . round ( 250 - $row2->pos_y / 7000e6 * 500 ) . ')</td><td>' . $row2->type_id . '</td><td>';
			if ($row2->player_id == $player->id) {
				echo $player->name . '</td><td>' . $row2->usedpop () . '/' . $row2->population . '</td>';
				echo '</tr>';
			} else {
				if ($row2->type_id == 1) {
					echo '-';
				} else {
					echo '?';
				}
				echo '</td></tr>';
			}
		}
	}
	unset ( $starsystem );
	echo '</table>';
	echo '</td></tr></table>';
}
?>

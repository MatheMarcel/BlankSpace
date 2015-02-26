<?php
if ($_SESSION ['gameuserid'] != 0) {
	
	echo 'Liste besetzter Planeten:';
	echo '<table border=1><tr><th>Planet</th><th>Bevölkerung<br/>(genutzt/total)</th><th>Landwirte</th><th>Ingenieure</th><th>Forscher</th><th></th></tr>';
	$player = new player ( $_SESSION ['gameuserid'] );
	$array = $player->planets ();
	
	foreach ( $array as $planet ) {
		
		if (isset ( $_GET ['pop'] )) {
			$popplanet = htmlspecialchars ( $_GET ['planet'] );
			if ($planet->id == $popplanet) {
				$popchange = htmlspecialchars ( $_GET ['pop'] );
				
				switch (substr ( $popchange, 0, 1 )) {
					case 'f' :
						if (substr ( $popchange, 1, 1 ) == "r") {
							$planet->change_farmers ( - 1 );
						} elseif (substr ( $popchange, 1, 1 ) == "a") {
							$planet->change_farmers ( + 1 );
						}
						break;
					case 'c' :
						if (substr ( $popchange, 1, 1 ) == "r") {
							$planet->change_craftsmen ( - 1 );
						} elseif (substr ( $popchange, 1, 1 ) == "a") {
							$planet->change_craftsmen ( + 1 );
						}
						break;
					case 'r' :
						if (substr ( $popchange, 1, 1 ) == "r") {
							$planet->change_researchers ( - 1 );
						} elseif (substr ( $popchange, 1, 1 ) == "a") {
							$planet->change_researchers ( + 1 );
						}
						break;
				}
			}
		}
		
		echo '<tr>';
		echo '<td>' . $planet->id;
		if ($planet->id == $player->homeplanet) {
			echo ' Heimat!';
		}
		echo '</td>';
		echo '<td><progress value="' . $planet->usedpop () . '" max="' . $planet->population . '"></progress>' . $planet->usedpop () . '/' . $planet->population . '</td>';
		echo '<td>';
		if ($planet->farmers > 0) {
			echo '<a href="./index.php?v=planets&amp;planet=' . $planet->id . '&amp;pop=fr">-</a>';
		}
		echo $planet->farmers;
		if (floor ( $planet->population ) - $planet->farmers - $planet->craftsmen - $planet->researchers > 0) {
			echo '<a href="./index.php?v=planets&amp;planet=' . $planet->id . '&amp;pop=fa">+</a>';
		}
		echo '</td>';
		echo '<td>';
		if ($planet->craftsmen > 0) {
			echo '<a href="./index.php?v=planets&amp;planet=' . $planet->id . '&amp;pop=cr">-</a>';
		}
		echo $planet->craftsmen;
		if (floor ( $planet->population ) - $planet->farmers - $planet->craftsmen - $planet->researchers > 0) {
			echo '<a href="./index.php?v=planets&amp;planet=' . $planet->id . '&amp;pop=ca">+</a>';
		}
		echo '</td>';
		echo '<td>';
		if ($planet->researchers > 0) {
			echo '<a href="./index.php?v=planets&amp;planet=' . $planet->id . '&amp;pop=rr">-</a>';
		}
		echo $planet->researchers;
		if (floor ( $planet->population ) - $planet->farmers - $planet->craftsmen - $planet->researchers > 0) {
			echo '<a href="./index.php?v=planets&amp;planet=' . $planet->id . '&amp;pop=ra">+</a>';
		}
		echo '</td>';
		echo '<td><a href="./index.php?v=planet&amp;id=' . $planet->id . '">Details</a> / <a href="./index.php?v=planetbahn&amp;id=' . $planet->id . '">Bahn</a></td>';
		echo '</tr>';
	}
	unset ( $array );
	echo '</table><br/>';
}
?>
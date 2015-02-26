<?php
if ($player->id != 0) {
	$master = new master ();
	
	echo date ( "d.m.Y" ) . ' - ' . date ( "H:i:s" ) . ' - ';
	$lang = htmlspecialchars ( $_SERVER ['HTTP_ACCEPT_LANGUAGE'] );
	$lang = explode ( ',', $lang );
	echo 'Sprache: ';
	if ($lang [0] == 'de') {
		echo 'deutsch';
	} elseif ($lang [0] == 'en') {
		echo 'english';
	} else {
		echo 'unknown';
	}
	echo ' - Username: ' . $player->name . ' - <a href="./index.php?restart=1">Spezies aufgeben und neu beginnen</a><br/>';
	
	echo 'Runde Nummer: ' . $master->round . '. ';
	echo 'Letzte Runde: ' . date ( "d.m.Y H:i:s", $master->lastround ) . '. Nächste Runde: ' . date ( "d.m.Y H:i:s", $master->lastround + ($master->nextround * 60) + 60 );
	
	echo '<table class="header" width=100%><tr>';
	echo '<td><a href="./index.php">Übersicht</a></td>';
	echo '<td><a href="./index.php?v=starsys">Sternensystem</a></td>';
	echo '<td><a href="./index.php?v=planets">Meine Planeten</a></td>';
	echo '<td><a href="./index.php?v=unittypes">Meine Einheitentypen</a></td>';
	echo '<td><a href="./index.php?v=technol">Meine Technologien</a></td>';
//	echo '<td><a href="./wiki/">Wiki</a></td>';
	echo '<td><a href="./index.php?v=admin">Admin</a></td>';
	echo '</tr></table>';
	
	echo '<hr/>';
}
?>
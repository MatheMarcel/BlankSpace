<?php
if ($player->id != 0) {
	if (isset ( $_GET ['unit_del'] )) {
		$uid = htmlspecialchars ( $_GET ['unit_del'] );
		$unit = new unit_type ( $uid );
		if ($unit->player_id == $player->id && $unit->count_units () == 0) {
			echo 'Unit ' . $unit->name . ' gelöscht.<br/><br/>';
			unset ( $unit );
			$player->delete_unit_type ( $uid );
		}
		unset ( $unit );
	}
	
	echo 'Baupläne für Einheiten:';
	echo '<table border=1><tr><th>Name</th><th>besteht aus</th><th>Gesamtkosten</th><th>Menge</th></tr>';
	$list = $player->list_unit_types ();
	foreach ( $list as $row ) {
		echo '<tr><td>' . $row->name . '</td>';
		$list2 = $row->modules ();
		$array = array ();
		foreach ( $list2 as $row2 ) {
			array_push ( $array, $row2->name );
		}
		echo '<td>' . implode ( ', ', $array ) . '</td><td>' . $row->cost () . '</td>';
		$a = $row->count_units ();
		echo '<td>' . ($a);
		if ($a == 0) {
			echo ' <a href="./index.php?v=unittypes&amp;unit_del=' . $row->id . '">löschen</a>';
		}
		echo '</td></tr>';
		unset ( $list2 );
	}
	unset ( $list );
	echo '</table>';
	
	echo '<hr/>Neuen Bauplan zusammenstellen:';
	
	if (isset ( $_GET ['module_add'] )) {
		$addid = htmlspecialchars ( $_GET ['module_add'] );
		if (! isset ( $_SESSION ['unittypes'] [$addid] )) {
			$_SESSION ['unittypes'] [$addid] = 0;
		}
		$_SESSION ['unittypes'] [$addid] ++;
	}
	if (isset ( $_GET ['module_rem'] )) {
		$addid = htmlspecialchars ( $_GET ['module_rem'] );
		if (! isset ( $_SESSION ['unittypes'] [$addid] )) {
			$_SESSION ['unittypes'] [$addid] = 0;
		}
		$_SESSION ['unittypes'] [$addid] --;
		if ($_SESSION ['unittypes'] [$addid] <= 0) {
			unset ( $_SESSION ['unittypes'] [$addid] );
		}
	}
	
	echo '<form action="./index.php?v=newunittype" method="post">';
	echo '<div style="';
	$anz = 0;
	if ($anz > 5) {
		echo 'height:160px;width:500px;';
	}
	echo 'overflow-y:auto">';
	echo '<table border=1><tr><th>Modul</th><th>Kosten</th><th>Anzahl</th></tr>';
	$list = $player->module_possible ();
	foreach ( $list as $row ) {
		if (! isset ( $_SESSION ['unittypes'] [$row->id] )) {
			$_SESSION ['unittypes'] [$row->id] = 0;
		}
		echo '<tr><td>' . $row->name . '</td><td>' . $row->cost . '</td><td><a href="./index.php?v=unittypes&amp;module_rem=' . $row->id . '"><img src="./pics/icons/List-remove.svg" height="20"/></a> ' . $_SESSION ['unittypes'] [$row->id] . ' <a href="./index.php?v=unittypes&amp;module_add=' . $row->id . '"><img src="./pics/icons/List-add.svg" height="20"/></a></td></tr>';
	}
	echo '</table>';
	
	echo '</div>';
	echo 'Einheitenname: <input name="name" type="text" size="30" maxlength="30"/> <input type="submit" value=" Speichern "/>';
	echo '</form>';
}
?>

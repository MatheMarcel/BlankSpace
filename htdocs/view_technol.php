<?php
if ($player->id != 0) {
	
	if (isset ( $_GET ['techchange'] )) {
		$techfield = htmlspecialchars ( $_GET ['techfield'] );
		$techchange = htmlspecialchars ( $_GET ['techchange'] );
		$player->change_research_distribution ( $techfield, $techchange );
	}
	
	echo 'Festlegen der Forschungsschwerpunkte:';
	echo '<table border=1><tr><th>Richtung</th><th></th><th></th><th>Wert</th><th></th><th></th></tr>';
	$tech = $player->get_research_data ();
	$techtotal = 0;
	foreach ( $tech as $row ) {
		echo '<tr>';
		echo '<td>' . $row ['name'] . '</td><td><a href="./index.php?v=technol&amp;techchange=-10&amp;techfield=' . $row ['field_id'] . '">-10</a></td><td><a href="./index.php?v=technol&amp;techchange=-1&amp;techfield=' . $row ['field_id'] . '">-1</a></td><td>' . $row ['distribution'] . '</td><td><a href="./index.php?v=technol&amp;techchange=1&amp;techfield=' . $row ['field_id'] . '">+1</a></td><td><a href="./index.php?v=technol&amp;techchange=10&amp;techfield=' . $row ['field_id'] . '">+10</a></td>';
		echo '<td>' . $row ['points'] . '</td>'; // zum debuggen
		echo '</tr>';
		$techtotal += $row ['distribution'];
	}
	echo '<tr></tr><tr><td>Verbleibend</td><td></td><td></td><td>' . (100 - $techtotal) . '</td><td></td><td></td></tr>';
	echo '</table><br/>';
	
	echo 'Bekannte Technologien:';
	echo '<table border=1><tr><th>Mathe</th><th>Physik</th><th>Bio/Chemie</th></tr>';
	$techlist = $player->list_tech ();
	for($i = 0; $i < max ( count ( $techlist [0] ), count ( $techlist [1] ), count ( $techlist [2] ) ); $i ++) {
		echo '<tr>';
		for($j = 0; $j <= 2; $j ++) {
			echo '<td>';
			if (count ( $techlist [$j] ) > $i) {
				echo $techlist [$j] [$i]->name;
			}
			echo '</td>';
		}
		echo '</tr>';
	}
	echo '</table>';
}
?>
<?php
$list = getlist ( 'players' );
foreach ( $list as $row ) {
	$techpoints = $row->calc_research_points ();
	$research_data = $row->get_research_data ();
	$fields = array ();
	foreach ( $research_data as $row2 ) {
		$field = array ();
		array_push ( $field, $row2 ['field_id'] );
		$feldzahl = $row2 ['distribution'] * $techpoints;
		if ($feldzahl >= 100) {
			$punkte = floor ( $feldzahl / 100 );
			$row->add_research ( $row2 ['field_id'], $punkte );
			$techpoints = $techpoints - $punkte;
			$feldzahl -= $punkte * 100;
		}
		array_push ( $field, $feldzahl );
		array_push ( $fields, $field );
	}
	unset ( $research_data );
	
	// entweder while oder for!
	while ( $techpoints > 0 ) { // hier kein techpunkt zu viel, keiner zu wenig
	                      // for($i=0;$i<$techpoints;$i++){ # hier vlt zu wenig, vlt zu viel
		foreach ( $fields as &$row3 ) {
			if ($techpoints > 0) {
				$a = mt_rand ( 1, 100 );
				if ($a <= $row3 [1]) {
					$row->add_research ( $row3 [0], 1 );
					$techpoints --;
				}
			}
			$row3 [1] ++;
		}
	}
}
unset ( $list );

// durchlaufe alle techfelder aller spieler
global $dblink;
$abfr = mysqli_query ( $dblink, 'SELECT player_id,field_id,points FROM game_players_tech_field' );
while ( $row = mysqli_fetch_array ( $abfr ) ) {
	// durchlaufe alle techs des feldes
	$abfr2 = mysqli_query ( $dblink, 'SELECT id,cost,name FROM game_tech WHERE field_id=' . $row ['field_id'] );
	while ( $alletech = mysqli_fetch_array ( $abfr2 ) ) {
		// teste ob spieler tech bereits hat
		$player_has = mysqli_query ( $dblink, 'SELECT tech_id FROM game_players_tech WHERE tech_id=' . $alletech ['id'] . ' AND player_id=' . $row ['player_id'] );
		
		if (mysqli_num_rows ( $player_has ) == 0) {
			// wenn nicht, schaue ob er voraussetzungen hat
			$abfr3 = mysqli_query ( $dblink, 'SELECT depends_on_id FROM game_tech_dep WHERE tech_id=' . $alletech ['id'] );
			$total = 0;
			$has = 0;
			while ( $deptech = mysqli_fetch_array ( $abfr3 ) ) {
				$total += 1;
				$abfr4 = mysqli_query ( $dblink, 'SELECT tech_id FROM game_players_tech WHERE player_id=' . $row ['player_id'] . ' AND tech_id=' . $deptech ['depends_on_id'] );
				$abfr4 = mysqli_fetch_array ( $abfr4 );
				if ($abfr4 ['tech_id'] == $deptech ['depends_on_id']) {
					$has += 1;
					// echo 'habs';
				}
			}
			// spieler kann es bekommen
			if ($has == $total) {
				$percent = $row ['points'] / $alletech ['cost'] * 100;
				if ($percent <= 50) {
					$chance = 1 / 200 * $percent;
				} else {
					$chance = (min ( $percent, 100 ) - 50) * 3 / 200 + 1 / 4;
				}
				if (mt_rand ( 0, 100 ) <= $chance * 100) {
					// neue tech gefunden
					$set = mysqli_query ( $dblink, 'INSERT INTO game_players_tech (player_id, tech_id) VALUES (' . $row ['player_id'] . ', ' . $alletech ['id'] . ')' );
					$del = mysqli_query ( $dblink, 'UPDATE game_players_tech_field SET points = 0 WHERE player_id=' . $row ['player_id'] . ' AND field_id=' . $row ['field_id'] );
					$player = new player ( $row ['player_id'] );
					$player->add_log ( 4, $alletech ['id'] );
					unset ( $player );
				}
			}
		}
	}
}
?>
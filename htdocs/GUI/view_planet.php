<?php
if ($_SESSION ['gameuserid'] != 0) {
	$id = htmlspecialchars ( $_GET ['id'] );
	$isOwnerOfPlanet = FALSE;
	$planet = new planet ( $id );
	if ($planet->player_id == $_SESSION ['gameuserid']) {
		$isOwnerOfPlanet = TRUE;
	}
	// only owner of planet can see details
	// TODO: anyone with satellite or intel antennas can see details
	if ($isOwnerOfPlanet) {
		// player wants to build a building
		if (isset ( $_GET {'buildid'} )) {
			$buildid = htmlspecialchars ( $_GET {'buildid'} );
			// TODO: test if building can be constructed
			$planet->add_construction ( 'building', $buildid );
		}
		// player wants to build a module
		if (isset ( $_GET {'moduleid'} )) {
			$moduleid = htmlspecialchars ( $_GET {'moduleid'} );
			// TODO: test if module can be constructed
			$planet->add_construction ( 'module', $moduleid );
		}
		// player wants to build an unit
		if (isset ( $_GET {'build_unitid'} )) {
			$unitid = htmlspecialchars ( $_GET {'build_unitid'} );
			// test if unit can be constructed
			
			$planet->add_construction ( 'unit', $unitid );
			$planet->add_planet_mission ( 1, $unitid, 1 );
		}
		
		// player wants to change priority
		if (isset ( $_GET ['prior'] )) {
			$prior = htmlspecialchars ( $_GET ['prior'] );
			if (isset ( $_GET ['buildingid'] )) {
				$buildingid = htmlspecialchars ( $_GET ['buildingid'] );
				$const = new construction ( $buildingid );
				$go = TRUE;
			} elseif (isset ( $_GET ['missionid'] )) {
				$missionid = htmlspecialchars ( $_GET ['missionid'] );
				$const = new planet_mission ( $missionid );
				$go = TRUE;
			}
			
			if ($go) {
				switch ($prior) {
					case "top" :
						$const->go_top ();
						break;
					case "up" :
						$const->go_up ();
						break;
					case "down" :
						$const->go_down ();
						break;
					case "bottom" :
						$const->go_bottom ();
						break;
					case "del" :
						$const->delete ();
						break;
					case "pause" :
						$const->pause ();
						break;
				}
				unset ( $const );
			}
		}
		
		echo 'Zeige Details von Planet ' . $planet->name . '<br/>';
		
		echo 'Bauliste:<br/>';
		$list = $planet->constructions ();
		if (count ( $list ) > 0) {
			echo '<table border=1><tr><th>Gebäude</th><th colspan="3">Priorität</th><th>%</th><th colspan=2>Status</th></tr>';
			foreach ( $list as $row ) {
				echo '<tr><td>' . $row->name . '</td><td>';
				if ($row->prev_id != 0) {
					echo '<a href="./index.php?v=planet&amp;id=' . $id . '&amp;prior=top&amp;buildingid=' . $row->id . '"><img src="./pics/icons/Go-top.svg" height="20"/></a>';
					echo '<a href="./index.php?v=planet&amp;id=' . $id . '&amp;prior=up&amp;buildingid=' . $row->id . '"><img src="./pics/icons/Go-up.svg" height="20"/></a>';
				}
				echo '</td><td>';
				if ($row->next_id != 0) {
					echo '<a href="./index.php?v=planet&amp;id=' . $id . '&amp;prior=down&amp;buildingid=' . $row->id . '"><img src="./pics/icons/Go-down.svg" height="20"/></a>';
					echo '<a href="./index.php?v=planet&amp;id=' . $id . '&amp;prior=bottom&amp;buildingid=' . $row->id . '"><img src="./pics/icons/Go-bottom.svg" height="20"/></a>';
				}
				echo '</td><td>';
				echo '<a href="./index.php?v=planet&amp;id=' . $id . '&amp;prior=del&amp;buildingid=' . $row->id . '"><img src="./pics/icons/Process-stop.svg" height="20"/></a>';
				echo '</td><td><progress value="' . $row->points . '" max="' . $row->cost . '"></progress>' . $row->points . '/' . $row->cost . '</td><td>';
				if ($row->status_id == 1) {
					echo 'im Bau</td><td><a href="./index.php?v=planet&amp;id=' . $id . '&amp;prior=pause&amp;buildingid=' . $row->id . '"><img src="./pics/icons/Pause.svg" height="20"/></a>';
				} elseif ($row->status_id == 2) {
					echo 'Bau pausiert</td><td><a href="./index.php?v=planet&amp;id=' . $id . '&amp;prior=pause&amp;buildingid=' . $row->id . '"><img src="./pics/icons/Start.svg" height="20"/></a>';
				} elseif ($row->status_id == 3) {
					echo 'läuft</td><td><a href="./index.php?v=planet&amp;id=' . $id . '&amp;prior=pause&amp;buildingid=' . $row->id . '"><img src="./pics/icons/Pause.svg" height="20"/></a>';
				} elseif ($row->status_id == 4) {
					echo 'pausiert</td><td><a href="./index.php?v=planet&amp;id=' . $id . '&amp;prior=pause&amp;buildingid=' . $row->id . '"><img src="./pics/icons/Start.svg" height="20"/></a>';
				}
				echo '</td></tr>';
			}
			unset ( $list );
			echo '</table><br/>';
		} else {
			echo 'Keine Gebäude in Bau.';
		}
		
		echo 'Bau möglich von:';
		$list = $planet->building_possible ();
		echo '<table border=1><tr><th>Gebäude</th><th>Kosten</th><th></th></tr>';
		foreach ( $list as $row ) {
			echo '<tr><td>' . $row->name . '</td><td align="center">' . $row->cost . '</td><td><a href="./index.php?v=planet&amp;id=' . $id . '&amp;buildid=' . $row->id . '">Bauen</a></td></tr>';
		}
		unset ( $list );
		echo '<tr><th>Module</th><th>Kosten</th><th></th></tr>';
		$list = $planet->module_possible ();
		foreach ( $list as $row ) {
			echo '<tr><td>' . $row->name . '</td><td align="center">' . $row->cost . '</td><td><a href="./index.php?v=planet&amp;id=' . $id . '&amp;moduleid=' . $row->id . '">Bauen</a></td></tr>';
		}
		unset ( $list );
		echo '<tr><th>Baupläne</th><th>Kosten</th><th></th></tr>';
		$list = $planet->unit_possible ();
		foreach ( $list as $row ) {
			if (count ( $row->list_module_ids () ) > 1) {
				echo '<tr><td>' . $row->name . '</td><td align="center">' . $row->cost () . '</td><td><a href="./index.php?v=planet&amp;id=' . $id . '&amp;build_unitid=' . $row->id . '">Bauen</a></td></tr>';
			}
		}
		unset ( $list );
		echo '</table><br/>';
		
		echo 'Vorhandene Gebäude:';
		echo '<table border=1><tr><th>Gebäude</th><th>HP</th><th>Status</th><th>Kapazität</th><th>Fracht</td></tr>';
		$anzahl = 0;
		$list = $planet->buildings ();
		foreach ( $list as $row ) {
			$fracht = array ();
			foreach ( $row->cargo () as $row2 ) {
				array_push ( $fracht, $row2->unit_type->name );
			}
			$fracht = implode ( ', ', $fracht );
			echo '<tr><td>' . $row->building_type->name . '</td><td>' . $row->hp . '</td><td>' . $row->status_id . '</td><td align="center">' . $row->building_type->capacity . '</td><td>' . $fracht . '</td></tr>';
			$anzahl ++;
		}
		unset ( $list );
		if ($anzahl < 1) {
			echo '<tr><td colspan=1>-</td></tr>';
		}
		echo '</table><br/>';
		
		echo 'Vorhandene Einheiten:';
		echo '<table border=1><tr><th>Einheit</th><th>HP</th><th>-</th></tr>';
		$anzahl = 0;
		$list = $planet->units ();
		foreach ( $list as $row ) {
			echo '<tr><td>' . $row->unit_type->name . '</td><td>' . $row->hp . '</td><td>-</td></td></tr>';
			$anzahl ++;
		}
		unset ( $list );
		if ($anzahl < 1) {
			echo '<tr><td colspan=1>-</td></tr>';
		}
		echo '</table><br/>';
		
		if (isset ( $_POST ['mission_id'] )) {
			$mission_id = htmlspecialchars ( $_POST ['mission_id'] );
			$object_id = htmlspecialchars ( $_POST ['object_id'] );
			$times = 1;
			$type = new unit_type ( $object_id );
			if ($type->player_id == $player->id) {
				$planet->add_planet_mission ( $mission_id, $object_id, $times );
			}
		}
		
		echo 'Missionsliste:<br/>';
		$mlist = $planet->list_planet_mission ();
		if (count ( $mlist ) > 0) {
			echo '<table border=1><tr><th>Mission</th><th>Objekt</th><th colspan="3">Priorität</th><th>Runden</th></tr>';
			foreach ( $mlist as $row ) {
				$nam = new unit_type ( $row->object_id );
				echo '<tr><td>' . $row->name . '</td><td>' . $nam->name . '</td><td>';
				unset ( $nam );
				if ($row->prev_id != 0) {
					echo '<a href="./index.php?v=planet&id=' . $id . '&prior=top&missionid=' . $row->id . '"><img src="./pics/icons/Go-top.svg" height="24"/></a>';
					echo '<a href="./index.php?v=planet&id=' . $id . '&prior=up&missionid=' . $row->id . '"><img src="./pics/icons/Go-up.svg" height="24"/></a>';
				}
				echo '</td><td>';
				if ($row->next_id != 0) {
					echo '<a href="./index.php?v=planet&amp;id=' . $id . '&amp;prior=down&amp;missionid=' . $row->id . '"><img src="./pics/icons/Go-down.svg" height="24"/></a>';
					echo '<a href="./index.php?v=planet&amp;id=' . $id . '&amp;prior=bottom&amp;missionid=' . $row->id . '"><img src="./pics/icons/Go-bottom.svg" height="24"/></a>';
				}
				echo '</td><td>';
				echo '<a href="./index.php?v=planet&amp;id=' . $id . '&amp;prior=del&amp;missionid=' . $row->id . '"><img src="./pics/icons/Process-stop.svg" height="24"/></a>';
				echo '</td><td>' . $row->times . '</td></tr>';
			}
			unset ( $mlist );
			echo '</table><br/>';
		} else {
			echo 'Keine Missionen vorhanden.<br/><br/>';
		}
		
		echo 'Neue Mission einfügen:<br/>';
		echo '<form action="./index.php?v=planet&id=' . $planet->id . '" method="post">';
		echo 'Mission: ';
		echo '<select name="mission_id" size="1">';
		echo '<option value="1">Zusammensetzen nach Bauplan</option>';
		echo '<option value="2">Startrampe beladen</option>';
		echo '</select>';
		echo '<select name="object_id" size="1">';
		$list = $player->list_unit_types ();
		foreach ( $list as $row ) {
			if (count ( $row->list_module_ids () ) > 1) {
				echo '<option value="' . $row->id . '">' . $row->name . '</option>';
			}
		}
		unset ( $list );
		echo '</select><input type="submit" value=" Einfügen "/>';
		echo '</form>';
	} else {
		echo "Nix zu sehen";
	}
}
?>
<?php
function array_intersect_fixed($array1, $array2) {
	$result = array ();
	foreach ( $array1 as $val ) {
		if (($key = array_search ( $val, $array2, TRUE )) !== false) {
			$result [] = $val;
			unset ( $array2 [$key] );
		}
	}
	return $result;
}
function find_delete($needed, $types, $units) {
	$result = array ();
	foreach ( $needed as $row ) {
		$key = array_search ( $row, $types, TRUE );
		array_push ( $result, $units [$key]->id );
		unset ( $types [$key] );
		unset ( $units [$key] );
	}
	return $result;
}

$listplayer = getlist ( 'players' );
foreach ( $listplayer as $tmp_player ) {
	$listplanet = $tmp_player->planets ();
	foreach ( $listplanet as $tmp_planet ) {
		$listmission = $tmp_planet->list_planet_mission ();
		foreach ( $listmission as $tmp_mission ) {
			if ($tmp_mission->mission_id == 1) {
				// welcher typ soll gebaut werden?
				$zieltype = new unit_type ( $tmp_mission->object_id );
				// welche module braucht er?
				$needed_modules = $zieltype->list_module_ids ();
				// welche unit_type entsprechen den modulen?
				$needed_unittypes = array ();
				foreach ( $needed_modules as $row ) {
					array_push ( $needed_unittypes, $tmp_player->get_unit_type ( array (
							$row 
					) ) );
				}
				unset ( $needed_modules );
				
				// welche units sind vorhanden?
				$avail_units = $tmp_planet->units ();
				
				// welche unittypen sind vorhanden?
				$avail_unittypes = array ();
				foreach ( $avail_units as $row ) {
					array_push ( $avail_unittypes, $row->unit_type->id );
				}
				
				// teste, ob die nötigen typen da sind
				$intersect = array_intersect_fixed ( $needed_unittypes, $avail_unittypes );
				
				if (count ( $intersect ) == count ( $needed_unittypes )) {
					// wenn da, lösche units und add neue
					$to_delete = find_delete ( $needed_unittypes, $avail_unittypes, $avail_units );
					unset ( $avail_units );
					foreach ( $to_delete as $row ) {
						$tmp_player->del_unit ( $row );
					}
					$zieltype->new_unit ( 100, 1, $tmp_planet->id );
				}
				
				unset ( $avail_units );
				unset ( $zieltype );
			} elseif ($tmp_mission->mission_id == 2) {
				// prüfe ob objekt auf planet vorhanden
				$zieltype = new unit_type ( $tmp_mission->object_id );
				$unitvorhanden = FALSE;
				foreach ( $tmp_planet->units () as $row ) {
					if ($row->unit_type->id == $zieltype->id) {
						$unitvorhanden = $row->id;
					}
				}
				if ($unitvorhanden != FALSE) {
					// prüfe ob startrampe vorhanden und leer
					$rampevorhanden = FALSE;
					foreach ( $tmp_planet->buildings as $row ) {
						if ($row->building_type->name == 'Startrampe' && count ( $row->cargo ) == 0) {
							$rampevorhanden = $row->id;
						}
					}
					// belade
					if ($rampevorhanden != FALSE) {
						$set = new unit ( $unitvorhanden );
						$set->location_type = 2;
						$set->location_id = $rampevorhanden;
						unset ( $set );
						$tmp_mission->delete ();
					}
				}
				unset ( $vorhanden );
				unset ( $zieltype );
			}
		}
		unset ( $listmission );
	}
	unset ( $listplanet );
}
unset ( $listplayer );

?>

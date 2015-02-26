<?php
include_once ('class_building.php');
include_once ('class_building_type.php');
include_once ('class_clan.php');
include_once ('class_construction.php');
include_once ('class_master.php');
include_once ('class_module_type.php');
include_once ('class_planet.php');
include_once ('class_planet_mission.php');
include_once ('class_player.php');
include_once ('class_starsystem.php');
include_once ('class_tech.php');
include_once ('class_tech_fields.php');
include_once ('class_unit.php');
include_once ('class_unit_type.php');
function gamma() {
	return 6.673848e-11;
}
function hamilton($pos_x, $pos_y, $vel_x, $vel_y, $mass) {
	// Summiere die kinetische Energie aller Objekte
	$ham = 0;
	for($i = 0; $i < count ( $pos_x ); $i ++) {
		$ham += $mass [$i] * ($vel_x [$i] * $vel_x [$i] + $vel_y [$i] * $vel_y [$i]) / 2;
	}
	// Summiere die potientielle Energie aller Objekte
	$hamd = 0;
	for($i = 0; $i < count ( $pos_x ); $i ++) {
		$objham = 0;
		for($j = 0; $j < count ( $pos_x ); $j ++) {
			if ($i != $j) {
				$objham += $mass [$j] / sqrt ( ($pos_x [$i] - $pos_x [$j]) * ($pos_x [$i] - $pos_x [$j]) + ($pos_y [$i] - $pos_y [$j]) * ($pos_y [$i] - $pos_y [$j]) );
			}
		}
		$hamd += $mass [$i] * $objham;
	}
	$sum = $ham - gamma () * $hamd;
	return $sum;
}
function admin_new_building($name, $cost, $info) {
	$query = mysqli_query ( $dblink, 'SELECT id from game_building WHERE name=' . $name );
	if (mysqli_num_rows ( $query ) == 0) {
		$query = mysqli_query ( $dblink, 'INSERT INTO game_building (name,cost,info) VALUES ("' . $name . '",' . $cost . ',"' . $info . '")' );
	}
}
function admin_new_module($name, $cost, $info) {
	$query = mysqli_query ( $dblink, 'SELECT id from game_module WHERE name=' . $name );
	if (mysqli_num_rows ( $query ) == 0) {
		$query = mysqli_query ( $dblink, 'INSERT INTO game_module (name,cost,info) VALUES ("' . $name . '",' . $cost . ',"' . $info . '")' );
	}
}
function getlist($what) {
	global $dblink;
	switch ($what) {
		case 'starsystems' :
			$list = array ();
			$array = mysqli_query ( $dblink, 'SELECT id FROM game_starsystems' );
			if (mysqli_num_rows ( $array ) > 0) {
				while ( $row = mysqli_fetch_array ( $array ) ) {
					array_push ( $list, new starsystem ( $row ['id'] ) );
				}
			}
			return $list;
			break;
		case 'players' :
			$list = array ();
			$array = mysqli_query ( $dblink, 'SELECT id FROM game_players' );
			if (mysqli_num_rows ( $array ) > 0) {
				while ( $row = mysqli_fetch_array ( $array ) ) {
					array_push ( $list, new player ( $row ['id'] ) );
				}
			}
			return $list;
			break;
		case 'building_types' :
			$list = array ();
			$array = mysqli_query ( $dblink, 'SELECT id FROM game_building' );
			if (mysqli_num_rows ( $array ) > 0) {
				while ( $row = mysqli_fetch_array ( $array ) ) {
					array_push ( $list, new building_type ( $row ['id'] ) );
				}
			}
			return $list;
			break;
		case 'module_types' :
			$list = array ();
			$array = mysqli_query ( $dblink, 'SELECT id FROM game_module' );
			if (mysqli_num_rows ( $array ) > 0) {
				while ( $row = mysqli_fetch_array ( $array ) ) {
					array_push ( $list, new module_type ( $row ['id'] ) );
				}
			}
			return $list;
			break;
		case 'tech' :
			$list = array ();
			$array = mysqli_query ( $dblink, 'SELECT id FROM game_tech' );
			if (mysqli_num_rows ( $array ) > 0) {
				while ( $row = mysqli_fetch_array ( $array ) ) {
					array_push ( $list, new tech ( $row ['id'] ) );
				}
			}
			return $list;
			break;
		case 'techfields' :
			$list = array ();
			$array = mysqli_query ( $dblink, 'SELECT id FROM game_tech_fields' );
			if (mysqli_num_rows ( $array ) > 0) {
				while ( $row = mysqli_fetch_array ( $array ) ) {
					array_push ( $list, new tech_field ( $row ['id'] ) );
				}
			}
			return $list;
			break;
	}
}

?>
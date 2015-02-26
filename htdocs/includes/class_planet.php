<?php
class planet {
	public $id = 0;
	public $name = "0";
	public $starsystem_id = 0;
	public $pos_x = 0;
	public $pos_y = 0;
	public $vel_x = 0;
	public $vel_y = 0;
	public $mass = 0;
	public $type_id = 0;
	public $player_id = 0;
	public $population = 0;
	public $farmers = 0;
	public $craftsmen = 0;
	public $researchers = 0;
	function __construct($planet_id) {
		if ($planet_id != 0) {
			global $dblink;
			$array = mysqli_query ( $dblink, 'SELECT id,starsystem_id, pos_x,pos_y,vel_x,vel_y,mass,type_id,player_id,population,farmers,craftsmen,researchers FROM game_planets WHERE id=' . $planet_id );
			if (mysqli_num_rows ( $array ) == 1) {
				$array = mysqli_fetch_array ( $array );
				$this->id = $array ["id"];
				$this->name = "".$array ["id"];
				$this->starsystem_id = $array ["starsystem_id"];
				$this->pos_x = $array ["pos_x"];
				$this->pos_y = $array ["pos_y"];
				$this->vel_x = $array ["vel_x"];
				$this->vel_y = $array ["vel_y"];
				$this->mass = $array ["mass"];
				$this->type_id = $array ["type_id"];
				$this->player_id = $array ["player_id"];
				$this->population = $array ["population"];
				$this->farmers = $array ["farmers"];
				$this->craftsmen = $array ["craftsmen"];
				$this->researchers = $array ["researchers"];
			}
		}
	}
	function __destruct() {
		if ($this->id != 0) {
			global $dblink;
			$query = '';
			$query = mysqli_query ( $dblink, 'UPDATE game_planets SET starsystem_id=' . $this->starsystem_id . ', pos_x=' . $this->pos_x . ',pos_y=' . $this->pos_y . ',vel_x=' . $this->vel_x . ',vel_y=' . $this->vel_y . ',mass=' . $this->mass . ',type_id=' . $this->type_id . ',player_id=' . $this->player_id . ',population=' . $this->population . ',farmers=' . $this->farmers . ',craftsmen=' . $this->craftsmen . ',researchers=' . $this->researchers . ' WHERE id=' . $this->id );
		}
	}
	function list_moons() {
		$return = array ();
		if ($this->id != 0 && $this->type_id == 2) {
			$sys = new starsystem ( $this->starsystem_id );
			$sun = $sys->sun ();
			$list_moons = $sys->moons ();
			$r = sqrt ( pow ( $sun->pos_x - $this->pos_x, 2 ) + pow ( $sun->pos_y - $this->pos_y, 2 ) );
			$hill = $r * pow ( $this->mass / 3 / $sun->mass, 1 / 3 );
			foreach ( $list_moons as $row ) {
				$abs = sqrt ( pow ( $row->pos_x - $this->pos_x, 2 ) + pow ( $row->pos_y - $this->pos_y, 2 ) );
				if ($abs <= ($hill / 2) && $this->id != $row->id) {
					array_push ( $return, $row );
				}
			}
		}
		return $return;
	}
	function create_moon() {
		if ($this->id != 0) {
			// teste ob mond möglich
			$mondliste = $this->list_moons ();
			$mondgewicht = 0;
			foreach ( $mondliste as $row ) {
				$mondgewicht += $row->mass;
			}
			$proz = 100 * $mondgewicht / $this->mass;
			if ($proz < 5) {
				// checke roche-grenze und hill-sphäre
				$roche = 2.423 * $this->get_radius ();
				$hill = $this->hill_radius ();
				if ($hill > $roche) {
					// wähle mondbahn
					$mondbahn = $roche + mt_rand ( 1, 100 ) / 100 * ($hill - $roche);
					// wähle mondmasse
					$mondmasse = mt_rand ( 1, 100 ) / 100 * (5 - $proz) / 100 * $this->mass;
					// wähle winkel
					$winkel = mt_rand ( 0, 360 );
					$mond_x = $this->pos_x + $mondbahn * cos ( deg2rad ( $winkel ) );
					$mond_y = $this->pos_y + $mondbahn * sin ( deg2rad ( $winkel ) );
					// wähle geschwindigkeit
					$v1 = sqrt ( 6.67384e-11 * ($this->mass - $mondmasse) / ($mondbahn * 1000) ) / 1000 * 60 * 60;
					$mondgeschwindigkeit = (mt_rand ( 1000, 1200 ) / 1000) * $v1;
					$vwinkel = ($winkel + 90) % 360;
					$mond_vx = $this->vel_x + $mondgeschwindigkeit * cos ( deg2rad ( $vwinkel ) );
					$mond_vy = $this->vel_y + $mondgeschwindigkeit * sin ( deg2rad ( $vwinkel ) );
					// erstelle mond
					global $dblink;
					$query = mysqli_query ( $dblink, 'INSERT INTO game_planets (starsystem_id, pos_x, pos_y, vel_x, vel_y, mass, type_id) VALUES (' . $this->starsystem_id . ',' . $mond_x . ',' . $mond_y . ',' . $mond_vx . ',' . $mond_vy . ',' . $mondmasse . ',3)' );
					$this->mass -= $mondmasse;
				}
			}
		}
	}
	function hill_radius() {
		$sys = new starsystem ( $this->starsystem_id );
		$r = sqrt ( pow ( $sys->sun ()->pos_x - $this->pos_x, 2 ) + pow ( $sys->sun ()->pos_y - $this->pos_y, 2 ) );
		$hill = $r * pow ( $this->mass / 3 / $sys->sun ()->mass, 1 / 3 );
		unset ( $sys );
		return ($hill / 2);
	}
	function roche_radius() {
		$sys = new starsystem ( $this->starsystem_id );
		$dichte = 5500 * 1000000000;
		$volumen = $this->mass / $dichte;
		$radius = pow ( 3 * $volumen / 4 / pi (), 1 / 3 );
		$roche = $radius * pow ( 2 * $sys->sun ()->mass / $this->mass, 1 / 3 );
		return $roche;
	}
	function get_radius() {
		$dichte = 5500 * 1000000000;
		$volumen = $this->mass / $dichte;
		$radius = pow ( 3 * $volumen / 4 / pi (), 1 / 3 );
		return $radius;
	}
	function get_forces($planet_id) {
		$return = 0;
		if ($this->id != 0) {
			$zwo = new planet ( $planet_id );
			if ($zwo->id != 0 and $zwo->id != $this->id) {
				if ($zwo->starsystem_id == $this->starsystem_id) {
					$sqdist = pow ( ($zwo->pos_x - $this->pos_x) * 1000, 2 ) + pow ( ($zwo->pos_y - $this->pos_y) * 1000, 2 );
					$return = 6.67384e-11 * $this->mass * $zwo->mass / $sqdist;
				}
			}
		}
		return $return;
	}
	function change_farmers($value) {
		$maxplus = floor ( $this->population ) - $this->farmers - $this->craftsmen - $this->researchers;
		$this->farmers = $this->farmers + min ( $maxplus, max ( 0, $value ) ) + max ( - $this->farmers, min ( $value, 0 ) );
	}
	function change_craftsmen($value) {
		$maxplus = floor ( $this->population ) - $this->farmers - $this->craftsmen - $this->researchers;
		$this->craftsmen = $this->craftsmen + min ( $maxplus, max ( 0, $value ) ) + max ( - $this->craftsmen, min ( $value, 0 ) );
	}
	function change_researchers($value) {
		$maxplus = floor ( $this->population ) - $this->farmers - $this->craftsmen - $this->researchers;
		$this->researchers = $this->researchers + min ( $maxplus, max ( 0, $value ) ) + max ( - $this->researchers, min ( $value, 0 ) );
	}
	function usedpop() {
		$usedpop = $this->farmers + $this->craftsmen + $this->researchers;
		return $usedpop;
	}
	function buildings() {
		$list = array ();
		if ($this->id != 0) {
			global $dblink;
			$array = mysqli_query ( $dblink, 'SELECT id FROM game_planets_buildings WHERE planet_id = ' . $this->id );
			if (mysqli_num_rows ( $array ) > 0) {
				while ( $row = mysqli_fetch_array ( $array ) ) {
					array_push ( $list, new building ( $row ['id'] ) );
				}
			}
		}
		return $list;
	}
	function units() {
		$list = array ();
		if ($this->id != 0) {
			global $dblink;
			$array = mysqli_query ( $dblink, 'SELECT id FROM game_players_units WHERE player_id = ' . $this->player_id . ' AND kind_of_location = 1 AND location_id = ' . $this->id );
			if (mysqli_num_rows ( $array ) > 0) {
				while ( $row = mysqli_fetch_array ( $array ) ) {
					array_push ( $list, new unit ( $row ['id'] ) );
				}
			}
		}
		return $list;
	}
	function constructions() {
		$list = array ();
		$sortedlist = array ();
		if ($this->id != 0) {
			global $dblink;
			$array = mysqli_query ( $dblink, 'SELECT id FROM game_planets_construction WHERE planet_id = ' . $this->id );
			if (mysqli_num_rows ( $array ) > 0) {
				while ( $row = mysqli_fetch_array ( $array ) ) {
					array_push ( $list, new construction ( $row ['id'] ) );
				}
				// ab hier sortierung nach rang
				$next_prev = 0;
				do {
					$i = 0;
					$j = 0;
					while ( $list [$i]->prev_id != $next_prev and $j < 25 ) {
						$i ++;
						$j ++;
					}
					array_push ( $sortedlist, $list [$i] );
					$next_prev = $list [$i]->id;
					array_splice ( $list, $i, 1 );
				} while ( count ( $list ) > 0 );
			}
		}
		return $sortedlist;
	}
	function building_possible() {
		$list = array ();
		if ($this->id != 0) {
			global $dblink;
			$array = mysqli_query ( $dblink, 'SELECT id FROM game_building ORDER BY cost,name' );
			if (mysqli_num_rows ( $array ) > 0) {
				while ( $row = mysqli_fetch_array ( $array ) ) {
					array_push ( $list, new building_type ( $row ['id'] ) );
				}
			}
		}
		return $list;
	}
	function module_possible() {
		$list = array ();
		if ($this->id != 0) {
			global $dblink;
			$array = mysqli_query ( $dblink, 'SELECT id FROM game_module ORDER BY cost,name' );
			if (mysqli_num_rows ( $array ) > 0) {
				while ( $row = mysqli_fetch_array ( $array ) ) {
					array_push ( $list, new module_type ( $row ['id'] ) );
				}
			}
		}
		return $list;
	}
	function unit_possible() {
		$list = array ();
		if ($this->id != 0) {
			global $dblink;
			$array = mysqli_query ( $dblink, 'SELECT id FROM game_unit_types WHERE player_id=' . $this->player_id . ' ORDER BY name' );
			if (mysqli_num_rows ( $array ) > 0) {
				while ( $row = mysqli_fetch_array ( $array ) ) {
					array_push ( $list, new unit_type ( $row ['id'] ) );
				}
			}
		}
		return $list;
	}
	function add_building($building_type_id, $status_id) {
		$typ = new building_type ( $building_type_id );
		if ($typ->id != 0) {
			$query = '';
			$query = mysqli_query ( $dblink, 'INSERT INTO game_planets_buildings (planet_id, building_id, hp, status_id) VALUES (' . $this->id . ',' . $typ->id . ',100,' . $status_id . ')' );
		}
		unset ( $typ );
	}
	function add_module($module_type_id, $status_id) {
		$typ = new module_type ( $module_type_id );
		if ($typ->id != 0) {
			$query = '';
			$query = mysqli_query ( $dblink, 'INSERT INTO game_planets_buildings (planet_id, module_id, hp, status_id) VALUES (' . $this->id . ',' . $typ->id . ',100,' . $status_id . ')' );
		}
		unset ( $typ );
	}
	function last_construction() {
		global $dblink;
		$last = mysqli_query ( $dblink, 'SELECT id FROM game_planets_construction WHERE planet_id=' . $this->id . ' and next_priority=0' );
		if (mysqli_num_rows ( $last ) == 0) {
			$id = 0;
		} else {
			$id = mysqli_fetch_array ( $last );
			$id = $id ['id'];
		}
		$id = new construction ( $id );
		return $id;
	}
	function add_construction($type, $id) {
		if ($type == 'building') {
			$building = new building_type ( $id );
			if ($building->id != 0) {
				// finde letztes element der bauliste
				$newprev = $this->last_construction ();
				
				global $dblink;
				$new = mysqli_query ( $dblink, 'INSERT INTO game_planets_construction (planet_id, building_id, prev_priority) VALUES (' . $this->id . ',' . $building->id . ',' . $newprev->id . ')' );
				$newnext = mysqli_insert_id ( $dblink );
				
				if ($newprev->id != 0) {
					$newprev->next_id = $newnext;
				}
				unset ( $newprev );
			}
		} elseif ($type == 'module') {
			$module = new module_type ( $id );
			if ($module->id != 0) {
				// finde letztes element der bauliste
				$newprev = $this->last_construction ();
				
				global $dblink;
				$new = mysqli_query ( $dblink, 'INSERT INTO game_planets_construction (planet_id, module_id, prev_priority) VALUES (' . $this->id . ',' . $module->id . ',' . $newprev->id . ')' );
				$newnext = mysqli_insert_id ( $dblink );
				
				if ($newprev->id != 0) {
					$newprev->next_id = $newnext;
				}
				unset ( $newprev );
			}
		} elseif ($type == 'unit') {
			$unit = new unit_type ( $id );
			if ($unit->id != 0) {
				$list = $unit->list_module_ids ();
				for($i = 0; $i < count ( $list ); $i ++) {
					$this->add_construction ( 'module', $list [$i] );
				}
				unset ( $list );
			}
			unset ( $unit );
		}
	}
	function del_all_constructions() {
		if ($this->id != 0) {
			$query = mysqli_query ( $dblink, 'DELETE FROM game_planets_construction WHERE planet_id = ' . $this->id );
		}
	}
	function del_all_buildings() {
		if ($this->id != 0) {
			$query = mysqli_query ( $dblink, 'DELETE FROM game_planets_buildings WHERE planet_id = ' . $this->id );
		}
	}
	function last_planet_mission() {
		global $dblink;
		$last = mysqli_query ( $dblink, 'SELECT id FROM game_planets_mission WHERE planet_id=' . $this->id . ' and next_priority=0' );
		if (mysqli_num_rows ( $last ) == 0) {
			$id = 0;
		} else {
			$id = mysqli_fetch_array ( $last );
			$id = $id ['id'];
		}
		$id = new planet_mission ( $id );
		return $id;
	}
	function add_planet_mission($mission_id, $object_id, $times) {
		// finde letztes element der bauliste
		$newprev = $this->last_planet_mission ();
		
		global $dblink;
		$new = mysqli_query ( $dblink, 'INSERT INTO game_planets_mission (planet_id, mission_id, object_id, times, prev_priority) VALUES (' . $this->id . ',' . $mission_id . ',' . $object_id . ',' . $times . ',' . $newprev->id . ')' );
		$newnext = mysqli_insert_id ( $dblink );
		
		if ($newprev->id != 0) {
			$newprev->next_id = $newnext;
		}
		unset ( $newprev );
	}
	function list_planet_mission() {
		$list = array ();
		$sortedlist = array ();
		if ($this->id != 0) {
			global $dblink;
			$array = mysqli_query ( $dblink, 'SELECT id FROM game_planets_mission WHERE planet_id = ' . $this->id );
			if (mysqli_num_rows ( $array ) > 0) {
				while ( $row = mysqli_fetch_array ( $array ) ) {
					array_push ( $list, new planet_mission ( $row ['id'] ) );
				}
				// ab hier sortierung nach rang
				$next_prev = 0;
				do {
					$i = 0;
					$j = 0;
					while ( $list [$i]->prev_id != $next_prev and $j < 25 ) {
						$i ++;
						$j ++;
					}
					array_push ( $sortedlist, $list [$i] );
					$next_prev = $list [$i]->id;
					array_splice ( $list, $i, 1 );
				} while ( count ( $list ) > 0 );
			}
		}
		return $sortedlist;
	}
}
?>

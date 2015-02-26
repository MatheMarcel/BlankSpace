<?php
class player {
	public $id = 0;
	public $name = '';
	public $homeplanet = 0;
	public $rounddone = 0;
	public $clan = 0;
	function __construct($playerid) {
		global $dblink;
		if ($playerid != 0) {
			$array = mysqli_query ( $dblink, 'SELECT id,name,rundefertig,home_id,clan_id FROM game_players WHERE id=' . $playerid );
			if (mysqli_num_rows ( $array ) == 1) {
				$array = mysqli_fetch_array ( $array );
				$this->id = $array ["id"];
				$this->name = $array ["name"];
				$this->rounddone = $array ["rundefertig"];
				$this->homeplanet = $array ["home_id"];
				$this->clan = new clan ( $array ["clan_id"] );
			}
		}
	}
	
	/**
	 * Diese Funktion liefert eine Liste aller Planeten, die dieser Spieler besitzt, als Klassen.
	 */
	function planets() {
		global $dblink;
		$list = array ();
		if ($this->id != 0) {
			$array = mysqli_query ( $dblink, 'SELECT id FROM game_planets WHERE player_id = ' . $this->id );
			if (mysqli_num_rows ( $array ) > 0) {
				while ( $row = mysqli_fetch_array ( $array ) ) {
					array_push ( $list, new planet ( $row ['id'] ) );
				}
			}
		}
		return $list;
	}
	
	/**
	 * Diese Funktion liefert eine Liste aller Module, die dieser Spieler bauen kann, als Klassen.
	 */
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
	function list_unit_types() {
		$list = array ();
		if ($this->id != 0) {
			global $dblink;
			$array = mysqli_query ( $dblink, 'SELECT id FROM game_unit_types WHERE player_id = ' . $this->id );
			if (mysqli_num_rows ( $array ) > 0) {
				while ( $row = mysqli_fetch_array ( $array ) ) {
					array_push ( $list, new unit_type ( $row ['id'] ) );
				}
			}
		}
		return $list;
	}
	function get_unit_type($module_ids) {
		$return_id = 0;
		if (is_array ( $module_ids )) {
			if (count ( $module_ids ) > 0) {
				sort ( $module_ids );
				$list = $this->list_unit_types ();
				// durchsuche alle unit_type von diesem spieler
				foreach ( $list as $row ) {
					$list2 = $row->list_module_ids ();
					sort ( $list2 );
					if ($list2 == $module_ids) {
						$return_id = $row->id;
						break;
					}
					unset ( $list2 );
				}
			}
		}
		return $return_id;
	}
	function delete_unit_type($type_id) {
		$utype = new unit_type ( $type_id );
		$anz = $utype->count_units ();
		$owner = $utype->player_id;
		unset ( $utype );
		if ($anz == 0 && $owner == $this->id) {
			$query = mysqli_query ( $dblink, 'DELETE FROM game_unit_modules WHERE unit_id=' . $type_id );
			$query = mysqli_query ( $dblink, 'DELETE FROM game_unit_types WHERE id=' . $type_id );
		}
	}
	function new_unit_type($module_ids, $name) {
		if ($this->get_unit_type ( $module_ids ) == 0) {
			$eindeutigername = FALSE;
			$list = $this->list_unit_types ();
			while ( ! $eindeutigername ) {
				$eindeutigername = TRUE;
				if ($name == '') {
					$name = 'Typ #' . mt_rand ( 1, 10000 );
				}
				foreach ( $list as $row ) {
					if ($row->name == $name) {
						$name = '';
						$eindeutigername = FALSE;
						break;
					}
				}
			}
			unset ( $list );
			$query = mysqli_query ( $dblink, 'INSERT INTO game_unit_types (name,player_id) VALUES ("' . $name . '",' . $this->id . ')' );
			$uid = mysqli_insert_id ();
			$unit = new unit_type ( $uid );
			foreach ( $module_ids as $row ) {
				$unit->add_module ( $row );
			}
			unset ( $unit );
		}
	}
	function calc_research_points() {
		$planets = $this->planets ();
		$techpoints = 0;
		foreach ( $planets as $row ) {
			$techpoints += $row->researchers;
		}
		unset ( $planets );
		return $techpoints;
	}
	function get_research_data() {
		$list = array ();
		if ($this->id != 0) {
			global $dblink;
			$array = mysqli_query ( $dblink, 'SELECT name, field_id, distribution, points FROM game_players_tech_field LEFT JOIN game_tech_fields ON game_tech_fields.id = field_id WHERE player_id = ' . $this->id );
			if (mysqli_num_rows ( $array ) > 0) {
				while ( $row = mysqli_fetch_array ( $array ) ) {
					array_push ( $list, $row );
				}
			}
		}
		return $list;
	}
	function add_research($field_id, $points) {
		if ($this->id != 0) {
			if ($points >= 0) {
				$query = mysqli_query ( $dblink, 'UPDATE game_players_tech_field SET points=points+' . $points . ' WHERE player_id=' . $this->id . ' AND field_id=' . $field_id );
			}
		}
	}
	function change_research_distribution($field_id, $value) {
		if ($this->id != 0) {
			global $dblink;
			$data = $this->get_research_data ();
			$oldvalue = 0;
			$used = 0;
			foreach ( $data as $row ) {
				$used += $row ['distribution'];
				if ($row ['field_id'] == $field_id) {
					$oldvalue = $row ['distribution'];
				}
			}
			$maxplus = floor ( 100 - $used );
			$newvalue = $oldvalue + min ( $maxplus, max ( 0, $value ) ) + max ( - $oldvalue, min ( $value, 0 ) );
			$set = mysqli_query ( $dblink, 'UPDATE game_players_tech_field SET distribution=' . $newvalue . ' WHERE player_id = ' . $this->id . ' AND field_id = ' . $field_id );
		}
	}
	function list_tech() {
		$techlist = array (
				array (),
				array (),
				array () 
		);
		global $dblink;
		$query = mysqli_query ( $dblink, 'SELECT tech_id FROM game_players_tech WHERE player_id=' . $this->id );
		while ( $row = mysqli_fetch_object ( $query ) ) {
			$a = new tech ( $row->tech_id );
			array_push ( $techlist [$a->field_id - 1], new tech ( $row->tech_id ) );
		}
		return $techlist;
	}
	function add_log($text_id, $ziel_id) {
		if ($this - id != 0) {
			$master = new master ();
			$query = mysqli_query ( $dblink, 'INSERT INTO game_players_log (player_id,text_id,ziel_id,round_id) VALUES (' . $this->id . ',' . $text_id . ',' . $ziel_id . ',' . $master->round . ')' );
		}
	}
	function del_log($log_id) {
		if ($this - id != 0) {
			global $dblink;			
			$query = mysqli_query ( $dblink, 'DELETE FROM game_players_log WHERE id=' . $log_id . ' AND player_id=' . $this->id . ' LIMIT 1' );
		}
	}
	function list_log() {
		$liste = array ();
		if ($this->id != 0) {
			global $dblink;
			$query = mysqli_query ( $dblink, 'SELECT id,text_id,ziel_id,round_id FROM game_players_log WHERE player_id=' . $this->id . ' ORDER BY round_id desc' );
			while ( $row = mysqli_fetch_array ( $query ) ) {
				array_push ( $liste, $row );
			}
		}
		return $liste;
	}
	function del_all_units() {
		if ($this->id != 0) {
			$query = mysqli_query ( $dblink, 'DELETE FROM game_players_units WHERE player_id = ' . $this->id );
		}
	}
	function del_unit($uid) {
		if ($this->id != 0) {
			$query = mysqli_query ( $dblink, 'DELETE FROM game_players_units WHERE player_id = ' . $this->id . ' AND id=' . $uid );
		}
	}
	function del_all_unit_types() {
		if ($this->id != 0) {
			$query = mysqli_query ( $dblink, 'DELETE FROM game_unit_types WHERE player_id = ' . $this->id );
		}
	}
	function del_all_research() {
		if ($this->id != 0) {
			$query = mysqli_query ( $dblink, 'DELETE FROM game_players_tech WHERE player_id = ' . $this->id );
		}
	}
	function del_all_planets() {
		if ($this->id != 0) {
			$query = mysqli_query ( $dblink, 'UPDATE game_planets SET player_id=0,population=0,farmers=0,craftsmen=0,researchers=0 WHERE player_id = ' . $this->id );
			$query = mysqli_query ( $dblink, 'UPDATE game_planets SET player_id=0,population=0,farmers=0,craftsmen=0,researchers=0 WHERE player_id = ' . $this->id );
			$query = mysqli_query ( $dblink, 'UPDATE game_players SET home_id = 0 WHERE id=' . $player->id );
		}
	}
}
?>

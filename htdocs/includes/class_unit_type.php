<?php
class unit_type {
	public $id = 0;
	public $name = '';
	public $player_id = '';
	function __construct($unit_type_id) {
		global $dblink;
		$query = mysqli_query ( $dblink, 'SELECT id, name, player_id FROM game_unit_types WHERE id=' . $unit_type_id );
		if (mysqli_num_rows ( $query ) == 1) {
			$query = mysqli_fetch_array ( $query );
			$this->id = $query ['id'];
			$this->name = $query ['name'];
			$this->player_id = $query ['player_id'];
		}
	}
	
	/**
	 * Diese Funktion liefert den Wert der Kosten aller Module, die für diesen Unit-Typ gebraucht werden.
	 */
	function cost() {
		$cost = 0;
		$list = $this->modules ();
		foreach ( $list as $row ) {
			$cost = $cost + $row->cost;
		}
		unset ( $list );
		return $cost;
	}
	
	/**
	 * Diese Funktion liefert eine Liste aller Modul-Typen als Klassen, die für diesen Unit-Typ gebraucht werden.
	 */
	function modules() {
		$list = array ();
		if ($this->id != 0) {
			global $dblink;
			$query = mysqli_query ( $dblink, 'SELECT module_id FROM game_unit_modules WHERE unit_id=' . $this->id );
			if (mysqli_num_rows ( $query ) > 0) {
				while ( $row = mysqli_fetch_array ( $query ) ) {
					array_push ( $list, new module_type ( $row ['module_id'] ) );
				}
			}
		}
		return $list;
	}
	
	/**
	 * Diese Funktion liefert eine Liste aller Ids von Modulen, die für diesen Unit-Typ gebraucht werden.
	 */
	function list_module_ids() {
		$list = array ();
		if ($this->id != 0) {
			global $dblink;
			$query = mysqli_query ( $dblink, 'SELECT module_id FROM game_unit_modules WHERE unit_id=' . $this->id );
			if (mysqli_num_rows ( $query ) > 0) {
				while ( $row = mysqli_fetch_array ( $query ) ) {
					array_push ( $list, $row ['module_id'] );
				}
			}
		}
		return $list;
	}
	function add_module($module_id) {
		if ($this->id != 0) {
			$modul = new module_type ( $module_id );
			if ($modul->id != 0) {
				$query = mysqli_query ( $dblink, 'INSERT INTO game_unit_modules (unit_id,module_id) VALUES (' . $this->id . ',' . $modul->id . ')' );
			}
		}
	}
	
	/**
	 * Diese Funktion liefert die Anzahl der von diesem Unit-Typ gebauten Einheiten.
	 */
	function count_units() {
		if ($this->id != 0) {
			global $dblink;
			$query = mysqli_query ( $dblink, 'SELECT count(*) as anz FROM game_players_units WHERE unit_id=' . $this->id );
			$query = mysqli_fetch_array ( $query );
		}
		return $query ['anz'];
	}
	function new_unit($hp, $location_type, $location_id) {
		if ($this->id != 0) {
			$query = mysqli_query ( $dblink, 'INSERT INTO game_players_units (player_id, unit_id, hp, kind_of_location, location_id) VALUES (' . $this->player_id . ',' . $this->id . ',100,' . $location_type . ',' . $location_id . ')' );
		}
	}
	function del_all_modules() {
		if ($this->id != 0) {
			$query = mysqli_query ( $dblink, 'DELETE FROM game_unit_modules WHERE unit_id = ' . $this->id );
		}
	}
}
?>

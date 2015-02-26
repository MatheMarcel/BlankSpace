<?php
class building_type {
	public $id = 0;
	public $name = '';
	public $cost = 0;
	public $info = '';
	public $capacity = 0;
	function __construct($building_id) {
		global $dblink;
		$query = mysqli_query ( $dblink, 'SELECT id, name, cost,info,capacity FROM game_building WHERE id=' . $building_id );
		if (mysqli_num_rows ( $query ) == 1) {
			$query = mysqli_fetch_array ( $query );
			$this->id = $query ['id'];
			$this->name = $query ['name'];
			$this->cost = $query ['cost'];
			$this->info = $query ['info'];
			$this->capacity = $query ['capacity'];
		}
	}
	
	/**
	 * Diese Funktion gibt ein Array aus, welche Gebäude für den Bau nötig sind, als Klassen.
	 */
	function list_dep() {
		$list = array ();
		if ($this->id != 0) {
			global $dblink;			
			$query = mysqli_query ( $dblink, 'SELECT depends_on_id FROM game_building_dep WHERE building_id=' . $this->id );
			while ( $row = mysqli_fetch_array ( $query ) ) {
				$a = new building_type ( $row ['depends_on_id'] );
				array_push ( $list, $a->name );
			}
		}
		return implode ( '; ', $list );
	}
	
	/**
	 * Diese Funktion gibt ein Array aus, welche Technologien für den Bau nötig sind, als Klassen.
	 */
	function list_techdep() {
		$list = array ();
		if ($this->id != 0) {
			global $dblink;			
			$query = mysqli_query ( $dblink, 'SELECT tech_id FROM game_building_techdep WHERE building_id=' . $this->id );
			while ( $row = mysqli_fetch_array ( $query ) ) {
				$a = new tech ( $row ['tech_id'] );
				array_push ( $list, $a->name );
			}
		}
		return implode ( '; ', $list );
	}
}
?>
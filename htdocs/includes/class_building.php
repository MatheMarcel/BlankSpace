<?php
class building {
	public $id = 0;
	public $building_type = 0;
	public $hp = 0;
	public $status_id = 0;
	function __construct($building_id) {
		global $dblink;
		$query = mysqli_query ( $dblink, 'SELECT id, building_id, hp, status_id FROM game_planets_buildings WHERE id=' . $building_id );
		if (mysqli_num_rows ( $query ) == 1) {
			$query = mysqli_fetch_array ( $query );
			$this->id = $query ['id'];
			$this->building_type = new building_type ( $query ['building_id'] );
			$this->hp = $query ['hp'];
			$this->status_id = $query ['status_id'];
		}
	}
	
	/**
	 * Diese Funktion liefert ein Array aller Units, die in diesem Gebäude gelagert sind, als Klassen.
	 */
	function cargo() {
		$return = array ();
		if ($this->id != 0) {
			global $dblink;
			$query = mysqli_query ( $dblink, 'SELECT id FROM game_players_units WHERE kind_of_location = 2 AND location_id = ' . $this->id );
			while ( $row = mysqli_fetch_array ( $query ) ) {
				array_push ( $return, new unit ( $row ) );
			}
		}
		return $return;
	}
}
?>

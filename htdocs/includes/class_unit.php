<?php
class unit {
	public $id = 0;
	public $unit_type = 0;
	public $hp = 0;
	public $location_type = 0; // 1 = on planet, 2 = in building, 3 = in unit, 4 = in space
	public $location_id = 0;
	function __construct($unit_id) {
		global $dblink;
		$query = mysqli_query ( $dblink, 'SELECT id, hp, unit_id, kind_of_location, location_id FROM game_players_units WHERE id=' . $unit_id );
		if (mysqli_num_rows ( $query ) == 1) {
			$query = mysqli_fetch_array ( $query );
			$this->id = $query ['id'];
			$this->unit_type = new unit_type ( $query ['unit_id'] );
			$this->hp = $query ['hp'];
			$this->location_type = $query ['kind_of_location'];
			$this->location_id = $query ['location_id'];
		}
	}
}

?>

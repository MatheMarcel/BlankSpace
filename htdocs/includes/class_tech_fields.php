<?php
class tech_field {
	public $id = 0;
	public $name = '';
	function __construct($id) {
		global $dblink;
		$query = mysqli_query ( $dblink, 'SELECT id, name FROM game_tech_fields WHERE id=' . $id );
		if (mysqli_num_rows ( $query ) == 1) {
			$query = mysqli_fetch_array ( $query );
			$this->id = $query ['id'];
			$this->name = $query ['name'];
		}
	}
	function add($name, $cost, $info) {
		if ($this->id != 0) {
			$query = mysqli_query ( $dblink, 'SELECT id from game_tech WHERE name=' . $name );
			if (mysqli_num_rows ( $query ) == 0) {
				$query = mysqli_query ( $dblink, 'INSERT INTO game_tech (name,cost,field_id,info) VALUES ("' . $name . '",' . $cost . ',' . $this->id . ',"' . $info . '")' );
			}
		}
	}
}

?>

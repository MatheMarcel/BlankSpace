<?php
class tech {
	public $id = 0;
	public $name = '';
	public $cost = '';
	public $field_id = '';
	public $tech_field = '';
	public $info = '';
	function __construct($id) {
		global $dblink;
		$query = mysqli_query ( $dblink, 'SELECT id, name, cost, field_id,info FROM game_tech WHERE id=' . $id );
		if (mysqli_num_rows ( $query ) == 1) {
			$query = mysqli_fetch_array ( $query );
			$this->id = $query ['id'];
			$this->name = $query ['name'];
			$this->cost = $query ['cost'];
			$this->field_id = $query ['field_id'];
			$this->tech_field = new tech_field ( $query ['field_id'] );
			$this->info = $query ['info'];
		}
	}
	function list_dep() {
		$list = array ();
		if ($this->id != 0) {
			global $dblink;
			$query = mysqli_query ( $dblink, 'SELECT depends_on_id FROM game_tech_dep WHERE tech_id=' . $this->id );
			while ( $row = mysqli_fetch_array ( $query ) ) {
				$a = new tech ( $row ['depends_on_id'] );
				array_push ( $list, $a->name );
			}
		}
		return implode ( '; ', $list );
	}
	function list_building_use() {
		$list = array ();
		if ($this->id != 0) {
			global $dblink;
			$query = mysqli_query ( $dblink, 'SELECT building_id FROM game_building_techdep WHERE tech_id=' . $this->id );
			while ( $row = mysqli_fetch_array ( $query ) ) {
				$a = new building_type ( $row ['building_id'] );
				array_push ( $list, $a->name );
			}
		}
		return implode ( '; ', $list );
	}
	function list_module_use() {
		$list = array ();
		if ($this->id != 0) {
			global $dblink;
			$query = mysqli_query ( $dblink, 'SELECT module_id FROM game_module_techdep WHERE tech_id=' . $this->id );
			while ( $row = mysqli_fetch_array ( $query ) ) {
				$a = new module_type ( $row ['module_id'] );
				array_push ( $list, $a->name );
			}
		}
		return implode ( '; ', $list );
	}
}

?>

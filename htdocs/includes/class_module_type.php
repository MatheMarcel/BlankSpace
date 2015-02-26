<?php
class module_type {
	public $id = 0;
	public $name = '';
	public $cost = 0;
	public $info = '';
	function __construct($module_id) {
		global $dblink;
		$query = mysqli_query ( $dblink, 'SELECT id, name, cost, info FROM game_module WHERE id=' . $module_id );
		if (mysqli_num_rows ( $query ) == 1) {
			$query = mysqli_fetch_array ( $query );
			$this->id = $query ['id'];
			$this->name = $query ['name'];
			$this->cost = $query ['cost'];
			$this->info = $query ['info'];
		}
	}
	function list_dep() {
		$list = array ();
		if ($this->id != 0) {
			global $dblink;
			$query = mysqli_query ( $dblink, 'SELECT depends_on_id FROM game_module_dep WHERE module_id=' . $this->id );
			while ( $row = mysqli_fetch_array ( $query ) ) {
				$a = new building_type ( $row ['depends_on_id'] );
				array_push ( $list, $a->name );
			}
		}
		return implode ( '; ', $list );
	}
	function list_techdep() {
		$list = array ();
		if ($this->id != 0) {
			global $dblink;
			$query = mysqli_query ( $dblink, 'SELECT tech_id FROM game_module_techdep WHERE module_id=' . $this->id );
			while ( $row = mysqli_fetch_array ( $query ) ) {
				$a = new tech ( $row ['tech_id'] );
				array_push ( $list, $a->name );
			}
		}
		return implode ( '; ', $list );
	}
}

?>

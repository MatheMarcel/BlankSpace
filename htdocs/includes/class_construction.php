<?php
class construction {
	public $id = 0;
	public $planet_id = 0;
	public $prev_id = 0;
	public $next_id = 0;
	public $building_type = 0;
	public $module_type = 0;
	public $status_id = 0;
	public $points = 0;
	public $name = '';
	public $cost = 0;
	function __construct($construction_id) {
		global $dblink;
		$query = mysqli_query ( $dblink, 'SELECT id, planet_id, building_id, module_id, prev_priority, next_priority, constructed, status_id FROM game_planets_construction WHERE id=' . $construction_id );
		if (is_resource ( $query )) {
			if (mysqli_num_rows ( $query ) == 1) {
				$query = mysqli_fetch_array ( $query );
				$this->id = $query ['id'];
				$this->planet_id = $query ['planet_id'];
				$this->building_type = new building_type ( $query ['building_id'] );
				$this->module_type = new module_type ( $query ['module_id'] );
				if ($query ['building_id'] != 0) {
					$this->name = $this->building_type->name;
					$this->cost = $this->building_type->cost;
				} elseif ($query ['module_id'] != 0) {
					$this->name = $this->module_type->name;
					$this->cost = $this->module_type->cost;
				}
				$this->prev_id = $query ['prev_priority'];
				$this->next_id = $query ['next_priority'];
				$this->status_id = $query ['status_id'];
				$this->points = $query ['constructed'];
			}
		}
	}
	function __destruct() {
		if ($this->id != 0) {
			$query = '';
			$query = mysqli_query ( $dblink, 'UPDATE game_planets_construction SET prev_priority=' . $this->prev_id . ',next_priority=' . $this->next_id . ',status_id=' . $this->status_id . ',constructed=' . $this->points . ' WHERE id=' . $this->id );
		}
	}
	function building_done() {
		if ($this->id != 0) {
			if ($this->building_type->id != 0 && $this->points >= $this->cost) {
				if ($this->status_id == 1) {
					$planet = new planet ( $this->planet_id );
					$planet->add_building ( $this->building_type->id, 3 );
					$player = new player ( $planet->player_id );
					$player->add_log ( 3, $planet->id );
					unset ( $player );
					unset ( $planet );
					$this->delete ();
				} elseif ($this->status_id == 2) {
					$planet = new planet ( $this->planet_id );
					$planet->add_building ( $this->building_type->id, 4 );
					$player = new player ( $planet->player_id );
					$player->add_log ( 3, $planet->id );
					unset ( $player );
					unset ( $planet );
					$this->delete ();
				}
			}
		}
	}
	function module_done() {
		if ($this->id != 0) {
			if (($this->module_type->id != 0) && ($this->points >= $this->cost)) {
				$planet = new planet ( $this->planet_id );
				$player = new player ( $planet->player_id );
				$array = array ();
				array_push ( $array, $this->module_type->id );
				$unit_type_id = $player->get_unit_type ( $array );
				if ($unit_type_id == 0) {
					$player->new_unit_type ( $array, $this->module_type->name );
					$unit_type_id = $player->get_unit_type ( $array );
				}
				if ($unit_type_id != 0) {
					$unit_type = new unit_type ( $unit_type_id );
					$unit_type->new_unit ( 100, 1, $planet->id );
					$player->add_log ( 3, $planet->id );
					unset ( $unit_type );
					$this->delete ();
				}
				unset ( $player );
				unset ( $planet );
			}
		}
	}
	function add_point() {
		// ist das construct ein gebäude?
		if ($this->building_type->cost > 0) {
			if ($this->points < $this->building_type->cost) {
				$this->points ++;
			}
		}
		
		// ist das construct ein module?
		if ($this->module_type->cost > 0) {
			if ($this->points < $this->module_type->cost) {
				$this->points ++;
			}
		}
	}
	function delete() {
		if ($this->id != 0) {
			if ($this->prev_id > 0) {
				$prev = new construction ( $this->prev_id );
				$prev->next_id = $this->next_id;
				unset ( $prev );
			}
			if ($this->next_id > 0) {
				$next = new construction ( $this->next_id );
				$next->prev_id = $this->prev_id;
				unset ( $next );
			}
			
			$del = '';
			$del = mysqli_query ( $dblink, 'DELETE FROM game_planets_construction WHERE id=' . $this->id );
			$this->id = 0;
			unset ( $this );
		}
	}
	function go_top() {
		while ( $this->prev_id != 0 ) {
			$this->go_up ();
		}
	}
	function go_up() {
		// setze b eins höher in der kette x-a-b-y
		if ($this->prev_id != 0) {
			$a = new construction ( $this->prev_id );
			
			$xid = $a->prev_id;
			$aid = $this->prev_id;
			$bid = $this->id;
			$yid = $this->next_id;
			
			// suche und setze x
			if ($xid != 0) {
				$x = new construction ( $xid );
				$x->next_id = $bid;
				unset ( $x );
			}
			// suche und setze y
			if ($yid != 0) {
				$y = new construction ( $yid );
				$y->prev_id = $aid;
				unset ( $y );
			}
			// setze a
			$a->prev_id = $bid;
			$a->next_id = $yid;
			unset ( $a );
			// setze b
			$this->prev_id = $xid;
			$this->next_id = $aid;
		}
	}
	function go_down() {
		// setze a eins tiefer in der kette x-a-b-y
		if ($this->next_id != 0) {
			$b = new construction ( $this->next_id );
			
			$xid = $this->prev_id;
			$aid = $this->id;
			$bid = $this->next_id;
			$yid = $b->next_id;
			
			// suche und setze x
			if ($xid != 0) {
				$x = new construction ( $xid );
				$x->next_id = $bid;
				unset ( $x );
			}
			// suche und setze y
			if ($yid != 0) {
				$y = new construction ( $yid );
				$y->prev_id = $aid;
				unset ( $y );
			}
			// setze a
			$b->prev_id = $xid;
			$b->next_id = $aid;
			unset ( $b );
			// setze b
			$this->prev_id = $bid;
			$this->next_id = $yid;
		}
	}
	function go_bottom() {
		while ( $this->next_id != 0 ) {
			$this->go_down ();
		}
	}
	function pause() {
		if ($this->status_id == 1) {
			$this->status_id = 2;
		} elseif ($this->status_id == 2) {
			$this->status_id = 1;
		}
	}
}

?>

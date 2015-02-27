<?php
class planet_mission {
	public $id = 0;
	public $planet_id = 0;
	public $prev_id = 0;
	public $next_id = 0;
	public $mission_id = 0;
	public $object_id = 0;
	public $times = 0;
	public $name = '';
	function __construct($planet_mission_id) {
		global $dblink;
		$query = mysqli_query ( $dblink, 'SELECT id, planet_id, mission_id, object_id, prev_priority, next_priority, times FROM game_planets_mission WHERE id=' . $planet_mission_id );
		if (mysqli_num_rows ( $query ) == 1) {
			$query = mysqli_fetch_array ( $query );
			$this->id = $query ['id'];
			$this->planet_id = $query ['planet_id'];
			$this->mission_id = $query ['mission_id'];
			if ($this->mission_id == 1) {
				$this->name = 'Zusammensetzen nach Bauplan';
			} elseif ($this->mission_id == 2) {
				$this->name = 'Startrampe beladen';
			}
			$this->object_id = $query ['object_id'];
			$this->prev_id = $query ['prev_priority'];
			$this->next_id = $query ['next_priority'];
			$this->times = $query ['times'];
		}
	}
	function __destruct() {
		if ($this->id != 0) {
			global $dblink;
			$query = '';
			$query = mysqli_query ( $dblink, 'UPDATE game_planets_mission SET prev_priority=' . $this->prev_id . ',next_priority=' . $this->next_id . ',times=' . $this->times . ' WHERE id=' . $this->id );
		}
	}
	function delete() {
		if ($this->id != 0) {
			if ($this->prev_id > 0) {
				$prev = new planet_mission ( $this->prev_id );
				$prev->next_id = $this->next_id;
				unset ( $prev );
			}
			if ($this->next_id > 0) {
				$next = new planet_mission ( $this->next_id );
				$next->prev_id = $this->prev_id;
				unset ( $next );
			}

			global $dblink;
			$del = '';
			$del = mysqli_query ( $dblink, 'DELETE FROM game_planets_mission WHERE id=' . $this->id );
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
			$a = new planet_mission ( $this->prev_id );
			
			$xid = $a->prev_id;
			$aid = $this->prev_id;
			$bid = $this->id;
			$yid = $this->next_id;
			
			// suche und setze x
			if ($xid != 0) {
				$x = new planet_mission ( $xid );
				$x->next_id = $bid;
				unset ( $x );
			}
			// suche und setze y
			if ($yid != 0) {
				$y = new planet_mission ( $yid );
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
			$b = new planet_mission ( $this->next_id );
			
			$xid = $this->prev_id;
			$aid = $this->id;
			$bid = $this->next_id;
			$yid = $b->next_id;
			
			// suche und setze x
			if ($xid != 0) {
				$x = new planet_mission ( $xid );
				$x->next_id = $bid;
				unset ( $x );
			}
			// suche und setze y
			if ($yid != 0) {
				$y = new planet_mission ( $yid );
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
}

?>

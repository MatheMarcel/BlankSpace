<?php
include_once ("dbconnect.php");
$playerlist = getlist ( 'players' );

foreach ( $playerlist as $player ) {
	$planetlist = $player->planets ();
	foreach ( $planetlist as $planet ) {
		$faktor = 1;
		$points = $faktor * $planet->craftsmen;
		// verteile baupunkte
		$break = 0;
		while ( $points > 0 ) {
			$break = $points;
			$constructions = $planet->constructions ();
			foreach ( $constructions as $row ) {
				if ($row->status_id == 1 && $row->points < $row->cost) {
					if ($points > 0) {
						$row->add_point ();
						$points --;
					}
				}
				unset ( $row );
			}
			unset ( $constructions );
			if ($break == $points) {
				$points = 0;
			}
		}
		
		// teste auf fertige gebäude/units
		$constructions = $planet->constructions ();
		$list = array ();
		foreach ( $constructions as $row ) {
			array_push ( $list, $row->id );
		}
		unset ( $constructions );
		foreach ( $list as $row ) {
			$con = new construction ( $row );
			$con->building_done ();
			$con->module_done ();
			unset ( $con );
		}
	}
	unset ( $planetlist );
}
unset ( $playerlist );
?>
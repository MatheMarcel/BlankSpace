<?php
class master {
	public $round = 0;
	public $lastround = 0;
	public $nextround = 0;
	function __construct() {
		global $dblink;
		$array = mysqli_query ( $dblink, 'SELECT wert FROM game_master WHERE variable="Runde"' );
		$array = mysqli_fetch_array ( $array );
		$this->round = $array ["wert"];
		
		$array = mysqli_query ( $dblink, 'SELECT wert FROM game_master WHERE variable="LastRound"' );
		$array = mysqli_fetch_array ( $array );
		$this->lastround = $array ["wert"];
		
		$array = mysqli_query ( $dblink, 'SELECT wert FROM game_master WHERE variable="NextRound"' );
		$array = mysqli_fetch_array ( $array );
		$this->nextround = $array ["wert"];
	}
}
?>
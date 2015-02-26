<?php
class clan {
	public $id = 0;
	public $name = '';
	public $password = '';
	public $leader_id = '';
	function __construct($clanid) {
		if ($clanid != 0) {
			global $dblink;
			$array = mysqli_query ( $dblink, 'SELECT id,name,password,leader_id FROM game_clans WHERE id=' . $clanid );
			if (mysqli_num_rows ( $array ) == 1) {
				$array = mysqli_fetch_array ( $array );
				$this->id = $array ["id"];
				$this->name = $array ["name"];
				$this->password = $array ["password"];
				$this->leader_id = $array ["leader_id"];
			}
		}
	}
	function __destruct() {
		if ($this->id != 0) {
			global $dblink;
			$query = '';
			$query = mysqli_query ( $dblink, 'UPDATE game_clans SET name=' . $this->name . ',password=' . $this->password . ',leader_id=' . $this->leader_id . ' WHERE id=' . $this->id );
		}
	}
	
	/**
	 * Diese Funktion liefert die Anzahl der Personen in diesem Clan.
	 */
	function count_members() {
		if ($this->id != 0) {
			$query = '';
			global $dblink;
			$query = mysqli_query ( $dblink, 'SELECT count(*) as anz FROM game_players WHERE clan_id=' . $this->id );
			$query = mysqli_fetch_array ( $query );
			return $query ['anz'];
		}
	}
}
?>
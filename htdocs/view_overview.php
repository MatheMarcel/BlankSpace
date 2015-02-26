<?php
if ($player->id != 0) {
	if ($player->clan->id != 0) {
		echo "Du bist im Clan: " . $player->clan->name . ". ";
		echo "Anzahl Mitglieder in diesem Clan: " . $player->clan->count_members () . ". ";
		$leader = new player ( $player->clan->leader_id );
		echo "Anführer des Clans: " . $leader->name . ". ";
	} else {
		echo "Du hast keinen Clan.";
	}
	echo "<br/>Du bekommt in dieser Runde " . ($player->calc_research_points ()) . " Techpunkt(e).";
}
?>

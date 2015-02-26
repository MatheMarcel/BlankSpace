<?php
session_start ();
// <!DOCTYPE html>
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xml:lang="de" xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="language" content="deutsch" />
<title>Game</title>
</head>
<body>

<?php
// Ohne Verschlüsselung wird nichts angezeigt.
if ($_SERVER ['HTTPS'] == "on") {
	include ("dbconnect.php");
	include ("includes/classes.inc");
	
	// Login per Cookie, setzen der SESSION
	if (! isset ( $_SESSION ['gameuserid'] )) {
		$_SESSION ['gameuserid'] = 0;
	}
	if ($_SESSION ['gameuserid'] == 0) {
		if (isset ( $_COOKIE ['gameuserpass'] )) {
			global $dblink;
			$result = mysqli_query ( $dblink, "SELECT id,cookie FROM game_players_cookies WHERE cookie=\"" . mysqli_real_escape_string ( $dblink, $_COOKIE ['gameuserpass'] ) . "\"" );
			$result = mysqli_fetch_assoc ( $result );
			
			if ($_COOKIE ['gameusername'] == $result ['id']) {
				$res = mysqli_query ( $dblink, "SELECT name FROM game_players WHERE id=\"" . mysqli_real_escape_string ( $dblink, $result ['id'] ) . "\"" );
				$res = mysqli_fetch_assoc ( $res );
				$_SESSION ['gameusername'] = $res ['name'];
				$_SESSION ['gameuserid'] = $result ['id'];
			}
		}
	}
	
	if ($_SESSION ['gameuserid'] != 0) {
		$player = 0;
		$player = new player ( $_SESSION ['gameuserid'] );
		include ("header.php");
		
		// Spezies aufgeben und neu beginnen
		if (isset ( $_GET ['restart'] )) {
			$restart = htmlspecialchars ( $_GET ['restart'] );
			if ($restart == 1) {
				// lösche units:
				$player->del_all_units ();
				
				$list = $player->planets ();
				foreach ( $list as $row ) {
					// lösche constructions:
					$row->del_all_constructions ();
					// lösche buildings:
					$row->del_all_buildings ();
				}
				unset ( $list );
				// lösche unit_types:
				$list = $player->list_unit_types ();
				foreach ( $list as $row ) {
					$row->del_all_modules ();
				}
				unset ( $list );
				$player->delete_all_unit_types ();
				// lösche forschung
				$player->delete_all_research ();
				// lösche forschungspunkte
				// todo
				// lösche planets
				$player->del_all_planets ();
				// new home planet
				$newhome = mysqli_query ( 'SELECT id FROM game_planets WHERE player_id = 0 AND type_id = 2 ORDER BY RAND() LIMIT 0,1' );
				$newhome = mysqli_fetch_array ( $newhome );
				$player->homeplanet = $newhome ['id'];
				$input = mysqli_query ( 'UPDATE game_players SET home_id = ' . $player->homeplanet . ' WHERE id=' . $player->id );
				$input = mysqli_query ( 'UPDATE game_planets SET population=1,farmers=1,player_id = ' . $player->id . ' WHERE id=' . $player->homeplanet );
			}
		}
		
		$view = "";
		if (isset ( $_GET ['v'] )) {
			$view = htmlspecialchars ( $_GET ['v'] );
		}
		
		if ($view == "technol") {
			include ("view_technol.php");
		} elseif ($view == "planets") {
			include ("view_planets.php");
		} elseif ($view == "planet") {
			include ("view_planet.php");
		} elseif ($view == "planetbahn") {
			include ("view_planetbahn.php");
		} elseif ($view == "starsys") {
			include ("view_starsystem.php");
		} elseif ($view == "unittypes") {
			include ("view_unit_types.php");
		} elseif ($view == "newunittype") {
			include ("view_new_unit_type.php");
		} elseif ($view == "admin") {
			include ("view_admin.php");
		} else {
			include ("view_overview.php");
		}
		
		echo '<hr/>';
		
		if (isset ( $_GET ['rundefertig'] )) {
			$rundefertig = "";
			$rundefertig = htmlspecialchars ( $_GET ['rundefertig'] );
			
			$set = "";
			$set = mysqli_query ( $dblink, 'UPDATE game_players SET rundefertig = 1 WHERE id=' . $_SESSION ['gameuserid'] );
			
			include ("cronjob.php");
		}
		
		if (isset ( $_GET ['dellog'] )) {
			$player->del_log ( htmlspecialchars ( $_GET ['dellog'] ) );
		}
		echo '<table width="100%"><tr><td>';
		echo '<table border=1><tr><th>Runde</th><th>Log</th><th></th></tr>';
		$log = $player->list_log ();
		if (count ( $log ) == 0) {
			echo '<tr><td></td><td>kein log</td><td></td></tr>';
		} else {
			foreach ( $log as $row ) {
				echo '<tr><td>' . $row ['round_id'] . '</td><td>';
				if ($row ['text_id'] == 1) {
					echo '<a href="./index.php?v=planet&id=' . $row ['ziel_id'] . '">Planet</a> hat Bevölkerung verloren!';
				} elseif ($row ['text_id'] == 2) {
					echo '<a href="./index.php?v=planet&id=' . $row ['ziel_id'] . '">Planet</a> hat untätige Bevölkerung.';
				} elseif ($row ['text_id'] == 3) {
					echo '<a href="./index.php?v=planet&id=' . $row ['ziel_id'] . '">Planet</a> hat Konstruktion vollendet.';
				} elseif ($row ['text_id'] == 2) {
					$tech = new research ( $row ['ziel_id'] );
					echo 'Eine neue Technologie wurde entdeckt:' . $tech->name . '.';
				}
				echo '</td><td><a href="./index.php?dellog=' . $row ['id'] . '"><img src="./pics/icons/Delete.svg" height="24px"/></a></td></tr>';
			}
		}
		echo '</table>';
		echo '</td><td>';
		// Liste der letzten 5 Runden
		echo '<table border=1><tr><th>Runde</th><th>Beginn</th></tr>';
		$runden = "";
		$runden = mysqli_query ( $dblink, "SELECT nummer, beginn FROM game_round ORDER BY nummer desc limit 0,5" );
		while ( $zeile = mysqli_fetch_object ( $runden ) ) {
			echo '<tr><td align="center">' . $zeile->nummer . '</td><td>' . date ( "d.m.Y H:i:s", $zeile->beginn ) . '</td></tr>';
		}
		echo '<tr><td align="center">...</td><td></td></tr></table>';
		echo '</td>';
		
		echo '<td>';
		// Liste der Spieler, Möglichkeit Runde zu beenden
		echo '<table border=1><tr><th>Spieler</th><th>Runde beendet</th></tr>';
		$rundefertig = "";
		$rundefertig = mysqli_query ( $dblink, 'SELECT id, name, rundefertig FROM game_players' );
		while ( $zeile = mysqli_fetch_object ( $rundefertig ) ) {
			echo '<tr><td align="center">' . $zeile->name . '</td>';
			if ($zeile->rundefertig == "0") {
				echo '<td>nein';
				if ($zeile->id == $_SESSION ['gameuserid']) {
					echo ' <a href="./index.php?rundefertig=1">Runde beenden?</a>';
				}
				echo '</td>';
			} else {
				echo '<td>ja</td>';
			}
			echo '</tr>';
		}
		echo '</table>';
		$zeit = mysqli_query ( $dblink, 'SELECT wert FROM game_master WHERE Variable="NextRound"' );
		$zeit = mysqli_fetch_array ( $zeit );
		echo '<br/>Nächste Runde startet:<br/>- wenn Spieleranzahl-1 bereit sind oder<br/>- wenn ' . (($zeit ['wert'] + 1) / 60) . ' Stunden vergangen sind.';
		echo '</td>';
		
		echo '</tr></table>';
	} else {
		echo 'Please log in! <a href="./login.php">Login</a>';
	}
} else {
	echo 'Please use an encrypted connection! <a href="https://fsmath.mathematik.tu-dortmund.de/game/">Link to encrypted website.</a>';
}
?>
</body>
</html>
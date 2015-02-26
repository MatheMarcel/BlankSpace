<?php
session_start ()?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xml:lang="de" xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="language" content="deutsch" />
<title>Game</title>
</head>
<body>

<?php
if ($_SERVER ['HTTPS'] == "on") {
	include ("../dbconnect.php");
	if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
		$username = "";
		$username = htmlspecialchars ( $_POST ['username'] );
		$password = "";
		$password = htmlspecialchars ( $_POST ['password'] );
		
		$result = mysqli_query ( $dblink, "SELECT id,password FROM game_players WHERE name=\"" . mysqli_real_escape_string ( $dblink, $username ) . "\"" );
		$result = mysqli_fetch_assoc ( $result );
		if (strlen ( $result ['password'] ) < 1) {
			echo "Login fehlerhaft. Username oder Password falsch.";
		} else {
			$saltedPassword = $result ['password'];
			$salt = substr ( $saltedPassword, 0, 12 );
			$hash = substr ( $saltedPassword, 12 );
			$globsalt = 'new salt already installed:P';
			$pass = hash ( 'whirlpool', $globsalt . $password . $salt );
			
			if ($pass == $hash) {
				$_SESSION ['gameusername'] = $username;
				$_SESSION ['gameuserid'] = $result ['id'];
				echo 'Login erfolgreich. Willkommen ' . $username . '. <a href="../index.php">Zum Spiel</a>';
				
				$rando = hash ( 'whirlpool', substr ( uniqid ( rand (), true ), 0, 30 ) );
				$cook = mysqli_query ( $dblink, "INSERT INTO game_players_cookies (id,cookie,date) VALUES (" . mysqli_real_escape_string ( $dblink, $_SESSION ['gameuserid'] ) . ", \"" . $rando . "\", NOW())" );
				setcookie ( "gameusername", $_SESSION ['gameuserid'], time () + (3600 * 24 * 30 * 6), "", "", TRUE );
				setcookie ( "gameuserpass", $rando, time () + (3600 * 24 * 30 * 6), "", "", TRUE );
			} else {
				echo "Login fehlerhaft. Username oder Passwort falsch.";
			}
		}
	}
} else {
	echo "Achtung: Nutzen Sie verschlüsselte Übertragung! <a href=\"https://fsmath.mathematik.tu-dortmund.de/game/user/login.php\">verschlüsselte Webseite</a>";
}
?>
</body>
</html>
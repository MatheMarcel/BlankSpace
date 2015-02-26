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
		$pwcusername = htmlspecialchars ( $_POST ['pwcusername'] );
		$pwccode = htmlspecialchars ( $_POST ['pwccode'] );
		$pwcpassword = htmlspecialchars ( $_POST ['pwcpassword'] );
		
		$result = mysqli_query ( $dblink, 'SELECT id,pwchange FROM game_players WHERE name="' . mysqli_real_escape_string ( $dblink, $pwcusername ) . '"' );
		$result = mysqli_fetch_assoc ( $result );
		$rightcode = $result ['pwchange'];
		
		if ($pwccode != $rightcode || strlen ( $rightcode ) < 10) {
			echo "Username und Code passen nicht zusammen. Bitte anderen wählen!";
		} elseif (strlen ( $pwcpassword ) < 6) {
			echo "Passwort zu kurz. Bitte mehr als 5 Zeichen wählen!";
		} else {
			$salt = substr ( hash ( 'whirlpool', substr ( uniqid ( rand (), true ), 0, 12 ) ), 0, 12 );
			$globsalt = 'new salt already installed :P';
			$pwcpassword = hash ( 'whirlpool', $globsalt . $pwcpassword . $salt );
			$result = mysqli_query ( $dblink, "UPDATE game_players set password=\"" . $salt . $pwcpassword . "\", pwchange=\"-\" WHERE id=" . $result ['id'] );
			echo "Passwort erfolgreich geändert.";
		}
	}
} else {
	echo "Achtung: Nutzen Sie verschlüsselte Übertragung! <a href=\"https://fsmath.mathematik.tu-dortmund.de/game/user/verifyemail.php\">verschlüsselte Webseite</a>";
}
?>
</body>
</html>
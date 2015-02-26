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
		$regusername = htmlspecialchars ( $_POST ['regusername'] );
		$regemail = htmlspecialchars ( $_POST ['regemail'] );
		$regemail = str_replace ( ";", "", str_replace ( ",", "", $regemail ) );
		
		$userexists = mysqli_query ( $dblink, 'SELECT id,password FROM game_players WHERE name="' . mysqli_real_escape_string ( $dblink, $regusername ) . '"' );
		$userexists = mysqli_fetch_assoc ( $userexists );
		$saltedPassword = $userexists ['password'];
		
		if ($saltedPassword != 0) {
			echo "Username existiert bereits. Bitte anderen wählen! <a href=\"./register.php\">Zurück zur Registration</a>";
		} else {
			$pwchange = hash ( 'whirlpool', substr ( uniqid ( rand (), true ), 0, 40 ) );
			$datum = date ( 'd.m.Y H:i:s' );
			$empfaenger = $regemail;
			$absendername = "FS-Game";
			$absendermail = "game@fsmath.mathematik.tu-dortmund.de";
			$betreff = "Neues Passwort";
			$text = "Nachricht vom FS-Game:

Am $datum wurde ein Benutzer mit dem Usernamen $regusername und mit Ihrer Email angelegt.
Bitte besuchen Sie die Seite https://fsmath.mathematik.tu-dortmund.de/game/user/verifyemail.php und geben Sie folgenden Code ein:

$pwchange

";
			$header = "From: $absendername <$absendermail>";
			mail ( $empfaenger, $betreff, $text, $header );
			
			$result = "";
			$result = mysqli_query ( $dblink, "INSERT INTO game_players (name, password, email, pwchange) VALUES (\"" . mysqli_real_escape_string ( $dblink, $regusername ) . "\", \"123\", \"" . mysqli_real_escape_string ( $dblink, $regemail ) . "\",\"" . mysqli_real_escape_string ( $dblink, $pwchange ) . "\")" );
			
			echo 'Eine Email mit ihrem persönlichen Zugangscode wurde verschickt. Zur <a href="./verifyemail.php">Code-Eingabe</a>.';
		}
	}
} else {
	echo "Achtung: Nutzen Sie verschlüsselte Übertragung! <a href=\"https://fsmath.mathematik.tu-dortmund.de/game/user/register.php\">verschlüsselte Webseite</a>";
}
?>
</body>
</html>
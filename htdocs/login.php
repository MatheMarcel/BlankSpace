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
	echo "Hinweis: Zum Login beim FS-Game wird von dieser Seite ein Cookie gesetzt. Sie brauchen sich nur einmal an einem PC anmelden.<br/>Wenn sie sich an fremden PCs anmelden, loggen sie sich nach dem Spielen aus!";
	echo "<form action=\"./login2.php\" method=\"post\">";
	echo "Username: <input type=\"text\" name=\"username\" /><br />";
	echo "Passwort: <input type=\"password\" name=\"password\" /><br />";
	echo "<input type=\"submit\" value=\"Anmelden\" />";
	echo '</form><br/><br/>Sie haben keine Login-Daten? Wollen Sie sich registrieren? <a href="./register.php">Registrieren</a>';
} else {
	echo "Achtung: Nutzen Sie verschlüsselte Übertragung! <a href=\"https://fsmath.mathematik.tu-dortmund.de/game/login.php\">verschlüsselte Webseite</a>";
}
?>
</body>
</html>
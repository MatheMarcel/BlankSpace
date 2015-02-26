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
	echo "<form action=\"./verifyemail2.php\" method=\"post\">";
	echo "Username: <input type=\"text\" name=\"pwcusername\" /><br />";
	echo "Code: <input type=\"text\" name=\"pwccode\" /><br />";
	echo "Neues Passwort: <input type=\"password\" name=\"pwcpassword\" /><br />";
	echo "<input type=\"submit\" value=\"neues Passwort speichern\" />";
	echo "</form>";
} else {
	echo "Achtung: Nutzen Sie verschlüsselte Übertragung! <a href=\"https://fsmath.mathematik.tu-dortmund.de/game/user/verifyemail.php\">verschlüsselte Webseite</a>";
}
?>
</body>
</html>
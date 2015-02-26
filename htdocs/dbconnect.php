<?php
	// Verbindung mit der Datenbank herstellen.
	//mysql_connect("host","user","password");
	$dblink = mysqli_connect("localhost","game","passwort");
	mysqli_select_db($dblink,"game");
?>
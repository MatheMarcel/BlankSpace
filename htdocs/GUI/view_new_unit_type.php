<?php
if ($player->id != 0) {
	if (isset ( $_POST ['name'] )) {
		$unitname = htmlspecialchars ( $_POST ['name'] );
		if (strlen ( $unitname ) > 0 && count ( $_SESSION ['unittypes'] ) > 1) {
			$module_array = array ();
			foreach ( array_keys ( $_SESSION ['unittypes'] ) as $key ) {
				for($i = 0; $i < $_SESSION ['unittypes'] [$key]; $i ++) {
					array_push ( $module_array, $key );
				}
			}
			sort ( $module_array );
			if ($player->get_unit_type ( $module_array ) == 0) {
				// neuen unit-type speichern
				$player->new_unit_type ( $module_array, $unitname );
				unset ( $_SESSION ['unittypes'] );
				echo 'Gespeichert!';
			} else {
				$unittype = new unit_type ( $player->get_unit_type ( $module_array ) );
				echo 'Zusammenstellung schon vorhanden mit Namen: ' . $unittype->name . '.';
			}
		} else {
			echo 'Kein Name oder zu wenig Module ausgewählt (mind. 2).';
		}
	}
}
echo '<br/><br/>Zurück <a href="./index.php?v=unittypes">zur Auswahl</a>.'?>

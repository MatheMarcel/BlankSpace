<?php
// $syslist = getlist('starsystems');
// foreach($syslist as $row){
// $row->move(24*7);
// }
// unset($syslist);

// TODO: teste ob admin!
if ($player->id != 0) {
	echo '<a href="./index.php?v=admin&amp;w=t">Techs</a> ';
	echo '<a href="./index.php?v=admin&amp;w=b">Buildings</a> ';
	echo '<a href="./index.php?v=admin&amp;w=m">Modules</a>';
	echo '<br/><br/>';
	
	$w = '';
	if (isset ( $_GET ['w'] )) {
		$w = htmlspecialchars ( $_GET ['w'] );
	}
	switch ($w) {
		
		case 'm' :
			if (isset ( $_POST ['newname'] )) {
				$newname = htmlspecialchars ( $_POST ['newname'] );
				$newcost = htmlspecialchars ( $_POST ['newcost'] );
				$newinfo = htmlspecialchars ( $_POST ['newinfo'] );
				
				admin_new_module ( $newname, $newcost, $newinfo );
			}
			
			echo 'Edit modules';
			$list = getlist ( 'module_types' );
			echo '<table border=1><tr><th>id</th><th colspan=2>Name</th><th>Kosten</th><th>Benötigt Gebäude</th><th>Benötigt Tech</th></tr>';
			foreach ( $list as $row ) {
				echo '<tr><td>' . $row->id . '</td><td>' . $row->name . '</td><td><img src="pics/icons/Help-browser.svg" height="20" title="' . $row->info . '" /></td><td align="center">' . $row->cost . '</td><td>' . $row->list_dep () . '</td><td>' . $row->list_techdep () . '</td></tr>';
			}
			echo '</table><br/>';
			echo 'Add module';
			echo '<div style="border-width:1px; border-style:solid; padding:0.5em;">';
			echo '<form action="./index.php?v=admin&amp;w=m" method="post">';
			echo 'Modulname: <input name="newname" type="text" size="30" maxlength="30"/><br/>';
			echo 'Modulkosten: <input name="newcost" type="text" size="10" maxlength="10"/><br/>';
			echo 'Modulinfo: <input name="newinfo" type="text" size="30" maxlength="50"/><br/>';
			echo '<input type="submit" value=" Speichern "/>';
			echo '</form>';
			echo '</div>';
			break;
		
		case 'b' :
			if (isset ( $_POST ['newname'] )) {
				$newname = htmlspecialchars ( $_POST ['newname'] );
				$newcost = htmlspecialchars ( $_POST ['newcost'] );
				$newinfo = htmlspecialchars ( $_POST ['newinfo'] );
				
				admin_new_building ( $newname, $newcost, $newinfo );
			}
			
			echo 'Edit buildings';
			$list = getlist ( 'building_types' );
			echo '<table border=1><tr><th>id</th><th colspan=2>Name</th><th>Kosten</th><th>Benötigt Gebäude</th><th>Benötigt Tech</th></tr>';
			foreach ( $list as $row ) {
				echo '<tr><td>' . $row->id . '</td><td>' . $row->name . '</td><td><img src="pics/icons/Help-browser.svg" height="20" title="' . $row->info . '" /></td><td align="center">' . $row->cost . '</td><td>' . $row->list_dep () . '</td><td>' . $row->list_techdep () . '</td></tr>';
			}
			echo '</table><br/>';
			echo 'Add building';
			echo '<div style="border-width:1px; border-style:solid; padding:0.5em;">';
			echo '<form action="./index.php?v=admin&amp;w=b" method="post">';
			echo 'Gebäudename: <input name="newname" type="text" size="30" maxlength="30"/><br/>';
			echo 'Gebäudekosten: <input name="newcost" type="text" size="10" maxlength="10"/><br/>';
			echo 'Gebäudeinfo: <input name="newinfo" type="text" size="30" maxlength="50"/><br/>';
			echo '<input type="submit" value=" Speichern "/>';
			echo '</form>';
			echo '</div>';
			break;
		
		default :
			if (isset ( $_POST ['newname'] )) {
				$newname = htmlspecialchars ( $_POST ['newname'] );
				$newcost = htmlspecialchars ( $_POST ['newcost'] );
				$newfield = htmlspecialchars ( $_POST ['newtechfield'] );
				$newinfo = htmlspecialchars ( $_POST ['newinfo'] );
				
				$tfield = new tech_field ( $newfield );
				$tfield->add ( $newname, $newcost, $newinfo );
			}
			
			echo 'Edit techs';
			$list = getlist ( 'tech' );
			echo '<table border=1><tr><th>id</th><th colspan=2>Name</th><th>Kosten</th><th>Feld</th><th>Benötigt Tech</th><th>Benötigt von Gebäude</th><th>Benötigt von Modul</th></tr>';
			foreach ( $list as $row ) {
				echo '<tr><td align="center">' . $row->id . '</td><td>' . $row->name . '</td>';
				echo '<td><img src="pics/icons/Help-browser.svg" height="20" title="' . $row->info . '" /></td><td align="center">' . $row->cost . '</td><td>' . $row->tech_field->name . '</td><td>' . $row->list_dep () . '</td><td>' . $row->list_building_use () . '</td><td>' . $row->list_module_use () . '</td></tr>';
			}
			echo '</table><br/>';
			echo 'Add tech';
			echo '<div style="border-width:1px; border-style:solid; padding:0.5em;">';
			$list = getlist ( 'techfields' );
			echo '<form action="./index.php?v=admin" method="post">';
			echo 'Techname: <input name="newname" type="text" size="30" maxlength="30"/><br/>';
			echo 'Techkosten: <input name="newcost" type="text" size="10" maxlength="10"/><br/>';
			echo 'Techfeld: <select name="newtechfield" size="1">';
			foreach ( $list as $row ) {
				echo '<option value="' . $row->id . '">' . $row->name . '</option>';
			}
			echo '</select><br/>';
			echo 'Techinfo: <input name="newinfo" type="text" size="30" maxlength="50"/><br/>';
			echo '<input type="submit" value=" Speichern "/>';
			echo '</form>';
			echo '</div>';
			break;
	}
}
?>

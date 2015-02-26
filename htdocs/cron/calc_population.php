<?php
include_once ("dbconnect.php");

$list = getlist ( 'players' );
foreach ( $list as $row ) {
	$list2 = $row->planets ();
	foreach ( $list2 as $row2 ) {
		if ($row2->usedpop () < floor ( $row2->population )) {
			$row->add_log ( 2, $row2->id );
		}
		
		// Abhängig von planet_class und zufall
		$planet_max_pop = 10;
		
		// Abhängig von Spezies
		$population_fertility = 1.1;
		
		// Food = Farmers * planeten-faktoren * weitere faktoren
		$food = $row2->farmers * 2 * 1;
		
		if ($food >= $row2->population) {
			// logistisches wachstum
			// $neupop = $row['population']/$planet_max_pop + $population_fertility * $row['population']/$planet_max_pop * (1- $row['population']/$planet_max_pop);
			$neupop = $row2->population;
			$neupop *= $population_fertility;
		} else {
			// rückgang
			$row->add_log ( 1, $row2->id );
			$neupop = $food;
		}
		
		$neupop = min ( $food, $neupop );
		$neupop = min ( $planet_max_pop, $neupop );
		$neupop = max ( 1, $neupop ); // debug: nicht bevölkerung von 0 erlauben
		$neupop = floor ( $neupop * 10 ) / 10;
		
		// echo ' '.$row2->population.'->'.$neupop.' ';
		$row2->population = $neupop;
		unset ( $row2 );
	}
	unset ( $list2 );
}
unset ( $list );
?>
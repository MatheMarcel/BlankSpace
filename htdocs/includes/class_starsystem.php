<?php
class starsystem {
	public $id = 0;
	public $pos_x = 0;
	public $pos_y = 0;
	public $vel_x = 0;
	public $vel_y = 0;
	public $mass = 0;
	function __construct($starsystem_id) {
		if ($starsystem_id != 0) {
			global $dblink;
			$array = mysqli_query ( $dblink, 'SELECT id,pos_x,pos_y,vel_x,vel_y,mass FROM game_starsystems WHERE id=' . $starsystem_id );
			if (mysqli_num_rows ( $array ) == 1) {
				$array = mysqli_fetch_array ( $array );
				$this->id = $array ["id"];
				$this->pos_x = $array ["pos_x"];
				$this->pos_y = $array ["pos_y"];
				$this->vel_x = $array ["vel_x"];
				$this->vel_y = $array ["vel_y"];
				$this->mass = $array ["mass"];
			}
		}
	}
	
	/**
	 * Diese Funktion liefert eine Liste aller Planeten (inkl.
	 * Sonnen und Monde) dieses Sonnensystems als Klassen.
	 */
	function planets() {
		$list = array ();
		if ($this->id != 0) {
			global $dblink;
			$array = mysqli_query ( $dblink, 'SELECT id FROM game_planets WHERE starsystem_id=' . $this->id . ' ORDER BY id' );
			if (mysqli_num_rows ( $array ) > 0) {
				while ( $row = mysqli_fetch_array ( $array ) ) {
					array_push ( $list, new planet ( $row ['id'] ) );
				}
			}
		}
		return $list;
	}
	
	/**
	 * Diese Funktion liefert eine Liste aller Sonnen dieses Sonnensystems als Klassen.
	 */
	function sun() {
		$sun = '';
		if ($this->id != 0) {
			global $dblink;
			$query = mysqli_query ( $dblink, 'SELECT id FROM game_planets WHERE type_id=1 AND starsystem_id=' . $this->id );
			$query = mysqli_fetch_array ( $query );
			$sun = new planet ( $query ['id'] );
		}
		return $sun;
	}
	
	/**
	 * Diese Funktion liefert eine Liste aller Monde dieses Sonnensystems als Klassen.
	 */
	function moons() {
		$list = array ();
		if ($this->id != 0) {
			global $dblink;
			$array = mysqli_query ( $dblink, 'SELECT id FROM game_planets WHERE type_id=3 AND starsystem_id=' . $this->id );
			if (mysqli_num_rows ( $array ) > 0) {
				while ( $row = mysqli_fetch_array ( $array ) ) {
					array_push ( $list, new planet ( $row ['id'] ) );
				}
			}
		}
		return $list;
	}
	
	/**
	 * Diese Funktion berechnet die Gesamtenergie des Sonnensystems (kinetische + potentielle Energie).
	 * Dieser Wert sollte zeitlich konstant bleiben!
	 */
	function hamilton() {
		$ham = 0;
		if ($this->id != 0) {
			$obj = $this->planets ();
			foreach ( $obj as $row ) {
				$ham += $row->mass * ($row->vel_x * $row->vel_x + $row->vel_y * $row->vel_y);
			}
			$hamd = 0;
			foreach ( $obj as $row_i ) {
				foreach ( $obj as $row_j ) {
					if ($row_i->id > $row_j->id) {
						$hamd += $row_i->mass * $row_j->mass / sqrt ( ($row_i->pos_x - $row_j->pos_x) * ($row_i->pos_x - $row_j->pos_x) + ($row_i->pos_y - $row_j->pos_y) * ($row_i->pos_y - $row_j->pos_y) );
					}
				}
			}
			unset ( $obj );
			$ham = 0.5 * $ham - gamma () * $hamd;
		}
		return $ham;
	}
	
	/**
	 * Diese Funktion bewegt die Planeten (inkl.
	 * Sonnen und Monde) dieses Sonnensystems.
	 * Die Zeit $time wird in Stunden gemessen. Für eine Woche also 24*7 eingeben.
	 *
	 * TODO: Kollision entdecken und Schrittweiten kontrollieren
	 */
	function move($time) {
		if ($this->id != 0) {
			$liste = '';
			$liste = $this->planets ();
			global $dblink;
			foreach ( $liste as $row ) {
				$query = mysqli_query ( $dblink, 'INSERT INTO game_oldmove SET planet_id=' . $row->id . ', pos_x=' . $row->pos_x . ',pos_y=' . $row->pos_y . ',vel_x=' . $row->vel_x . ',vel_y=' . $row->vel_y );
			}
			$num_planets = 0;
			$num_planets = count ( $liste );
			for($ind = 0; $ind <= $time; $ind ++) {
				$planets_forces = array ();
				for($i = 0; $i < $num_planets; $i ++) {
					$planets_forces ['x'] [$i] = 0;
					$planets_forces ['y'] [$i] = 0;
				}
				for($i = 0; $i < $num_planets; $i ++) {
					for($j = $i + 1; $j < $num_planets; $j ++) {
						
						$sqdist = pow ( ($liste [$i]->pos_x - $liste [$j]->pos_x) * 1000, 2 ) + pow ( ($liste [$i]->pos_y - $liste [$j]->pos_y) * 1000, 2 );
						$force = gamma () * $liste [$i]->mass * $liste [$j]->mass / $sqdist;
						
						if ($sqdist < 1000000) {
							// echo "Crash!";
						}
						
						$force_diff_x = ($liste [$i]->pos_x - $liste [$j]->pos_x) * 1000 / sqrt ( $sqdist ) * $force;
						$force_diff_y = ($liste [$i]->pos_y - $liste [$j]->pos_y) * 1000 / sqrt ( $sqdist ) * $force;
						
						$planets_forces ['x'] [$i] -= $force_diff_x;
						$planets_forces ['y'] [$i] -= $force_diff_y;
						$planets_forces ['x'] [$j] += $force_diff_x;
						$planets_forces ['y'] [$j] += $force_diff_y;
					}
					
					$gfaktor = 1;
					$liste [$i]->vel_x += $gfaktor * $planets_forces ['x'] [$i] / $liste [$i]->mass * 60 * 60 / 1000;
					$liste [$i]->vel_y += $gfaktor * $planets_forces ['y'] [$i] / $liste [$i]->mass * 60 * 60 / 1000;
					
					$vfaktor = 1;
					$liste [$i]->pos_x += $vfaktor * $liste [$i]->vel_x;
					$liste [$i]->pos_y += $vfaktor * $liste [$i]->vel_y;
				}
			}
			unset ( $liste );
		}
	}
	
	/**
	 * Diese Funktion bewegt die Planeten (inkl.
	 * Sonnen und Monde) dieses Sonnensystems.
	 * Die Zeit $time wird in Sekunden gemessen.
	 * Bitte Potenzen von 2 angeben, damit halbiert werden kann. Für eine Woche also 524288 eingeben.
	 */
	function move_rk($time) {
		if ($this->id != 0) {
			
			echo "<br/>Runge-Kutta<br/>";
			// Laden der Positionen, Geschwindigkeiten und Massen der Planeten
			$objektliste = $this->planets ();
			$positionen_x = array ();
			$positionen_y = array ();
			$geschwindigkeiten_x = array ();
			$geschwindigkeiten_y = array ();
			$massen = array ();
			for($i = 0; $i < count ( $objektliste ); $i ++) {
				array_push ( $positionen_x, $objektliste [$i]->pos_x * 1000 );
				array_push ( $positionen_y, $objektliste [$i]->pos_y * 1000 );
				array_push ( $geschwindigkeiten_x, $objektliste [$i]->vel_x * 1000 / 60 / 60 );
				array_push ( $geschwindigkeiten_y, $objektliste [$i]->vel_y * 1000 / 60 / 60 );
				array_push ( $massen, $objektliste [$i]->mass );
			}
			
			// Initialisiere Schleifenwerte
			$berechnete_zeit = 0;
			$schritte = 2;
			$schrittweite = $time / $schritte;
			$new_pos_x = $positionen_x;
			$new_pos_y = $positionen_y;
			$new_vel_x = $geschwindigkeiten_x;
			$new_vel_y = $geschwindigkeiten_y;
			$new_hamilton = hamilton ( $new_pos_x, $new_pos_y, $new_vel_x, $new_vel_y, $massen );
			echo "Vorher pos: <pre>";
			print_r ( $new_pos_x );
			echo "</pre><br/>";
			echo "Vorher vel: <pre>";
			print_r ( $new_vel_x );
			echo "</pre><br/>";
			// echo "Vorher ham: ".$new_hamilton."<br/>";
			
			while ( $berechnete_zeit < $time ) {
				$old_pos_x = $new_pos_x;
				$old_pos_y = $new_pos_y;
				$old_vel_x = $new_vel_x;
				$old_vel_y = $new_vel_y;
				$old_hamilton = $new_hamilton;
				
				// Berechne k_1 für Positionen
				$kp1_x = $old_vel_x;
				$kp1_y = $old_vel_y;
				
				// Berechne k_1 für Geschwindigkeiten
				$kv1_x = array ();
				$kv1_y = array ();
				for($i = 0; $i < count ( $old_pos_x ); $i ++) {
					$tmp_x = 0;
					$tmp_y = 0;
					for($j = 0; $j < count ( $old_pos_x ); $j ++) {
						if ($i != $j) {
							$sqrabs = (pow ( $old_pos_x [$i] - $old_pos_x [$j], 2 ) + pow ( $old_pos_y [$i] - $old_pos_y [$j], 2 ));
							if ($old_pos_x [$i] - $old_pos_x [$j] > 0) {
								$tmp_x -= $massen [$j] / $sqrabs * sqrt ( pow ( $old_pox_x [$i] - $old_pos_x [$j], 2 ) ) / $sqrabs;
							} elseif ($old_pos_x [$i] - $old_pos_x [$j] < 0) {
								$tmp_x += $massen [$j] / $sqrabs * sqrt ( pow ( $old_pox_x [$i] - $old_pos_x [$j], 2 ) ) / $sqrabs;
							}
							if ($old_pos_y [$i] - $old_pos_y [$j] > 0) {
								$tmp_y -= $massen [$j] / $sqrabs * sqrt ( pow ( $old_pox_y [$i] - $old_pos_y [$j], 2 ) ) / $sqrabs;
							} elseif ($old_pos_y [$i] - $old_pos_y [$j] < 0) {
								$tmp_y += $massen [$j] / $sqrabs * sqrt ( pow ( $old_pox_y [$i] - $old_pos_y [$j], 2 ) ) / $sqrabs;
							}
						}
					}
					array_push ( $kv1_x, gamma () * $tmp_x );
					array_push ( $kv1_y, gamma () * $tmp_y );
				}
				
				// Berechne k_2 für Positionen
				for($i = 0; $i < count ( $old_pos_x ); $i ++) {
					$kp2_x [$i] = $old_vel_x [$i] * $kv1_x [$i] * $schrittweite / 2;
					$kp2_y [$i] = $old_vel_y [$i] * $kv1_y [$i] * $schrittweite / 2;
				}
				
				// Berechne k_2 für Geschwindigkeiten
				$kv2_x = array ();
				$kv2_y = array ();
				for($i = 0; $i < count ( $old_pos_x ); $i ++) {
					$tmp_x = 0;
					$tmp_y = 0;
					for($j = 0; $j < count ( $old_pos_x ); $j ++) {
						if ($i != $j) {
							$tmp_xi = $old_pos_x [$i] + $kp1_x [$i] * $schrittweite / 2;
							$tmp_xj = $old_pos_x [$j] + $kp1_x [$j] * $schrittweite / 2;
							$tmp_yi = $old_pos_y [$i] + $kp1_y [$i] * $schrittweite / 2;
							$tmp_yj = $old_pos_y [$j] + $kp1_y [$j] * $schrittweite / 2;
							$sqrabs = pow ( $tmp_xi - $tmp_xj, 2 ) + pow ( $tmp_yi - $tmp_yj );
							if ($tmp_xi - $tmp_xj > 0) {
								$tmp_x -= $massen [$j] / $sqrabs * sqrt ( pow ( $tmp_xi - $tmp_xj, 2 ) ) / $sqrabs;
							} elseif ($tmp_xi - $tmp_xj < 0) {
								$tmp_x += $massen [$j] / $sqrabs * sqrt ( pow ( $tmp_xi - $tmp_xj, 2 ) ) / $sqrabs;
							}
							if ($tmp_yi - $tmp_yj > 0) {
								$tmp_y -= $massen [$j] / $sqrabs * sqrt ( pow ( $tmp_yi - $tmp_yj, 2 ) ) / $sqrabs;
							} elseif ($tmp_yi - $tmp_yj < 0) {
								$tmp_y += $massen [$j] / $sqrabs * sqrt ( pow ( $tmp_yi - $tmp_yj, 2 ) ) / $sqrabs;
							}
						}
					}
					array_push ( $kv2_x, gamma () * $tmp_x );
					array_push ( $kv2_y, gamma () * $tmp_y );
				}
				
				// Berechne k_3 für Positionen
				for($i = 0; $i < count ( $old_pos_x ); $i ++) {
					$kp3_x [$i] = $old_vel_x [$i] * $kv2_x [$i] * $schrittweite / 2;
					$kp3_y [$i] = $old_vel_y [$i] * $kv2_y [$i] * $schrittweite / 2;
				}
				
				// Berechne k_3 für Geschwindigkeiten
				$kv3_x = array ();
				$kv3_y = array ();
				for($i = 0; $i < count ( $old_pos_x ); $i ++) {
					$tmp_x = 0;
					$tmp_y = 0;
					for($j = 0; $j < count ( $old_pos_x ); $j ++) {
						if ($i != $j) {
							$tmp_xi = $old_pos_x [$i] + $kp2_x [$i] * $schrittweite / 2;
							$tmp_xj = $old_pos_x [$j] + $kp2_x [$j] * $schrittweite / 2;
							$tmp_yi = $old_pos_y [$i] + $kp2_y [$i] * $schrittweite / 2;
							$tmp_yj = $old_pos_y [$j] + $kp2_y [$j] * $schrittweite / 2;
							$sqrabs = pow ( $tmp_xi - $tmp_xj, 2 ) + pow ( $tmp_yi - $tmp_yj );
							if ($tmp_xi - $tmp_xj > 0) {
								$tmp_x -= $massen [$j] / $sqrabs * sqrt ( pow ( $tmp_xi - $tmp_xj, 2 ) ) / $sqrabs;
							} elseif ($tmp_xi - $tmp_xj < 0) {
								$tmp_x += $massen [$j] / $sqrabs * sqrt ( pow ( $tmp_xi - $tmp_xj, 2 ) ) / $sqrabs;
							}
							if ($tmp_yi - $tmp_yj > 0) {
								$tmp_y -= $massen [$j] / $sqrabs * sqrt ( pow ( $tmp_yi - $tmp_yj, 2 ) ) / $sqrabs;
							} elseif ($tmp_yi - $tmp_yj < 0) {
								$tmp_y += $massen [$j] / $sqrabs * sqrt ( pow ( $tmp_yi - $tmp_yj, 2 ) ) / $sqrabs;
							}
						}
					}
					array_push ( $kv3_x, gamma () * $tmp_x );
					array_push ( $kv3_y, gamma () * $tmp_y );
				}
				
				// Berechne k_4 für Positionen
				for($i = 0; $i < count ( $old_pos_x ); $i ++) {
					$kp4_x [$i] = $old_vel_x [$i] * $kv3_x [$i] * $schrittweite;
					$kp4_y [$i] = $old_vel_y [$i] * $kv3_y [$i] * $schrittweite;
				}
				
				// Berechne k_4 für Geschwindigkeiten
				$kv4_x = array ();
				$kv4_y = array ();
				for($i = 0; $i < count ( $old_pos_x ); $i ++) {
					$tmp_x = 0;
					$tmp_y = 0;
					for($j = 0; $j < count ( $old_pos_x ); $j ++) {
						if ($i != $j) {
							$tmp_xi = $old_pos_x [$i] + $kp3_x [$i] * $schrittweite;
							$tmp_xj = $old_pos_x [$j] + $kp3_x [$j] * $schrittweite;
							$tmp_yi = $old_pos_y [$i] + $kp3_y [$i] * $schrittweite;
							$tmp_yj = $old_pos_y [$j] + $kp3_y [$j] * $schrittweite;
							$sqrabs = pow ( $tmp_xi - $tmp_xj, 2 ) + pow ( $tmp_yi - $tmp_yj );
							if ($tmp_xi - $tmp_xj > 0) {
								$tmp_x -= $massen [$j] / $sqrabs * sqrt ( pow ( $tmp_xi - $tmp_xj, 2 ) ) / $sqrabs;
							} elseif ($tmp_xi - $tmp_xj < 0) {
								$tmp_x += $massen [$j] / $sqrabs * sqrt ( pow ( $tmp_xi - $tmp_xj, 2 ) ) / $sqrabs;
							}
							if ($tmp_yi - $tmp_yj > 0) {
								$tmp_y -= $massen [$j] / $sqrabs * sqrt ( pow ( $tmp_yi - $tmp_yj, 2 ) ) / $sqrabs;
							} elseif ($tmp_yi - $tmp_yj < 0) {
								$tmp_y += $massen [$j] / $sqrabs * sqrt ( pow ( $tmp_yi - $tmp_yj, 2 ) ) / $sqrabs;
							}
						}
					}
					array_push ( $kv4_x, gamma () * $tmp_x );
					array_push ( $kv4_y, gamma () * $tmp_y );
				}
				
				// Berechne neue Pos und Geschw
				for($i = 0; $i < count ( $old_pos_x ); $i ++) {
					$new_pos_x [$i] = $old_pos_x [$i] + $schrittweite / 6 * ($kp1_x [$i] + 2 * $kp2_x [$i] + 2 * $kp3_x [$i] + $kp4_x [$i]);
					$new_pos_y [$i] = $old_pos_y [$i] + $schrittweite / 6 * ($kp1_y [$i] + 2 * $kp2_y [$i] + 2 * $kp3_y [$i] + $kp4_y [$i]);
					$new_vel_x [$i] = $old_vel_x [$i] + $schrittweite / 6 * ($kv1_x [$i] + 2 * $kv2_x [$i] + 2 * $kv3_x [$i] + $kv4_x [$i]);
					$new_vel_y [$i] = $old_vel_y [$i] + $schrittweite / 6 * ($kv1_y [$i] + 2 * $kv2_y [$i] + 2 * $kv3_y [$i] + $kv4_y [$i]);
				}
				$new_hamilton = hamilton ( $new_pos_x, $new_pos_y, $new_vel_x, $new_vel_y, $massen );
				
				// Berechne ob die Hamiltonfunktion sich nicht zu stark ändert
				if (abs ( $old_hamilton - $new_hamilton ) > 1e17 and $schrittweite > 2048) {
					$schrittweite = $schrittweite / 4;
					// echo 'Zu groß! Neue Schrittweite: '.$schrittweite.' ';
					// echo "Dann diff: ".($old_hamilton-$new_hamilton)."<br/>";
					$new_pos_x = $old_pos_x;
					$new_pos_y = $old_pos_y;
					$new_vel_x = $old_vel_x;
					$new_vel_y = $old_vel_y;
					$new_hamilton = $old_hamilton;
				} else {
					// Setze Timer auf nächsten Zeitschritt
					$berechnete_zeit += $schrittweite;
					$schrittweite = $time / $schritte;
				}
			}
			
			echo "Dann pos: <pre>";
			print_r ( $new_pos_x );
			echo "</pre><br/>";
			echo "Dann vel: <pre>";
			print_r ( $new_vel_x );
			echo "</pre><br/>";
			
			// Setze Objekte auf die berechneten Werte
			for($i = 0; $i < count ( $objektliste ); $i ++) {
				$objektliste [$i]->pos_x = $new_pos_x [$i] / 1000;
				$objektliste [$i]->pos_y = $new_pos_y [$i] / 1000;
				$objektliste [$i]->vel_x = $new_vel_x [$i] / 1000 * 60 * 60;
				$objektliste [$i]->vel_y = $new_vel_y [$i] / 1000 * 60 * 60;
			}
			unset ( $objektliste );
			echo "Ende Runge-Kutta<br/>";
		}
	}
}

?>
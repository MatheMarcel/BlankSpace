
<canvas id="testcanvas1" width="500" height="500">Canvas geht in deinem Browser nicht.</canvas>
<script type="text/javascript">
function drawCanvas(){
  var canvas = document.getElementById('testcanvas1');
  if(canvas.getContext){
    var context = canvas.getContext('2d');
    context.fillStyle = "rgb(0, 0, 0)";
    context.fillRect(0, 0, canvas.width, canvas.height);

    for (var i = 0; i <= 100; i++) {
      context.beginPath();
      context.arc(500*Math.random(),500*Math.random(),0.7,0,2*Math.PI,false);
      context.fillStyle="rgb(255,255,255)";
      context.fill();
      context.closePath();
    }
 
<?php
$planets = mysqli_query ( $dblink, 'SELECT id,pos_x,pos_y,vel_x,vel_y,type_id FROM game_planets WHERE (type_id = 1 OR type_id = 2) AND starsystem_id = ' . mysqli_real_escape_string ( $dblink, $starsystem->id ) . ' ORDER BY type_id' );
while ( $planet = mysqli_fetch_object ( $planets ) ) {
	$mass = 0;
	if ($planet->type_id == 1) {
		$mass = 6;
	}
	if ($planet->type_id == 2 or $planet->type_id == 3) {
		$mass = 2;
	}
	
	$owner = mysqli_query ( $dblink, 'SELECT player_id FROM game_planets WHERE id = ' . $planet->id );
	$owner = mysqli_fetch_array ( $owner );
	if ($owner ['player_id'] == $_SESSION ['gameuserid']) {
		echo 'context.beginPath();';
		echo 'context.arc(' . (250 + $planet->pos_x / 7000e6 * 500) . ',' . (250 - $planet->pos_y / 7000e6 * 500) . ',' . (2 + $mass) . ',0,2*Math.PI,false);';
		echo 'context.lineWidth = 1;';
		echo 'context.strokeStyle = "red";';
		echo 'context.stroke();';
		echo 'context.closePath();' . PHP_EOL;
	}
	
	echo 'context.beginPath();';
	echo 'context.arc(' . (250 + $planet->pos_x / 7000e6 * 500) . ',' . (250 - $planet->pos_y / 7000e6 * 500) . ',' . $mass . ',0,360,false);';
	if ($planet->type_id == 3) {
		echo 'context.fillStyle ="rgb(255,0,0)";';
	} else {
		echo 'context.fillStyle ="rgb(255,255,255)";';
	}
	echo 'context.fill();';
	echo 'context.closePath();' . PHP_EOL;
}

?>
  }
}
drawCanvas();
</script>
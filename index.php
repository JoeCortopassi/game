<style>
.normal {
	background-color: yellow;
	border: 1px black solid;
	float:left;
	height: 10px;
	width: 10px;
}

.selected {
	background-color: green;
	border: 1px black solid;
	float:left;
	height: 10px;
	width: 10px;
}

.offMap {
	display:none;
}
</style>

<?php

$height = 10;
$width  = 100;

$map = "[";

for ( $h = 0; $h < $height; $h++ )
{
	$map .="[";
	?>
		<div>
	<?php
	for ( $w = 0; $w < $width; $w++ )
	{
		$map .= '0,';
		?>
			<div id="<?php echo 'cell_' . ( $height - ( $h + 1 ) ) . '_' . $w; ?>" class="normal"></div>
		<?php
	}
	
	$map = rtrim( $map, ',' );
	?>
			<div style="clear:both;">
		</div>
	<?php
	$map .= "],";
}

$map = rtrim( $map, ',' );
$map .= "]";
?>


<script>
var player = { 'x' : 0, 'y' : 0 };
var direction = 'none';
var map = <?php echo $map; ?>;
var view = { 'start': 0, 'end': 9 };

window.onkeyup = function(e) {
	if ( e.keyCode == 65 || e.keyCode == 37 ){
		direction = 'left';
	}
	else if ( e.keyCode == 68 || e.keyCode == 39 ){
		direction = 'right';
	}
	else if ( e.keyCode == 32 || e.keyCode == 38 || e.keyCode == 87 ){
		direction = 'up';
	}
	else if ( e.keyCode == 40 ){
		direction = 'down';
	}
}


function updatePlayer()
{
	// Remove the old player marker
	var old_cell = document.getElementById('cell_'+player.y+'_'+player.x);
	old_cell.className = 'normal';
	
	// Update player position
	if ( direction === 'left' )
	{
		player.x -= 1;
	}
	else if ( direction === 'right' )
	{
		player.x += 1;
	}
	else if ( direction === 'up' )
	{
		player.y += 1;
	}
	else if ( direction === 'down' )
	{
		player.y -= 1;
	}
	else
	{
		
	}
	
	
	// Keep player within the bounds of the map
	if ( player.y >= map.length )
	{
		player.y = map.length - 1;	
	}
	else if ( player.y < 0 )
	{
		player.y = 0;	
	}
	else if ( player.x >= map[0].length )
	{
		player.x = map[0].length - 1;	
	}
	else if ( player.x < 0 )
	{
		player.x = 0;	
	}
	else
	{
		
	}
	
	// Place the player back on the map
	if ( player.x <= view.end && player.x >= view.start )
	{
		var new_cell = document.getElementById('cell_'+player.y+'_'+player.x)
		new_cell.className = 'selected';
	}
	
	direction = 'none';
}



function updateView()
{
	var cell;
	// Loop for rows in map
	for ( var col = 0; col < map[0].length; col++ )
	{
		for ( var row = 0; row < map.length; row++ )
		{
			if ( col <= view.end && col >= view.start )
			{
				cell = document.getElementById('cell_'+row+'_'+col);
				cell.className = "normal";
			}
			else
			{
				cell = document.getElementById('cell_'+row+'_'+col);
				cell.className = "offMap";
			}
		}
	}
}



function gameLoop()
{
	updateView(); 
	updatePlayer();

	setTimeout(gameLoop, 30);
}

setTimeout(gameLoop, 3000);
</script>
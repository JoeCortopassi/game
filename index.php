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

.wall {
	background-color: blue;
	border: 1px black solid;
	float:left;
	height: 10px;
	width: 10px;
}

.enemy {
	background-color: red;
	border: 1px black solid;
	float:left;
	height: 10px;
	width: 10px;
}

.offMap {
	background-color: white;
	border: 1px black solid;
	float:left;
	height: 10px;
	width: 10px;
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
// Non-game related helper functions

// 
function cloneObject(obj) {
    var clone = {};

    for (var i in obj) {
        if (typeof obj[i] == 'object') {
            clone[i] = cloneObject(obj[i]);
        } else {
            clone[i] = obj[i];
        }
    }

    return clone;
}


</script>

<script>
var player = { 'x' : 0, 'y' : 0 };
var direction = 'none';
var map = <?php echo $map; ?>;
var view = { 'start': 0, 'end': 9, 'buffer': 2};
var object_template = { 'position' : { 'x' : 0, 'y': 0 }, 'velocity' : { 'x': 0, 'y': 0 }, 'vertices' : {}, 'type' : '' };
var objects = [];

function setupObjects()
{
	var wall1 = cloneObject(object_template);
	wall1.position = { 'x': 20, 'y': 0 };
	wall1.vertices = [
		{ 'x': 0, 'y': 0 },
		{ 'x': 0, 'y': 1 },
		{ 'x': 0, 'y': 2 },
		{ 'x': 1, 'y': 0 },
		{ 'x': 1, 'y': 1 },
		{ 'x': 1, 'y': 2 }
	];
	wall1.type = 'wall';
	objects.push(wall1);
	
	
	var wall2 = cloneObject(object_template);
	wall2.position = { 'x': 40, 'y': 0 };
	wall2.vertices = [
		{ 'x': 0, 'y': 0 },
		{ 'x': 1, 'y': 0 },
		{ 'x': 1, 'y': 1 },
		{ 'x': 2, 'y': 0 },
		{ 'x': 2, 'y': 1 },
		{ 'x': 2, 'y': 2 },
		{ 'x': 3, 'y': 0 },
		{ 'x': 3, 'y': 1 },
		{ 'x': 4, 'y': 0 }
	];
	wall2.type = 'wall';
	objects.push(wall2);
	
	
	var wall3 = cloneObject(object_template);
	wall3.position = { 'x': 60, 'y': 0 };
	wall3.vertices = [
		{ 'x': 0, 'y': 0 },
		{ 'x': 0, 'y': 1 },
		{ 'x': 1, 'y': 0 },
		{ 'x': 1, 'y': 1 },
		{ 'x': 2, 'y': 0 },
		{ 'x': 2, 'y': 1 }
	];
	wall3.type = 'wall';
	objects.push(wall3);
	
	
	var enemy1 = cloneObject(object_template);
	enemy1.position = { 'x': 100, 'y': 0 };
	enemy1.velocity = { 'x': -0.1, 'y': 0 };
	enemy1.vertices = [
		{ 'x': 0, 'y': 0 },
		{ 'x': 0, 'y': 1 },
		{ 'x': 1, 'y': 0 },
		{ 'x': 1, 'y': 1 },
		{ 'x': 2, 'y': 0 },
		{ 'x': 2, 'y': 1 }
	];
	enemy1.type = 'enemy';
	objects.push(enemy1);
	
	
}




// Set up movement to be based on key presses
var keyDownEvent = function(e) {
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

// Allow holding down of a key to be used for movement
window.onkeydown = keyDownEvent;
window.onkeypress = keyDownEvent;




function updatePlayer()
{	
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
	else if ( player.x > view.end )
	{
		player.x = view.end;	
	}
	else if ( player.x < view.start )
	{
		player.x = view.start;	
	}
	else
	{
		
	}
	
	direction = 'none';
}



function scrollViewWithPlayer()
{
	if ( ( player.x > view.end - view.buffer ) && player.x < map[0].length - view.buffer )
	{
		view.start++;
		view.end++;
	}
	else if ( ( player.x < view.start + view.buffer ) && player.x >= 0 + view.buffer )
	{
		view.start--;
		view.end--;
	}
	else
	{
		
	}
}



function updateView()
{
	var cell;
	
	// Only show 
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
	
	// Place the player back on the map
	if ( player.x <= view.end && player.x >= view.start )
	{
		var new_cell = document.getElementById('cell_'+player.y+'_'+player.x)
		new_cell.className = 'selected';
	}
}


function upateObjects()
{
	for( var i = 0; i < objects.length; i++ )
	{
		if ( objects[i].velocity.x != 0 )
		{
			objects[i].position.x += objects[i].velocity.x;
		}
		
		if ( objects[i].velocity.y != 0 )
		{
			objects[i].position.y += objects[i].velocity.y;
		}
	}
}

function drawObjects()
{
	for( var i = 0; i < objects.length; i++ )
	{
		var object = objects[i];

		for( var j = 0; j < object.vertices.length; j++ )
		{
			var vertice = object.vertices[j];

			var verticePosition = { 'x': 0, 'y':0 }; 
			
			verticePosition.x = object.position.x + vertice.x;
			verticePosition.y = object.position.y + vertice.y;
			
			if ( verticePosition.x <= view.end && verticePosition.x >= view.start )
			{
				drawObjectAtCoordinatesWithType( verticePosition.x, verticePosition.y, object.type );
			}
		}
	}
}


function drawObjectAtCoordinatesWithType( x, y, type )
{
	x = Math.round(x);
	y = Math.round(y);
	
	var cell = document.getElementById( 'cell_' + y + '_' + x );
	
	if ( cell !== null )
	{
		cell.className = type;
	}
}







function gameLoop()
{
	
	updatePlayer();
	upateObjects();
	scrollViewWithPlayer();
	updateView();
	drawObjects();

	setTimeout(gameLoop, 10);
}

setupObjects();
setTimeout(gameLoop, 300);
</script>
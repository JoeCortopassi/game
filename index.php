<style>
.normal {
	background-color: yellow;
	border: 1px black solid;
	float:left;
	height: 10px;
	width: 10px;
}

.player {
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
	display:none;
	/*
	background-color: white;
	border: 1px black solid;
	float:left;
	height: 10px;
	width: 10px;
	*/
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
var direction = 'none';
var map = <?php echo $map; ?>;
var view = { 'start': 0, 'end': 14, 'buffer': 2};
var environment_constants = { 'gravity': 0.2, 'ground_friction': 0.2, 'air_friction': 0.1 }
var object_template = { 'position' : { 'x' : 0, 'y': 0 }, 'velocity' : { 'x': 0, 'y': 0 }, 'vertices' : {}, 'type' : '', 'name': '' };
var environmentObjects = [];
var enemyObjects = [];
var playerObjects = [];
var list_of_all_objects = [];



function setupPlayer()
{
	player = cloneObject(object_template);
	player.position = { 'x': 0, 'y': 0 };
	player.vertices = [
		{ 'x': 0, 'y': 0 }
	];
	player.type = 'player';
	player.name = 'player';
	playerObjects.push(player);
	
	list_of_all_objects.push(playerObjects);
}


function setupEnvironmentObjects()
{
	wall1 = cloneObject(object_template);
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
	wall1.name = 'wall1';
	environmentObjects.push(wall1);
	
	
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
	wall2.name = 'wall2';
	environmentObjects.push(wall2);
	
	
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
	wall3.name = 'wall3';
	environmentObjects.push(wall3);
	
	list_of_all_objects.push(environmentObjects);
}


function setupEnemyObjects()
{
	var enemy1 = cloneObject(object_template);
	enemy1.position = { 'x': 30, 'y': 0 };
	enemy1.velocity = { 'x': 0.2, 'y': 0 };
	enemy1.vertices = [
		{ 'x': 0, 'y': 0 }
	];
	enemy1.type = 'enemy';
	enemy1.name = 'enemy1';
	enemyObjects.push(enemy1);
	
	list_of_all_objects.push(enemyObjects);
}


function setupObjects()
{
	setupEnvironmentObjects();
	setupEnemyObjects();
	setupPlayer();
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












/**
 *   Functions dealing with the view
 */
function scrollViewWithPlayer()
{
	if ( ( player.position.x > view.end - view.buffer ) && player.position.x < map[0].length - view.buffer )
	{
		view.start++;
		view.end++;
	}
	else if ( ( player.position.x < view.start + view.buffer ) && player.position.x >= 0 + view.buffer )
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
	
	
}








/**
 *	Functions dealing with collision detection
 */
function collidesWithObjectFromDirection( name, position_x, position_y, velocity_x, velocity_y )
{
	var collision_detected = false;
	
	// All object lists
	for( var i = 0; i < list_of_all_objects.length; i++ )
	{
		// All objects in a list
		for( var j = 0; j < list_of_all_objects[i].length; j++ )
		{
			var object = list_of_all_objects[i][j];
			
			if ( object.name === name )
			{
				continue;
			}
			
			// All vertices for an object
			for( var k = 0; k < object.vertices.length; k++ )
			{
				var vertice = { 'x': object.vertices[k].x, 'y': object.vertices[k].y };
			
				vertice.x += object.position.x;
				vertice.y += object.position.y;
				
				if ( vertice.x === Math.round(position_x) && vertice.y === Math.round(position_y) )
				{
					collision_detected = true;
					velocity_x *= -1;
					velocity_y *= -1;
				}
				
				if( collision_detected )
				{
					return { 'x': velocity_x, 'y': velocity_y };
				}
			}
		}
	}
	
	return false;
}










/**
 *  Function dealing with updating objects
 */


function upateObjects( object_list )
{
	for( var i = 0; i < object_list.length; i++ )
	{
		var new_position = {
			'x': object_list[i].position.x + object_list[i].velocity.x,
			'y': object_list[i].position.y + object_list[i].velocity.y
		};
		
		var new_velocity = collidesWithObjectFromDirection( object_list[i].name, new_position.x, new_position.y, object_list[i].velocity.x, object_list[i].velocity.y );
		
		if ( new_velocity )
		{
			object_list[i].velocity = new_velocity;
		}
		
		if ( object_list[i].velocity.x != 0 )
		{
			object_list[i].position.x += object_list[i].velocity.x;
		}
		
		if ( object_list[i].velocity.y != 0 )
		{
			object_list[i].position.y += object_list[i].velocity.y;
		}
		
		// Add gravity
		object_list[i].velocity.y -= environment_constants.gravity;
		
		if ( object_list[i].position.y < 0 )
		{
			object_list[i].position.y = 0;
			object_list[i].velocity.y = 0;
		}
		
		
		// Add friction
		//if ( object_list[i].position.x === 0 )
		//{
		//	object_list[i].velocity.x -= ( object_list[i].velocity.x * environment_constants.ground_friction );
		//}
		//else
		//{
		//	object_list[i].velocity.x -= ( object_list[i].velocity.x * environment_constants.air_friction );
		//}
	}
}



function updatePlayer()
{	
	// Update player position
	if ( direction === 'left' )
	{
		player.velocity.x -= 1;
	}
	else if ( direction === 'right' )
	{
		player.velocity.x += 1;
	}
	else if ( direction === 'up' )
	{
		player.velocity.y += 1;
	}
	else if ( direction === 'down' )
	{
		player.velocity.y -= 1;
	}
	else
	{
		
	}
	
	player.position.x += player.velocity.x;
	player.position.y += player.velocity.y;
	
	
	
		// Add gravity
		player.velocity.y -= environment_constants.gravity;
		
		if ( player.position.y < 0 )
		{
			player.position.y = 0;
			player.velocity.y = 0;
		}
		
		
		// Add friction
		if ( player.position.y === 0 )
		{
			player.velocity.x -= ( player.velocity.x * environment_constants.ground_friction );
		}
		else
		{
			player.velocity.x -= environment_constants.air_friction;
		}
	
	
	
	
	// Keep player within the bounds of the map
	if ( player.position.y >= map.length )
	{
		player.position.y = map.length - 1;	
	}
	else if ( player.position.y < 0 )
	{
		player.position.y = 0;	
	}
	else if ( player.position.x > view.end )
	{
		player.position.x = view.end;	
	}
	else if ( player.position.x < view.start )
	{
		player.position.x = view.start;	
	}
	else
	{
		
	}
	
	direction = 'none';
}






/**
 *	 Functions dealing with drawing/display
 */

function drawObjects( object_array )
{
	for( var i = 0; i < object_array.length; i++ )
	{
		var object = object_array[i];

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
	upateObjects(enemyObjects);
	scrollViewWithPlayer();
	updateView();
	drawObjects(environmentObjects);
	drawObjects(enemyObjects); 
	drawObjects(playerObjects); 

	setTimeout(gameLoop, 100);
}

setupObjects();
setTimeout(gameLoop, 300);
</script>
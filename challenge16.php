#!/usr/bin/php
<?php
/* Challenge 16 - ÑAPA
 * 
 * Diego Villar
 *		Email:		dvil88@gmail.com
 * 		Twitter:	@dvil88
 */


/*
 * https://www.youtube.com/watch?v=zOjhkEmEmUI
 */


$lines = file('php://stdin');
list($startLine,$numbers) = explode(',',trim(array_shift($lines)));

$asteroids = array();
$fp = fopen('./points','r');
$i = 1;

$minX = $minY = $maxX = $maxY = false;
while(($line = trim(fgets($fp))) && $numbers > 0){
	if($i++ < $startLine){continue;}

	preg_match('/([0-9]+)\s+([0-9]+)\s+([0-9]+)/',$line,$m);
	$asteroids[] = array(
		'x'=>$m[1],
		'y'=>$m[2],
		'r'=>$m[3],
	);
	$numbers--;
}
fclose($fp);

$collisions = calculateCollisions($asteroids);
echo $collisions,PHP_EOL;

function calculateCollisions($asteroids){
	$collisions = 0;
	foreach($asteroids as $a=>$asteroid){
		$coordsA1 = $asteroid['x'].'#'.$asteroid['y'];
		var_dump(count($asteroids));
		foreach($asteroids as $a2=>$asteroid2){
			$coordsA2 = $asteroid2['x'].'#'.$asteroid2['y'];
			if($a == $a2){continue;}

			$radioSum = $asteroid['r'] + $asteroid2['r'];
			$d = getDistanceBetween2Points($coordsA1,$coordsA2);
			if($radioSum > $d){
				// Oh god we're gonna dieeeeeeeeeeeeeeee
				$collisions++;
			}

		}
		unset($asteroids[$a]);
	}

	return $collisions;
}

function getDistanceBetween2Points($point1,$point2){
	$p1 = explode('#',$point1);
	$p2 = explode('#',$point2);

	$d = sqrt(pow($p2[0] - $p1[0],2) + pow($p2[1] - $p1[1],2));
	return $d;
}
?>
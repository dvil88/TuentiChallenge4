#!/usr/bin/php
<?php
/* Challenge 12 - Taxi Driver
 * 
 * Diego Villar
 *		Email:		dvil88@gmail.com
 * 		Twitter:	@dvil88
 */

$lines = file('php://stdin');
$testCases = trim(array_shift($lines));

for($case = 1;$case <= $testCases;$case++){
	$dimension = array_shift($lines);
	list($width,$height) = explode(' ',$dimension);

	$start = '';
	$minMoves = 0;
	$taxiTrip = false;
	$city = array();

	for($h = 0;$h < $height;$h++){
		$cityLine = trim(array_shift($lines));
		$cityLine = preg_split('/(?<!^)(?!$)/u',$cityLine);

		for($w = 0;$w < $width;$w++){
			$city[$w][$h] = $cityLine[$w];
			if($cityLine[$w] == 'S'){$start = $w.'#'.$h;}
			elseif($cityLine[$w] == 'X'){$target = $w.'#'.$h;}
		}
	}

	getNextMoves($start,$start,$target,0,array());
	echo 'Case #',$case,': ',($taxiTrip ? $minMoves : 'ERROR'),PHP_EOL;
}

// Modification of the A* algorithm implementation with constraints
function getNextMoves($coords,$prevCoords,$target,$moves,$visited){
	global $minMoves;
	global $city;
	global $taxiTrip;


	if($minMoves > 0 && $moves > $minMoves){
		// Oh shit, the tourist realised I was trying to scam him
		return;
	}

	if(!isset($visited[$coords])){$visited[$coords] = 0;}
	else{$visited[$coords]++;}

	list($x,$y) = explode('#',$coords);
	$actualCoords = $x.'#'.$y;

	if($city[$x][$y] == 'X'){
		// - to where? 
		// + to the third mouthstreet
		// - It's 10000
		$taxiTrip = true;
		$minMoves = $moves;
		return;
	}
	

	list($ax,$ay) = explode('#',$actualCoords);
	list($px,$py) = explode('#',$prevCoords);
	list($tx,$ty) = explode('#',$target);
	$allowedMoves = array();
	if($actualCoords == $prevCoords){
		// Punto inicial
		if(isset($city[$ax][$ay+1]) && $city[$ax][$ay+1] != '#'){
			$fVar = 1 + abs($ax-$tx) + abs($ay+1-$ty);
			$allowedMoves[$ax.'#'.($ay+1)] = $fVar;
		}
		if(isset($city[$ax][$ay-1]) && $city[$ax][$ay-1] != '#'){
			$fVar = 1 + abs($ax-$tx) + abs($ay-1-$ty);
			$allowedMoves[$ax.'#'.($ay-1)] = $fVar;
		}
		if(isset($city[$ax+1][$ay]) && $city[$ax+1][$ay] != '#'){
			$fVar = 1 + abs($ax+1-$tx) + abs($ay-$ty);
			$allowedMoves[($ax+1).'#'.$ay] = $fVar;
		}
		if(isset($city[$ax-1][$ay]) && $city[$ax-1][$ay] != '#'){
			$fVar = 1 + abs($ax-1-$tx) + abs($ay-$ty);
			$allowedMoves[($ax-1).'#'.$ay] = $fVar;
		}
	}else{
		if($ax == $px){
			// Vertical
			if($ay > $py){
				// Down
				$newCoords = $ax.'#'.($ay+1);
				if(isset($city[$ax][$ay+1]) && $city[$ax][$ay+1] != '#' && (!isset($visited[$newCoords]) || $visited[$newCoords] < 2)){
					$fVar = 1 + abs($ax-$tx) + abs($ay+1-$ty);
					$allowedMoves[$newCoords] = $fVar;
				}
				$newCoords = ($ax-1).'#'.$ay;
				if(isset($city[$ax-1][$ay]) && $city[$ax-1][$ay] != '#' && (!isset($visited[$newCoords]) || $visited[$newCoords] < 2)){
					$fVar = 1 + abs($ax-1-$tx) + abs($ay-$ty);
					$allowedMoves[$newCoords] = $fVar;
				}
			}else{
				// Up
				$newCoords = $ax.'#'.($ay-1);
				if(isset($city[$ax][$ay-1]) && $city[$ax][$ay-1] != '#' && (!isset($visited[$newCoords]) || $visited[$newCoords] < 2)){
					$fVar = 1 + abs($ax-$tx) + abs($ay-1-$ty);
					$allowedMoves[$newCoords] = $fVar;
				}
				$newCoords = ($ax+1).'#'.$ay;
				if(isset($city[$ax+1][$ay]) && $city[$ax+1][$ay] != '#' && (!isset($visited[$newCoords]) || $visited[$newCoords] < 2)){
					$fVar = 1 + abs($ax+1-$tx) + abs($ay-$ty);
					$allowedMoves[$newCoords] = $fVar;
				}
			}
		}

		if($ay == $py){
			// Horizontal
			if($ax > $px){
				// Right
				$newCoords = ($ax+1).'#'.$ay;
				if(isset($city[$ax+1][$ay]) && $city[$ax+1][$ay] != '#' && (!isset($visited[$newCoords]) || $visited[$newCoords] < 2)){
					$fVar = 1 + abs($ax+1-$tx) + abs($ay-$ty);
					$allowedMoves[$newCoords] = $fVar;
				}
				$newCoords = $ax.'#'.($ay+1);
				if(isset($city[$ax][$ay+1]) && $city[$ax][$ay+1] != '#' && (!isset($visited[$newCoords]) || $visited[$newCoords] < 2)){
					$fVar = 1 + abs($ax-$tx) + abs($ay+1-$ty);
					$allowedMoves[$newCoords] = $fVar;
				}
			}else{
				// Left
				$newCoords = ($ax-1).'#'.$ay;
				if(isset($city[$ax-1][$ay]) && $city[$ax-1][$ay] != '#' && (!isset($visited[$newCoords]) || $visited[$newCoords] < 2)){
					$fVar = 1 + abs($ax-1-$tx) + abs($ay-$ty);
					$allowedMoves[$newCoords] = $fVar;
				}
				$newCoords = $ax.'#'.($ay-1);
				if(isset($city[$ax][$ay-1]) && $city[$ax][$ay-1] != '#' && (!isset($visited[$newCoords]) || $visited[$newCoords] < 2)){
					$fVar = 1 + abs($ax-$tx) + abs($ay-1-$ty);
					$allowedMoves[$newCoords] = $fVar;
				}
			}
		}
	}

	foreach($allowedMoves as $coord => $cost){
		if(isset($visited[$actualCoords]) && $visited[$actualCoords] == 1 && isset($visited[$coord]) && $visited[$coord] == 0){
			// Prevent cycles, we don't want to be in a roundabout all the day
			unset($allowedMoves[$coord]);
		}
	}

	if(count($allowedMoves) == 0){return;}
	uasort($allowedMoves,function($a,$b){return $a > $b;});
	foreach($allowedMoves as $nextMove => $dummy){
		getNextMoves($nextMove,$actualCoords,$target,($moves+1),$visited);
	}
}

?>
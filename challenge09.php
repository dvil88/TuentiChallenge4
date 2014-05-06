#!/usr/bin/php
<?php
/* Challenge 9 - Bendito Caos
 * 
 * Diego Villar
 *		Email:		dvil88@gmail.com
 * 		Twitter:	@dvil88
 */

$lines = file('php://stdin');
$testCases = trim(array_shift($lines));

$cities = array();
$results =  array();
for($i=0;$i<$testCases;$i++){
	$city = array();
	$intersections = array();
	$maxPeopleIn = $maxPeopleOut = 0;

	$city['name'] = trim(array_shift($lines));

	$speeds = explode(' ',trim(array_shift($lines)));
	$city['speeds']['normal'] = $speeds[0];
	$city['speeds']['dirt'] = $speeds[1];

	$roads = explode(' ',trim(array_shift($lines)));
	$city['intersections'] = $roads[0];
	for($j=0;$j < $roads[1];$j++){
		$r = explode(' ',trim(array_shift($lines)));

		// Calculate max traffic flow
		$maxFlow = (($city['speeds'][$r[2]] * 1000) / 5) * $r[3];

		if($r[1] == 'AwesomeVille'){$maxPeopleIn += $maxFlow;}
		if($r[0] == $city['name']){$maxPeopleOut += $maxFlow;}


		if(preg_match('/^[0-9]+$/',$r[0]) && $r[1] != $city['name']){
			// Salida de intersección
			$intersections[$r[0]]['out'][$r[1]] = $maxFlow;
		}
		if(preg_match('/^[0-9]+$/',$r[1]) && $r[0] != 'AwesomeVille'){
			// Entrada en intersección
			$intersections[$r[1]]['in'][$r[0]] = $maxFlow;
		}

		$city['maxPeopleIn'] = $maxPeopleIn;
		$city['maxPeopleOut'] = $maxPeopleOut;
	}

	if($city['maxPeopleOut'] <= $city['maxPeopleIn']){
		// Si salen menos o igual de los que pueden entrar quiere decir que entran los que salen
		$results[$city['name']] = $city['maxPeopleOut'];
		continue;
	}
	
	// Limpiar intersecciones
	foreach($intersections as $intersection=>$v){
		if(!isset($v['in'])){
			$outIntersections = array_keys($v['out']);
			foreach($outIntersections as $int){
				unset($intersections[$int]['in'][$intersection]);
			}
			unset($intersections[$intersection]);
		}
		if(!isset($v['out'])){
			$inIntersections = array_keys($v['in']);
			foreach($inIntersections as $int){
				unset($intersections[$int]['out'][$intersection]);
			}
			unset($intersections[$intersection]);
		}
	}

	ksort($intersections);
	foreach($intersections as $k=>$v){
		$maxIn = array_sum($v['in']);
		$maxOut = array_sum($v['out']);
		$intersections[$k]['maxIn'] = $maxIn;
		$intersections[$k]['maxOut'] = $maxOut;
	}

	$count = 0;
	do{
		$finished = true;
		$intersections = recalculateMaxFlowNodes($intersections,$city['name']);
		foreach($intersections as $k=>$v){
			if($v['maxOut'] > $v['maxIn']){$finished = false;}
		}
	}while($finished === false && ++$count < 100);

	$maxPeopleIn = 0;
	foreach($intersections as $k=>$v){
		if(isset($v['out']['AwesomeVille'])){
			$maxPeopleIn = $v['out']['AwesomeVille'];
			$city['maxPeopleIn'] = $maxPeopleIn;

		}
	}
	$results[$city['name']] = ($city['maxPeopleIn'] < 0 ? 0 : $city['maxPeopleIn']);
}

foreach($results as $city=>$population){
	echo $city,' ',$population,PHP_EOL;
}

function recalculateMaxFlowNodes($intersections,$cityName){
	foreach($intersections as $k=>$v){
		if($v['maxOut'] > $v['maxIn']){
			// Si la salida es mayor que la entrada hay que ajustar la salida 
			$diff = $v['maxOut'] - $v['maxIn'];
			$nodes = count($v['out']);

			// Lo que hay que reducir por cada salida, lo repartimos equitativamente
			$reduce = $diff/$nodes;

			foreach($v['out'] as $node=>$max){
				if(isset($intersections[$k])){
					$intersections[$k]['out'][$node] -= $reduce;
					$intersections[$k]['maxOut'] -= $reduce;
				}
				if(isset($intersections[$node])){
					$intersections[$node]['in'][$k] -= $reduce;
					$intersections[$node]['maxIn'] -= $reduce;
				}
			}
		}
	}

	return $intersections;
}


?>
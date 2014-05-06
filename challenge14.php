#!/usr/bin/php
<?php
/* Challenge 14 - Train Empire
 * 
 * Diego Villar
 *		Email:		dvil88@gmail.com
 * 		Twitter:	@dvil88
 */

$lines = file('php://stdin');
$cases = trim(array_shift($lines));

/* 
 * http://www.examiner.com/images/blog/EXID24122/images/I_choo_choose_you.JPG
 */

for($case = 0;$case < $cases;$case++){
	list($stations,$routes,$fuel) = explode(',',trim(array_shift($lines)));

	$trainStations = array();
	for($station = 0;$station < $stations;$station++){
		sscanf(trim(array_shift($lines)),'%s %d,%d %s %d',$st,$stX,$stY,$stT,$cv);
		$trainStations[$st] = array('x'=>$stX,'y'=>$stY,'position'=>($station*10),'destination'=>$stT,'cargo'=>$cv);
	}

	$trainRoutes = array();
	for($route = 0;$route < $routes;$route++){
		$originConections = 0;
		$r = array('origin'=>'');
		$routeStr = explode(' ',trim(array_shift($lines)));
		foreach($routeStr as $k=>$v){
			$s = explode('-',$v);
			if(!isset($r[$s[0]])){$r[$s[0]] = '';}

			if(isset($s[1])){
				$r[$s[1]]['origin'] = $s[0];
				$r[$s[0]]['destination'] = $s[1];

				if($r['origin'] == $s[0] || $r['origin'] == $s[1]){
					$originConections++;
				}
			}else{
				$r['origin'] = $s[0];
			}
		}
		if($originConections > 1){
			// Inicio intermedio, hay que buscar el origen
			foreach($r as $k=>$v){
				if($k == 'origin' || $k == $r['origin']){continue;}
				if($v['origin'] == $r['origin'] && $k != $r[$r['origin']]['destination']){
					$r[$r['origin']]['origin'] = $k;
					$r[$k]['destination'] = $r['origin'];
					unset($r[$k]['origin']);
				}
			}
		}
		
		$passedMiddle = false;
		$initialStation = '';foreach($r as $k=>$v){if($k != 'origin' && count($v) == 1 && !isset($v['origin'])){$initialStation = $k;break;}}
		for($count = 0;$count < $stations;$count++){
			$position = $count;

			if($passedMiddle){
				$position = $count + $trainStations[$r['origin']]['position'];
			}

			if($initialStation == $r['origin']){
				$position = $trainStations[$r['origin']]['position'];
				$passedMiddle = true;
			}


			$trainStations[$initialStation]['position'] = $position;

			if(!isset($r[$initialStation]['destination'])){break;}
			$initialStation = $r[$initialStation]['destination'];
		}

		$trainRoutes['routes'][] = $r;
	}

	
	$maxPoints = 0;
	foreach($trainRoutes['routes'] as $route){
		$routeFuel = $fuel;
		$originStation = $previousStation = $route['origin'];
		$origin = $trainStations[$originStation];
			
		$cargo = array('destination'=>'','cargo'=>0);
		if(isset($trainStations[$originStation]['destination']) && isset($route[$trainStations[$originStation]['destination']])){
			$cargo = array('destination'=>$origin['destination'],'cargo'=>$origin['cargo']);
			unset($trainStations[$originStation]['destination']);
		}

		while($routeFuel >= 0){
			$nextStation = false;
			$currentStation = $trainStations[$originStation];
			$o = $currentStation['x'].'#'.$currentStation['y'];

			// Cargo change
			if(isset($currentStation['destination']) && $currentStation['destination'] == $cargo['destination'] && $currentStation['cargo'] > $cargo['cargo']){
				$temp = $cargo['cargo'];
				$cargo['cargo'] = $currentStation['cargo'];
				$trainStations[$originStation]['cargo'] = $temp;
			}

			if($originStation == $cargo['destination']){
				$maxPoints += $cargo['cargo'];

				$cargo = array('destination'=>'','cargo'=>0);

				if(isset($trainStations[$originStation]['destination']) && isset($route[$trainStations[$originStation]['destination']])){
					foreach($route[$originStation] as $next){
						$or = $trainStations[$originStation]['x'].'#'.$trainStations[$originStation]['y'];
						$de = $trainStations[$next]['x'].'#'.$trainStations[$next]['y'];
						$usage = (getFuelUsage($or,$de) * 2);

						// routeFuel > usage because we have to move at least twice
						if($routeFuel > $usage &&  isset($trainStations[$next]['destination']) && $trainStations[$next]['cargo'] > $trainStations[$originStation]['cargo']){
							$cargo = array('destination'=>'','cargo'=>0);
							$nextStation = $next;
						}
					}

					if($cargo['destination'] == '' && !$nextStation){
						$cargo = array('destination'=>$trainStations[$originStation]['destination'],'cargo'=>$trainStations[$originStation]['cargo']);
						unset($trainStations[$originStation]['destination']);
					}
				}
			}

			if(!$nextStation && $cargo['destination'] == '' && isset($trainStations[$originStation]['destination']) && isset($route[$trainStations[$originStation]['destination']])){
				$cargo = array('destination'=>$trainStations[$originStation]['destination'],'cargo'=>$trainStations[$originStation]['cargo']);
				unset($trainStations[$originStation]['destination']);
			}

			if(!$nextStation){
				if(isset($trainStations[$cargo['destination']]['position']) && isset($trainStations[$originStation]['position'])){
					if(isset($route[$originStation]['destination']) && $trainStations[$cargo['destination']]['position'] > $trainStations[$originStation]['position']){
						$nextStation = $route[$originStation]['destination'];
					}else{
						$nextStation = $route[$originStation]['origin'];
					}
				}else{
					if($trainStations[$previousStation]['position'] >= $trainStations[$originStation]['position']){
						$nextStation = $route[$originStation]['destination'];
					}else{
						$nextStation = $route[$originStation]['origin'];
					}
				}
			}

			$destination = $trainStations[$nextStation];
			$d = $destination['x'].'#'.$destination['y'];
			

			$fuelUsage = getFuelUsage($o,$d);
			$routeFuel -= $fuelUsage;

			$previousStation = $originStation;
			$originStation = $nextStation;
		}
	}

	echo ($maxPoints).PHP_EOL;
}

function getFuelUsage($origin,$destination){
	$o = explode('#',$origin);
	$d = explode('#',$destination);

	$fuelUsage = sqrt(pow(abs($d[0]-$o[0]),2)+pow(abs($d[1]-$o[1]),2));
	return $fuelUsage;
}

?>
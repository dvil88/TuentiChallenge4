#!/usr/bin/php
<?php
/* Challenge 15 - Take a corner
 * 
 * Diego Villar
 *		Email:		dvil88@gmail.com
 * 		Twitter:	@dvil88
 */


$GLOBALS['players'] = array(
	'White'=>'O',
	'Black'=>'X',
);
$letters = 'abcdefgh';

$lines = file('php://stdin');

$puzzles = trim(array_shift($lines));
for($puzzle = 0;$puzzle < $puzzles;$puzzle++){
	sscanf(trim(array_shift($lines)),'%s in %d',$player,$moves);
	$player = $GLOBALS['players'][$player];
	
	$board = array();
	for($i=0;$i<8;$i++){
		$board[] = str_split(trim(array_shift($lines)));
	}

	$GLOBALS['possiblePlayings'] = array();
	resolveGame($board,$player,($moves * 2),$player,array());
	$totalSuccess = array();
	if(count($GLOBALS['possiblePlayings'])){
		foreach($GLOBALS['possiblePlayings'] as $playings){
			$newBoard = $board;
			$newPlayer = $player;
			foreach($playings as $coords=>$damage){
				$newBoard = makeMove($newBoard,$coords,$newPlayer);
				$newPlayer = ($newPlayer == 'X' ? 'O' : 'X');
			}

			$punt = getPunctuation($newBoard);
			$p = $player;
			$o = ($player == 'X' ? 'O' : 'X');

			// $points = ($punt[$p] + ($punt['.']) * 0.5) - $punt[$o];
			$points = ($punt[$p] + ($punt['.']) * 0.5);
			if($points <= 0){continue;}

			$move = key(array_splice($playings,0,1,true));
			if(!isset($totalSuccess[$move])){$totalSuccess[$move] = 0;}
			if($totalSuccess[$move] > $points){continue;}
			$totalSuccess[$move] = $points;
		}

		arsort($totalSuccess);
		print_r($totalSuccess);
		foreach($totalSuccess as $winingMovement=>$v){
			list($row,$col) = explode('#',$winingMovement);
		}

		$winingMovement = key(array_splice($totalSuccess,0,1,true));
		list($row,$col) = explode('#',$winingMovement);

		echo $letters[$col].($row+1).PHP_EOL;
	}else{
		echo 'Impossible'.PHP_EOL;
	}
}

function getPossibleMoves($board,$player,$gemToFlip = false){
	$movements = array();
	$playerGems = array();

	$opponent = ($player == 'X' ? 'O' : 'X');

	foreach($board as $row=>$playerRow){
		foreach($playerRow as $col=>$playerGem){
			if($playerGem != $player){continue;}
			// Up
			if(isset($board[$row-1][$col]) && $board[$row-1][$col] == $opponent){
				for($i = $row;$i >= 0;$i--){
					if($board[$i][$col] == '.'){
						if(!isset($movements[$i.'#'.$col])){$movements[$i.'#'.$col] = 0;}
						$movements[$i.'#'.$col] += ($row-$i);
						break;
					}
				}
			}
			// Up right
			if(isset($board[$row-1][$col+1]) && $board[$row-1][$col+1] == $opponent){
				$r = $row;
				for($i = $col;$i < 8;$i++){
					$newRow = $r--;
					if($newRow < 0){break;}
					if($board[$newRow][$i] == '.'){
						if(!isset($movements[$newRow.'#'.$i])){$movements[$newRow.'#'.$i] = 0;}
						$movements[$newRow.'#'.$i] += ($i-$col);
						break;
					}
				}
			}
			// Right
			if(isset($board[$row][$col+1]) && $board[$row][$col+1] == $opponent){
				for($i = $col;$i < 8;$i++){
					if($board[$row][$i] == '.'){
						if(!isset($movements[$row.'#'.$i])){$movements[$row.'#'.$i] = 0;}
						$movements[$row.'#'.$i] += ($i-$col);
						break;
					}
				}
			}
			// Down right
			if(isset($board[$row+1][$col+1]) && $board[$row+1][$col+1] == $opponent){
				$r = $row;
				for($i = $col;$i < 8;$i++){
					$newRow = $r++;
					if($newRow >= 8){break;}
					if($board[$newRow][$i] == '.'){
						if(!isset($movements[$newRow.'#'.$i])){$movements[$newRow.'#'.$i] = 0;}
						$movements[$newRow.'#'.$i] += ($i-$col);
						break;
					}
				}
			}
			// Down
			if(isset($board[$row+1][$col]) && $board[$row+1][$col] == $opponent){
				for($i = $row;$i < 8;$i++){
					if($board[$i][$col] == '.'){
						if(!isset($movements[$i.'#'.$col])){$movements[$i.'#'.$col] = 0;}
						$movements[$i.'#'.$col] += ($i-$row);
						break;
					}
				}
			}
			// Down left
			if(isset($board[$row+1][$col-1]) && $board[$row+1][$col-1] == $opponent){
				$r = $row;
				for($i = $col;$i >= 0;$i--){
					$newRow = $r++;
					if($newRow >= 8){break;}
					if($board[$newRow][$i] == '.'){
						if(!isset($movements[$newRow.'#'.$i])){$movements[$newRow.'#'.$i] = 0;}
						$movements[$newRow.'#'.$i] += ($col-$i);
						break;
					}
				}
			}
			// Left
			if(isset($board[$row][$col-1]) && $board[$row][$col-1] == $opponent){
				for($i = $col;$i >= 0;$i--){
					if($board[$row][$i] == '.'){
						if(!isset($movements[$row.'#'.$i])){$movements[$row.'#'.$i] = 0;}
						$movements[$row.'#'.$i] += ($col-$i);
						break;
					}
				}
			}
			// Up left
			if(isset($board[$row-1][$col-1]) && $board[$row-1][$col-1] == $opponent){
				$r = $row;
				for($i = $col;$i >= 0;$i--){
					$newRow = $r--;
					if($newRow < 0){break;}
					if($board[$newRow][$i] == '.'){
						if(!isset($movements[$newRow.'#'.$i])){$movements[$newRow.'#'.$i] = 0;}
						$movements[$newRow.'#'.$i] += ($col-$i);
						break;
					}
				}
			}
		}
	}
	return $movements;
}

function makeMove($board,$coords,$player){
	$b = $board;
	$opponent = ($player == 'X' ? 'O' : 'X');

	list($row,$col) = explode('#',$coords);

	// Up
	if(isset($board[$row-1][$col]) && $board[$row-1][$col] == $opponent){
		$i = $row-1;
		$cells = array($coords=>'');
		while($i >= 0 && $board[$i][$col] == $opponent){
			$cells[$i.'#'.$col] = '';
			$i--;
		}
		if(isset($board[$i][$col]) && $board[$i][$col] == $player){
			foreach($cells as $coords => $v){
				list($x,$y) = explode('#',$coords);
				$b[$x][$y] = $player;
			}
		}
	}
	// Up right
	if(isset($board[$row-1][$col+1]) && $board[$row-1][$col+1] == $opponent){
		$i = $row-1;
		$j = $col+1;
		$cells = array($coords=>'');
		while($i > 0 && $j < 8 && $board[$i][$j] == $opponent){
			$cells[$i.'#'.$j] = '';
			$i--;
			$j++;
		}
		if(isset($board[$i][$j]) && $board[$i][$j] == $player){
			foreach($cells as $coords => $v){
				list($x,$y) = explode('#',$coords);
				$b[$x][$y] = $player;
			}
		}
	}
	// Right
	if(isset($board[$row][$col+1]) && $board[$row][$col+1] == $opponent){
		$i = $col+1;
		$cells = array($coords=>'');
		while($i < 8 && $board[$row][$i] == $opponent){
			$cells[$row.'#'.$i] = '';
			$i++;
		}
		if(isset($board[$row][$i]) && $board[$row][$i] == $player){
			foreach($cells as $coords => $v){
				list($x,$y) = explode('#',$coords);
				$b[$x][$y] = $player;
			}
		}
	}
	// Down right
	if(isset($board[$row+1][$col+1]) && $board[$row+1][$col+1] == $opponent){
		$i = $row+1;
		$j = $col+1;
		$cells = array($coords=>'');
		while($i < 8 && $j < 8 && $board[$i][$j] == $opponent){
			$cells[$i.'#'.$j] = '';
			$i++;
			$j++;
		}
		if(isset($board[$i][$j]) && $board[$i][$j] == $player){
			foreach($cells as $coords => $v){
				list($x,$y) = explode('#',$coords);
				$b[$x][$y] = $player;
			}
		}
	}
	// Down
	if(isset($board[$row+1][$col]) && $board[$row+1][$col] == $opponent){
		$i = $row+1;
		$cells = array($coords=>'');
		while($i < 8 && $board[$i][$col] == $opponent){
			$cells[$i.'#'.$col] = '';
			$i++;
		}
		if(isset($board[$i][$col]) && $board[$i][$col] == $player){
			foreach($cells as $coords => $v){
				list($x,$y) = explode('#',$coords);
				$b[$x][$y] = $player;
			}
		}
	}
	
	// Down left
	if(isset($board[$row+1][$col-1]) && $board[$row+1][$col-1] == $opponent){
		$i = $row+1;
		$j = $col-1;
		$cells = array($coords=>'');
		while($i < 8 && $j >= 0 && $board[$i][$j] == $opponent){
			$cells[$i.'#'.$j] = '';
			$i++;
			$j--;
		}
		if(isset($board[$i][$j]) && $board[$i][$j] == $player){
			foreach($cells as $coords => $v){
				list($x,$y) = explode('#',$coords);
				$b[$x][$y] = $player;
			}
		}
	}
	// Left
	if(isset($board[$row][$col-1]) && $board[$row][$col-1] == $opponent){
		$i = $col-1;
		$cells = array($coords=>'');
		while($i >= 0 && $board[$row][$i] == $opponent){
			$cells[$row.'#'.$i] = '';
			$i--;
		}
		if(isset($board[$row][$i]) && $board[$row][$i] == $player){
			foreach($cells as $coords => $v){
				list($x,$y) = explode('#',$coords);
				$b[$x][$y] = $player;
			}
		}
	}
	// Up left
	if(isset($board[$row-1][$col-1]) && $board[$row-1][$col-1] == $opponent){
		$i = $row-1;
		$j = $col-1;
		$cells = array($coords=>'');
		while($i >= 0 && $j >= 0 && $board[$i][$j] == $opponent){
			$cells[$i.'#'.$j] = '';
			$i--;
			$j--;
		}
		if(isset($board[$i][$j]) && $board[$i][$j] == $player){
			foreach($cells as $coords => $v){
				list($x,$y) = explode('#',$coords);
				$b[$x][$y] = $player;
			}
		}
	}

	return $b;
}

function resolveGame($board,$player,$moves,$winner,$coordinates,$prevCoords = false){
	$moves--;
	if($moves < 0){return;}
	$movements = getPossibleMoves($board,$player,($prevCoords && $player != $winner ? $prevCoords : false));
	arsort($movements);

	foreach($movements as $coords=>$damage){
		$newBoard = makeMove($board,$coords,$player);
		$newPlayer = ($player == 'X' ? 'O' : 'X');

		$c = $coordinates;
		$c[$coords] = $damage;

		if($moves == 1 && ($coords == '0#0' || $coords == '0#7' || $coords == '7#7' || $coords == '7#0')){
			$GLOBALS['possiblePlayings'][] = $c;
			return;
		}

		resolveGame($newBoard,$newPlayer,$moves,$winner,$c,$coords);
	}
}

function getPunctuation($board){
	$points = array('X'=>0,'O'=>0,'.'=>0);
	foreach($board as $row=>$boardRow){
		foreach($boardRow as $col=>$color){
			$points[$color]++;
		}
	}

	return $points;
}

function printBoard($board){
	echo PHP_EOL;
	foreach($board as $line){
		echo implode('',$line),PHP_EOL;
	}
	echo PHP_EOL;
}

?>
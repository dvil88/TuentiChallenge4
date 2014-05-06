#!/usr/bin/php
<?php
/* Challenge 5 - Tribblemaker
 * 
 * Diego Villar
 *		Email:		dvil88@gmail.com
 * 		Twitter:	@dvil88
 */

$lines = file('php://stdin');

$i = 0;
$grid = $grid2 = array();
while($line = trim(array_shift($lines))){
	$grid[$i++] = str_split($line);
}


$iterations = 0;
$states = array();
while($iterations <= 100){
	$str = '';
	$total = count($grid);
	for($x=0;$x<$total;$x++){
		for($y=0;$y<count($grid[$x]);$y++){
			$neighbours = 0;
			if(isset($grid[$x-1][$y-1]) && $grid[$x-1][$y-1] == 'X'){$neighbours++;}
			if(isset($grid[$x][$y-1]) && $grid[$x][$y-1] == 'X'){$neighbours++;}
			if(isset($grid[$x+1][$y-1]) && $grid[$x+1][$y-1] == 'X'){$neighbours++;}
			if(isset($grid[$x-1][$y]) && $grid[$x-1][$y] == 'X'){$neighbours++;}
			if(isset($grid[$x+1][$y]) && $grid[$x+1][$y] == 'X'){$neighbours++;}
			if(isset($grid[$x-1][$y+1]) && $grid[$x-1][$y+1] == 'X'){$neighbours++;}
			if(isset($grid[$x][$y+1]) && $grid[$x][$y+1] == 'X'){$neighbours++;}
			if(isset($grid[$x+1][$y+1]) && $grid[$x+1][$y+1] == 'X'){$neighbours++;}
			

			/*
			SOLUTION:
			
			Conway's Game of Life

			Any live cell with fewer than two live neighbours dies, as if caused by under-population.
			Any live cell with two or three live neighbours lives on to the next generation.
			Any live cell with more than three live neighbours dies, as if by overcrowding.
			Any dead cell with exactly three live neighbours becomes a live cell, as if by reproduction.

			http://en.wikipedia.org/wiki/Conway%27s_Game_of_Life
			*/

			$tempGrid[$x][$y] = $grid[$x][$y];
			if($grid[$x][$y] == 'X'){
				if($neighbours == 2 || $neighbours == 3){$tempGrid[$x][$y] = 'X';}
				else{$tempGrid[$x][$y] = '-';}
			}else{
				if($neighbours == 3){$tempGrid[$x][$y] = 'X';}
			}

			$str .= $grid[$x][$y];
		}
	}

	$grid = $tempGrid;
	
	if(isset($states[$str])){
		$loopStart = $states[$str];
		$loopEnd = $iterations - $loopStart;
		break;
	}

	$states[$str] = $iterations;
	$iterations++;
}

echo $loopStart,' ',$loopEnd,PHP_EOL;
?>
#!/usr/bin/php
<?php
/* Challenge 2 - F1 - Bird's-eye Circuit
 * 
 * Diego Villar
 *		Email:		dvil88@gmail.com
 * 		Twitter:	@dvil88
 */

$lines = file('php://stdin');
$trackLine = trim(array_shift($lines));

/*
	Orientation
         3
	     |
	2 <- # ->0
         |
         1
 */


$track = array();
$orientation = $x = $y = $xMin = $xMax = 0;

for($i=0;$i < strlen($trackLine); $i++){
	if($x > $xMax){$xMax = $x;}
	if($x < $xMin){$xMin = $x;}

	$char = $trackLine[$i];
	if($trackLine[$i] == '-' && ($orientation == 1 || $orientation == 3)){$char = '|';}
	$track[$y][$x] = $char;

	if($trackLine[$i] == '-' || $trackLine[$i] == '#' ){
		if($orientation == 0){$x++;}
		elseif($orientation == 2){$x--;}
		elseif($orientation == 1){$y++;}
		elseif($orientation == 3){$y--;}
	}elseif($trackLine[$i] == '/'){
		if($orientation == 0){$y--;$orientation = 3;}
		elseif($orientation == 2){$y++;$orientation = 1;}
		elseif($orientation == 1){$x--;$orientation = 2;}
		elseif($orientation == 3){$x++;$orientation = 0;}
	}elseif($trackLine[$i] == '\\'){
		if($orientation == 0){$y++;$orientation = 1;}
		elseif($orientation == 2){$y--;$orientation = 3;}
		elseif($orientation == 1){$x++;$orientation = 0;}
		elseif($orientation == 3){$x--;$orientation = 2;}
	}
}

ksort($track);
$track = array_values($track);

$total = count($track);
for($i=0;$i<count($track);$i++){
	if(!isset($track[$i])){$track[$i] = array();}
	for($j = $xMin;$j <= $xMax; $j++){
		if(!isset($track[$i][$j])){$track[$i][$j] = ' ';}
	}
	ksort($track[$i]);
	$track[$i] = array_values($track[$i]);
}

foreach($track as $i=>$line){
	echo implode('',$track[$i]),PHP_EOL;
}

?>
#!/usr/bin/php
<?php
/* Challenge 3 - The Gambler’s Club - Monkey Island 2
 * 
 * Diego Villar
 *		Email:		dvil88@gmail.com
 * 		Twitter:	@dvil88
 */

$lines = file('php://stdin');
$testCases = array_shift($lines);

/* 
	"Look behind you, a Three-Headed Monkey!"
	
	Algorithm:
	   ___________
	 \/ x^2 + y^2

 */

while($line = trim(array_shift($lines))){
	list($x,$y) = explode(' ',$line);
	$res = sqrt(pow($x,2)+pow($y,2));
	echo round($res,2),PHP_EOL;
}
?>
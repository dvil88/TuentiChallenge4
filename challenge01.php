#!/usr/bin/php
<?php
/* Challenge 1 - Anonymous Poll
 * 
 * Diego Villar
 *		Email:		dvil88@gmail.com
 * 		Twitter:	@dvil88
 */

$lines = file('php://stdin');
$testCases = array_shift($lines);

$students = file_get_contents('students');
$i = 0;
while($line = trim(array_shift($lines))){
	echo 'Case #',(++$i),': ';

	if(preg_match_all('/^([^,]+),'.preg_quote($line).'/msi',$students,$m)){
		usort($m[1],function($a,$b){return $a[0]>$b[0];});
		echo implode(',',$m[1]);
	}else{
		echo 'NONE';
	}
	echo PHP_EOL;
}
?>
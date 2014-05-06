#!/usr/bin/php
<?php
/* Challenge 4 - Shape shifters
 * 
 * Diego Villar
 *		Email:		dvil88@gmail.com
 * 		Twitter:	@dvil88
 */

$lines = file('php://stdin');

$init = trim(array_shift($lines));
$end = trim(array_shift($lines));

$states = array();foreach($lines as $l){$states[] = trim($l);}
$total = count($states);

$hops = array();
foreach($states as $state){
	$nucleotids = strlen($state);
	for($i = 0;$i<$total;$i++){
		if($state == $states[$i]){continue;}

		$stateHop = 0;
		for($j=0;$j<$nucleotids;$j++){if($state[$j] != $states[$i][$j]){$stateHop++;}}
		if($stateHop != 1){continue;}

		$hops[$state][$states[$i]] = $stateHop;
	}
}

$GLOBALS['DNAstates'] = array($init=>'');
mutate($hops,$init,$end);

echo implode('->',array_keys($GLOBALS['DNAstates'])),PHP_EOL;


function mutate($mutations,$init,$end){
	foreach($mutations[$init] as $m=>$v){
		if(isset($GLOBALS['DNAstates'][$m]) || !isset($mutations[$m])){continue;}
		$GLOBALS['DNAstates'][$m] = '';
		if($m == $end){return;}
		mutate($mutations,$m,$end);
	}
}

?>
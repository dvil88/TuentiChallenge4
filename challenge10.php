#!/usr/bin/php
<?php
/* Challenge 10 - Random Password
 * 
 * Diego Villar
 *		Email:		dvil88@gmail.com
 * 		Twitter:	@dvil88
 */

include_once('inc_htmlCurl.php');

$lines = file('php://stdin');
$input = trim(array_shift($lines));

// $pid = getPPID($input);
$pid = 1336;

// Get the time of the server
$url = 'http://random.contest.tuenti.net/';
$data = html_petition($url);
preg_match('/Date: ([a-zA-Z0-9 :,]+)/',$data['pageHeader'],$m);
$time = strtotime($m[1]);

// Generate the seed of the server and calculate the first rand()
srand(mktime(date('H',$time),date('i',$time),0)*$pid);
$rand = rand();
$url = 'http://random.contest.tuenti.net/?&password='.$rand.'&input='.$input;
$data = html_petition($url);

echo $data['pageContent'],PHP_EOL;


function getPPID($input){
	$url = 'http://random.contest.tuenti.net/';
	$data = html_petition($url);
	preg_match('/Date: ([a-zA-Z0-9 :,]+)/',$data['pageHeader'],$m);
	$time = strtotime($m[1]);

	for($pid = 1300;$pid < 5000;$pid++){
		srand(mktime(date('H',$time),date('i',$time),0)*$pid);
		$rand = rand();

		$url = 'http://random.contest.tuenti.net/?'.$input.'&password='.$rand;
		$data = html_petition($url);
		preg_match('/Date: ([a-zA-Z0-9 :,]+)/',$data['pageHeader'],$m);
		$time = strtotime($m[1]);

		if($data['pageContent'] != 'wrong!'){
			return $pid;
		}

	}
}
?>
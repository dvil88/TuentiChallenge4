#!/usr/bin/php
<?php
/* Challenge 11 - Pheasant
 * 
 * Diego Villar
 *		Email:		dvil88@gmail.com
 * 		Twitter:	@dvil88
 */


$lines = file('php://stdin');

// Array to save previous decrypted keys 
$GLOBALS['decryptionKeys'] = array();

foreach($lines as $line){
	$data = explode(';',$line);
	$data = array_diff($data,array(''));
	$events = array_shift($data);

	$feed = array();
	while($user = array_shift($data)){
		list($userId,$userKey) = explode(',',trim($user));

		$feedContent = decryptFile($userId,$userKey);
		preg_match_all('/^([0-9]+) ([0-9]+) ([0-9]+)$/msi',$feedContent,$m);
		foreach($m[0] as $k=>$v){
			$feed[$m[3][$k]] = $m[2][$k];
		}
	}

	uasort($feed,function($a,$b){return $a < $b;});
	$feed = array_slice($feed,0,$events,true);
	echo implode(' ',array_keys($feed)),PHP_EOL;;
}


function decryptFile($userId,$userKey){
	$userIndex = substr($userId,-2);

	$feedFile = getcwd().'/feeds/encrypted/'.$userIndex.'/'.$userId.'.feed';
	$timestampFile = getcwd().'/feeds/last_times/'.$userIndex.'/'.$userId.'.timestamp';

	$timestamp = file_get_contents($timestampFile);
	$decryptedStr = $userId.' '.$timestamp;

	$feed = file_get_contents($feedFile);

	if(isset($GLBALS['decryptionKeys'][$userId])){
		return mcrypt_decrypt('rijndael-128',$key,$feed,'ecb');
	}

	// Yes mom, I'm using bruteforce and I know it's not the best way to do it, but this is killing me
	// There is a better way to decrypt it, I just need NSA phone number, they know the keys, and even the events well ordered
	$letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$totalLetters = strlen($letters);
	for($i=0;$i<$totalLetters;$i++){
		for($j=0;$j<$totalLetters;$j++){
			for($k=0;$k<$totalLetters;$k++){
				$key = $userKey.$letters[$i].$letters[$j].$letters[$k];
				
				$r = mcrypt_decrypt('rijndael-128',$key,$feed,'ecb');
				if(strpos($r,$decryptedStr) !== false){
					// We have decrypted the file, return its content
					$GLBALS['decryptionKeys'][$userId] = $key;
					return $r;
				}
			}
		}
	}
}
?>
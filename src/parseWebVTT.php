<?php namespace parsewebvtt;
/*
 - File = parseWebVTT.php
 - Author = Valentin Letertre & David Lapierre
 - Licence = CeCILL-B
 - Principe = Parse file WebVTT to return list of cue objects.
	- Class Cue : it's an object to add in the database
	- Class WebVTT : return list of Cue
*/

class Cue{
	//attribut for the class Cue
	var $titre;
	var $begin;
	var $end;
	var $text;

	//Function allow to convert string to millisecond
	public static function formatStringMS($string){
		$heure = intval(substr($string, 0,2));
		$minute = intval(substr($string, 3,2));
		$seconde = intval(substr($string, 6,2));
		$ms = intval(substr($string, 9,3));

		$res = $ms + ($seconde*1000) + ($minute * 60000) + ($heure * 3600000);
		return $res;
	}
}


class WebVTT{
	public static function parseWebVTT($fileText){
		//Define de state of the line (number, time or text)
		define('WEBVTT_STATE_SUBNUMBER', 0);
		define('WEBVTT_STATE_TIME',      1);
		define('WEBVTT_STATE_TEXT',      2);
		define('WEBVTT_STATE_BLANK',     3);

		//Open the file WebVTT
		//$lines   = file('./example.vtt');
		$lines   = $fileText;
		$subs    = array();
		$state   = WEBVTT_STATE_SUBNUMBER;
		$subNum  = 0;
		$subText = '';
		$subTime = '';

		//parameter to access to the first line
		$boucle=false;
		foreach($lines as $line) {
			if($line==1 || $boucle){
			 switch($state) {
				case WEBVTT_STATE_SUBNUMBER:
				    $subNum = trim($line);
				    $state  = WEBVTT_STATE_TIME;
				    break;

				case WEBVTT_STATE_TIME:
				    $subTime = trim($line);
				    $state   = WEBVTT_STATE_TEXT;
				    break;

				case WEBVTT_STATE_TEXT:
				    if (trim($line) == '') {
					$sub = new Cue;
					$sub->titre = $subNum;
					list($begin, $end) = explode(' --> ', $subTime);
					$sub->begin = Cue::formatStringMS($begin);
					$sub->end = Cue::formatStringMS($end);
					$sub->text   = $subText;
					$subText     = '';
					$state       = WEBVTT_STATE_SUBNUMBER;

					$subs[]      = $sub;
				    } else {
					$subText .= $line;

				    }
				    break;
			 }
			 $boucle=true;
			}   
		}
		//add the last occurence
		$sub = new Cue;
		$sub->titre = $subNum;
		list($begin, $end) = explode(' --> ', $subTime);
		$sub->begin = Cue::formatStringMS($begin);
		$sub->end = Cue::formatStringMS($end);
		$sub->text   = $subText;
		$subText     = '';
		$state       = WEBVTT_STATE_SUBNUMBER;
		$subs[]      = $sub;

		return $subs;
	}
}

//To test
/*$test   = file("./example.vtt");
print_r(WebVTT::parseWebVTT($l));*/
?>

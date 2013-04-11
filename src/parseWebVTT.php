<?php 
namespace parsewebvtt;
/**
 * Parse file WebVTT to return list of cue objects.
 *	- Class Cue : it's an object to add in the database
 *	- Class WebVTT : return list of Cue
 *
 *
 * @package    mod
 * @subpackage elang
 * @copyright  2013 University of La Rochelle, France
 * @license    http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 */

class Cue{
	//attribut for the class Cue
	protected $titre;
	protected $begin;
	protected $end;
	protected $text;

	//Function allow to convert string to millisecond
	public static function formatStringMS($string){
		//If the begin or the end use format 00:00.000
		if(strlen($string)==9){
			$heure = 0;
			$minute = intval(substr($string, 0,2));
			$seconde = intval(substr($string, 3,2));
			$ms = intval(substr($string, 6,3));
		}else{
		//If the begin or the end use format 00:00:00.000
			$heure = intval(substr($string, 0,2));
			$minute = intval(substr($string, 3,2));
			$seconde = intval(substr($string, 6,2));
			$ms = intval(substr($string, 9,3));
		}
		$res = $ms + ($seconde*1000) + ($minute * 60000) + ($heure * 3600000);
		return $res;
	}
	
	//getters
	public function getTitre(){
		return $this->titre;
	}
	public function getBegin(){
		return $this->begin;
	}
	public function getend(){
		return $this->end;
	}
	public function getText(){
		return $this->text;
	}
	//setters
	public function setTitre($titre){
		$this->titre = $titre;
	}
	public function setBegin($begin){
		$this->begin = $begin;
	}
	public function setEnd($end){
		$this->end = $end;
	}
	public function setText($text){
		$this->text = $text;
	}
}


class WebVTT{

	protected $cueList = array();
	//Define de state of the line (number, time or text)
	const WEBVTT_STATE_SUBNUMBER = 0;
	const WEBVTT_STATE_TIME = 1;
	const WEBVTT_STATE_TEXT = 2;
	const WEBVTT_STATE_BLANK = 3;
	
	const REGEXP_TIME1 = "/^[0-9]{2}:[0-9]{2}.[0-9]{3}/";
	const REGEXP_TIME2 = "/^[0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{3}/";
	
	function __construct($textContents){
		$this->setCueList($this->parseWebVTT($textContents));
	}
	
	private function parseWebVTT($fileText){
		//Open the file WebVTT
		//split the file text into a list in function of : \r\n => windows, \n => linux, \r=> mac
		$lines   = preg_split("/(\r\n|\n|\r)/",$fileText);
		$subs    = array();
		$state   = WebVTT::WEBVTT_STATE_SUBNUMBER;
		$subNum  = "";
		$subText = '';
		$subTime = '';

		//variable to access to the first line
		$boucle=false;
		foreach($lines as $line) {
			if($line==1 || $boucle || strpos($line,"-->")){
				if(strpos($line, "-->")) $state = constant('parsewebvtt\WebVTT::WEBVTT_STATE_TIME');
				switch($state) {
					case  WebVTT::WEBVTT_STATE_SUBNUMBER:
						$subNum = trim($line);
						$state  = WebVTT::WEBVTT_STATE_TIME;
						break;

					case constant('parsewebvtt\WebVTT::WEBVTT_STATE_TIME'):
						$subTime = trim($line);
						$state   = WebVTT::WEBVTT_STATE_TEXT;
						break;

					case WebVTT::WEBVTT_STATE_TEXT:
						if (trim($line) == '') {
						$sub = new Cue;
						$sub->setTitre($subNum);
						list($begin, $end) = explode(' --> ', $subTime);
						//just get the end time without information as 'align:end size:50%'
						if(preg_match(WebVTT::REGEXP_TIME1,$end,$matches)||preg_match(WebVTT::REGEXP_TIME2,$end,$matches)){
							$sub->setEnd(Cue::formatStringMS($matches[0]));
						}else{
							$sub->setEnd(Cue::formatStringMS($end));
						}
						$sub->setBegin(Cue::formatStringMS($begin));
						$sub->setText($subText);
						$subText     = '';
						$state       = WebVTT::WEBVTT_STATE_SUBNUMBER;

						$subs[]      = $sub;
						} else {
						$subText .= $line;

						}
						break;
				}
				$boucle=true;
			}   
		}
		//add the last Cue from the file
		$sub = new Cue;
		$sub->setTitre($subNum);
		list($begin, $end) = explode(' --> ', $subTime);
		$sub->setBegin(Cue::formatStringMS($begin));
		$sub->setEnd(Cue::formatStringMS($end));
		$sub->setText($subText);
		$subText     = '';
		$state       = WebVTT::WEBVTT_STATE_SUBNUMBER;
		$subs[]      = $sub;
		return $subs;
	}
	
	public function setCueList($list){
		$this->cueList = $list;
	}
	public function getCueList(){
		return $this->cueList;
	}
}
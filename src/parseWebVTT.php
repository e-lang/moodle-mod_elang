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
	protected $title;
	protected $begin;
	protected $end;
	protected $text;

	//Function allow to convert string to millisecond
	public static function formatStringMS($string){
		//If the begin or the end use format 00:00.000
		if(strlen($string)==9){
			$hour = 0;
			$minute = intval(substr($string, 0,2));
			$second = intval(substr($string, 3,2));
			$ms = intval(substr($string, 6,3));
		}else{
		//If the begin or the end use format 00:00:00.000
			$hour = intval(substr($string, 0,2));
			$minute = intval(substr($string, 3,2));
			$second = intval(substr($string, 6,2));
			$ms = intval(substr($string, 9,3));
		}
		$res = $ms + ($second*1000) + ($minute * 60000) + ($hour * 3600000);
		return $res;
	}
	
	//Function allow to convert millisecond to string
	public static function formatMSString($ms){
		//Millisecond
		$uSec = $ms % 1000;
		$ms = floor($ms / 1000);
		//Second
		$second = $ms % 60;
		$ms = floor($ms / 60);
		//Minute
		$minute = $ms % 60;
		$ms = floor($ms / 60);
		//Hour
		$hour = $ms % 60;
		$ms = floor($ms / 60);
		return  sprintf("%02d", $hour) . ":" . sprintf("%02d", $minute) . ":" . sprintf("%02d", $second) . "." . sprintf("%03d", $uSec);
	}
	
	function __toString(){
		$res="";
		//Title
		if($this->title){
			$res.=$this->title;
			$res.="<br/>";
		}
		//Time
		$res .=Cue::formatMSString($this->begin).' --> '.Cue::formatMSString($this->end);
		$res.="<br/>";
		//Text
		$res .=$this->text;
		$res.="<br/>";
		return $res; 
	}
	//getters
	public function getTitle(){
		return $this->title;
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
	public function setTitle($title){
		$this->title = $title;
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

class WebVTT implements \Iterator{

	private $position = 0;
	protected $cueList = array();
	//Define de state of the line (number, time or text)
	const WEBVTT_STATE_SUBNUMBER = 0;
	const WEBVTT_STATE_TIME = 1;
	const WEBVTT_STATE_TEXT = 2;
	const WEBVTT_STATE_BLANK = 3;
	
	const REGEXP_TIME1 = "/^[0-9]{2}:[0-9]{2}.[0-9]{3}/";
	const REGEXP_TIME2 = "/^[0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{3}/";
	
	function __construct($textContents){
		$this->position = 0;
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
		$loop=false;
		$lineBefore;
		foreach($lines as $line) {
			if($loop || strpos($line,"-->")){
				if(strpos($line, "-->")){

					$state = WebVTT::WEBVTT_STATE_TIME;
					$subNum=trim($lineBefore);
				}
				switch($state) {
					case  WebVTT::WEBVTT_STATE_SUBNUMBER:
						$subNum = trim($line);
						$state  = WebVTT::WEBVTT_STATE_TIME;
						break;

					case WebVTT::WEBVTT_STATE_TIME:
						$subTime = trim($line);
						$state   = WebVTT::WEBVTT_STATE_TEXT;
						break;

					case WebVTT::WEBVTT_STATE_TEXT:
						if (trim($line) == '') {
						$sub = new Cue;
						$sub->setTitle($subNum);
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
				$loop=true;
			}
			$lineBefore=$line;
		}
		//add the last Cue from the file
		$sub = new Cue;
		$sub->setTitle($subNum);
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
	//iterator's functions
	function rewind() {
        $this->position = 0;
    }

    function current() {
        return $this->cueList[$this->position];
    }

    function key() {
        return $this->position;
    }

    function next() {
        ++$this->position;
    }

    function valid() {
        return isset($this->cueList[$this->position]);
    }
	
	//This function allow to generate WebVTT string with Cue's list 
	function __toString(){
		$it = $this;
		//header of file WebVtt
		$res = "WEBVTT";
		$res .= "<br/><br/>";
		foreach($it as $key => $value) {
			//Each Cue (call function Cue's toString function)
			$res.=$value;
			$res.="<br/>";
		}
		return $res;
	}
}
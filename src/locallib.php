<?php

/**
 * Internal library of functions for module elang
 *
 * All the elang specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 */

namespace Elang;

defined('MOODLE_INTERNAL') || die();

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
			$res.="\n";
		}
		//Time
		$res .=Cue::formatMSString($this->begin).' --> '.Cue::formatMSString($this->end);
		$res.="\n";
		//Text
		$res .=$this->text;
		$res.="\n";
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
	//Define state of the line (number, time or text)
	const WEBVTT_STATE_TIME = 1;
	const WEBVTT_STATE_TEXT = 2;
	
	const REGEXP_TIME1 = "/^[0-9]{2}:[0-9]{2}[.,][0-9]{3}/";
	const REGEXP_TIME2 = "/^[0-9]{2}:[0-9]{2}:[0-9]{2}[,.][0-9]{3}/";
	
	
	function __construct(){
		$num_args=func_num_args();
		switch ($num_args) {
			//case to create list of cues from the webVtt file, the argument must be the content's vtt file
			case 1:$this->setCueList($this->parseWebVTT(func_get_arg(0)));
		}
		$this->position = 0;
	}
	
	public function addCue($cue){
		$this->cueList[] = $cue;
	}
	
	private function parseWebVTT($fileText){
		//Open the file WebVTT
		//split the file text into a list in function of : \r\n => windows, \n => linux, \r=> mac
		$lines   = preg_split("/(\r\n|\n|\r)/",$fileText);
		$subs    = array();
		$state   = WebVTT::WEBVTT_STATE_TIME;
		$subNum  = "";
		$subText = '';
		$subTime = '';

		//variable to access to the first line
		$loop=false;
		$lineBefore;
		foreach($lines as $line) {
			if($loop || strpos($line,"-->")){
				switch($state) {
					case WebVTT::WEBVTT_STATE_TIME:
						if(strpos($line, "-->")){
							$subTime = trim($line);
							$subNum = trim($lineBefore);
							$state   = WebVTT::WEBVTT_STATE_TEXT;
						}
						break;
					case WebVTT::WEBVTT_STATE_TEXT:
							$sub = new Cue;
							$sub->setTitle($subNum);
							list($begin, $end) = explode(' --> ', $subTime);
							//just get the end time without information as 'align:end size:50%'
							if(preg_match(WebVTT::REGEXP_TIME1,$end,$matches)||preg_match(WebVTT::REGEXP_TIME2,$end,$matches)){
								$sub->setEnd(Cue::formatStringMS($matches[0]));
							}
							$sub->setBegin(Cue::formatStringMS($begin));
							$subText = $line;
							$sub->setText($subText);
							$subText     = '';
							$state       = WebVTT::WEBVTT_STATE_TIME;
							$subs[]      = $sub;
						break;
				}
				$loop=true;
			}
			$lineBefore=$line;
		}
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
		$res .= "\n\n";
		foreach($it as $key => $value) {
			//Each Cue (call function Cue's toString function)
			$res.=$value;
			$res.="\n";
		}
		return $res;
	}
	
}

/**
 * マルチバイト文字列、英数字の混じった文字列を１文字ずつ配列に分割
 * 参考:http://detail.chiebukuro.yahoo.co.jp/qa/question_detail/q1417635014#
 * mb_split, str_split, preg_splitことごとく使えない。
 */
function mbStringToArray($string, $encoding = 'UTF-8') {
	$arrayResult = array();
	while ($iLen = mb_strlen($string, $encoding)) {
		array_push($arrayResult, mb_substr($string, 0, 1, $encoding));
		$string = mb_substr($string, 1, $iLen, $encoding);
	}
	return $arrayResult;
}

/**
 * 編集距離（レーベンシュタイン距離）を求める（マルチバイト文字対応）
 * @param $str1
 * @param $str2
 * @param $encoding
 * @param $costReplace
 * @return 数値（距離）,かぶっていた文字の数
 */
function LevenshteinDistance($str1, $str2, $costReplace = 2, $encoding = 'UTF-8') {
	$count_same_letter = 0;
	$d = array();
	$mb_len1 = mb_strlen($str1, $encoding);
	$mb_len2 = mb_strlen($str2, $encoding);

	$mb_str1 = mbStringToArray($str1, $encoding);
	$mb_str2 = mbStringToArray($str2, $encoding);

	for ($i1 = 0; $i1 <= $mb_len1; $i1++) {
		$d[$i1] = array();
		$d[$i1][0] = $i1;
	}

	for ($i2 = 0; $i2 <= $mb_len2; $i2++) {
		$d[0][$i2] = $i2;
	}

	for ($i1 = 1; $i1 <= $mb_len1; $i1++) {
		for ($i2 = 1; $i2 <= $mb_len2; $i2++) {
			//			$cost = ($str1[$i1 - 1] == $str2[$i2 - 1]) ? 0 : 1;
			if ($mb_str1[$i1 - 1] === $mb_str2[$i2 - 1]) {
				$cost = 0;
				$count_same_letter++;
			} else {
				$cost = $costReplace; //置換
				}
			$d[$i1][$i2] = min($d[$i1 - 1][$i2] + 1, //挿入
			$d[$i1][$i2 - 1] + 1, //削除
			$d[$i1 - 1][$i2 - 1] + $cost);
		}
	}
	//return $d[$mb_len1][$mb_len2];
	return array('distance' => $d[$mb_len1][$mb_len2], 'count_same_letter' => $count_same_letter);
}


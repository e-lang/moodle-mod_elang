<?php

/**
 * Internal library of functions for module elang
 *
 * All the elang specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013-2015 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       0.0.1
 */

namespace Elang;

defined('MOODLE_INTERNAL') || die();

/**
 * Class Cue
 *
 * @since  0.0.1
 */
class Cue
{
	/**
	 * @var  $title  string  Cue title
	 */
	protected $title;

	/**
	 * @var  $begin  integer  Cue beginning
	 */
	protected $begin;

	/**
	 * @var  $end  integer  Cue ending
	 */
	protected $end;

	/**
	 * @var  $text  string  Cue text
	 */
	protected $text;

	/**
	 * Convert string to milliseconds
	 *
	 * @param   string  $string  String representation of time
	 *
	 * @return  integer  Time in milliseconds
	 *
	 * @since  0.0.1
	 */
	public static function millisecondsFromString($string)
	{
		if (strlen($string) == 9)
		{
			// If the begin or the end use format 00:00.000
			$hour = 0;
			$minute = intval(substr($string, 0, 2));
			$second = intval(substr($string, 3, 2));
			$ms = intval(substr($string, 6, 3));
		}
		else
		{
			// If the begin or the end use format 00:00:00.000
			$hour = intval(substr($string, 0, 2));
			$minute = intval(substr($string, 3, 2));
			$second = intval(substr($string, 6, 2));
			$ms = intval(substr($string, 9, 3));
		}

		$res = $ms + ($second * 1000) + ($minute * 60000) + ($hour * 3600000);

		return $res;
	}

	/**
	 * Convert milliseconds to string
	 *
	 * @param   integer  $ms  Time in milliseconds
	 *
	 * @return  string  String representation of time
	 *
	 * @since  0.0.1
	 */
	public static function millisecondsToString($ms)
	{
		// Millisecond
		$uSec = $ms % 1000;
		$ms = floor($ms / 1000);

		// Second
		$second = $ms % 60;
		$ms = floor($ms / 60);

		// Minute
		$minute = $ms % 60;
		$ms = floor($ms / 60);

		// Hour
		$hour = $ms % 60;
		$ms = floor($ms / 60);

		return  sprintf("%02d", $hour) . ":" . sprintf("%02d", $minute) . ":" . sprintf("%02d", $second) . "." . sprintf("%03d", $uSec);
	}

	/**
	 * toString magic method
	 *
	 * @return  string  String representation of $this
	 *
	 * @since  0.0.1
	 */
	public function __toString()
	{
		$res = "";

		// Title
		if ($this->title)
		{
			$res .= $this->title;
			$res .= "\n";
		}

		// Time
		$res .= self::millisecondsToString($this->begin) . ' --> ' . self::millisecondsToString($this->end);
		$res .= "\n";

		// Text
		$res .= $this->text;
		$res .= "\n";

		return $res;
	}

	/**
	 * title getter
	 *
	 * @return  string  The cue title
	 *
	 * @since  0.0.1
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * begin getter
	 *
	 * @return  integer  The cue begin
	 *
	 * @since  0.0.1
	 */
	public function getBegin()
	{
		return $this->begin;
	}

	/**
	 * end getter
	 *
	 * @return  integer  The cue end
	 *
	 * @since  0.0.1
	 */
	public function getend()
	{
		return $this->end;
	}

	/**
	 * text getter
	 *
	 * @return  string  The cue text
	 *
	 * @since  0.0.1
	 */
	public function getText()
	{
		return $this->text;
	}

	/**
	 * title setter
	 *
	 * @param   string  $title  The new title
	 *
	 * @return  $this  For chaining
	 *
	 * @since  0.0.1
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * begin setter
	 *
	 * @param   integer  $begin  The new begin
	 *
	 * @return  $this  For chaining
	 *
	 * @since  0.0.1
	 */
	public function setBegin($begin)
	{
		$this->begin = $begin;
	}

	/**
	 * end setter
	 *
	 * @param   integer  $end  The new end
	 *
	 * @return  $this  For chaining
	 *
	 * @since  0.0.1
	 */
	public function setEnd($end)
	{
		$this->end = $end;
	}

	/**
	 * text setter
	 *
	 * @param   string  $text  The new text
	 *
	 * @return  $this  For chaining
	 *
	 * @since  0.0.1
	 */
	public function setText($text)
	{
		$this->text = $text;
	}
}

/**
 * Parse file WebVTT to return list of cue objects.
 *
 * @since  0.0.1
 */
class WebVTT implements \Iterator
{
	/**
	 * @var  integer  $position  Current cue index
	 */
	private $position = 0;

	/**
	 * @var  array  $cueList  Cue list
	 */
	protected $cueList = array();

	/**
	 * Define state of the line (number, time or text)
	 */
	const WEBVTT_STATE_TIME = 1;
	const WEBVTT_STATE_TEXT = 2;

	/**
	 * Regular expression to parse the subtitle string
	 */
	const REGEXP_TIME1 = "/^[0-9]{2}:[0-9]{2}[.,][0-9]{3}/";
	const REGEXP_TIME2 = "/^[0-9]{2}:[0-9]{2}:[0-9]{2}[,.][0-9]{3}/";

	/**
	 * Constructor
	 *
	 * @param   string|null  $content  Content to parse
	 *
	 * @since  0.0.1
	 */
	public function __construct($content = null)
	{
		if ($content !== null)
		{
			// Create list of cues from the webVtt content
			$this->setCueList($this->parseWebVTT($content));
		}

		$this->position = 0;
	}

	/**
	 * Add a cue
	 *
	 * @param   Cue  $cue  The cue to be added
	 *
	 * @return  $this
	 *
	 * @since  0.0.1
	 */
	public function addCue($cue)
	{
		$this->cueList[] = $cue;

		return $this;
	}

	/**
	 * Change the cue list
	 *
	 * @param   array  $list  New list of cues
	 *
	 * @return  $this
	 *
	 * @since  0.0.1
	 */
	public function setCueList($list)
	{
		$this->cueList = $list;

		return $this;
	}

	/**
	 * Get the cue list
	 *
	 * @return  array  List of cues
	 *
	 * @since  0.0.1
	 */
	public function getCueList()
	{
		return $this->cueList;
	}

	/**
	 * Rewind the iterator
	 *
	 * @return  void
	 *
	 * @since  0.0.1
	 */
	public function rewind()
	{
		$this->position = 0;
	}

	/**
	 * Get the current cue
	 *
	 * @return  Cue
	 *
	 * @since  0.0.1
	 */
	public function current()
	{
		return $this->cueList[$this->position];
	}

	/**
	 * Get the current cue number
	 *
	 * @return  integer
	 *
	 * @since  0.0.1
	 */
	public function key()
	{
		return $this->position;
	}

	/**
	 * Go to the next cue
	 *
	 * @return  void
	 *
	 * @since  0.0.1
	 */
	public function next()
	{
		$this->position++;
	}

	/**
	 * Is the iterator valid
	 *
	 * @return  boolean
	 *
	 * @since  0.0.1
	 */
	public function valid()
	{
		return isset($this->cueList[$this->position]);
	}

	/**
	 * toString magic method
	 *
	 * @return  string  String representation of $this
	 *
	 * @since  0.0.1
	 */
	public function __toString()
	{
		// Header of WebVtt file
		$res = "WEBVTT";
		$res .= "\n\n";

		foreach ($this as $key => $value)
		{
			// Each Cue (call function Cue's toString function)
			$res .= $value;
			$res .= "\n";
		}

		return $res;
	}

	/**
	 * Parse a string
	 *
	 * @param   string  $content  Subtitle string
	 *
	 * @return  $this
	 *
	 * @since  0.0.1
	 */
	private function parseWebVTT($content)
	{
		// Split the file text into a list in function of : \r\n => windows, \n => linux, \r=> mac
		$lines   = preg_split("/(\r\n|\n|\r)/", $content);
		$subs    = array();
		$state   = self::WEBVTT_STATE_TIME;
		$subNum  = "";
		$subText = '';
		$subTime = '';

		// Variable to access to the first line
		$loop = false;
		$lineBefore;

		foreach ($lines as $line)
		{
			if ($loop || strpos($line, "-->"))
			{
				switch ($state)
				{
					case self::WEBVTT_STATE_TIME:
						if (strpos($line, "-->"))
						{
							$subTime = trim($line);
							$subNum = trim($lineBefore);
							$state   = self::WEBVTT_STATE_TEXT;
						}
						break;
					case self::WEBVTT_STATE_TEXT:
							$sub = new Cue;
							$sub->setTitle($subNum);
							list($begin, $end) = explode(' --> ', $subTime);

							// Just get the end time without information as 'align:end size:50%'
							if (preg_match(self::REGEXP_TIME1, $end, $matches) || preg_match(self::REGEXP_TIME2, $end, $matches))
							{
								$sub->setEnd(Cue::millisecondsFromString($matches[0]));
							}

							$sub->setBegin(Cue::millisecondsFromString($begin));
							$subText = $line;
							$sub->setText($subText);
							$subText     = '';
							$state       = self::WEBVTT_STATE_TIME;
							$subs[]      = $sub;
						break;
				}

				$loop = true;
			}

			$lineBefore = $line;
		}

		return $subs;
	}
}

/**
 * Send a json response
 *
 * @param   mixed  $response  Response to be sent
 *
 * @return  void
 *
 * @since   0.0.3
 */
function sendResponse($response)
{
	header('Content-type: application/json');

	// Get the optional jsonp callback
	$callback = optional_param('callback', '', PARAM_ALPHANUMEXT);

	// Send the response
	if (empty($callback))
	{
		echo json_encode($response);
	}
	else
	{
		echo $callback . '(' . json_encode($response) . ');';
	}

	die;
}

/**
 * Generate an elang title
 *
 * @param   object  $elang    E-Lang object
 * @param   array   $options  Array of options
 *
 * @return  string  String representation of the title
 *
 * @since  0.0.1
 */
function generateTitle($elang, $options)
{
	// Get all the languages
	$languages = getLanguages();

	// Get the page title
	if ($options['showlanguage'])
	{
		return sprintf(get_string('formatname', 'elang'), $elang->name, $languages[$elang->language]);
	}
	else
	{
		return $elang->name;
	}
}

/**
 * Generate a cue text
 *
 * @param   array    $data      Cue data
 * @param   array    $user      User data
 * @param   string   $char      Character used for filling blanks
 * @param   integer  $repeated  Number of times $char is repeated
 *
 * @return  string  String representation of the cue
 *
 * @since  0.0.1
 */
function generateCueText($data, $user, $char='-', $repeated = 10)
{
	$text = array();

	foreach ($data as $number => $element)
	{
		if ($element['type'] == 'input')
		{
			if (isset($user[$number]))
			{
				if (!empty($user[$number]['content']))
				{
					if ($user[$number]['content'] == $element['content'])
					{
						$text[] = '<c.success>' . $user[$number]['content'] . '</c>';
					}
					else
					{
						$text[] = '<c.error>' . $user[$number]['content'] . '</c>';
					}
				}
				elseif ($user[$number]['help'])
				{
					$text[] = '<c.help>' . $element['content'] . '</c>';
				}
				else
				{
					$text[] = str_repeat($char, ((int) ((mb_strlen($element['content'], 'UTF-8') - 1) / $repeated) + 1) * $repeated);
				}
			}
			else
			{
				$text[] = str_repeat($char, ((int) ((mb_strlen($element['content'], 'UTF-8') - 1) / $repeated) + 1) * $repeated);
			}
		}
		else
		{
			$text[] = $element['content'];
		}
	}

	return implode($text);
}

/**
 * Save files for an instance
 *
 * @param   object  $elang  An object from the form in mod_form.php
 *
 * @return void
 *
 * @since  0.0.1
 */
function saveFiles(\stdClass $elang)
{
	global $DB;

	require_once dirname(__FILE__) . '/locallib.php';

	$id = $elang->id;
	$cmid = $elang->coursemodule;
	$context = \context_module::instance($cmid);

	// Storage of files from the filemanager (videos):
	$draftitemid = $elang->videos;

	if ($draftitemid)
	{
		file_save_draft_area_files(
			$draftitemid,
			$context->id,
			'mod_elang',
			'videos',
			0
		);
	}

	// Storage of files from the filemanager (subtitle):
	$draftitemid = $elang->subtitle;

	if ($draftitemid)
	{
		file_save_draft_area_files(
			$draftitemid,
			$context->id,
			'mod_elang',
			'subtitle',
			0
		);
	}

	// Storage of files from the filemanager (poster):
	$draftitemid = $elang->poster;

	if ($draftitemid)
	{
		file_save_draft_area_files(
			$draftitemid,
			$context->id,
			'mod_elang',
			'poster',
			0
		);
	}

	// Delete old records
	$DB->delete_records('elang_cues', array('id_elang' => $id));
	$DB->delete_records('elang_users', array('id_elang' => $id));

	$fs = get_file_storage();
	$files = $fs->get_area_files($context->id, 'mod_elang', 'subtitle', 0);

	foreach ($files as $file)
	{
		if ($file->get_source())
		{
			$contents = $file->get_content();

			// Detect bom
			$bom = pack("CCC", 0xef, 0xbb, 0xbf);

			if (0 == strncmp($contents, $bom, 3))
			{
				$contents = substr($contents, 3);
			}

			$vtt = new WebVTT($contents);

			$cue = new \stdClass;

			foreach ($vtt->getCueList() as $i => $elt)
			{
				$cue->id_elang = $id;
				$title = $elt->getTitle();
				$text = strip_tags($elt->getText());

				if (empty($title) || is_numeric($title))
				{
					$title = preg_replace('/(\[[^\]]*\]|{[^}]*})/', '...', $text);

					if (mb_strlen($title, 'UTF-8') > $elang->titlelength)
					{
						$cue->title = preg_replace('/ [^ ]*$/', ' ...', mb_substr($title, 0, $elang->titlelength, 'UTF-8'));
					}
					else
					{
						$cue->title = $title;
					}
				}
				else
				{
					$cue->title	= $title;
				}

				$cue->begin	= $elt->getBegin();
				$cue->end = $elt->getend();
				$cue->number = $i + 1;
				$texts = preg_split('/(\[[^\]]*\]|{[^}]*})/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
				$data = array();
				$i = 1;

				foreach ($texts as $text)
				{
					if (isset($text[0]))
					{
						if ($text[0] == '[' && $text[strlen($text) - 1] == ']')
						{
							$data[] = array('type' => 'input', 'content' => substr($text, 1, strlen($text) - 2), 'order' => $i++, 'help' => true);
						}
						elseif ($text[0] == '{' && $text[strlen($text) - 1] == '}')
						{
							$data[] = array('type' => 'input', 'content' => substr($text, 1, strlen($text) - 2), 'order' => $i++, 'help' => false);
						}
						else
						{
							$data[] = array('type' => 'text', 'content' => $text);
						}
					}
				}

				$cue->json = json_encode($data);
				$DB->insert_record('elang_cues', $cue);
			}
		}
	}
}

/**
 * Split a string into array of multi-bytes characters
 *
 * @param   string  $string    Multi-bytes string
 * @param   string  $encoding  String encoding
 *
 * @return  array  Array of multi-bytes characteres
 *
 * @since  0.0.1
 */
function mbStringToArray($string, $encoding = 'UTF-8')
{
	$arrayResult = array();

	while ($iLen = mb_strlen($string, $encoding))
	{
		array_push($arrayResult, mb_substr($string, 0, 1, $encoding));
		$string = mb_substr($string, 1, $iLen, $encoding);
	}

	return $arrayResult;
}

/**
 * Compute the Levenshtein distance between two multi-bytes string
 *
 * @param   string   $str1         First string
 * @param   string   $str2         Second string
 * @param   integer  $costReplace  Replacement cost
 * @param   string   $encoding     Strings encoding
 *
 * @return  number  Levenshtein distance between $str1 and $str2
 *
 * @since  0.0.1
 */
function LevenshteinDistance($str1, $str2, $costReplace = 2, $encoding = 'UTF-8')
{
	$d = array();
	$mb_len1 = mb_strlen($str1, $encoding);
	$mb_len2 = mb_strlen($str2, $encoding);

	$mb_str1 = mbStringToArray($str1, $encoding);
	$mb_str2 = mbStringToArray($str2, $encoding);

	for ($i1 = 0; $i1 <= $mb_len1; $i1++)
	{
		$d[$i1] = array();
		$d[$i1][0] = $i1;
	}

	for ($i2 = 0; $i2 <= $mb_len2; $i2++)
	{
		$d[0][$i2] = $i2;
	}

	for ($i1 = 1; $i1 <= $mb_len1; $i1++)
	{
		for ($i2 = 1; $i2 <= $mb_len2; $i2++)
		{
			$d[$i1][$i2] = min(
				$d[$i1 - 1][$i2] + 1,
				$d[$i1][$i2 - 1] + 1,
				$d[$i1 - 1][$i2 - 1] + ($mb_str1[$i1 - 1] === $mb_str2[$i2 - 1] ? 0 : $costReplace)
			);
		}
	}

	return $d[$mb_len1][$mb_len2];
}

/**
 * Get the list of all languages
 *
 * @return  array  Map array of the form tag => Language name
 *
 * @since  0.0.1
 */
function getLanguages()
{
	return array(
		'af-ZA' => 'Afrikaans (South Africa)',
		'ar-AA' => 'Arabic Unitag (العربية الموحدة)',
		'hy-AM' => 'Armenian',
		'az-AZ' => 'Azeri-Azərbaycanca (Azərbaycan)',
		'id-ID' => 'Bahasa Indonesia',
		'be-BY' => 'Belarusian-Беларуская (Беларусь)',
		'bn-BD' => 'Bengali (Bangladesh)',
		'bs-BA' => 'Bosanski (Bosnia)',
		'bg-BG' => 'Bulgarian (Български)',
		'ca-ES' => 'Catalan',
		'zh-CN' => 'Chinese Simplified 简体中文',
		'zh-TW' => 'Chinese Traditional (Taiwan)',
		'hr-HR' => 'Croatian',
		'cs-CZ' => 'Czech (Czech republic)',
		'da-DK' => 'Danish (DK)',
		'en-AU' => 'English (Australia)',
		'en-GB' => 'English (United Kingdom)',
		'en-US' => 'English (United States)',
		'eo-XX' => 'Esperanto',
		'et-EE' => 'Estonian',
		'eu-ES' => 'Euskara (Basque)',
		'fi-FI' => 'Finnish (Suomi)',
		'fr-FR' => 'Français (Fr)',
		'gl-ES' => 'Galician (Galiza)',
		'de-DE' => 'German (DE-CH-AT)',
		'el-GR' => 'Greek',
		'gu-IN' => 'Gujarati (India)',
		'he-IL' => 'Hebrew (Israel)',
		'hi-IN' => 'Hindi-हिंदी (India)',
		'hu-HU' => 'Hungarian (Magyar)',
		'it-IT' => 'Italian (Italy)',
		'ja-JP' => 'Japanese 日本語',
		'km-KH' => 'Khmer (Cambodia)',
		'ko-KR' => 'Korean (Republic of Korea)',
		'ckb-IQ' => 'Kurdish Soran&icirc; (کوردى)',
		'lo-LA' => 'Lao-ລາວ(ພາສາລາວ)',
		'lv-LV' => 'Latvian (LV)',
		'lt-LT' => 'Lithuanian',
		'mk-MK' => 'Macedonian-Македонски',
		'ml-IN' => 'Malayalam-മലയാളം(India)',
		'mn-MN' => 'Mongolian-Монгол (Монгол Улс)',
		'nl-NL' => 'Nederlands nl-NL',
		'nb-NO' => 'Norsk bokm&aring;l (Norway)',
		'nn-NO' => 'Norsk nynorsk (Norway)',
		'fa-IR' => 'Persian (پارسی)',
		'pl-PL' => 'Polski (Polska)',
		'pt-BR' => 'Portugu&ecirc;s (Brasil)',
		'pt-PT' => 'Portugu&ecirc;s (pt-PT)',
		'ro-RO' => 'Rom&acirc;nă (Rom&acirc;nia)',
		'ru-RU' => 'Russian-Русский (CIS)',
		'gd-GB' => 'Scottish Gaelic (GB)',
		'sr-RS' => 'Serbian (Cyrilic)',
		'sr-YU' => 'Serbian (Latin)',
		'sq-AL' => 'Shqip-AL',
		'sk-SK' => 'Slovak (Slovenčina)',
		'es-ES' => 'Spanish (Espa&ntilde;ol)',
		'sv-SE' => 'Svenska (Sverige)',
		'sw-KE' => 'Swahili',
		'sy-IQ' => 'Syriac (Iraq)',
		'ta-IN' => 'Tamil-தமிழ் (India)',
		'th-TH' => 'Thai-ไทย (ภาษาไทย)',
		'tr-TR' => 'T&uuml;rk&ccedil;e (T&uuml;rkiye)',
		'uk-UA' => 'Ukrainian-Українська (Україна)',
		'ur-PK' => 'Urdu Pakistan (اردو)',
		'ug-CN' => 'Uyghur (ئۇيغۇرچە)',
		'vi-VN' => 'Vietnamese (Vietnam)',
		'cy-GB' => 'Welsh (United Kingdom)'
	);
}

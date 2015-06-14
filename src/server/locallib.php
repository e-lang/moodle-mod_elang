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

require_once dirname(__FILE__) . '/vendor/autoload.php';

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
						if (empty($_SERVER['HTTP_USER_AGENT']))
						{
							$text[] = str_repeat($char, ((int) ((mb_strlen($element['content'], 'UTF-8') - 1) / $repeated) + 1) * $repeated);
						}
						else
						{
							$user_agent = $_SERVER['HTTP_USER_AGENT'];

							if (preg_match('/MSIE/i', $user_agent) || preg_match('/Trident/i', $user_agent))
							{
								$text[] = str_repeat($char, ((int) ((mb_strlen($element['content'], 'UTF-8') - 1) / $repeated) + 1) * $repeated);
							}
							else
							{
								$text[] = '<c.error><i>' . $user[$number]['content'] . '</i></c>';
							}
						}
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
 * Detect and transcode encoding of a string into UTF-8 or another given encoding (using iconv instead the less reliable mb_convert_encoding)
 *
 * @param   string  $contents     the string to be analysed and to be converted (as reference)
 * @param   string  $encoding_to  the target encoding
 *
 * @return  string  the new content
 *
 * @since  1.1.0
 *
 * @author  Ralf Erlebach
 */
function transcodeSubtitle($contents, $encoding_to = 'UTF-8')
{
	// $encoding_to is explicitely given as null, there is nothing to do
	if (!$encoding_to)
	{
		return $contents;
	}

	// Set the assumed encoding of the string as false
	$encoding = false;

	// Detect bom for different encodings and strip it from the string
	$bom_encoding = array(
		'UTF-32BE' => pack("CCCC", 0x00, 0x00, 0xFE, 0xFF),
		'UTF-32LE' => pack("CCCC", 0xFF, 0xFE, 0x00, 0x00),
		'GB-18030' => pack("CCCC", 0x84, 0x31, 0x95, 0x33),
		'UTF-8' => pack("CCC", 0xEF, 0xBB, 0xBF),
		'UTF-16BE' => pack("CC", 0xFE, 0xFF),
		'UTF-16LE' => pack("CC", 0xFF, 0xFE)
	);

	foreach ($bom_encoding AS $enc => $bom)
	{
		if (0 == strncmp($contents, $bom, strlen($bom)))
		{
			$contents = substr($contents, strlen($bom));
			$encoding = $enc;
			break;
		}
	}

	if (!$encoding)
	{
		// Try to detect concurrent encoding and convert into UTF-8
		$detect_order = array(
			'ASCII',
			'UTF-8',
			'UCS-2',
			'UCS-2BE',
			'UCS-2LE',
			'UCS-4',
			'UCS-4BE',
			'UCS-4LE',
			'UTF-16',
			'UTF-16BE',
			'UTF-16LE',
			'UTF-32',
			'UTF-32BE',
			'UTF-32LE',
			'ISO-8859-1',
			'ISO-8859-2',
			'ISO-8859-3',
			'ISO-8859-4',
			'ISO-8859-5',
			'ISO-8859-6',
			'ISO-8859-7',
			'ISO-8859-8',
			'ISO-8859-9',
			'ISO-8859-10',
			'ISO-8859-11',
			'ISO-8859-13',
			'ISO-8859-14',
			'ISO-8859-15',
			'ISO-8859-16',
			'KOI8-R',
			'KOI8-U',
			'KOI8-RU',
			'WINDOWS-1250',
			'WINDOWS-1251',
			'WINDOWS-1252',
			'WINDOWS-1253',
			'WINDOWS-1254',
			'WINDOWS-1255',
			'WINDOWS-1256',
			'WINDOWS-1257',
			'WINDOWS-1258',
			'MACROMAN',
			'MACCENTRALEUROPE',
			'MACICELAND',
			'MACCROATIAN',
			'MACROMANIA',
			'MACCYRILLIC',
			'MACUKRAINE',
			'MACGREEK',
			'MACTURKISH',
			'MACHEBREW',
			'MACARABIC',
			'MACTHAI',
			'KOI8-T',
			'ISO-IR-166',
			'WINDOWS-874',
			'ISO-IR-14',
			'JISX0201-1976',
			'ISO-IR-87',
			'ISO-IR-159',
			'GB_1988-80',
			'GB_2312-80',
			'ISO-IR-149',
			'EUC-JP',
			'SHIFT-JIS',
			'ISO-2022-JP',
			'ISO-2022-JP-1',
			'ISO-2022-JP-2',
			'EUC-CN',
			'GBK',
			'WINDOWS-936',
			'GB18030',
			'ISO-2022-CN',
			'BIG-5',
			'CP950',
			'EUC-KR',
			'ISO-2022-KR',
			'DEC-KANJI',
			'DEC-HANYU',
			'EUC-JISX0213',
			'SHIFT_JISX0213',
			'ISO-2022-JP-3'
		);

		// Test for all encodings given above
		foreach ($detect_order as $enc)
		{
			if ($contents === @iconv($enc, $enc, $contents))
			{
				$encoding = $enc;
				break;
			}
		}
	}

	// Convert the encoding
	if ($encoding)
	{
		return iconv($encoding, $encoding_to . '//IGNORE', $contents);
	}
	else
	{
		return false;
	}
}

/**
 * Save files for an instance
 *
 * @param   object               $elang  An object from the form in mod_form.php
 * @param   \mod_elang_mod_form  $mform  The form
 *
 * @return void
 *
 * @since  0.0.1
 */
function saveFiles(\stdClass $elang, \mod_elang_mod_form $mform)
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

	$cue = new \stdClass;

	foreach ($mform->getVtt()->getCues() as $i => $elt)
	{
		$cue->id_elang = $id;
		$text = strip_tags($elt->getText());

		$title = preg_replace('/(\[[^\]]*\]|{[^}]*})/', '...', $text);

		if (mb_strlen($title, 'UTF-8') > $elang->titlelength)
		{
			$cue->title = preg_replace('/ [^ ]*$/', ' ...', mb_substr($title, 0, $elang->titlelength, 'UTF-8'));
		}
		else
		{
			$cue->title = $title;
		}

		$cue->begin	= $elt->getStartMS();
		$cue->end = $elt->getStopMS();
		$cue->number = $i + 1;
		$texts = preg_split('/(\[[^\]]*\]|{[^}]*})/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
		$data = array();

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
function levenshteinDistance($str1, $str2, $costReplace = 2, $encoding = 'UTF-8')
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
		'ckb-IQ' => 'Kurdish Soranî (کوردى)',
		'lo-LA' => 'Lao-ລາວ(ພາສາລາວ)',
		'lv-LV' => 'Latvian (LV)',
		'lt-LT' => 'Lithuanian',
		'mk-MK' => 'Macedonian-Македонски',
		'ml-IN' => 'Malayalam-മലയാളം(India)',
		'mn-MN' => 'Mongolian-Монгол (Монгол Улс)',
		'nl-NL' => 'Nederlands nl-NL',
		'nb-NO' => 'Norsk bokmål (Norway)',
		'nn-NO' => 'Norsk nynorsk (Norway)',
		'fa-IR' => 'Persian (پارسی)',
		'pl-PL' => 'Polski (Polska)',
		'pt-BR' => 'Português (Brasil)',
		'pt-PT' => 'Português (pt-PT)',
		'ro-RO' => 'Română (România)',
		'ru-RU' => 'Russian-Русский (CIS)',
		'gd-GB' => 'Scottish Gaelic (GB)',
		'sr-RS' => 'Serbian (Cyrilic)',
		'sr-YU' => 'Serbian (Latin)',
		'sq-AL' => 'Shqip-AL',
		'sk-SK' => 'Slovak (Slovenčina)',
		'es-ES' => 'Spanish (Español)',
		'sv-SE' => 'Svenska (Sverige)',
		'sw-KE' => 'Swahili',
		'sy-IQ' => 'Syriac (Iraq)',
		'ta-IN' => 'Tamil-தமிழ் (India)',
		'th-TH' => 'Thai-ไทย (ภาษาไทย)',
		'tr-TR' => 'Türkçe (Türkiye)',
		'uk-UA' => 'Ukrainian-Українська (Україна)',
		'ur-PK' => 'Urdu Pakistan (اردو)',
		'ug-CN' => 'Uyghur (ئۇيغۇرچە)',
		'vi-VN' => 'Vietnamese (Vietnam)',
		'cy-GB' => 'Welsh (United Kingdom)'
	);
}

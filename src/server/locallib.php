<?php

/**
 * Internal library of functions for module elang
 *
 * All the elang specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013-2016 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       0.0.1
 */

namespace Elang;

defined('MOODLE_INTERNAL') || die();

if (is_file(dirname(__FILE__) . '/vendor/autoload.php'))
{
	require_once dirname(__FILE__) . '/vendor/autoload.php';
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
 * Get the list of supported encodings
 *
 * @return  array  Supported encodings
 *
 * @since  1.3.0
 */
function getEncodings()
{
	return array(
		"UTF-8",
		"ASCII",
		"ISO-8859-1",
		"ISO-8859-2",
		"ISO-8859-3",
		"ISO-8859-4",
		"ISO-8859-5",
		"ISO-8859-6",
		"ISO-8859-7",
		"ISO-8859-8",
		"ISO-8859-9",
		"ISO-8859-10",
		"ISO-8859-13",
		"ISO-8859-14",
		"ISO-8859-15",
		"ISO-8859-16",
		"KOI8-R",
		"KOI8-U",
		"Windows-1251",
		"Windows-1252",
		"Windows-1254"
	);
}

/**
 * Detect and transcode encoding of a string into UTF-8 or another given encoding (using iconv instead the less reliable mb_convert_encoding)
 *
 * @param   string  $contents      the string to be analysed and to be converted (as reference)
 * @param   string  $encoding_to   the target encoding
 * @param   string  $detect_order  the list of encodings in a specicific order
 *
 * @return  string  the new content
 *
 * @since  1.1.0
 *
 * @author  Ralf Erlebach
 */
function transcodeSubtitle($contents, $encoding_to = 'UTF-8', $detect_order = null)
{
	// $encoding_to is explicitely given as null, there is nothing to do
	if (!$encoding_to)
	{
		return $contents;
	}

	// Get the detect order
	if ($detect_order == null)
	{
		$detect_order = getEncodings();
	}

	if (function_exists('mb_detect_encoding'))
	{
		$encoding = mb_detect_encoding($contents, $detect_order, true);
	}
	else
	{
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
			// Convert to md5 for fast comparison
			$md5 = md5($contents);

			// Try to detect concurrent encoding and convert into UTF-8
			foreach ($detect_order as $enc)
			{
				if ($md5 === md5(@iconv($enc, $enc, $contents)))
				{
					$encoding = $enc;
					break;
				}
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

	$cues = $mform->getVtt()->getCues();

	if ($cues)
	{
		foreach ($cues as $i => $elt)
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
					// Detect type of part
					if ($text[0] == '[' && $text[strlen($text) - 1] == ']')
					{
						// Input text with help button
						$help = true;
					}
					elseif ($text[0] == '{' && $text[strlen($text) - 1] == '}')
					{
						// Input text without help button
						$help = false;
					}
					else
					{
						// Display text
						$help = null;
					}

					if ($help === null)
					{
						// Display text
						$data[] = array('type' => 'text', 'content' => $text);
					}
					else
					{
						// Input text
						preg_match('/([^(]*)(\((.*)\))?$/', substr($text, 1, strlen($text) - 2), $results);
						$text = preg_replace(array('/^\s*/', '/\s*$/', '/\s+/'), array('', '', ' '), $results[1]);
						$element = array('type' => 'input', 'content' => $text, 'order' => $i++, 'help' => $help);

						// Help button detected
						if (isset($results[3]))
						{
							$element['link'] = $results[3];
						}

						$data[] = $element;
					}
				}
			}

			$cue->json = json_encode($data);
			$DB->insert_record('elang_cues', $cue);
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
 * Compute the Jaro distance between two multi-bytes string
 *
 * @param   string  $str1  First string
 * @param   string  $str2  Second string
 *
 * @return  float  Jaro distance between $str1 and $str2
 */
function jaro($str1, $str2)
{
	$str1_len   = mb_strlen($str1);
	$str2_len   = mb_strlen($str2);
	$range      = intval(max($str1_len, $str2_len) - 1);

	// Found common chars in each string
	$str1_match = str_repeat("0", $str1_len);
	$str2_match = str_repeat("0", $str2_len);

	// Search all matching characters
	$common_chars = 0;

	for ($i = 0; $i < $str1_len; $i++)
	{
		$bottom_limit = max(0, $i - $range);
		$top_limit    = min($str2_len, $i + $range);

		for ($j = $bottom_limit; $j <= $top_limit; $j++)
		{
			if (mb_substr($str1, $i, 1) == mb_substr($str2, $j, 1) && $str2_match[$j] != "1")
			{
				$str1_match[$i] = "1";
				$str2_match[$j] = "1";
				$common_chars++;
				break;
			}
		}
	}

	// No characters in common
	if ($common_chars == 0)
	{
		return 0.;
	}

	$k              = 0;
	$transpositions = 0;

	for ($i = 0; $i < $str1_len; $i++)
	{
		if ($str1_match[$i] == "1")
		{
			for ($j = $k; $j < $str2_len; $j++)
			{
				if ($str2_match[$j] == "1")
				{
					$k = $j + 1;
					break;
				}
			}

			if ($str1[$i] != $str2[$j])
			{
				$transpositions++;
			}
		}
	}

	$m = floatval($common_chars);
	$t = floatval($transpositions) / 2.;

	return floatval(($m / $str1_len + $m / $str2_len + ($m - $t) / $m) / 3.);
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

/**
 * Get the list of all locales
 *
 * @param   string  $tag  Language tag
 *
 * @return  array  List of locale (windows and linux) for the given language
 *
 * @since  1.3.0
 */
function getLocale($tag)
{
	$locales = array(
		'af-ZA' => array('af_ZA.UTF-8', 'Afrikaans_South Africa.1252'),
		'ar-AA' => array('ar_SA.UTF-8',	'Arabic_Saudi Arabia.1256'),
		'hy-AM' => array('hy_AM.UTF-8', 'Armenian'),
		'az-AZ' => array('az_AZ.UTF-8'),
		'id-ID' => array('id_ID.UTF-8', 'Indonesian_indonesia.1252'),
		'be-BY' => array('be_BY.UTF-8', 'Belarusian_Belarus.1251'),
		'bn-BD' => array('bn_IN.UTF-8'),
		'bs-BA' => array('bs_BA.UTF-8', 'Bosnian (Latin)'),
		'bg-BG' => array('bg_BG.UTF-8', 'Bulgarian_Bulgaria.1251'),
		'ca-ES' => array('ca_ES.UTF-8', 'Catalan_Spain.1252'),
		'zh-CN' => array('zh_CN.UTF-8', 'Chinese_China.936'),
		'zh-TW' => array('zh_TW.UTF-8', 'Chinese_Taiwan.950'),
		'hr-HR' => array('hr_HR.UTF-8', 'Croatian_Croatia.1250'),
		'cs-CZ' => array('cs_CZ.UTF-8', 'Czech_Czech Republic.1250'),
		'da-DK' => array('da_DK.UTF-8', 'Danish_Denmark.1252'),
		'en-AU' => array('en_AU.UTF-8', 'english-aus'),
		'en-GB' => array('en_GB.UTF-8', 'english-uk'),
		'en-US' => array('en_US.UTF-8', 'english-us'),
		'eo-XX' => array('en_XX.UTF-8'),
		'et-EE' => array('et_EE.UTF-8', 'Estonian_Estonia.1257'),
		'eu-ES' => array('eu_ES.UTF-8', 'Basque_Spain.1252'),
		'fi-FI' => array('fi_FI.UTF-8', 'Finnish_Finland.1252'),
		'fr-FR' => array('fr_FR.UTF-8', 'fr_CH.UTF-8', 'fr_BE.UTF-8', 'French_France.1252'),
		'gl-ES' => array('gl_ES.UTF-8', 'Galician_Spain.1252'),
		'de-DE' => array('de_DE.UTF-8', 'German_Germany.1252'),
		'el-GR' => array('el_GR.UTF-8', 'Greek_Greece.1253'),
		'gu-IN' => array('gu.UTF-8', 'Gujarati_India.0'),
		'he-IL' => array('he_IL.utf8', 'Hebrew_Israel.1255'),
		'hi-IN' => array('hi_IN.UTF-8', 'Hindi.65001'),
		'hu-HU' => array('hu.UTF-8', 'Hungarian_Hungary.1250'),
		'it-IT' => array('it_IT.UTF-8', 'Italian_Italy.1252'),
		'ja-JP' => array('ja_JP.UTF-8', 'Japanese_Japan.932'),
		'km-KH' => array('km_KH.UTF-8', 'Khmer.65001'),
		'ko-KR' => array('ko_KR.UTF-8', 'Korean_Korea.949'),
		'ckb-IQ' => array('ckb_IQ.UTF-8'),
		'lo-LA' => array('lo_LA.UTF-8', 'Lao_Laos.UTF-8'),
		'lv-LV' => array('lat.UTF-8', 'Latvian_Latvia.1257'),
		'lt-LT' => array('lt_LT.UTF-8', 'Lithuanian_Lithuania.1257'),
		'mk-MK' => array('mk_MK.UTF-8'),
		'ml-IN' => array('ml_IN.UTF-8', 'Malayalam_India.x-iscii-ma'),
		'mn-MN' => array('mn.UTF-8', 'Cyrillic_Mongolian.1251'),
		'nl-NL' => array('nl_NL.UTF-8', 'Dutch_Netherlands.1252'),
		'nb-NO' => array('nb_NO.UTF-8',),
		'nn-NO' => array('nn_NO.UTF-8', 'Norwegian-Nynorsk_Norway.1252'),
		'fa-IR' => array('fa_IR.UTF-8', 'Farsi_Iran.1256'),
		'pl-PL' => array('pl.UTF-8', 'Polish_Poland.1250'),
		'pt-BR' => array('pt_BR.UTF-8', 'Portuguese_Brazil.1252'),
		'pt-PT' => array('pt_PT.UTF-8', 'Portuguese_Portugal.1252'),
		'ro-RO' => array('ro_RO.UTF-8', 'Romanian_Romania.1250'),
		'ru-RU' => array('ru_RU.UTF-8', 'Russian_Russia.1251'),
		'gd-GB' => array('gd_GB.UTF-8'),
		'sr-RS' => array('sr_RS.UTF-8'),
		'sr-YU' => array('SH_YU.UTF-8'),
		'sq-AL' => array('sq_AL.UTF-8', 'Albanian_Albania.1250'),
		'sk-SK' => array('sk_SK.UTF-8', 'Slovak_Slovakia.1250'),
		'es-ES' => array('es_ES.UTF-8', 'Spanish_Spain.1252'),
		'sv-SE' => array('sv_SE.UTF-8', 'Swedish_Sweden.1252'),
		'sw-KE' => array('sw_KE.UTF-8'),
		'ta-IN' => array('ta_IN.UTF-8', 'English_Australia.1252'),
		'th-TH' => array('th_TH.UTF-8', 'Thai_Thailand.874'),
		'tr-TR' => array('tr_TR.UTF-8', 'Turkish_Turkey.1254'),
		'uk-UA' => array('uk_UA.UTF-8', 'Ukrainian_Ukraine.1251'),
		'ur-PK' => array('ur_PK.UTF-8'),
		'ug-CN' => array('ug_CN/UTF-8'),
		'vi-VN' => array('vi_VN.UTF-8'),
		'cy-GB' => array('CY_GB.UTF-8')
	);

	if (array_key_exists($tag, $locales))
	{
		return $locales[$tag];
	}
	else
	{
		return array();
	}
}

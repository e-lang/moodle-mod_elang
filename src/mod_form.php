<?php

/**
 * The main elang configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package	 mod
 * @subpackage  elang
 * @copyright   2013 University of La Rochelle, France
 * @license	 http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 */

defined('MOODLE_INTERNAL') || die();

require_once $CFG->dirroot . '/course/moodleform_mod.php';

/**
 * Module instance settings form
 *
 * @since  0.0.1
 */
class mod_elang_mod_form extends moodleform_mod
{
	/**
	 * Defines forms elements
	 *
	 * @return  void
	 */
	public function definition()
	{
		// Get the setting for elang
		$config = get_config('elang');

		// Get the form
		$mform = $this->_form;

		// Adding the "general" fieldset, where all the common settings are showed
		$mform->addElement('header', 'general', get_string('general', 'form'));

		// Adding the standard "name" field
		$mform->addElement('text', 'name', get_string('elangname', 'elang'), array('size' => '64'));

		if (!empty($CFG->formatstringstriptags))
		{
			$mform->setType('name', PARAM_TEXT);
		}
		else
		{
			$mform->setType('name', PARAM_CLEAN);
		}

		$mform->addRule('name', null, 'required', null, 'client');
		$mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
		$mform->addHelpButton('name', 'elangname', 'elang');

		// Adding the standard "intro" and "introformat" fields
		$this->add_intro_editor($config->requiremodintro);

		// Adding the rest of elang settings, spreeading all them into this fieldset
		$mform->addElement('header', 'elangfieldset', get_string('upload', 'elang'));
		$mform->addElement(
			'select',
			'language',
			get_string('language', 'elang'),
			array(
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
				'fr-FR" selected="selected' => 'Fran&ccedil;ais (Fr)',
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
			)
		)->setSelected('en-GB');
		$mform->addRule('language', null, 'required', null, 'client');
		$mform->addElement(
			'filemanager',
			'videos',
			get_string('videos', 'elang'),
			null,
			array('subdirs' => 0, 'maxbytes' => $config->videomaxsize, 'maxfiles' => 20, 'accepted_types' => array('video'))
		);
		$mform->addHelpButton('videos', 'videos', 'elang');
		$mform->addRule('videos', null, 'required', null, 'client');

		$mform->addElement(
			'filemanager',
			'subtitle',
			get_string('subtitle', 'elang'),
			null,
			array('subdirs' => 0, 'maxbytes' => $config->subtitlemaxsize, 'maxfiles' => 1, 'accepted_types' => array('.vtt'))
		);
		$mform->addHelpButton('subtitle', 'subtitle', 'elang');
		$mform->addRule('subtitle', null, 'required', null, 'client');

		$mform->addElement(
			'filemanager',
			'poster',
			get_string('poster', 'elang'),
			null,
			array('subdirs' => 0, 'maxbytes' => $config->postermaxsize, 'maxfiles' => 1, 'accepted_types' => array('image'))
		);
		$mform->addHelpButton('poster', 'poster', 'elang');

		// Add standard elements, common to all modules
		$this->standard_coursemodule_elements();

		// Add standard buttons, common to all modules
		$this->add_action_buttons();
	}

	/**
	 * Preprocess data before creating form
	 *
	 * @param   array  &$default_values  Array of default values
	 *
	 * @return  void
	 */
	public function data_preprocessing(&$default_values)
	{
		if ($this->current->instance)
		{
			$draftitemid = file_get_submitted_draft_itemid('videos');
			file_prepare_draft_area(
				$draftitemid,
				$this->context->id,
				'mod_elang',
				'videos',
				$this->current->id
			);
			$default_values['videos'] = $draftitemid;

			$draftitemid = file_get_submitted_draft_itemid('subtitle');
			file_prepare_draft_area(
				$draftitemid,
				$this->context->id,
				'mod_elang',
				'subtitle',
				$this->current->id
			);
			$default_values['subtitle'] = $draftitemid;

			$draftitemid = file_get_submitted_draft_itemid('poster');
			file_prepare_draft_area(
				$draftitemid,
				$this->context->id,
				'mod_elang',
				'poster',
				$this->current->id
			);
			$default_values['poster'] = $draftitemid;
		}
	}
}

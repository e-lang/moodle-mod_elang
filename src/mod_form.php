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
		$languages = elang_get_languages();
		$options = array();
		foreach (explode(',', $config->language) as $key)
		{
			$options[$key] = $languages[$key];
		}
		$element = $mform->addElement('select', 'language', get_string('language', 'elang'), $options);
		if ($options['en-GB'])
		{
			$element->setSelected('en-GB');
		}
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

		// Adding the "general" fieldset, where all the common settings are showed
		$mform->addElement('header', 'setting', get_string('settings', 'elang'));
		$mform->addElement('checkbox', 'showlanguage', get_string('showlanguage', 'elang'));
		$mform->addHelpButton('showlanguage', 'showlanguage', 'elang');
		$mform->addElement('text', 'repeatedunderscore', get_string('repeatedunderscore', 'elang'));
		$mform->addHelpButton('repeatedunderscore', 'repeatedunderscore', 'elang');
		$mform->addRule('repeatedunderscore', get_string('repeatedunderscore_error', 'elang'), 'numeric', null, 'client');
		$mform->addElement('text', 'titlelength', get_string('titlelength', 'elang'));
		$mform->addHelpButton('titlelength', 'titlelength', 'elang');
		$mform->addRule('titlelength', get_string('titlelength_error', 'elang'), 'numeric', null, 'client');

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
			$options = json_decode(isset($default_values['options']) ? $default_values['options'] : '{}', true);
			$default_values['showlanguage'] = $options['showlanguage'];
			$default_values['repeatedunderscore'] = $options['repeatedunderscore'];
			$default_values['titlelength'] = $options['titlelength'];
		}
		else
		{
			$config = get_config('elang');
			var_dump($config);
			$default_values['repeatedunderscore'] = $config->repeatedunderscore;
			$default_values['showlanguage'] = $config->showlanguage;
			$default_values['titlelength'] = $config->titlelength;
		}
	}
}

<?php

/**
 * The main elang configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013-2015 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       0.0.1
 */

defined('MOODLE_INTERNAL') || die();

// Get the moodle form library
require_once $CFG->dirroot . '/course/moodleform_mod.php';

/**
 * Module instance settings form
 *
 * @since  0.0.1
 */
class mod_elang_mod_form extends moodleform_mod
{
	/**
	 * The \Captioning\Format\WebvttFile object is successfull
	 *
	 * @since  0.0.1
	 */
	protected $vtt;

	/**
	 * Get the vtt object
	 *
	 * @return  \Captioning\Format\WebvttFile  The vtt object
	 *
	 * @since  0.0.1
	 */
	public function getVtt()
	{
		return $this->vtt;
	}

	/**
	 * Defines forms elements
	 *
	 * @return  void
	 *
	 * @since  0.0.1
	 */
	public function definition()
	{
		global $CFG;

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
		if (version_compare(moodle_major_version(true), '2.9', '>='))
		{
			$this->standard_intro_elements();
		}
		else
		{
			$this->add_intro_editor($config->requiremodintro);
		}

		// Adding the rest of elang settings, spreeading all them into this fieldset
		$mform->addElement('header', 'elangfieldset', get_string('upload', 'elang'));

		require_once dirname(__FILE__) . '/locallib.php';
		$languages = Elang\getLanguages();
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

/**		//TODO deal with specific mimetype (moodle does not seem to take this code into account when file is uploaded
		require_once $CFG->libdir . '/filelib.php';
		$info = & get_mimetypes_array();
		$info['vtt'] = array('type' => 'text/vtt', 'icon'=>'text', 'defaulticon'=>true, 'groups' => array('subtitle'));
		$info['srt'] = array('type' => 'text/plain', 'icon'=>'text', 'defaulticon'=>true, 'groups' => array('subtitle'));
*/
		$mform->addElement(
			'filemanager',
			'subtitle',
			get_string('subtitle', 'elang'),
			null,
			array('subdirs' => 0, 'maxbytes' => $config->subtitlemaxsize, 'maxfiles' => 1, 'accepted_types' => array('subtitle'))
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
		$mform->setType('repeatedunderscore', PARAM_INT);

		$mform->addElement('text', 'titlelength', get_string('titlelength', 'elang'));
		$mform->addHelpButton('titlelength', 'titlelength', 'elang');
		$mform->addRule('titlelength', get_string('titlelength_error', 'elang'), 'numeric', null, 'client');
		$mform->setType('titlelength', PARAM_INT);

		$element = $mform->addElement('select', 'limit', get_string('limit', 'elang'), array(5 => 5, 10 => 10, 15 => 15, 20 => 20, 25 => 25));
		$mform->addHelpButton('limit', 'limit', 'elang');
		$mform->addRule('limit', get_string('limit_error', 'elang'), 'numeric', null, 'client');
		$mform->setType('limit', PARAM_INT);
		$element->setSelected(10);

		$mform->addElement('text', 'left', get_string('left', 'elang'));
		$mform->addHelpButton('left', 'left', 'elang');
		$mform->addRule('left', get_string('left_error', 'elang'), 'numeric', null, 'client');
		$mform->setType('left', PARAM_INT);

		$mform->addElement('text', 'top', get_string('top', 'elang'));
		$mform->addHelpButton('top', 'top', 'elang');
		$mform->addRule('top', get_string('top_error', 'elang'), 'numeric', null, 'client');
		$mform->setType('top', PARAM_INT);

		$mform->addElement('text', 'size', get_string('size', 'elang'));
		$mform->addHelpButton('size', 'size', 'elang');
		$mform->addRule('size', get_string('size_error', 'elang'), 'numeric', null, 'client');
		$mform->setType('size', PARAM_INT);

		// Add standard elements, common to all modules
		$this->standard_coursemodule_elements();

		// Add standard buttons, common to all modules
		$this->add_action_buttons();

		// Add a warning when a module is being updated
		if (!empty($this->current->instance))
		{
			$mform->addElement('warning', null, null, get_string('update_warning', 'elang'));
		}
	}

	/**
	 * Preprocess data before creating form
	 *
	 * @param   array  &$default_values  Array of default values
	 *
	 * @return  void
	 *
	 * @since  0.0.1
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
				0
			);
			$default_values['videos'] = $draftitemid;

			$draftitemid = file_get_submitted_draft_itemid('subtitle');
			file_prepare_draft_area(
				$draftitemid,
				$this->context->id,
				'mod_elang',
				'subtitle',
				0
			);
			$default_values['subtitle'] = $draftitemid;

			$draftitemid = file_get_submitted_draft_itemid('poster');
			file_prepare_draft_area(
				$draftitemid,
				$this->context->id,
				'mod_elang',
				'poster',
				0
			);
			$default_values['poster'] = $draftitemid;

			$options = json_decode(isset($default_values['options']) ? $default_values['options'] : '{}', true);
			$default_values['showlanguage'] = isset($options['showlanguage']) ? $options['showlanguage'] : true;
			$default_values['repeatedunderscore'] = isset($options['repeatedunderscore']) ? $options['repeatedunderscore'] : 10;
			$default_values['titlelength'] = isset($options['titlelength']) ? $options['titlelength'] : 100;
			$default_values['limit'] = isset($options['limit']) ? $options['limit'] : 10;
			$default_values['left'] = isset($options['left']) ? $options['left'] : 20;
			$default_values['top'] = isset($options['top']) ? $options['top'] : 20;
			$default_values['size'] = isset($options['size']) ? $options['size'] : 16;
		}
		else
		{
			$config = get_config('elang');
			$default_values['repeatedunderscore'] = $config->repeatedunderscore;
			$default_values['showlanguage'] = $config->showlanguage;
			$default_values['titlelength'] = $config->titlelength;
			$default_values['limit'] = $config->limit;
			$default_values['left'] = $config->left;
			$default_values['top'] = $config->top;
			$default_values['size'] = $config->size;
		}
	}

	/**
	 * Perform minimal validation on the settings form
	 *
	 * @param   array  $data   Data to be validated
	 * @param   array  $files  Files to be validated
	 *
	 * @return  array
	 */
	public function validation($data, $files)
	{
		$errors = parent::validation($data, $files);

		global $USER;
		$fs = get_file_storage();
		$context = context_user::instance($USER->id);
		$files = $fs->get_area_files($context->id, 'user', 'draft', $data['subtitle'], 'id DESC', false);

		$noerror = false;

		foreach ($files as $file)
		{
			try
			{
				$filepath = $file->copy_content_to_temp();
			}
			catch (\Exception $e)
			{
				$errors['subtitle'] = get_string('subtitleunabletosave', 'elang');
				break;
			}

			try
			{
				$caption = new \Captioning\Format\WebvttFile($filepath);
				$noerror = true;
				break;
			}
			catch (\Exception $e)
			{
			}

			try
			{
				$caption = new \Captioning\Format\SubripFile($filepath);
				$noerror = true;
				break;
			}
			catch (\Exception $e)
			{
			}
		}

		if ($noerror)
		{
			$this->vtt = new \Captioning\Format\WebvttFile;

			foreach ($caption->getCues() as $cue)
			{
				$this->vtt->addCue(
					$cue->getText(),
					\Captioning\Format\WebvttCue::ms2tc($cue->getStartMS()),
					\Captioning\Format\WebvttCue::ms2tc($cue->getStopMS())
				);
			}
		}
		elseif (!isset($errors['subtitle']))
		{
			$errors['subtitle'] = get_string('subtitleinvalidformat', 'elang');
		}

		return $errors;
	}
}

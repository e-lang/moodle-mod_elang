<?php

/**
 * The main elang configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod
 * @subpackage elang
 * @copyright  2013 University of La Rochelle, France
 * @license    http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form
 */
class mod_elang_mod_form extends moodleform_mod {

	/**
	 * Defines forms elements
	 */
	public function definition() {

		$mform = $this->_form;

		//-------------------------------------------------------------------------------
		// Adding the "general" fieldset, where all the common settings are showed
		$mform->addElement('header', 'general', get_string('general', 'form'));

		// Adding the standard "name" field
		$mform->addElement('text', 'name', get_string('elangname', 'elang'), array('size'=>'64'));
		if (!empty($CFG->formatstringstriptags)) {
			$mform->setType('name', PARAM_TEXT);
		} else {
			$mform->setType('name', PARAM_CLEAN);
		}
		$mform->addRule('name', null, 'required', null, 'client');
		$mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
		$mform->addHelpButton('name', 'elangname', 'elang');

		// Adding the standard "intro" and "introformat" fields
		$this->add_intro_editor();

		//-------------------------------------------------------------------------------
		// Adding the rest of elang settings, spreeading all them into this fieldset
		// or adding more fieldsets ('header' elements) if needed for better logic
		//$mform->addElement('static', 'label1', 'elangsetting1', 'Your elang fields go here. Replace me!');
		/*$options = array(	NOGROUPS	   => get_string('element1'),
							SEPARATEGROUPS => get_string('element2'),
							VISIBLEGROUPS  => get_string('element3'));
		$mform->addElement('select', 'monid', 'Theme', $options, NOGROUPS);*/
		
		/*function debug($str)
		{
			$debug = fopen('/debug.txt', 'a');
			fputs($debug,  $str . "\n");
		}
		
		debug('mod_form.php');
		debug($mform->id);*/
		
		$mform->addElement('header', 'elangfieldset', get_string('upload', 'elang'));
		//$mform->addElement('static', 'label2', 'elangsetting2', 'Your elang fields go here. Replace me!');
		$mform->addElement('filemanager', 'videos', get_string('videos', 'elang'), null, array('subdirs' => 0, 'maxbytes' => 5000000000000000000000000000000, 'maxfiles' => 20, 'accepted_types' => array('video')));
		$mform->addHelpButton('videos', 'videos', 'elang');
		//$mform->addRule('videos', null, 'required', null, 'client');
		//TODO, required inputs + change accepted_types for subtitle and exercise (?) :
		$mform->addElement('filepicker', 'subtitle', get_string('subtitle', 'elang'), null, array('maxbytes' => 20000000, 'accepted_types' => '*'));
		$mform->addHelpButton('subtitle', 'subtitle', 'elang');
		//$mform->addRule('subtitle', null, 'required', null, 'client');
		/*$mform->addElement('filepicker', 'exercise', get_string('exercise', 'elang'), null, array('maxbytes' => 20000000, 'accepted_types' => '*'));
		$mform->addHelpButton('exercise', 'exercise', 'elang');*/
		//$mform->addRule('exercise', null, 'required', null, 'client');
		$mform->addElement('filepicker', 'poster', get_string('poster', 'elang'), null, array('maxbytes' => 20000000, 'accepted_types' => array('image')));
		$mform->addHelpButton('poster', 'poster', 'elang');
		//$mform->addRule('poster', null, 'required', null, 'client');
		//$mform->addHelpButton('name', 'poster_help', 'elang');
		
		//-------------------------------------------------------------------------------
		// add standard elements, common to all modules
		$this->standard_coursemodule_elements();
		//-------------------------------------------------------------------------------
		// add standard buttons, common to all modules
		$this->add_action_buttons();
	}
}

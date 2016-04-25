<?php

/**
 * elang restore task that provides all the settings 
 * and steps to perform one complete restore of the activity
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013-2016 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       1.1.0
 */

// Because it exists (must)
require_once $CFG->dirroot . '/mod/elang/backup/moodle2/restore_elang_stepslib.php';

/**
 * elang restore task that provides all the settings 
 * and steps to perform one complete restore of the activity
 *
 * @since  1.1.0
 */
class restore_elang_activity_task extends restore_activity_task
{
	/**
	 * Define (add) particular settings this activity can have
	 *
	 * @return void
	 */
	protected function define_my_settings()
	{
		// No particular settings for this activity
	}

	/**
	 * Define (add) particular steps this activity can have
	 *
	 * @return void
	 */
	protected function define_my_steps()
	{
		// Our elang only has one structure step
		$this->add_step(new restore_elang_activity_structure_step('elang_structure', 'elang.xml'));
	}

	/**
	 * Define the contents in the activity that must be
	 * processed by the link decoder
	 *
	 * @return $contents
	 */
	static public function define_decode_contents()
	{
		$contents = array();

		return $contents;
	}

	/**
	 * Define the decoding rules for links belonging
	 * to the activity to be executed by the link decoder
	 *
	 * @return $rules
	 */
	static public function define_decode_rules()
	{
		$rules = array();

		$rules[] = new restore_decode_rule('ELANGVIEWBYID', '/mod/elang/view.php?id=$1', 'course_module');
		$rules[] = new restore_decode_rule('ELANGINDEX', '/mod/elang/index.php?id=$1', 'course');

		return $rules;
	}

	/**
	 * Define the restore log rules that will be applied
	 * by the {@link restore_logs_processor} when restoring
	 * elang logs. It must return one array
	 * of {@link restore_log_rule} objects
	 *
	 * @return $rules
	 */
	static public function define_restore_log_rules()
	{
		$rules = array();

		$rules[] = new restore_log_rule('elang', 'add', 'view.php?id={course_module}', '{elang}');
		$rules[] = new restore_log_rule('elang', 'update', 'view.php?id={course_module}', '{elang}');
		$rules[] = new restore_log_rule('elang', 'view', 'view.php?id={course_module}', '{elang}');
		$rules[] = new restore_log_rule('elang', 'choose', 'view.php?id={course_module}', '{elang}');
		$rules[] = new restore_log_rule('elang', 'choose again', 'view.php?id={course_module}', '{elang}');
		$rules[] = new restore_log_rule('elang', 'report', 'report.php?id={course_module}', '{elang}');

		return $rules;
	}

	/**
	 * Define the restore log rules that will be applied
	 * by the {@link restore_logs_processor} when restoring
	 * course logs. It must return one array
	 * of {@link restore_log_rule} objects
	 * Note this rules are applied when restoring course logs
	 * by the restore final task, but are defined here at
	 * activity level. All them are rules not linked to any module instance (cmid = 0)
	 *
	 * @return $rules
	 */
	static public function define_restore_log_rules_for_course()
	{
		$rules = array();

		// Fix old wrong uses (missing extension)
		$rules[] = new restore_log_rule('elang', 'view all', 'index?id={course}', null, null, null, 'index.php?id={course}');
		$rules[] = new restore_log_rule('elang', 'view all', 'index.php?id={course}', null);

		return $rules;
	}
}

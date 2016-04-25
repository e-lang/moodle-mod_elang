<?php

/**
 * Log an add check event
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013-2016 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       1.0.0
 */

namespace mod_elang\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_elang instance add check event class.
 *
 * @since  1.0.0
 */
class check_added extends \core\event\base
{
	/**
	 * Set basic properties for the event.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function init()
	{
		$this->data['objecttable'] = 'elang_check';
		$this->data['crud'] = 'c';
		$this->data['edulevel'] = self::LEVEL_PARTICIPATING;
	}

	/**
	 * Returns localised general event name.
	 *
	 * @return  string
	 *
	 * @since   1.0.0
	 */
	public static function get_name()
	{
		return get_string('eventcheckadded', 'mod_elang');
	}

	/**
	 * Get URL related to the action.
	 *
	 * @return  \moodle_url
	 *
	 * @since   1.0.0
	 */
	public function get_url()
	{
		return new \moodle_url('/mod/elang/server.php', array('id' => $this->contextinstanceid));
	}

	/**
	 * Returns non-localised event description with id's for admin use only.
	 *
	 * @return  string
	 *
	 * @since   1.0.0
	 */
	public function get_description()
	{
		return "The user with id '$this->userid' added a new check [{$this->other['user']}]=[{$this->other['info']}] for cue number " .
			"'{$this->other['cue']}' and guess number '{$this->other['guess']}' to the elang activity course module id '$this->contextinstanceid'.";
	}

	/**
	 * Replace add_to_log() statement.
	 *
	 * @return  array  array of parameters to be passed to legacy add_to_log() function.
	 *
	 * @since   1.0.0
	 */
	protected function get_legacy_logdata()
	{
		return array(
			$this->courseid,
			'elang',
			'add check',
			'view.php?id=' . $this->contextinstanceid,
			$this->objectid,
			$this->contextinstanceid
		);
	}

	/**
	 * Custom validations.
	 *
	 * @throws  \coding_exception  when validation fails.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function validate_data()
	{
		parent::validate_data();

		if (!isset($this->other['user']))
		{
			throw new \coding_exception("The 'user' value must be set in other.");
		}

		if (!isset($this->other['info']))
		{
			throw new \coding_exception("The 'info' value must be set in other.");
		}

		if (!isset($this->other['cue']))
		{
			throw new \coding_exception("The 'cue' value must be set in other.");
		}

		if (!isset($this->other['guess']))
		{
			throw new \coding_exception("The 'guess' value must be set in other.");
		}
	}
}

<?php

/**
 * Log a view event
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
 * The mod_elang course module viewed event class.
 *
 * @since  1.0.0
 */
class course_module_viewed extends \core\event\course_module_viewed
{
	/**
	 * Initialisation function
	 *
	 * @return  void
	 *
	 * @since 1.0.0
	 */
	protected function init()
	{
		$this->data['objecttable'] = 'elang';
		parent::init();
	}
}

<?php
/* This file is part of Moodle - http://moodle.org/

 Moodle is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 Moodle is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
/ along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
*/


defined('MOODLE_INTERNAL') || die;

	// Because it exists (must)
require_once $CFG->dirroot . '/mod/elang/backup/moodle2/backup_elang_stepslib.php';

	// Because it exists (optional)
require_once $CFG->dirroot . '/mod/elang/backup/moodle2/backup_elang_settingslib.php';

/**
 * Tasks executed for elang backup, using stepslib
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package     Mod
 * @subpackage  elang
 * @copyright   2013-2015 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       0.0.1
 */
class backup_elang_activity_task extends backup_activity_task
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
		$this->add_step(new backup_elang_activity_structure_step('elang_structure', 'elang.xml'));
	}

	/**
	 * Code the transformations to perform in the activity in
	 * order to get transportable (encoded) links
	 *
	 * @param   string  $content  The content to encode.
	 *
	 * @return string encoded content
	 */
	static public function encode_content_links($content)
	{
	global $CFG;

	$base = preg_quote($CFG->wwwroot, "/");

	// Link to the list of elang

	$search  = "/($base\/mod\/elang\/index.php\?id=)([0-9]+)/";
	$content = preg_replace($search, '$@ELANGINDEX*$2@$', $content);

	$search  = "/($base\/mod\/book\/index.php\?id=)([0-9]+)/";
	$content = preg_replace($search, '$@ELANGVIEWBYID*$2@$', $content);

	return $content;
	}
}

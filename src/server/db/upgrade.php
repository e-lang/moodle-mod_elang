<?php

/**
 * This file keeps track of upgrades to the elang module
 *
 * Sometimes, changes between versions involve alterations to database
 * structures and other major things that may break installations. The upgrade
 * function in this file will attempt to perform all the necessary actions to
 * upgrade your older installation to the current version. If there's something
 * it cannot do itself, it will tell you what you need to do. The commands in
 * here will all be database-neutral, using the functions defined in DLL libraries.
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013-2016 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       0.0.1
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute elang upgrade from the given old version
 *
 * @param   int  $oldversion  The old version
 *
 * @return  bool
 *
 * @since  0.0.1
 */
function xmldb_elang_upgrade($oldversion)
{
	global $DB;

	// Loads ddl manager and xmldb classes
	$dbman = $DB->get_manager();

	/* And upgrade begins here. For each one, you'll need one
	 * block of code similar to the next one. Please, delete
	 * this comment lines once this file start handling proper
	 * upgrade code.
	 */

	/*
	 * See https://github.com/moodlehq/moodle-mod_newmodule/blob/master/db/upgrade.php
	 */

	// Final return of upgrade result (true, all went good) to Moodle.
	return true;
}

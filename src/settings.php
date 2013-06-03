<?php

/**
 * Elang module admin settings and defaults
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package	 mod
 * @subpackage  elang
 * @copyright   2013 University of La Rochelle, France
 * @license	 http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree)
{
	require_once $CFG->libdir . '/resourcelib.php';

	// General settings
	$settings->add(
		new admin_setting_configcheckbox(
			'elang/requiremodintro',
			get_string('requiremodintro', 'admin'),
			get_string('configrequiremodintro', 'admin'),
			1
		)
	);

	$settings->add(
		new admin_setting_configtext(
			'elang/videomaxsize',
			get_string('videomaxsize', 'elang'),
			get_string('videomaxsize_config', 'elang'),
			10000000,
			PARAM_INT
		)
	);

	$settings->add(
		new admin_setting_configtext(
			'elang/postermaxsize',
			get_string('postermaxsize', 'elang'),
			get_string('postermaxsize_config', 'elang'),
			1000000,
			PARAM_INT
		)
	);

	$settings->add(
		new admin_setting_configtext(
			'elang/subtitlemaxsize',
			get_string('subtitlemaxsize', 'elang'),
			get_string('subtitlemaxsize_config', 'elang'),
			100000,
			PARAM_INT
		)
	);
	$settings->add(
		new admin_setting_configtext(
			'elang/repeatedunderscore',
			get_string('repeatedunderscore', 'elang'),
			get_string('repeatedunderscore_config', 'elang'),
			10,
			PARAM_INT
		)
	);
	$settings->add(
		new admin_setting_configtext(
			'elang/titlelength',
			get_string('titlelength', 'elang'),
			get_string('titlelength_config', 'elang'),
			100,
			PARAM_INT
		)
	);
	$settings->add(
		new admin_setting_configtext(
			'elang/timeout',
			get_string('timeout', 'elang'),
			get_string('timeout_config', 'elang'),
			3000,
			PARAM_INT
		)
	);
	$settings->add(
		new admin_setting_configcheckbox(
			'elang/showlanguage',
			get_string('showlanguage', 'elang'),
			get_string('showlanguage_config', 'elang'),
			1
		)
	);
}

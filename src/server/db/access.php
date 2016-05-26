<?php

/**
 * Capability definitions for the elang module
 *
 * The capabilities are loaded into the database table when the module is
 * installed or updated. Whenever the capability definitions are updated,
 * the module version number should be bumped up.
 *
 * The system has four possible values for a capability:
 * CAP_ALLOW, CAP_PREVENT, CAP_PROHIBIT, and inherit (not set).
 *
 * It is important that capability names are unique. The naming convention
 * for capabilities that are specific to modules and blocks is as follows:
 *   [mod/block]/<plugin_name>:<capabilityname>
 *
 * component_name should be the same as the directory name of the mod or block.
 *
 * Core moodle capabilities are defined thus:
 *	moodle/<capabilityclass>:<capabilityname>
 *
 * Examples: mod/forum:viewpost
 *		   block/recent_activity:view
 *		   moodle/site:deleteuser
 *
 * The variable name for the capability definitions array is $capabilities
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013-2016 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       0.0.1
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = array(

	'mod/elang:view' => array(
		'captype' => 'read',
		'contextlevel' => CONTEXT_MODULE,
		'archetypes' => array(
			'student' => CAP_ALLOW,
			'teacher' => CAP_ALLOW,
			'editingteacher' => CAP_ALLOW,
			'manager' => CAP_ALLOW
		)
	),

	'mod/elang:addinstance' => array(
		'riskbitmask' => RISK_XSS,
		'captype' => 'write',
		'contextlevel' => CONTEXT_COURSE,
		'archetypes' => array(
			'editingteacher' => CAP_ALLOW,
			'manager' => CAP_ALLOW
		),
		'clonepermissionsfrom' => 'moodle/course:manageactivities'
	),

	'mod/elang:report' => array(
		'captype' => 'read',
		'contextlevel' => CONTEXT_MODULE,
		'archetypes' => array(
			'teacher' => CAP_ALLOW,
			'editingteacher' => CAP_ALLOW,
			'manager' => CAP_ALLOW
		)
	),

	'mod/elang:isinreport' => array(
		'captype' => 'read',
		'contextlevel' => CONTEXT_MODULE,
		'archetypes' => array(
			'student' => CAP_ALLOW,
		),
		'clonepermissionsfrom' => 'moodle/course:isincompletionreports'
	),
);

<?php

/**
 * Definition of log events
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013-2016 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       0.0.1
 */

defined('MOODLE_INTERNAL') || die();

$logs = array(
	array('module' => 'elang', 'action' => 'add', 'mtable' => 'elang', 'field' => 'name'),
	array('module' => 'elang', 'action' => 'update', 'mtable' => 'elang', 'field' => 'name'),
	array('module' => 'elang', 'action' => 'view', 'mtable' => 'elang', 'field' => 'name'),
	array('module' => 'elang', 'action' => 'view all', 'mtable' => 'elang', 'field' => 'name'),
	array('module' => 'elang', 'action' => 'view report', 'mtable' => 'elang', 'field' => 'name'),

	array(
		'module' => 'elang',
		'action' => 'view help',
		'mtable' => 'elang_help',
		'field' => $DB->sql_concat('cue', "','", 'guess', "','", "'['", 'info', "']'")
	),
	array(
		'module' => 'elang',
		'action' => 'add check',
		'mtable' => 'elang_check',
		'field' => $DB->sql_concat('cue', "','", 'guess', "','", "'['", 'info', "']=['", 'user', "']'")
	),
);

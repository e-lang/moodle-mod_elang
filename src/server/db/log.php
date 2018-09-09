<?php
// This file is part of mod_elang for moodle.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Definition of log events
 *
 * @package     mod_elang
 *
 * @copyright   2013-2018 University of La Rochelle, France
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

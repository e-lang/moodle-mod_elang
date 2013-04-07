<?php

/**
 * Definition of log events
 *
 * NOTE: this is an example how to insert log event during installation/update.
 * It is not really essential to know about it, but these logs were created as example
 * in the previous 1.9 NEWMODULE.
 *
 * @package    mod
 * @subpackage elang
 * @copyright  2013 University of La Rochelle, France
 * @license    http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 */

defined('MOODLE_INTERNAL') || die();

global $DB;

$logs = array(
    array('module'=>'elang', 'action'=>'add', 'mtable'=>'elang', 'field'=>'name'),
    array('module'=>'elang', 'action'=>'update', 'mtable'=>'elang', 'field'=>'name'),
    array('module'=>'elang', 'action'=>'view', 'mtable'=>'elang', 'field'=>'name'),
    array('module'=>'elang', 'action'=>'view all', 'mtable'=>'elang', 'field'=>'name')
);

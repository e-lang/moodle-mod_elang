<?php

/**
 * Defines the version of elang
 *
 * This code fragment is called by moodle_needs_upgrading() and
 * /admin/index.php
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013-2015 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 */

defined('MOODLE_INTERNAL') || die();

// The current module version (Date: YYYYMMDDXX). If version == 0 then module will not be installed
$module->version   = @VERSION@;

// Requires this Moodle version
$module->requires  = @REQUIRES@;

// Period for cron to check this module (secs)
$module->cron      = 0;

// To check on upgrade, that module sits in correct place
$module->component = 'mod_elang';

// Human-friendly version name
$module->release   = '@RELEASE@';

// Maturity: MATURITY_ALPHA, MATURITY_BETA, MATURITY_RC, MATURITY_STABLE
$module->maturity  = @MATURITY@;


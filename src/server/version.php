<?php

/**
 * Defines the version of elang
 *
 * This code fragment is called by moodle_needs_upgrading() and
 * /admin/index.php
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013-2018 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 */

defined('MOODLE_INTERNAL') || die();

// The current module version (Date: YYYYMMDDXX). If version == 0 then module will not be installed
$plugin->version   = @VERSION@;

// Requires this Moodle version
$plugin->requires  = 2012062500;

// Period for cron to check this module (secs)
$plugin->cron      = 0;

// To check on upgrade, that module sits in correct place
$plugin->component = 'mod_elang';

// Human-friendly version name
$plugin->release   = '@RELEASE@';

// Maturity: MATURITY_ALPHA, MATURITY_BETA, MATURITY_RC, MATURITY_STABLE
$plugin->maturity  = @MATURITY@;

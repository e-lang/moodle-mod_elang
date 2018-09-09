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
 * Defines the version of elang
 *
 * This code fragment is called by moodle_needs_upgrading() and
 * /admin/index.php
 *
 * @package     mod_elang
 *
 * @copyright   2013-2018 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 */

defined('MOODLE_INTERNAL') || die();

// The current module version (Date: YYYYMMDDXX). If version == 0 then module will not be installed.
$plugin->version   = @VERSION@;

// Requires this Moodle version.
$plugin->requires  = 2017111300;

// The moodle branch.
$plugin->branch   = '34';

// Period for cron to check this module (secs).
$plugin->cron      = 0;

// To check on upgrade, that module sits in correct place.
$plugin->component = 'mod_elang';

// Human-friendly version name.
$plugin->release   = '@RELEASE@';

// Maturity: MATURITY_ALPHA, MATURITY_BETA, MATURITY_RC, MATURITY_STABLE.
$plugin->maturity  = @MATURITY@;

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
 * Register the Captioning classes
 *
 * @package     mod_elang
 *
 * @copyright   2013-2018 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       1.3.4
 */

defined('MOODLE_INTERNAL') || die();


spl_autoload_register(function ($class) {
    switch ($class) {
        case 'Captioning\\Converter':
            include(__DIR__ . '/Captioning/Converter.php');
        break;
        case 'Captioning\\CueInterface':
            include(__DIR__ . '/Captioning/CueInterface.php');
        break;
        case 'Captioning\\Cue':
            include(__DIR__ . '/Captioning/Cue.php');
        break;
        case 'Captioning\\FileInterface':
            include(__DIR__ . '/Captioning/FileInterface.php');
        break;
        case 'Captioning\\File':
            include(__DIR__ . '/Captioning/File.php');
        break;
        case 'Captioning\\Format\\JsonCue':
            include(__DIR__ . '/Captioning/Format/JsonCue.php');
        break;
        case 'Captioning\\Format\\SBVCue':
            include(__DIR__ . '/Captioning/Format/SBVCue.php');
        break;
        case 'Captioning\\Format\\SubripCue':
            include(__DIR__ . '/Captioning/Format/SubripCue.php');
        break;
        case 'Captioning\\Format\\SubstationalphaCue':
            include(__DIR__ . '/Captioning/Format/SubstationalphaCue.php');
        break;
        case 'Captioning\\Format\\TtmlCue':
            include(__DIR__ . '/Captioning/Format/TtmlCue.php');
        break;
        case 'Captioning\\Format\\WebvttCue':
            include(__DIR__ . '/Captioning/Format/WebvttCue.php');
        break;
        case 'Captioning\\Format\\WebvttRegion':
            include(__DIR__ . '/Captioning/Format/WebvttRegion.php');
        break;
        case 'Captioning\\Format\\JsonFile':
            include(__DIR__ . '/Captioning/Format/JsonFile.php');
        break;
        case 'Captioning\\Format\\SBVFile':
            include(__DIR__ . '/Captioning/Format/SBVFile.php');
        break;
        case 'Captioning\\Format\\SubripFile':
            include(__DIR__ . '/Captioning/Format/SubripFile.php');
        break;
        case 'Captioning\\Format\\SubstationalphaFile':
            include(__DIR__ . '/Captioning/Format/SubstationalphaFile.php');
        break;
        case 'Captioning\\Format\\TtmlFile':
            include(__DIR__ . '/Captioning/Format/TtmlFile.php');
        break;
        case 'Captioning\\Format\\WebvttFile':
            include(__DIR__ . '/Captioning/Format/WebvttFile.php');
        break;
    }
});

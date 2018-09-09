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
 * Log a view event
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package     mod_elang
 *
 * @copyright   2013-2018 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       1.0.0
 */

namespace mod_elang\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_elang course module viewed event class.
 *
 * @copyright   2013-2018 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since  1.0.0
 */
class course_module_viewed extends \core\event\course_module_viewed
{
    /**
     * Initialisation function
     *
     * @return  void
     *
     * @since 1.0.0
     */
    protected function init() {
        $this->data['objecttable'] = 'elang';
        parent::init();
    }
}

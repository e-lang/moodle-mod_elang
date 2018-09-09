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
 * Log a report view event
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package     mod_elang
 *
 * @copyright   2013-2018 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       1.1.0
 */

namespace mod_elang\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_elang instance report view event class.
 *
 * @copyright   2013-2018 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since  1.1.0
 */
class report_viewed extends \core\event\base
{
    /**
     * Set basic properties for the event.
     *
     * @return  void
     *
     * @since   1.1.0
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    /**
     * Returns localised general event name.
     *
     * @return  string
     *
     * @since   1.1.0
     */
    public static function get_name() {
        return get_string('eventreportviewed', 'mod_elang');
    }

    /**
     * Get URL related to the action.
     *
     * @return  \moodle_url
     *
     * @since   1.1.0
     */
    public function get_url() {
        if ($this->other['action'] == 'csv') {
            return new \moodle_url('/mod/elang/view.php', array('id' => $this->contextinstanceid, 'format' => 'csv'));
        } else if ($this->other['action'] == 'all') {
            return new \moodle_url('/mod/elang/view.php', array('id' => $this->contextinstanceid));
        } else {
            return new \moodle_url(
                '/mod/elang/view.php',
                array('id' => $this->contextinstanceid, 'id_user' => $this->other['id_user'])
            );
        }
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return  string
     *
     * @since   1.1.0
     */
    public function get_description() {
        if ($this->other['action'] == 'csv') {
            return "The user with id '$this->userid' downloaded a csv report " .
                "for the elang activity course module id '$this->contextinstanceid'.";
        } else if ($this->other['action'] == 'all') {
            return "The user with id '$this->userid' view a full report " .
                "for the elang activity course module id '$this->contextinstanceid'.";
        } else {
            return "The user with id '$this->userid' view a user report '{$this->other['id_user']}'" .
                "for the elang activity course module id '$this->contextinstanceid'.";
        }
    }

    /**
     * Replace add_to_log() statement.
     *
     * @return  array  array of parameters to be passed to legacy add_to_log() function.
     *
     * @since   1.1.0
     */
    protected function get_legacy_logdata() {
        if ($this->other['action'] == 'csv') {
            return array(
                $this->courseid,
                'elang',
                'view report',
                'view.php?id?id=' . $this->contextinstanceid . 'format=csv',
                'csv',
                $this->contextinstanceid
            );
        } else if ($this->other['action'] == 'all') {
            return array(
                $this->courseid,
                'elang',
                'view report',
                'view.php?id?id=' . $this->contextinstanceid ,
                'all',
                $this->contextinstanceid
            );
        } else {
            return array(
                $this->courseid,
                'elang',
                'view report',
                'view.php?id?id=' . $this->contextinstanceid ,
                'user ' . $this->other['id_user'],
                $this->contextinstanceid
            );
        }
    }

    /**
     * Custom validations.
     *
     * @throws  \coding_exception  when validation fails.
     *
     * @return  void
     *
     * @since   1.1.0
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['action'])) {
            throw new \coding_exception("The 'action' value must be set in other.");
        }

        if ($this->other['action'] == 'one' && !isset($this->other['id_user'])) {
            throw new \coding_exception("The 'id_user' value must be set in other.");
        }
    }
}

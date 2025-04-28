<?php
// This file is part of Moodle - http://moodle.org/
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

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Mink\Exception\ExpectationException as ExpectationException;

/**
 * Behat steps in plugin mod_webpart
 *
 * @package    mod_webpart
 * @category   test
 * @copyright  2025 Andrew Rowatt <A.J.Rowatt@massey.ac.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_webpart extends behat_base {
/**
     * Checks that the specified webpart is hidden from students. You need to be in the course page.
     *
     * @Then /^"(?P<activity_or_resource_string>(?:[^"]|\\")*)" webpart should be hidden$/
     * @param string $activityname
     * @throws ExpectationException
     */
    public function webpart_should_be_hidden($activityname) {
        if ($this->is_course_editor()) {
            // The activity should exist.
            $activitynode = $this->get_activity_node($activityname);

            // Should be hidden.
            $exception = new ExpectationException('"' . $activityname . '" is not hidden', $this->getSession());
            $this->find('named_partial', array('badge', get_string('hiddenfromstudents')), $exception, $activitynode);
        }
    }

}

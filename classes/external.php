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

/**
 * Simple label external API
 *
 * @package    mod_simplelabel
 * @category   external
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.3
 */

use core_course\external\helper_for_get_mods_by_courses;

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

/**
 * Simple label external functions
 *
 * @package    mod_simplelabel
 * @category   external
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.3
 */
class mod_simplelabel_external extends external_api {

    /**
     * Describes the parameters for get_simplelabels_by_courses.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_simplelabels_by_courses_parameters() {
        return new external_function_parameters (
            array(
                'courseids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'Course id'), 'Array of course ids', VALUE_DEFAULT, array()
                ),
            )
        );
    }

    /**
     * Returns a list of simplelabels in a provided list of courses.
     * If no list is provided all simplelabels that the user can view will be returned.
     *
     * @param array $courseids course ids
     * @return array of warnings and simplelabels
     * @since Moodle 3.3
     */
    public static function get_simplelabels_by_courses($courseids = array()) {

        $warnings = array();
        $returnedsimplelabels = array();

        $params = array(
            'courseids' => $courseids,
        );
        $params = self::validate_parameters(self::get_simplelabels_by_courses_parameters(), $params);

        $mycourses = array();
        if (empty($params['courseids'])) {
            $mycourses = enrol_get_my_courses();
            $params['courseids'] = array_keys($mycourses);
        }

        // Ensure there are courseids to loop through.
        if (!empty($params['courseids'])) {

            list($courses, $warnings) = external_util::validate_courses($params['courseids'], $mycourses);

            // Get the simplelabels in this course, this function checks users visibility permissions.
            // We can avoid then additional validate_context calls.
            $simplelabels = get_all_instances_in_courses("simplelabel", $courses);
            foreach ($simplelabels as $simplelabel) {
                helper_for_get_mods_by_courses::format_name_and_intro($simplelabel, 'mod_simplelabel');
                $returnedsimplelabels[] = $simplelabel;
            }
        }

        $result = array(
            'simplelabels' => $returnedsimplelabels,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Describes the get_simplelabels_by_courses return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_simplelabels_by_courses_returns() {
        return new external_single_structure(
            array(
                'simplelabels' => new external_multiple_structure(
                    new external_single_structure(array_merge(
                        helper_for_get_mods_by_courses::standard_coursemodule_elements_returns(),
                        [
                            'timemodified' => new external_value(PARAM_INT, 'Last time the simplelabel was modified'),
                        ]
                    ))
                ),
                'warnings' => new external_warnings(),
            )
        );
    }
}

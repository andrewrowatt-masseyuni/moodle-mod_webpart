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

namespace mod_webpart;

/**
 * PHPUnit webpart generator testcase
 *
 * @package    mod_webpart
 * @category   phpunit
 * @copyright  2013 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class generator_test extends \advanced_testcase {
    public function test_generator(): void {
        global $DB;

        $this->resetAfterTest(true);

        $this->assertEquals(0, $DB->count_records('webpart'));

        $course = $this->getDataGenerator()->create_course();

        /** @var mod_webpart_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_webpart');
        $this->assertInstanceOf('mod_webpart_generator', $generator);
        $this->assertEquals('webpart', $generator->get_modulename());

        $generator->create_instance(array('course'=>$course->id));
        $generator->create_instance(array('course'=>$course->id));
        $webpart = $generator->create_instance(array('course'=>$course->id));
        $this->assertEquals(3, $DB->count_records('webpart'));

        $cm = get_coursemodule_from_instance('webpart', $webpart->id);
        $this->assertEquals($webpart->id, $cm->instance);
        $this->assertEquals('webpart', $cm->modname);
        $this->assertEquals($course->id, $cm->course);

        $context = \context_module::instance($cm->id);
        $this->assertEquals($webpart->cmid, $context->instanceid);
    }
}

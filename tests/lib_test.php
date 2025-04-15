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
 * Unit tests for the activity webpart's lib.
 *
 * @package    mod_webpart
 * @category   test
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class lib_test extends \advanced_testcase {

    /**
     * Set up.
     */
    public function setUp(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
    }

    public function test_webpart_core_calendar_provide_event_action() {
        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $webpart = $this->getDataGenerator()->create_module('webpart', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $webpart->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_webpart_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('view'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_webpart_core_calendar_provide_event_action_as_non_user() {
        global $CFG;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $webpart = $this->getDataGenerator()->create_module('webpart', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $webpart->id,
                \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Now log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_webpart_core_calendar_provide_event_action($event, $factory);

        // Confirm the event is not shown at all.
        $this->assertNull($actionevent);
    }

    public function test_webpart_core_calendar_provide_event_action_in_hidden_section() {
        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $webpart = $this->getDataGenerator()->create_module('webpart', array('course' => $course->id));

        // Create a student.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $webpart->id,
                \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Set sections 0 as hidden.
        set_section_visible($course->id, 0, 0);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_webpart_core_calendar_provide_event_action($event, $factory, $student->id);

        // Confirm the event is not shown at all.
        $this->assertNull($actionevent);
    }

    public function test_webpart_core_calendar_provide_event_action_for_user() {
        global $CFG;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $webpart = $this->getDataGenerator()->create_module('webpart', array('course' => $course->id));

        // Enrol a student in the course.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $webpart->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Now, log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_webpart_core_calendar_provide_event_action($event, $factory, $student->id);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('view'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_webpart_core_calendar_provide_event_action_already_completed() {
        global $CFG;

        $CFG->enablecompletion = 1;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $webpart = $this->getDataGenerator()->create_module('webpart', array('course' => $course->id),
            array('completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS));

        // Get some additional data.
        $cm = get_coursemodule_from_instance('webpart', $webpart->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $webpart->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed.
        $completion = new \completion_info($course);
        $completion->set_module_viewed($cm);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_webpart_core_calendar_provide_event_action($event, $factory);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    public function test_webpart_core_calendar_provide_event_action_already_completed_for_user() {
        global $CFG;

        $CFG->enablecompletion = 1;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $webpart = $this->getDataGenerator()->create_module('webpart', array('course' => $course->id),
                array('completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS));

        // Enrol a student in the course.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Get some additional data.
        $cm = get_coursemodule_from_instance('webpart', $webpart->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $webpart->id,
                \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed for the student.
        $completion = new \completion_info($course);
        $completion->set_module_viewed($cm, $student->id);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_webpart_core_calendar_provide_event_action($event, $factory, $student->id);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    /**
     * Check webpart name with different content inserted in the webpart intro.
     *
     * @param string $webpartcontent
     * @param string $webpartformat
     * @param string $expectedwebpartname
     * @return void
     * @covers       \get_webpart_name
     * @dataProvider webpart_get_name_data_provider
     */
    public function test_webpart_get_webpart_name(string $webpartcontent, string $webpartformat, string $expectedwebpartname): void {
        $course = $this->getDataGenerator()->create_course();
        // When creating the module, get_webpart_name is called and fills webpart->name.
        $webpart = $this->getDataGenerator()->create_module('webpart', [
                'course' => $course->id,
                'intro' => $webpartcontent,
                'introformat' => $webpartformat
            ]
        );
        $this->assertEquals($expectedwebpartname, $webpart->name);
    }

    /**
     * Dataprovider for test_webpart_get_webpart_name
     *
     * @return array
     */
    public static function webpart_get_name_data_provider(): array {
        return [
            'simple' => [
                'content' => '<p>Simple textual content<p>',
                'format' => FORMAT_HTML,
                'expected' => 'Simple textual content'
            ],
            'empty' => [
                'content' => '',
                'format' => FORMAT_HTML,
                'expected' => 'Test webpart 1'
            ],
            'withaudiocontent' => [
                'content' => '<p>Test with audio</p>
<p>&nbsp; &nbsp;<audio controls="controls">
<source src="@@PLUGINFILE@@/moodle-hit-song.mp3">
@@PLUGINFILE@@/moodle-hit-song.mp3
</audio>&nbsp;</p>',
                'format' => FORMAT_HTML,
                'expected' => 'Test with audio'
            ],
            'withvideo' => [
                'content' => '<p>Test video</p>
<p>&nbsp;<video controls="controls">
        <source src="https://www.youtube.com/watch?v=xxxyy">
    https://www.youtube.com/watch?v=xxxyy
</video>&nbsp;</p>',
                'format' => FORMAT_HTML,
                'expected' => 'Test video https://www.youtube.com/watch?v=xxxyy'
            ],
            'with video trimming' => [
                'content' => '<p>Test with video to be trimmed</p>
<p>&nbsp;<video controls="controls">
        <source src="https://www.youtube.com/watch?v=xxxyy">
    https://www.youtube.com/watch?v=xxxyy
</video>&nbsp;</p>',
                'format' => FORMAT_HTML,
                'expected' => 'Test with video to be trimmed https://www.youtube....'
            ],
            'with plain text' => [
                'content' => 'Content with @@PLUGINFILE@@/moodle-hit-song.mp3 nothing',
                'format' => FORMAT_HTML,
                'expected' => 'Content with nothing'
            ],
            'with several spaces' => [
                'content' => "Content with @@PLUGINFILE@@/moodle-hit-song.mp3 \r &nbsp; several spaces",
                'format' => FORMAT_HTML,
                'expected' => 'Content with several spaces'
            ],
            'empty spaces' => [
                'content' => ' &nbsp; ',
                'format' => FORMAT_HTML,
                'expected' => 'Web part'
            ],
            'only html' => [
                'content' => '<audio controls="controls"><source src=""></audio>',
                'format' => FORMAT_HTML,
                'expected' => 'Web part'
            ],
            'markdown' => [
                'content' => "##Simple Title\n simple markdown format",
                'format' => FORMAT_MARKDOWN,
                'expected' => 'Simple Title simple markdown format'
            ],
            'markdown with pluginfile' => [
                'content' => "##Simple Title\n simple markdown format @@PLUGINFILE@@/moodle-hit-song.mp3",
                'format' => FORMAT_MARKDOWN,
                'expected' => 'Simple Title simple markdown format'
            ],
            'plain text' => [
                'content' => "Simple plain text @@PLUGINFILE@@/moodle-hit-song.mp3",
                'format' => FORMAT_PLAIN,
                'expected' => 'Simple plain text'
            ],
            'moodle format text' => [
                'content' => "Simple plain text @@PLUGINFILE@@/moodle-hit-song.mp3",
                'format' => FORMAT_MOODLE,
                'expected' => 'Simple plain text'
            ],
            'html format text' => [
                'content' => "<h1>Simple plain title</h1><p> with plain text</p> @@PLUGINFILE@@/moodle-hit-song.mp3",
                'format' => FORMAT_HTML,
                'expected' => 'Simple plain title with plain text'
            ],
        ];
    }

    /**
     * Creates an action event.
     *
     * @param int $courseid The course id.
     * @param int $instanceid The instance id.
     * @param string $eventtype The event type.
     * @return bool|calendar_event
     */
    private function create_action_event($courseid, $instanceid, $eventtype) {
        $event = new \stdClass();
        $event->name = 'Calendar event';
        $event->modulename  = 'webpart';
        $event->courseid = $courseid;
        $event->instance = $instanceid;
        $event->type = CALENDAR_EVENT_TYPE_ACTION;
        $event->eventtype = $eventtype;
        $event->timestart = time();

        return \calendar_event::create($event);
    }
}

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
 * Add webpart form
 *
 * @package mod_webpart
 * @copyright  2006 Jamie Pratt, 2025 Andrew Rowatt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_webpart_mod_form extends moodleform_mod {

    function definition() {
        global $PAGE;

        $PAGE->force_settings_menu();

        $mform = $this->_form;

        $mform->addElement('header', 'generalhdr', get_string('general'));
        // $this->standard_intro_elements(get_string('webparttext', 'webpart'));

        $mform->addElement('hidden', 'intro', '<div><h3>This is a web part</h3></div>');
        $mform->setType('intro', PARAM_RAW);

        $mform->addElement('hidden', 'introformat', 1);
        $mform->setType('introformat', PARAM_INT);
       

        // Web part does not add "Show description" checkbox meaning that 'intro' is always shown on the course page.
        $mform->addElement('hidden', 'showdescription', 1);
        $mform->setType('showdescription', PARAM_INT);

        $contentypeoptions = [
            'heading' => 'Heading',
            'divider' => 'Divider',
            'spacer' => 'Space'
        ];
        $select = $mform->addElement('select', 'contenttype', get_string('contenttype','mod_webpart'), $contentypeoptions);
        $select->setSelected('heading');

        $spacingoptions = [
            '0' => 'No spacing',
            '1' => 'Small spacing (0.5em)',
            '2' => 'Medium spacing (1em)',
            '3' => 'Large spacing (1.5em)',
            '4' => 'Very large spacing (2em)',
            '6' => 'Extra large spacing (3em)'
        ];
        $select = $mform->addElement('select', 'spacingbefore', get_string('spacingbefore','mod_webpart'), $spacingoptions);
        $select->setSelected('2');
        $mform->hideIf('spacingbefore', 'contenttype', 'in', ['spacer']);

        $mform->addElement('text', 'heading', 'Heading', ['size' => 32]);
        $mform->setDefault('heading', '');
        $mform->setType('heading', PARAM_TEXT);
        $mform->hideIf('heading', 'contenttype', 'in', ['divider','spacer']);

        $headingleveloptions = [
            'h3' => 'H3',
            'h4' => 'H4',
            'h5' => 'H5',
            'h6' => 'H6'
        ];
        $select = $mform->addElement('select', 'headinglevel', get_string('headinglevel','mod_webpart'), $headingleveloptions);
        $select->setSelected('h3');
        $mform->hideIf('headinglevel', 'contenttype', 'in', ['divider','spacer']);

        $dividerstyleoptions = [
            'theme1' => 'Theme 1',
            'theme2' => 'Theme 2',
            'theme3' => 'Theme 3'
        ];
        $select = $mform->addElement('select', 'dividerstyle', get_string('dividerstyle','mod_webpart'), $dividerstyleoptions);
        $select->setSelected('h3');
        $mform->hideIf('dividerstyle', 'contenttype', 'in', ['heading','spacer']);

        $select = $mform->addElement('select', 'spacingafter', get_string('spacingafter','mod_webpart'), $spacingoptions);
        $select->setSelected('2');
        $mform->hideIf('spacingafter', 'contenttype', 'in', ['spacer']);

        $this->standard_coursemodule_elements();

        $this->add_action_buttons(true, false, null);
    }

    /**
     * Takes the custom UI data and processes it into the standard HTML intro
     *
     * @param object $data Data submitted by the form.
     */

    public function data_postprocessing($data): void {
        $content = '';
        $spacing = "mt-$data->spacingbefore mb-$data->spacingafter";

        switch ($data->contenttype) {
            case 'heading':
                $content = "<$data->headinglevel class=\"mb-0\" style=\"overflow: hidden;\">$data->heading</$data->headinglevel>";
                break;
            case 'divider':
                $content = "<hr class=\"sl-$data->dividerstyle mt-0 mb-0\"/>";
                break;
            case 'spacer':
                $spacing = 'mb-6';
                break;
        }

        $data->intro = "<div class=\"$spacing\">$content</div>";
    }

    /**
     * Parses the intro field into values for the UI
     *
     * @param array $default_values Default values for the form.
     */

     public function data_preprocessing(&$default_values): void {
        if(isset($default_values['intro'])) {
            $matches = [];
            /* Get spacing infomation */
            $result = preg_match('/<div class="mt-(\d) mb-(\d)">/', $default_values['intro'], $matches);
            if($result) {
                $default_values['spacingbefore'] = $matches[1];
                $default_values['spacingafter'] = $matches[2];
            } else {
                $default_values['spacingbefore'] = 2;
                $default_values['spacingafter'] = 2;
            }

            $matches = [];
            $result = preg_match('/<(h\d).*?>(.*?)<\/h\d>/', $default_values['intro'], $matches);
            if($result === 1) {
                $default_values['heading'] = $matches[2];
                $default_values['headinglevel'] = $matches[1];
                $default_values['contenttype'] = 'heading';
            } else {
                $result = preg_match('/<hr class="sl-(.*?) mt-0 mb-0"\/>/', $default_values['intro'], $matches);
                if($result === 1) {
                    $default_values['contenttype'] = 'divider';
                    $default_values['dividerstyle'] = $matches[1];
                } else {
                    $default_values['contenttype'] = 'spacer';
                }
            }
        }
    }
}

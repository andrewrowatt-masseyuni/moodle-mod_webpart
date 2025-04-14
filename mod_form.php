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
 * Add simplelabel form
 *
 * @package mod_simplelabel
 * @copyright  2006 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_simplelabel_mod_form extends moodleform_mod {

    function definition() {
        global $PAGE;

        $PAGE->force_settings_menu();

        $mform = $this->_form;

        $mform->addElement('header', 'generalhdr', get_string('general'));
        // $this->standard_intro_elements(get_string('simplelabeltext', 'simplelabel'));

        $mform->addElement('hidden', 'intro', '<div><h3>This is a simple label</h3></div>');
        $mform->setType('intro', PARAM_RAW);

        $mform->addElement('hidden', 'introformat', 1);
        $mform->setType('introformat', PARAM_INT);
       

        // Simple label does not add "Show description" checkbox meaning that 'intro' is always shown on the course page.
        $mform->addElement('hidden', 'showdescription', 1);
        $mform->setType('showdescription', PARAM_INT);

        $options = [
            'heading' => 'Heading',
            'divider' => 'Divider',
            'spacer' => 'Space'
        ];
        $select = $mform->addElement('select', 'contenttype', get_string('contenttype','mod_simplelabel'), $options);
        // This will select the colour blue.
        $select->setSelected('heading');


        $mform->addElement('text', 'heading', 'Heading', ['size' => 32]);
        $mform->setDefault('heading', '');
        $mform->setType('heading', PARAM_TEXT);
        $mform->hideIf('heading', 'contenttype', 'in', ['divider','spacer']);

        $this->standard_coursemodule_elements();

//-------------------------------------------------------------------------------
// buttons
        $this->add_action_buttons(true, false, null);

    }

    public function data_postprocessing($data) {
        $content = '';
        $spacing = '';

        switch ($data->contenttype) {
            case 'heading':
                $content = '<h3>' . $data->heading . '</h3>';
                break;
            case 'divider':
                $content = '<hr />';
                break;
            case 'spacer':
                $spacing = 'mb-6';
                break;
        }
        $data->intro = "<div class=\"$spacing\">$content</h3></div>";
    }

    function data_preprocessing(&$default_values){
        if(isset($default_values['intro'])) {
            switch($default_values['intro']) {
                case '<h3>This is a simple label</h3>':
                    $default_values['contenttype'] = 'heading';
                    break;
                case '<div class=""><hr /></h3></div>':
                    $default_values['contenttype'] = 'divider';
                    break;
                case '<div class="mb-6"></div>':
                    $default_values['contenttype'] = 'spacer';
                    break;
            }
        }

        $default_values['heading'] = $default_values['intro'];
    }

    public function xget_data() {
        $data = parent::get_data();
        $content = '<h3>Heading</h3';

        if ($data) {
            if($data->phone) {
                $content = '<h3 style="color:red;">' . $data->phone . ' (H2) </h3>';
            }

            $data->intro = '<div>'. $content .'</div>';

            $this->data_postprocessing($data);
        }
        return $data;
    }
}

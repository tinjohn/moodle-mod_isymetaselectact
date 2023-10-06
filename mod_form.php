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
 * isymetaselectact configuration form
 *
 * @package    mod_isymetaselectact
 * @copyright 2023 Tina John <tina.john@th-luebeck.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once ($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/isymetaselectact/locallib.php');

class mod_isymetaselectact_mod_form extends moodleform_mod {
    function definition() {
        global $CFG, $DB;
        $mform = $this->_form;

        $config = get_config('isymetaselectact');

        //-------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('name'), array('size'=>'48'));
        $mform->addHelpButton('name', 'name', 'isymetaselectact');
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
      
            
        $this->standard_intro_elements();
        $element = $mform->getElement('introeditor');
        $attributes = $element->getAttributes();
        $attributes['rows'] = 5;
        $element->setAttributes($attributes);
        //-------------------------------------------------------
        $mform->addElement('text', 'coursedesc', get_string('coursedesc'), array('size'=>'48'));
        $mform->addHelpButton('coursedesc', 'coursedesc', 'isymetaselectact');
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('coursedesc', PARAM_TEXT);
        } else {
            $mform->setType('coursedesc', PARAM_CLEANHTML);
        }
        $mform->addRule('coursedesc', null, 'required', null, 'client');
        $mform->addRule('coursedesc', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        //-------------------------------------------------------
        $this->standard_coursemodule_elements();

        //-------------------------------------------------------
        $this->add_action_buttons();
    }

}

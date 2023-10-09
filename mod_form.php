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
require_once($CFG->dirroot . '/blocks/ildmetaselect/locallib.php'); // Adjust the path as needed
require_once($CFG->dirroot.'/blocks/ildmetaselect/ildmetaselect_form.php');

class mod_isymetaselectact_mod_form extends moodleform_mod {
    function definition() {
        global $CFG, $DB;
        $mform = $this->_form;

        //$mformims = new ildmetaselect_form(null,$customdata);

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
        $mform->addElement('text', 'coursedesc', get_string('coursedesc','isymetaselectact'), array('size'=>'48'));
        $mform->addHelpButton('coursedesc', 'coursedesc', 'isymetaselectact');
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('coursedesc', PARAM_TEXT);
        } else {
            $mform->setType('coursedesc', PARAM_CLEANHTML);
        }
        //$mform->addRule('coursedesc', null, 'required', null, 'client');
        $mform->addRule('coursedesc', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // Entweder oder
        $mform->addElement('static','hinteitheror',get_string('hinteitheror','isymetaselectact'));
        // Von ildmetasselect
        // Meta select form
        $data = new stdClass();
        $data->courselanguage = optional_param('courselanguage', 1, PARAM_INT);
        $data->subjectarea = optional_param('subjectarea', 1, PARAM_INT);
        $data->provider = optional_param('provider', 1, PARAM_INT);
        $data->processingtime = optional_param('processingtime', "all", PARAM_TEXT);
        $data->starttime = optional_param('starttime', "all", PARAM_TEXT);

        $records = get_courses_records($data);

        $provider_list = get_filtered_provider_list($records);
        $subjectarea_list = get_filtered_subjectarea_list($records);
        $processingtime_list = get_filtered_processingtime_list($records);
        $starttime_list = get_filtered_starttime_list($records);
        $lang_list = get_filtered_lang_list($records);

        $subjectarea = $mform->createElement('select', 'subjectarea', '', [], array());
		$mform->setType('subjectarea', PARAM_RAW);

		foreach ($subjectarea_list as $value => $label) {
			$attributes = array();
			if ($value === 0) {
				$attributes['disabled'] = 'disabled';
				$attributes['selected'] = 'selected';
			}
			$subjectarea->addOption(explode("=>", $label)[1], explode("=>", $label)[0], $attributes);
		}
		$mform->addElement($subjectarea);
		// ADDED option tinjohn 20221010
		if(	get_config('block_ildmetaselect','add_formmenu_provider')) {
			$provider = $mform->createElement('select', 'provider', '', [], array());
			$mform->setType('provider', PARAM_RAW);

			foreach ($provider_list as $value => $label) {
				$attributes = array();
				if ($value === 0) {
					$attributes['disabled'] = 'disabled';
					$attributes['selected'] = 'selected';
				}
				$provider->addOption(explode("=>", $label)[1], explode("=>", $label)[0], $attributes);
			}
			$mform->addElement($provider);
		}


        // ADDED option tinjohn 20221010.
		if (get_config('block_ildmetaselect','add_formmenu_courselanguage')) {
			$courselanguage = $mform->createElement('select', 'courselanguage', '', [], array());
			$mform->setType('courselanguage', PARAM_RAW);

			foreach ($lang_list as $value => $label) {
				$attributes = array();
				if ($value === 0) {
					$attributes['disabled'] = 'disabled';
					$attributes['selected'] = 'selected';
				}
				$courselanguage->addOption(explode("=>", $label)[1], explode("=>", $label)[0], $attributes);
			}
			$mform->addElement($courselanguage);
	  }


		$processingtime = $mform->createElement('select', 'processingtime', '', [], array());
		$mform->setType('processingtime', PARAM_RAW);


		foreach ($processingtime_list as $value => $label) {
			$attributes = array();
			if ($value === '-') {
				$attributes['disabled'] = 'disabled';
				$attributes['selected'] = 'selected';
			}
			$processingtime->addOption(explode("=>", $label)[1], explode("=>", $label)[0], $attributes);
		}

		$mform->addElement($processingtime);

		// ADDED option tinjohn 20221010.
		if (get_config('block_ildmetaselect','add_formmenu_starttime')) {
			$starttime = $mform->createElement('select', 'starttime', '', [], array());
			$mform->setType('starttime', PARAM_RAW);

			foreach ($starttime_list as $value => $label) {
				$attributes = array();
				if ($value === '-') {
					$attributes['disabled'] = 'disabled';
					$attributes['selected'] = 'selected';
				}
				$starttime->addOption(explode("=>", $label)[1], explode("=>", $label)[0], $attributes);
			}

			$mform->addElement($starttime);
        }

        // ENDE ildmetaselect


        //$mform->addElement('html',$mformims->render());
        //-------------------------------------------------------
        $this->standard_coursemodule_elements();

        //-------------------------------------------------------
        $this->add_action_buttons();
    }

}

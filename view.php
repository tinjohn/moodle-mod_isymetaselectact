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
 * Isy Course View Activity module main user interface
 *
 * @package    mod_isymetaselectact
 * @copyright 2023 Tina John <tina.john@th-luebeck.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


 require_once("../../config.php");
 require_once("lib.php");
 require_once("locallib.php");
  // Load the necessary files and classes from the "ildmetaselect" block
require_once($CFG->dirroot . '/blocks/ildmetaselect/get_metacourses.php'); // Adjust the path as needed
require_once($CFG->dirroot . '/blocks/ildmetaselect/locallib.php'); // Adjust the path as needed

 
 $cmid = required_param('id', PARAM_INT); // Course Module ID
 $cm = get_coursemodule_from_id('isymetaselectact', $cmid, 0, false, MUST_EXIST);
 $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
 
 require_course_login($course, true, $cm);
 // Define the page context
 $context = context_module::instance($cm->id);
 
  // Set up the page header
  $PAGE->set_url('/mod/isymetaselectact/view.php', array('id' => $cm->id));
  $PAGE->set_title(format_string($cm->name));
  $PAGE->set_heading(format_string($course->fullname));
  //$PAGE->set_pagelayout('embedded');

 // Retrieve the course description for query from the database
 $coursedesc = $DB->get_field('isymetaselectact', 'coursedesc', array('id' => $cm->instance));
  
// $data = new stdClass();
//         $data->subjectarea = 0;
//         $data->provider = 0;
//         $data->courselanguage = 0;
//         $data->processingtime = "-";
//         $data->starttime = "-";
// $records = get_courses_records($data);
$records = llsearchterm($coursedesc);
$content = '';
 // Display the course description text
$content .= '<div class="coursedesc">';
$content .= get_metacourses($records,$context);
$content .= '</div>';

 
// Output starts here
echo $OUTPUT->header();
echo $content;
// Output ends here
echo $OUTPUT->footer();
 
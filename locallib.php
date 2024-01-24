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
 * Library of functions and constants for module isymetaselectact
 * outside of what is required for the core moodle api
 *
 * @package   mod_isymetaselectact
 * @copyright 2023 Tina John <tina.john@th-luebeck.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

//require_once("../../config.php");

 // Load the necessary files and classes from the "ildmetaselect" block
 require_once($CFG->dirroot . '/blocks/ildmetaselect/get_metacourses.php'); // Adjust the path as needed
 require_once($CFG->dirroot . '/blocks/ildmetaselect/locallib.php'); // Adjust the path as needed
   
function get_content($cm,$context) {
    global $DB;

    // Retrieve the course description for query from the database
    $coursedesc = $DB->get_field('isymetaselectact', 'coursedesc', array('id' => $cm->instance));
    if($coursedesc != "") {
        $records = llsearchterm($coursedesc);
    } else {
        $data = $DB->get_record('isymetaselectact', array('id' => $cm->instance));
    
        // $data = new stdClass();
        //         $data->subjectarea = $DB->get_field('isymetaselectact', 'subjectarea', array('id' => $cm->instance));
        //         $data->provider = 0;
        //         $data->courselanguage = 0;
        //         $data->processingtime = "-";
        //         $data->starttime = "-";
        $records = get_courses_records($data);    
    }

    $content = '';
    // Display the course description text
    $content .= '<div class="coursedesc">';
    $content .= get_metacourses($records,$context);
    $content .= '</div>';
    return $content;
}  

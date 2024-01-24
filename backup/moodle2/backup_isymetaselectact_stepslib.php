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
 * Define all the backup steps that will be used by the backup_isymetaselectact_activity_task
 *
 * @package    mod_isymetaselectact
 * @copyright  2023 Tina John <tina.john@th-luebeck.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//defined('MOODLE_INTERNAL') || die;

 /**
 * Define the complete isymetaselectact structure for backup, with file and id annotations
 */
class backup_isymetaselectact_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        //the isymetaselectact module stores no user info

        // Define each element separated
        $isymetaselectact = new backup_nested_element('isymetaselectact', array('id'), array(
            'name', 'intro', 'introformat', 'coursedesc',
            'subjectarea','provider','courselanguage','processingtime','starttime',
            'display','timemodified'));

        // Build the tree
        //nothing here for isymetaselectacts

        // Define sources
        $isymetaselectact->set_source_table('isymetaselectact', array('id' => backup::VAR_ACTIVITYID));

        // Define id annotations
        //module has no id annotations

        // Define file annotations
        $isymetaselectact->annotate_files('mod_isymetaselectact', 'intro', null); // This file area hasn't itemid

        // Return the root element (isymetaselectact), wrapped into standard activity structure
        return $this->prepare_activity_structure($isymetaselectact);

    }
}

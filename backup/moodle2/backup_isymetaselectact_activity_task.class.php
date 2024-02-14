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
 * @package  mod_isymetaselectact
 * @copyright 2023 Tina John <tina.john@th-luebeck.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/mod/isymetaselectact/backup/moodle2/backup_isymetaselectact_stepslib.php'); // Because it exists (must)
require_once($CFG->dirroot . '/mod/isymetaselectact/backup/moodle2/backup_isymetaselectact_settingslib.php'); // Because it exists (optional)

/**
 * choice backup task that provides all the settings and steps to perform one
 * complete backup of the activity
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/isymetaselectact/backup/moodle2/backup_isymetaselectact_stepslib.php');

/**
 * Provides all the settings and steps to perform one complete backup of the activity
 */
class backup_isymetaselectact_activity_task extends backup_activity_task {

    /**
     * No specific settings for this activity
     */
    protected function define_my_settings() {
    }

    /**
     * Defines a backup step to store the instance data in the isymetaselectact.xml file
     */
    protected function define_my_steps() {
        $this->add_step(new backup_isymetaselectact_activity_structure_step('isymetaselectact_structure', 'isymetaselectact.xml'));
    }

    /**
     * Encodes URLs to the index.php and view.php scripts
     *
     * @param string $content some HTML text that eventually contains isymetaselectacts to the activity instance scripts
     * @return string the content with the URLs encoded
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot.'/mod/isymetaselectact','#');

        //Access a list of all links in a course
        $pattern = '#('.$base.'/index\.php\?id=)([0-9]+)#';
        $replacement = '$@ISYMETASELECTACTINDEX*$2@$';
        $content = preg_replace($pattern, $replacement, $content);

        //Access the link supplying a course module id
        $pattern = '#('.$base.'/view\.php\?id=)([0-9]+)#';
        $replacement = '$@ISYMETASELECTACTVIEWBYID*$2@$';
        $content = preg_replace($pattern, $replacement, $content);

        //Access the link supplying an instance id
        // $pattern = '#('.$base.'/view\.php\?u=)([0-9]+)#';
        // $replacement = '$@ISYMETASELECTACTVIEWBYU*$2@$';
        // $content = preg_replace($pattern, $replacement, $content);

        return $content;
    }
}

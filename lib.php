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
 * Mandatory public API of isymetaselectact module
 *
 * @package    mod_isymetaselectact
 * @copyright 2023 Tina John <tina.john@th-luebeck.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * List of features supported in isymetaselectact module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know or string for the module purpose.
 */
function isymetaselectact_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;
        case FEATURE_MOD_PURPOSE:             return MOD_PURPOSE_CONTENT;

        default: return null;
    }
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function isymetaselectact_reset_userdata($data) {

    // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
    // See MDL-9367.

    return array();
}

/**
 * List the actions that correspond to a view of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = 'r' and edulevel = LEVEL_PARTICIPATING will
 *       be considered as view action.
 *
 * @return array
 */
function isymetaselectact_get_view_actions() {
    return array('view', 'view all');
}

/**
 * List the actions that correspond to a post of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = ('c' || 'u' || 'd') and edulevel = LEVEL_PARTICIPATING
 *       will be considered as post action.
 *
 * @return array
 */
function isymetaselectact_get_post_actions() {
    return array('update', 'add');
}

/**
 * Add isymetaselectact instance.
 * @param object $data
 * @param object $mform
 * @return int new isymetaselectact instance id
 */
function isymetaselectact_add_instance($data, $mform) {
    global $CFG, $DB;

    require_once($CFG->dirroot.'/mod/isymetaselectact/locallib.php');

    $data->coursedesc = $data->coursedesc;

    $data->timemodified = time();
    $data->id = $DB->insert_record('isymetaselectact', $data);

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($data->coursemodule, 'isymetaselectact', $data->id, $completiontimeexpected);

    return $data->id;
}

/**
 * Update isymetaselectact instance.
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function isymetaselectact_update_instance($data, $mform) {
    global $CFG, $DB;

    require_once($CFG->dirroot.'/mod/isymetaselectact/locallib.php');

    $data->coursedesc = $data->coursedesc;
    $data->timemodified = time();
    $data->id           = $data->instance;

    $DB->update_record('isymetaselectact', $data);

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($data->coursemodule, 'isymetaselectact', $data->id, $completiontimeexpected);

    return true;
}

/**
 * Delete isymetaselectact instance.
 * @param int $id
 * @return bool true
 */
function isymetaselectact_delete_instance($id) {
    global $DB;

    if (!$isymetaselectact = $DB->get_record('isymetaselectact', array('id'=>$id))) {
        return false;
    }

    $cm = get_coursemodule_from_instance('isymetaselectact', $id);
    \core_completion\api::update_completion_date_event($cm->id, 'isymetaselectact', $id, null);

    // note: all context files are deleted automatically

    $DB->delete_records('isymetaselectact', array('id'=>$isymetaselectact->id));

    return true;
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 *
 * See {@link course_modinfo::get_array_of_activities()}
 *
 * @param object $coursemodule
 * @return cached_cm_info info
 */
function isymetaselectact_get_coursemodule_info($coursemodule) {
    global $CFG, $DB;
    require_once("$CFG->dirroot/mod/isymetaselectact/locallib.php");

    if (!$isymetaselectact = $DB->get_record('isymetaselectact', array('id'=>$coursemodule->instance),
            'id, name, display, coursedesc, intro, introformat')) {
        return NULL;
    }

    $info = new cached_cm_info();
    $info->name = $isymetaselectact->name;

    if ($coursemodule->showdescription) {
        // Convert intro to html. Do not filter cached version, filters run at display time.
        $info->content = format_module_intro('isymetaselectact', $isymetaselectact, $coursemodule->id, false);
    }

    // The icon will be filtered if it will be the default module icon.
    $info->customdata['filtericon'] = empty($info->icon);

    return $info;
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function isymetaselectact_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $module_pagetype = array('mod-isymetaselectact-*'=>get_string('page-mod-isymetaselectact-x', 'isymetaselectact'));
    return $module_pagetype;
}

/**
 * Export isymetaselectact resource contents
 *
 * @return array of file content
 */
function isymetaselectact_export_contents($cm, $baseurl) {
    global $CFG, $DB;
    $contents = array();
    $context = context_module::instance($cm->id);
    $isymetaselectact = $DB->get_record('isymetaselectact', array('id'=>$cm->instance), '*', MUST_EXIST);

    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_isymetaselectact', 'content', 0, 'sortorder DESC, id ASC', false);

    foreach ($files as $fileinfo) {
        $file = array();
        $file['type'] = 'file';
        $file['filename']     = $fileinfo->get_filename();
        $file['filepath']     = $fileinfo->get_filepath();
        $file['filesize']     = $fileinfo->get_filesize();
        $file['fileurl']      = file_encode_url("$CFG->wwwroot/" . $baseurl, '/'.$context->id.'/mod_isymetaselectact/content/'.$isymetaselectact->revision.$fileinfo->get_filepath().$fileinfo->get_filename(), true);
        $file['timecreated']  = $fileinfo->get_timecreated();
        $file['timemodified'] = $fileinfo->get_timemodified();
        $file['sortorder']    = $fileinfo->get_sortorder();
        $file['userid']       = $fileinfo->get_userid();
        $file['author']       = $fileinfo->get_author();
        $file['license']      = $fileinfo->get_license();
        $file['mimetype']     = $fileinfo->get_mimetype();
        $file['isexternalfile'] = $fileinfo->is_external_file();
        if ($file['isexternalfile']) {
            $file['repositorytype'] = $fileinfo->get_repository_type();
        }
        $contents[] = $file;
    }

    return $contents;
}

/**
 * Register the ability to handle drag and drop file uploads
 * @return array containing details of the files / types the mod can handle
 */
function isymetaselectact_dndupload_register() {
    return array('types' => array(
                     array('identifier' => 'text', 'message' => get_string('createisymetaselectact', 'isymetaselectact'))
                 ));
}

/**
 * Handle a file that has been uploaded
 * @param object $uploadinfo details of the file / content that has been uploaded
 * @return int instance id of the newly created mod
 */
function isymetaselectact_dndupload_handle($uploadinfo) {
    // Gather all the required data.
    $data = new stdClass();
    $data->course = $uploadinfo->course->id;
    $data->name = $uploadinfo->displayname;
    $data->intro = '<p>'.$uploadinfo->displayname.'</p>';
    $data->introformat = FORMAT_HTML;
    $data->coursedesc = clean_param($uploadinfo->content, PARAM_URL);
    $data->timemodified = time();
    $data->coursemodule = $uploadinfo->coursemodule;

    // Set the display options to the site defaults.
    $config = get_config('isymetaselectact');
    $data->display = $config->display;
    $data->popupwidth = $config->popupwidth;
    $data->popupheight = $config->popupheight;
    $data->printintro = $config->printintro;

    return isymetaselectact_add_instance($data, null);
}

/**
 * Mark the activity completed (if required) and trigger the course_module_viewed event.
 *
 * @param  stdClass $isymetaselectact        isymetaselectact object
 * @param  stdClass $course     course object
 * @param  stdClass $cm         course module object
 * @param  stdClass $context    context object
 * @since Moodle 3.0
 */
function isymetaselectact_view($isymetaselectact, $course, $cm, $context) {

    // Trigger course_module_viewed event.
    $params = array(
        'context' => $context,
        'objectid' => $isymetaselectact->id
    );

    $event = \mod_isymetaselectact\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('isymetaselectact', $isymetaselectact);
    $event->trigger();

    // Completion.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}

/**
 * Check if the module has any update that affects the current user since a given time.
 *
 * @param  cm_info $cm course module data
 * @param  int $from the time to check updates from
 * @param  array $filter  if we need to check only specific updates
 * @return stdClass an object with the different type of areas indicating if they were updated or not
 * @since Moodle 3.2
 */
function isymetaselectact_check_updates_since(cm_info $cm, $from, $filter = array()) {
    $updates = course_check_module_updates_since($cm, $from, array('content'), $filter);
    return $updates;
}

/**
 * This function receives a calendar event and returns the action associated with it, or null if there is none.
 *
 * This is used by block_myoverview in order to display the event appropriately. If null is returned then the event
 * is not displayed on the block.
 *
 * @param calendar_event $event
 * @param \core_calendar\action_factory $factory
 * @param int $userid ID override for calendar events
 * @return \core_calendar\local\event\entities\action_interface|null
 */
function mod_isymetaselectact_core_calendar_provide_event_action(calendar_event $event,
                                                       \core_calendar\action_factory $factory, $userid = 0) {

    global $USER;
    if (empty($userid)) {
        $userid = $USER->id;
    }

    $cm = get_fast_modinfo($event->courseid, $userid)->instances['isymetaselectact'][$event->instance];

    $completion = new \completion_info($cm->get_course());

    $completiondata = $completion->get_data($cm, false, $userid);

    if ($completiondata->completionstate != COMPLETION_INCOMPLETE) {
        return null;
    }

    return $factory->create_instance(
        get_string('view'),
        new \moodle_url('/mod/isymetaselectact/view.php', ['id' => $cm->id]),
        1,
        true
    );
}

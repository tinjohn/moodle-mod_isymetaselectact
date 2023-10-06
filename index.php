<?php

/// This page lists all the instances of isymetaselectact in a particular course
/// Replace isymetaselectact with the name of your module

require_once("../../config.php");
require_once("lib.php");
require_once("$CFG->libdir/rsslib.php");
require_once("$CFG->dirroot/course/lib.php");

$id = required_param('id', PARAM_INT);   // course

$PAGE->set_url('/mod/isymetaselectact/index.php', array('id'=>$id));

if (!$course = $DB->get_record('course', array('id'=>$id))) {
    throw new \moodle_exception('invalidcourseid');
}

require_course_login($course);
$PAGE->set_pagelayout('incourse');
$context = context_course::instance($course->id);

$event = \mod_isymetaselectact\event\course_module_instance_list_viewed::create(array(
    'context' => $context
));
$event->add_record_snapshot('course', $course);
$event->trigger();

/// Get all required strings

$strisymetaselectacts = get_string("modulenameplural", "isymetaselectact");
$strisymetaselectact  = get_string("modulename", "isymetaselectact");
$strrss = get_string("rss");


/// Print the header
$PAGE->navbar->add($strisymetaselectacts, "index.php?id=$course->id");
$PAGE->set_title($strisymetaselectacts);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($strisymetaselectacts), 2);

/// Get all the appropriate data

if (! $isymetaselectacts = get_all_instances_in_course("isymetaselectact", $course)) {
    notice(get_string('thereareno', 'moodle', $strisymetaselectacts), "../../course/view.php?id=$course->id");
    die;
}

$usesections = course_format_uses_sections($course->format);

/// Print the list of instances (your module will probably extend this)

$timenow = time();
$strname  = get_string("name");
$strentries  = get_string("entries", "isymetaselectact");

$table = new html_table();

if ($usesections) {
    $strsectionname = get_string('sectionname', 'format_'.$course->format);
    $table->head  = array ($strsectionname, $strname, $strentries);
    $table->align = array ('center', 'left', 'center');
} else {
    $table->head  = array ($strname, $strentries);
    $table->align = array ('left', 'center');
}

if ($show_rss = (isset($CFG->enablerssfeeds) && isset($CFG->isymetaselectact_enablerssfeeds) &&
                 $CFG->enablerssfeeds && $CFG->isymetaselectact_enablerssfeeds)) {
    $table->head[] = $strrss;
    $table->align[] = 'center';
}

$currentsection = "";

foreach ($isymetaselectacts as $isymetaselectact) {
    if (!$isymetaselectact->visible && has_capability('moodle/course:viewhiddenactivities',
            context_module::instance($isymetaselectact->coursemodule))) {
        // Show dimmed if the mod is hidden.
        $link = "<a class=\"dimmed\" href=\"view.php?id=$isymetaselectact->coursemodule\">".format_string($isymetaselectact->name,true)."</a>";
    } else if ($isymetaselectact->visible) {
        // Show normal if the mod is visible.
        $link = "<a href=\"view.php?id=$isymetaselectact->coursemodule\">".format_string($isymetaselectact->name,true)."</a>";
    } else {
        // Don't show the isymetaselectact.
        continue;
    }
    $printsection = "";
    if ($usesections) {
        if ($isymetaselectact->section !== $currentsection) {
            if ($isymetaselectact->section) {
                $printsection = get_section_name($course, $isymetaselectact->section);
            }
            if ($currentsection !== "") {
                $table->data[] = 'hr';
            }
            $currentsection = $isymetaselectact->section;
        }
    }

    // TODO: count only approved if not allowed to see them

    $count = $DB->count_records_sql("SELECT COUNT(*) FROM {isymetaselectact_entries} WHERE (isymetaselectactid = ? OR sourceisymetaselectactid = ?)", array($isymetaselectact->id, $isymetaselectact->id));

    //If this isymetaselectact has RSS activated, calculate it
    if ($show_rss) {
        $rsslink = '';
        if ($isymetaselectact->rsstype and $isymetaselectact->rssarticles) {
            //Calculate the tolltip text
            $tooltiptext = get_string("rsssubscriberss","isymetaselectact",format_string($isymetaselectact->name));
            if (!isloggedin()) {
                $userid = 0;
            } else {
                $userid = $USER->id;
            }
            //Get html code for RSS link
            $rsslink = rss_get_link($context->id, $userid, 'mod_isymetaselectact', $isymetaselectact->id, $tooltiptext);
        }
    }

    if ($usesections) {
        $linedata = array ($printsection, $link, $count);
    } else {
        $linedata = array ($link, $count);
    }

    if ($show_rss) {
        $linedata[] = $rsslink;
    }

    $table->data[] = $linedata;
}

echo "<br />";

echo html_writer::table($table);

/// Finish the page

echo $OUTPUT->footer();


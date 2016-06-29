<?php
/**
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
/**
 * @author Daniel Tome <danieltomefer@gmail.com>
 */

require_once 'classes/local_gradebook_constants.php';
require_once '../../config.php';
require_once $CFG->dirroot . '/local/' . Constants::PLUGIN_NAME . '/lib.php';
require_once $CFG->dirroot . '/grade/lib.php';
require_once $CFG->dirroot . '/grade/edit/tree/lib.php';
require_once $CFG->dirroot . '/local/' . Constants::PLUGIN_NAME . '/locallib.php';

//Get course id from route
$courseid = required_param('id', PARAM_INT);

//Always check if grade_items.idnumber is set. Otherwise we create one.
local_gradebook_complete_grade_idnumbers($courseid);

//Get course given an ID from DB
if (!$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST)) {
    print_error('nocourseid');
}
$context = context_course::instance($course->id);
require_login($course);

$url = new moodle_url('/local/' . Constants::PLUGIN_NAME . '/index.php');
// Prevent caching of this page to stop confusion when changing page after making AJAX changes
$PAGE->set_cacheable(false);
$PAGE->set_context($context);
$PAGE->set_url($url, ['id' => $courseid]);
$PAGE->set_heading($SITE->fullname);
$PAGE->set_pagelayout('course');
$PAGE->get_renderer('format_' . $course->format);
$PAGE->set_title(get_string('pluginname', 'local_gradebook'));

$context = context_course::instance($course->id, MUST_EXIST);

$gpr = new grade_plugin_return(array('type' => 'edit', 'plugin' => 'tree', 'courseid' => $courseid));

//Return URL to grade tree form
$url = '/local/' . Constants::PLUGIN_NAME . '/operations.php';
$returnurl = new moodle_url($url, ['id' => $courseid]);
$gtree = new grade_tree($courseid, false, false);
$grade_edit_tree = new local_gradebook_tree($gtree, false, $gpr);

echo $OUTPUT->header();
echo html_writer::tag('h2', get_string('pluginname', 'local_gradebook'));

//// Print Table of categories and items
echo $OUTPUT->box_start('gradetreebox generalbox');

echo '<form id="localgradetreeform" method="post" action="' . $returnurl . '">';
echo '<div>';
echo '<input type="hidden" name="sesskey" value="' . sesskey() . '" />';

echo html_writer::table($grade_edit_tree->table);

echo '</div>';

//Save changes button
echo '<input class="advanced" type="submit" value="' . get_string('save_changes', 'local_gradebook') . '" />';

echo '</form>';
echo $OUTPUT->box_end();
echo $OUTPUT->footer();

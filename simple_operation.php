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
require_once '../../config.php';
require_once $CFG->dirroot . '/grade/lib.php';

$courseid = required_param('courseid', PARAM_TEXT);
$id = required_param('id', PARAM_TEXT);

/// Make sure they can even access this course
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('nocourseid');
}

require_login($course);
$context = context_course::instance($course->id);

$gtree = new grade_tree($courseid, false, false);

$url = new \moodle_url('/local/' . local_gradebook\Constants::PLUGIN_NAME . '/simple_operation.php',
    [
        'id' => $id,
        'courseid' => $courseid,
    ]);
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_gradebook'));
$PAGE->set_context($context);

// Get renderer on last step
$output = $PAGE->get_renderer('local_gradebook');

echo $OUTPUT->header();
$localGradebookFunctions = new local_gradebook\Functions();
$items = $localGradebookFunctions->local_gradebook_get_list_items($gtree, $gtree->top_element);

// Display all grades tree in a checkbox input list
echo $output->gradesInputSelection($courseid, $id, $items);

echo $output->startGradesSimpleOperations();

$buttons = $localGradebookFunctions->local_gradebook_get_simple_options();

echo $output->operationButtons($buttons);
echo $output->endGradesSimpleOptions();

echo $OUTPUT->footer();

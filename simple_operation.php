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
require_once $CFG->libdir . '/mathslib.php';

$courseid = required_param('courseid', PARAM_TEXT);
$id = required_param('id', PARAM_TEXT);

/// Make sure they can even access this course
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('nocourseid');
}

require_login($course);
$context = context_course::instance($course->id);
$gtree = new grade_tree($courseid, false, false);

$PAGE->set_pagelayout('admin');
$PAGE->set_context($context);
$url = new \moodle_url('/local/' . local_gradebook\Constants::PLUGIN_NAME . '/simple_operation.php',
    [
        'id' => $id,
        'courseid' => $courseid,
    ]);
$PAGE->set_url($url);
$PAGE->set_title(get_string('pluginname', 'local_gradebook'));

$mform = new local_gradebook\form\SimpleOperationForm(null,
    ['gtree' => $gtree, 'element' => $gtree->top_element, 'courseid' => $courseid, 'id' => $id]);
/**
 * If post data is given
 */

if ($formData = $mform->get_data()) {
    //Make sure they can even access this course
    if (!$course = $DB->get_record('course', array('id' => $formData->courseid))) {
        print_error('nocourseid');
    }
    if (!$grade_item = grade_item::fetch(array('id' => $formData->id, 'courseid' => $course->id))) {
        print_error('invaliditemid');
    }
    if (empty($formData->grades)) {
        print_error('no_grades_selected', 'local_gradebook');
    }
    $localGrade = new local_gradebook\grade\Grade();
    $calculation = $localGrade->getCalculationFromParams(array_keys($formData->grades), $formData->operation);
    $calculation = \calc_formula::unlocalize($calculation);
    if (!$grade_item->validate_formula($calculation)) {
        print_error('error');
    }
    $grade_item->set_calculation($calculation);
    $message = get_string('add_operation_success', 'local_gradebook');
    $urlToRedirect = new \moodle_url('/local/gradebook/index.php', ['id' => $courseid]);
    redirect($urlToRedirect, $message, null, \core\output\notification::NOTIFY_SUCCESS);
}


// Get renderer on last step
$output = $PAGE->get_renderer('local_gradebook');


echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();

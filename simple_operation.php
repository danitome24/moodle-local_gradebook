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

$id = required_param('id', PARAM_TEXT);
$gradeid = required_param('gradeid', PARAM_TEXT);

/// Make sure they can even access this course
if (!$course = $DB->get_record('course', array('id' => $id))) {
    print_error('nocourseid');
}

require_login($course);
$context = context_course::instance($course->id);
$gtree = new grade_tree($id, false, false);

$PAGE->set_pagelayout('admin');
$PAGE->set_context($context);
$url = new \moodle_url('/local/' . local_gradebook\Constants::PLUGIN_NAME . '/simple_operation.php',
    [
        'id' => $id,
        'gradeid' => $gradeid,
    ]);
$PAGE->set_url($url);
$PAGE->set_title(get_string('pluginname', 'local_gradebook'));
$PAGE->requires->js('/local/gradebook/js/simple_op.js');

$mform = new local_gradebook\form\SimpleOperationForm(null,
    ['gtree' => $gtree, 'element' => $gtree->top_element, 'gradeid' => $gradeid, 'id' => $id]);
/**
 * If post data is given
 */
if ($formData = $mform->get_data()) {
    //Make sure they can even access this course
    if (!$course = $DB->get_record('course', array('id' => $formData->id))) {
        print_error('nocourseid');
    }
    if (!$grade_item = grade_item::fetch(array('id' => $formData->gradeid, 'courseid' => $course->id))) {
        print_error('invaliditemid');
    }
    if (empty($formData->grades) && !isset($formData->clearbutton)) {
        print_error('no_grades_selected', 'local_gradebook');
    }
    if (isset($formData->resetbutton)) {
        $calculation = '';
    } else {
        $localGrade = new local_gradebook\grade\Grade();
        $calculation = $localGrade->getCalculationFromParams(array_keys($formData->grades), $formData->operation);
        $calculation = \calc_formula::unlocalize($calculation);
    }

    if (!$grade_item->validate_formula($calculation)) {
        print_error('error');
    }
    $grade_item->set_calculation($calculation);
    $message = get_string('add_operation_success', 'local_gradebook');
    $urlToRedirect = new \moodle_url('/local/gradebook/index.php', ['id' => $id]);
    redirect($urlToRedirect, $message, null, \core\output\notification::NOTIFY_SUCCESS);
}

// Get info to display in form
if (!$grade_item = grade_item::fetch(array('id' => $gradeid, 'courseid' => $id))) {
    print_error('invaliditemid');
}
$calculation = $grade_item->get_calculation();
if (isset($calculation)) {
    $formDataToFillContent = new stdClass();
    $formDataToFillContent->id = $id;
    $formDataToFillContent->gradeid = $gradeid;

    $formDataToFillContent->grades = local_gradebook\grade\Grade::getIdNumbersInArrayFromCalculation($calculation);
    $formDataToFillContent->operation = local_gradebook\grade\Grade::getOperationFromCalculation($calculation);
}

// Get renderer on last step
$output = $PAGE->get_renderer('local_gradebook');

//require_once $CFG->dirroot . '/local/' . local_gradebook\Constants::PLUGIN_NAME . '/modals/remove_confirmation.php';

echo $OUTPUT->header();
if (isset($calculation)) {
    $mform->set_data($formDataToFillContent);
}
$mform->display();

echo $OUTPUT->footer();

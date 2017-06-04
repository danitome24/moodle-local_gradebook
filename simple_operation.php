<?php
// This file is part of Moodle - http://moodle.org/
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
//
// @author Daniel Tome <danieltomefer@gmail.com>.

use local_gradebook\grade\GradeCalculationFormatter;

require_once('../../config.php');
require_once($CFG->dirroot . '/grade/lib.php');
require_once($CFG->libdir . '/mathslib.php');

$id = required_param('id', PARAM_TEXT);
$gradeid = required_param('gradeid', PARAM_TEXT);

// Always check if grade_items.idnumber is set. Otherwise we create one.
$localgrade = new local_gradebook\grade\Grade();
$localgrade->complete_grade_idnumbers($id);

// Make sure they can even access this course.
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
$PAGE->requires->js_call_amd('local_gradebook/simple_op', 'initialise');

$mform = new local_gradebook\form\SimpleOperationForm(null,
    ['gtree' => $gtree, 'element' => $gtree->top_element, 'gradeid' => $gradeid, 'id' => $id]);

if ($formdata = $mform->get_data()) {
    // Make sure they can even access this course.
    if (!$course = $DB->get_record('course', array('id' => $formdata->id))) {
        print_error('nocourseid');
    }

    if (!$gradeitem = grade_item::fetch(array('id' => $formdata->gradeid, 'courseid' => $course->id))) {
        print_error('invaliditemid');
    }

    if (isset($formdata->resetbutton)) {
        $calculation = '';
    } else {
        if (empty($formdata->grades) && !isset($formdata->clearbutton)) {
            print_error('no_grades_selected', 'local_gradebook');
        }

        $calculation = $formdata->calculation;

        if (!$gradeitem->validate_formula($calculation)) {
            print_error('error');
        }
    }

    $gradeitem->set_calculation($calculation);
    $message = get_string('add_operation_success', 'local_gradebook');
    $urltoredirect = new \moodle_url('/local/gradebook/index.php', ['id' => $id]);
    redirect($urltoredirect, $message, null, \core\output\notification::NOTIFY_SUCCESS);
}

// Get info to display in form.
if (!$gradeitem = grade_item::fetch(array('id' => $gradeid, 'courseid' => $id))) {
    print_error('invaliditemid');
}
$calculation = $gradeitem->get_calculation();
if (isset($calculation)) {
    $formdatatofillcontent = new stdClass();
    $formdatatofillcontent->id = $id;
    $formdatatofillcontent->gradeid = $gradeid;
    $formdatatofillcontent->calculation = grade_item::denormalize_formula($gradeitem->calculation, $course->id);
}

// Get renderer on last step.
$output = $PAGE->get_renderer('local_gradebook');

echo $OUTPUT->header();
if (isset($calculation)) {
    $mform->set_data($formdatatofillcontent);
}
$mform->display();

echo $OUTPUT->footer();

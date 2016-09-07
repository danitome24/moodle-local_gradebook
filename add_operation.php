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
require_once 'lib.php';
require_once $CFG->libdir . '/mathslib.php';
require_once $CFG->dirroot . '/grade/lib.php';

//Id of grades to add into the operation
$operation = required_param('operation', PARAM_TEXT);
$courseid = required_param('courseid', PARAM_TEXT);
$id = required_param('id', PARAM_TEXT); //Where to put the calculation
$grades = optional_param_array('grades', [], PARAM_TEXT);

/// Make sure they can even access this course
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('nocourseid');
}
require_login($course);
$context = context_course::instance($course->id);

if (!$grade_item = grade_item::fetch(array('id' => $id, 'courseid' => $course->id))) {
    print_error('invaliditemid');
}

if (!$grade_item->is_category_item()) {
    print_error('element_calculation_novalid');
}

$url = new \moodle_url('/local/' . local_gradebook\Constants::PLUGIN_NAME . '/add_operation.php',
    [
        'id' => $id,
        'operation' => $operation,
        'courseid' => $courseid,
    ]);

if (empty($grades)) {
    print_error('no_grades_selected', 'local_gradebook');
}
$localGradebookFunctions = new local_gradebook\Functions();
$calculation = $localGradebookFunctions->local_gradebook_get_calculation_from_params($grades, $operation);
$calculation = \calc_formula::unlocalize($calculation);
if (!$grade_item->validate_formula($calculation)) {
    print_error('error');
}
$grade_item->set_calculation($calculation);
$message = get_string('add_operation_success', 'local_gradebook');

$urlToRedirect = new \moodle_url('/local/gradebook/index.php', ['id' => $courseid]);
redirect($urlToRedirect, $message, null, \core\output\notification::NOTIFY_SUCCESS);

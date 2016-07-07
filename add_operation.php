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
require_once 'classes/local_gradebook_constants.php';

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

$url = new moodle_url('/local/' . Constants::PLUGIN_NAME . '/add_operation.php',
    [
        'id' => $id,
        'operation' => $operation,
        'courseid' => $courseid,
    ]);
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_gradebook'));
$PAGE->set_context($context);

echo $OUTPUT->header();
if (empty($grades)) {
    print_error('no_grades_selected', 'local_gradebook');
}

echo 'ID activitat a ser aplicat: ' . $id . "\n ID del curs: " . $courseid . "\n Activitats seleccionades: " . implode('-', $grades) . "\n Operació a aplicar: " . $operation;
getCalculationFromParams($id, $courseid, $grades, $operation);
echo $OUTPUT->footer();

die;

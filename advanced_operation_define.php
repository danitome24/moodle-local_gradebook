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
require_once $CFG->libdir . '/pagelib.php';

$courseId = required_param('id', PARAM_TEXT);
$gradeId = required_param('gradeid', PARAM_TEXT);

if (!$course = $DB->get_record('course', array('id' => $courseId))) {
    print_error('nocourseid');
}
if (!$grade_item = grade_item::fetch(array('id' => $gradeId, 'courseid' => $courseId))) {
    print_error('invaliditemid');
}

require_login($course);
$context = context_course::instance($course->id);

$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url(new moodle_url('/local/gradebook/advanced_operation_define.php'));

echo $OUTPUT->header();
echo "<h3>Definició d'operació</h3>";
echo "<p>Escull l'activitat i la fòrmula que vols aplicar en cas que la condició es compleixi</p>";
echo '<div class="row-fluid">';
echo '<div class="row-fluid">
        <div class="span4">
            <p>Escull una opearció entre les següents</p>
            <input type="radio" name="operation" value="operacio1">Operació1<br>
            <input type="radio" name="operation" value="operacio2">Operació2<br>
            <input type="radio" name="operation" value="operacio3">Operació3
        </div>
        <div class="span4">
            <p>Escull una activitat entre les següents</p>
            <input type="radio" name="item" value="avtivitat1">Activitat1<br>
            <input type="radio" name="item" value="activitat2">Activitat2<br>
            <input type="radio" name="item" value="activitat3">Activitat3
        </div>
 </div>';
echo '<br><br>';
echo '<div class="row-fluid">
        <div class="span4">
            <a class="btn btn-default" href="' . new moodle_url('/local/gradebook/advanced_operation.php', ['id' => $courseId, 'gradeid' => $gradeId]) . '">Enrere</a>
        </div>
        <div class="offset5">
            <a class="btn btn-success" href="' . new moodle_url('/local/gradebook/advanced_operation.php', ['id' => $courseId, 'gradeid' => $gradeId]) . '">Guardar</a>
        </div>
      </div>';
echo '</div>';
echo $OUTPUT->footer();

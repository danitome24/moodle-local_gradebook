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
//Id of course
$courseId = required_param('id', PARAM_TEXT);
$gradeId = required_param('gradeid', PARAM_TEXT);

/// Make sure they can even access this course
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
$PAGE->set_url(new moodle_url('/local/gradebook/advanced_operation.php', ['id' => $courseId, 'gradeid' => $gradeId]));
$PAGE->set_title(get_string('pluginname', 'local_gradebook'));
$PAGE->set_cacheable(false);
echo $OUTPUT->header();
echo '<h3>Configuració de càlcul avançat</h3>';

echo '<div class="container-fluid advanced-operation">
        <div class="row-fluid">
            <form class="form-inline">
                <p>Comparació entre elements de qualificació</p>
                <select>
                  <option>Activitat1</option>
                  <option>Activitat2</option>
                  <option>Categoria1</option>
                  <option>Categoria2</option>
                </select>
                <select>
                  <option> < </option>
                  <option> > </option>
                  <option> >= </option>
                  <option> = </option>
                  <option> <= </option>
                </select>
                <select>
                  <option>Activitat1</option>
                  <option>Activitat2</option>
                  <option>Categoria1</option>
                  <option>Categoria2</option>
                </select>
                <br><br>
                <p>El resultat per la categoria o element en cas de ser comparació <strong>positiva</strong> serà:</p>
			    <select>
                  <option>Activitat1</option>
                  <option>Activitat2</option>
                  <option>Categoria1</option>
                  <option>Categoria2</option>
                </select>
                <br><br>
                <p>El resultat per la categoria o element en cas de ser comparació <strong>positiva</strong> serà:</p>
			    <select>
                  <option>Activitat1</option>
                  <option>Activitat2</option>
                  <option>Categoria1</option>
                  <option>Categoria2</option>
                </select>
			</form>
		    </div>
	   </div>';
echo '<br><br><br>';
$backButton = new moodle_url('/local/gradebook/index.php', ['id' => $courseId]);
echo '<div class="row-fluid">
        <div class="span4">
            <a class="btn btn-default" href="' . $backButton . '">Enrere</a>
        </div>
        <div class="offset5">
            <button class="btn btn-success">Guardar canvis</button>
        </div>
      </div>';
echo $OUTPUT->footer();

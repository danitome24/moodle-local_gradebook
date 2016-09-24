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
$courseId = required_param('courseid', PARAM_TEXT);
$gradeId = required_param('gradeid', PARAM_TEXT);

/// Make sure they can even access this course
if (!$course = $DB->get_record('course', array('id' => $courseId))) {
    print_error('nocourseid');
}

require_login($course);
$context = context_course::instance($course->id);
$PAGE->requires->js_init_call(new moodle_url($CFG->wwwroot . '/local/gradebook/module.js'));
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url('/local/gradebook/advanced_operation.php', ['courseid' => $courseId, 'gradeid' => $gradeId]);
$PAGE->set_title(get_string('pluginname', 'local_gradebook'));
$PAGE->requires->js('/local/gradebook/js/module.js');
$PAGE->requires->js('/local/gradebook/js/jquery-bootstrap-modal-steps.js');
$PAGE->set_cacheable(false);
echo $OUTPUT->header();
echo '<h3>Configuració de càlcul avançat</h3>';
//Add modals
require_once $CFG->dirroot . '/local/' . local_gradebook\Constants::PLUGIN_NAME . '/modals/choose_operation_modal.php';

echo '<div class="container-fluid">
			<div class="row-fluid">
				<div class="span12">
					<!-- first row of graphic-->
					<div class="row-fluid">
						<div class="offset5">
							    <div class="input-append">
                                    <input class="" id="appendedInputButton" type="text">
                                    <a href="#myModal" data-toggle="modal" role="button" class="btn btn-default" type="button">'
                                        . get_string('add') . '</a>
                                </div>
						</div>
					</div>
					<!-- second row of graphic -->
					<div class="row-fluid">
					<div class="span12">
						<div class="btn-group input-append">
                                <input id="appendedInputButton" type="text" >
                            </div>
							<div class="btn-group local-gradebook-margin-bottom">
								<a class="local-gradebook-condition-button btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
								<span id="operationSelected">
									' . get_string('math_sign', 'local_gradebook') . ' </span> <span class="caret"></span>
								</a>
								<ul class="dropdown-menu">
									<li><a href="#">></a></li>
									<li><a href="#">>=</a></li>
									<li><a href="#"><</a></li>
									<li><a href="#"><=</a></li>
								</ul>
							</div>
							<div class="btn-group">
						        <input type="text" class="" name="firstname">
						    </div>
					    </div>
					</div>

					<!-- third row of graphic-->
					<div class="row-fluid">
						<div class="offset5">
							<div class="input-append">
                                <input class="" id="appendedInputButton" type="text">
                                <a href="#myModal" data-toggle="modal" role="button" class="btn btn-default" type="button">'
                                    . get_string('add') . '</a>
                            </div>
						</div>
					</div>
				</div>
			</div>
		</div>';
echo '<br><br><br>';
echo '<div class="row-fluid">
        <div class="span4">
            <button class="btn btn-danger">Neteja</button>
        </div>
        <div class="offset5">
            <button class="offset4 btn btn-success">Guardar canvis</button>
        </div>
      </div>';
echo $OUTPUT->footer();

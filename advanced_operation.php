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

//Id of course
$courseId = required_param('courseid', PARAM_TEXT);
$gradeId = required_param('gradeid', PARAM_TEXT);

/// Make sure they can even access this course
if (!$course = $DB->get_record('course', array('id' => $courseId))) {
    print_error('nocourseid');
}

require_login($course);
$context = context_course::instance($course->id);

$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url('/local/gradebook/advanced_operation.php', ['courseid' => $courseId, 'gradeid' => $gradeId]);
$PAGE->set_title(get_string('pluginname', 'local_gradebook'));

echo $OUTPUT->header();

echo '<div class="container-fluid">
			<div class="row-fluid">
				<div class="span6">
					<!-- first row of graphic-->
					<div class="row-fluid">
						<div class="offset5">
							<input type="text" name="firstname">
						</div>
					</div>
					<!-- second row of graphic -->
					<div class="row-fluid">
						<input type="text" class="span4" name="firstname"/>
							<div class="btn-group local-gradebook-margin-bottom">
								<a class="local-gradebook-condition-button btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
									Choose <span class="caret"></span>
								</a>
								<ul class="dropdown-menu">
									<li><a href="#">></a></li>
									<li><a href="#">>=</a></li>
									<li><a href="#"><</a></li>
									<li><a href="#"><=</a></li>
								</ul>
							</div>
						<input type="text" class="span1" name="firstname"/>
					</div>

					<!-- third row of graphic-->
					<div class="row-fluid">
						<div class="offset5">
							<input type="text"  name="firstname">
						</div>
					</div>
				</div>
		        <div class="span6">
					<div class="row-fluid">
						<div class="span6">
							<table class="table">
								<thead>
									<tr>
										<th>Categories/tasques</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><button class="btn btn-small" type="button">Tasca1</button></td>
										<td><button class="btn btn-small" type="button">Tasca2</button></td>
									</tr>
									<tr>
										<td><button class="btn btn-small" type="button">Categoria1</button></td>
									</tr>
								</tbody>
							</table>
							<!-- primera tabla-->
						</div>
						<div class="span6">
							<!-- segunda tabla -->
							<table class="table">
								<thead>
									<tr>
										<th>Operacions</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><button class="btn btn-small" type="button">Suma</button></td>
										<td><button class="btn btn-small" type="button">Resta</button></td>
									</tr>
									<tr>
										<td><button class="btn btn-small" type="button">Mitjana</button></td>
										<td><button class="btn btn-small" type="button">Màxim</button></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
                </div>
			</div>
		</div>';

echo $OUTPUT->footer();

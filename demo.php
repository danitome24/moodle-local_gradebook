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

$courseid = required_param('id', PARAM_INT);

/// Make sure they can even access this course
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('nocourseid');
}

require_login($course);
$context = context_course::instance($course->id);

$url = new moodle_url('/local/gradebook/demo.php', array('id' => $courseid));
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_gradebook'));
$PAGE->requires->js('/local/gradebook/js/demo.js');
$PAGE->requires->js_call_amd('local_gradebook/democalc', 'initialise');

// return tracking object
$gpr = new grade_plugin_return(array('type' => 'edit', 'plugin' => 'tree', 'courseid' => $courseid));
$returnurl = $gpr->get_return_url(null);

// get the grading tree object
// note: total must be first for moving to work correctly, if you want it last moving code must be rewritten!
$gtree = new grade_tree($courseid, false, false);

$output = $PAGE->get_renderer('local_gradebook');

echo $output->header();

echo $output->getGradesDemoTree($gtree, false, $gpr);
echo $output->buildParametersToSendByAjax($courseid);
echo $output->getDemoButtons();

echo $output->footer();

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

// Include config.php
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once (__DIR__ . '/../lib.php');

$courseid = required_param('id', PARAM_INT);

// Set page context
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_gradebook'));
$PAGE->set_heading(get_string('pluginname', 'local_gradebook'));

// Set page layout
$PAGE->set_pagelayout('standard');
$PAGE->set_url('/local/gradebook/view/view.php', array('id' => $courseid));

/// Make sure they can even access this course
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('nocourseid');
}

$activities = get_array_of_activities($course->id);
$activitiesSorted = sort_activities_by_mod($activities);

echo $OUTPUT->header();
var_dump($activitiesSorted);
echo 'Benvinguts a la plana gradebook';
echo $OUTPUT->footer();

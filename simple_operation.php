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
require_once 'classes/local_gradebook_constants.php';
require_once $CFG->dirroot . '/grade/lib.php';

$courseid = required_param('id', PARAM_TEXT);

/// Make sure they can even access this course
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('nocourseid');
}

require_login($course);
$context = context_course::instance($course->id);

$gtree = new grade_tree($courseid, false, false);

$url = new moodle_url('/local/' . Constants::PLUGIN_NAME . '/simple_operation.php', array('id' => $courseid));
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_gradebook'));
$PAGE->set_context($context);

echo $OUTPUT->header();

echo '<div class="row-fluid">';
$items = getListItems($gtree, $gtree->top_element);
echo '<div class="span4">';
echo '<h3>' . get_string('qualifier_elements', 'local_gradebook') . '</h3>';
echo $items;
echo '</div>';
echo '<div class="span8">';
echo '<h3>' . get_string('operations', 'local_gradebook') . '</h3>';
$buttons = local_gradebook_get_base_options(['id' => $courseid]);
echo '<table><tbody>';
$count = 0;
foreach ($buttons as $button) {
    if (!fmod($count, 2)) {
        echo '<tr>';
    }
    echo '<td>' . $button . '</td>';
    if (fmod($count, 2)) {
        echo '</tr>';
    }
    $count++;
}
echo '</tbody></table>';
echo '</div>';
echo '</div>';

echo $OUTPUT->footer();

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

require_once 'classes/local_gradebook_constants.php';
require_once '../../config.php';
require_once $CFG->dirroot . '/local/' . Constants::PLUGIN_NAME . '/lib.php';
require_once $CFG->dirroot . '/grade/lib.php';
require_once $CFG->dirroot . '/grade/edit/tree/lib.php';

//Get course id from route
$courseId = optional_param('id', 0, PARAM_INT);

//Get course given an ID from DB
if (!$course = $DB->get_record('course', ['id' => $courseId], '*', MUST_EXIST)) {
    print_error('nocourseid');
}

context_helper::preload_course($course->id);
$context = context_course::instance($course->id, MUST_EXIST);

//$gtree = new grade_tree($courseId, false, false);
//$gpr = new grade_plugin_return(array('type' => 'edit', 'plugin' => 'tree', 'courseid' => $courseId));
//$grade_edit_tree = new grade_edit_tree($gtree, false, $gpr);
//
//$gpr = new grade_plugin_return(array('type' => 'edit', 'plugin' => 'tree', 'courseid' => $courseId));
//$returnurl = $gpr->get_return_url(null);
//$moving = false;

require_login($course);

// Prevent caching of this page to stop confusion when changing page after making AJAX changes
$PAGE->set_cacheable(false);
$PAGE->set_context($context);
$PAGE->set_url('/local/' . Constants::PLUGIN_NAME . '/index.php', ['id' => $courseId]);
$PAGE->set_heading($SITE->fullname);
$PAGE->set_pagelayout('course');
$PAGE->get_renderer('format_' . $course->format);
$PAGE->set_title(get_string('pluginname', 'local_gradebook'));

echo $OUTPUT->header();
echo html_writer::tag('h2', get_string('pluginname', 'local_gradebook'));
//// Print Table of categories and items
echo $OUTPUT->box_start('gradetreebox generalbox');

echo '<form id="gradetreeform" method="post" action="' . $returnurl . '">';
echo '<div>';
echo '<input type="hidden" name="sesskey" value="' . sesskey() . '" />';


echo html_writer::table($grade_edit_tree->table);

echo '<div id="gradetreesubmit">';
if (!$moving) {
    echo '<input class="advanced" type="submit" value="' . get_string('savechanges') . '" />';
}

// We don't print a bulk move menu if there are no other categories than course category
if (!$moving && count($grade_edit_tree->categories) > 1) {
    echo '<br /><br />';
    echo '<input type="hidden" name="bulkmove" value="0" id="bulkmoveinput" />';
    $attributes = array('id' => 'menumoveafter', 'class' => 'ignoredirty singleselect');
    echo html_writer::label(get_string('moveselectedto', 'grades'), 'menumoveafter');
    echo html_writer::select($grade_edit_tree->categories, 'moveafter', '', array('' => 'choosedots'), $attributes);
    $OUTPUT->add_action_handler(new component_action('change', 'submit_bulk_move'), 'menumoveafter');
    echo '<div id="noscriptgradetreeform" class="hiddenifjs">
            <input type="submit" value="' . get_string('go') . '" />
          </div>';
}

echo '</div>';

echo '</div></form>';

echo $OUTPUT->box_end();

echo $OUTPUT->footer();

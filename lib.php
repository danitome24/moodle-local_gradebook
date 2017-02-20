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
/**
 * Function to display a link on navigation
 * @codeCoverageIgnore
 * @param settings_navigation $nav
 * @param context $context
 * @return bool
 */
function local_gradebook_extend_settings_navigation(settings_navigation $nav, context $context)
{
    global $PAGE;
    //Check capability of user
    if (!has_capability('local/gradebook:access', $context)) {
        return false;
    }

    if (!($courseAdminNode = $nav->find('courseadmin', navigation_node::TYPE_COURSE))) {
        return false;
    }
    $courseId = optional_param('id', 0, PARAM_INT);
    $gradeid = optional_param('gradeid', 0, PARAM_INT);

    $navigationCollection = navigation_node::create(get_string('navbar_link', 'local_gradebook'));
    $navigationNode = $courseAdminNode->add_node($navigationCollection);

    $localGradebookNode = navigation_node::create(
        get_string('navbar_link', 'local_gradebook'),
        new moodle_url('/local/gradebook/index.php', ['id' => $courseId]),
        navigation_node::TYPE_CUSTOM,
        null,
        null,
        new pix_icon('t/calc', get_string('navbar_link', 'local_gradebook'))
    );
    $demoNode = navigation_node::create(
        get_string('demo_navbar', 'local_gradebook'),
        new moodle_url('/local/gradebook/demo.php', ['id' => $courseId]),
        navigation_node::TYPE_CUSTOM,
        null,
        null,
        new pix_icon('i/report', get_string('navbar_link', 'local_gradebook'))
    );

    if ($PAGE->url->compare(new moodle_url('/local/gradebook/simple_operation.php',
        []), URL_MATCH_BASE)
    ) {
        $simpleOperationNode = navigation_node::create(get_string('simple_operation', 'local_gradebook'));
        $simpleOperationNode->make_active();
        $simpleOperationNode->force_open();
        $navigationNode->force_open();
        $localGradebookNode->add_node($simpleOperationNode);
    }

    if ($PAGE->url->compare(new moodle_url('/local/gradebook/advanced_operation.php',
        []), URL_MATCH_BASE)
    ) {
        $advancedOperation = navigation_node::create(get_string('advanced_operation', 'local_gradebook'));
        $advancedOperation->make_active();
        $advancedOperation->force_open();
        $navigationNode->force_open();
        $localGradebookNode->add_node($advancedOperation);
    }

    $navigationNode->add_node($demoNode);
    $navigationNode->add_node($localGradebookNode);
}

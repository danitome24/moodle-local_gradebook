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

defined('MOODLE_INTERNAL') || die;

/**
 * Method to insert gradebook plugin link into menu
 * @param settings_navigation $nav
 * @param context $context
 * @return bool
 * @throws coding_exception
 */
function local_gradebook_extend_settings_navigation(settings_navigation $nav, context $context)
{

    if (! ($courseAdminNode = $nav->find('courseadmin', navigation_node::TYPE_COURSE))) {
        return false;
    }
    //Getting course id
    $courseid = required_param('id', PARAM_INT);

    $url = new moodle_url('/local/gradebook/view/view.php', array('id' => $courseid));
    $node = navigation_node::create(get_string('pluginname', 'local_gradebook'), $url, navigation_node::TYPE_CONTAINER, null, 'gradebook');
    $courseAdminNode->add_node($node);
}

/**
 * Method to sort all activities depending on his mod
 * @param $activities Array with activities from a given course
 * @return array Array with activities sorted
 */
function sort_activities_by_mod($activities) {
    $sortedActivities = array();
    foreach($activities as $activity) {
        $sortedActivities[$activity->mod][] = $activity;
    }
    return $sortedActivities;
}

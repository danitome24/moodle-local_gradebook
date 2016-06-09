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
require_once($CFG->dirroot . '/local/gradebook/settings.php');

function local_gradebook_extend_settings_navigation(settings_navigation $nav, context $context)
{
    if (!($courseAdminNode = $nav->find('courseadmin', navigation_node::TYPE_COURSE))) {
        return false;
    }
    $url = new moodle_url('/local/' . constants::PLUGIN_NAME . '/view.php');
    $name = get_string('pluginname', 'local_gradebook');
    $type = navigation_node::TYPE_CONTAINER;
    $node = navigation_node::create($name, $url, $type, null, constants::PLUGIN_NAME, new pix_icon('t/calc', $name));
    $courseAdminNode->add_node($node);
}

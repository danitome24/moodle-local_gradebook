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

function local_gradebook_extend_settings_navigation(settings_navigation $nav, context $context)
{

    require_once 'classes/local_gradebook_constants.php';

    //Check capability of user
    if (!has_capability('local/gradebook:access', $context)) {
        return false;
    }

    if (!($courseAdminNode = $nav->find('courseadmin', navigation_node::TYPE_COURSE))) {
        return false;
    }
    $courseId = optional_param('id', 0, PARAM_INT);

    $url = new moodle_url('/local/' . Constants::PLUGIN_NAME . '/index.php', ['id' => $courseId]);
    $name = get_string('pluginname', 'local_gradebook');
    $type = navigation_node::TYPE_CONTAINER;
    $node = navigation_node::create($name, $url, $type, null, Constants::PLUGIN_NAME, new pix_icon('t/calc', $name));
    $courseAdminNode->add_node($node);
}

/**
 * Function to get base options buttons
 */
function local_gradebook_get_simple_options($params)
{
    $buttonNames = ['op:average', 'op:maximum', 'op:minimum', 'op:add'];
    $url = new moodle_url('/local/' . Constants::PLUGIN_NAME . '/operations.php', $params);
    $buttons = [];
    foreach ($buttonNames as $buttonName) {
        $buttons[] = '<button name="operation" type="submit" value="' . $buttonName . '">' . get_string($buttonName, 'local_gradebook') . '</button>';
//        $buttons[] = '<input class="advanced" type="submit" name="operation" value="' . get_string($buttonName, 'local_gradebook') . '" />';
    }

    return $buttons;
}

function local_gradebook_complete_grade_idnumbers($courseid)
{
    $gradeByCourse = grade_item::fetch_all(['courseid' => $courseid]);
    foreach ($gradeByCourse as $grade) {
        if (empty($grade->idnumber) || null == $grade->idnumber) {
            $grade->add_idnumber('idnum_' . $grade->id);
        }
    }
}


function getListItems(&$gtree, $element, $current_itemid = null, $errors = null)
{
    global $CFG;

    $object = $element['object'];
    $eid = $element['eid'];
    $type = $element['type'];
    $grade_item = $object->get_grade_item();

    $name = $object->get_name();
    $return_string = '';

    //TODO: improve outcome visualisation
    if ($type == 'item' and !empty($object->outcomeid)) {
        $name = $name . ' (' . get_string('outcome', 'grades') . ')';
    }

    $idnumber = $object->get_idnumber();

    // Don't show idnumber or input field for current item if given to function. Highlight the item instead.
    if ($type != 'category') {
        $closingdiv = '';
        if (!empty($errors[$grade_item->id])) {
            $name .= '<div class="error"><span class="error">' . $errors[$grade_item->id] . '</span><br />' . "\n";
            $closingdiv = "</div>\n";
        }
        $name .= '<label class="accesshide" for="id_idnumber_' . $grade_item->id . '">' . get_string('gradeitems', 'grades') . '</label>';
        $name .= '<input type="checkbox" name="grades[]" value="' . $grade_item->id . '">';
        $name .= $closingdiv;
    }

    $icon = $gtree->get_element_icon($element, true);
    $last = '';
    $catcourseitem = ($element['type'] == 'courseitem' or $element['type'] == 'categoryitem');

    if ($type != 'category') {
        $return_string .= '<li class=" list-without-style ' . $type . '">' . $icon . $name . '</li>' . "\n";
    } else {
        $return_string .= '<li class=" list-without-style ' . $type . '">' . $icon . $name . "\n";
        $return_string .= '<ul class="catlevel' . $element['depth'] . '">' . "\n";
        $last = null;
        foreach ($element['children'] as $child_el) {
            $return_string .= getListItems($gtree, $child_el, $current_itemid, $errors);
        }
        if ($last) {
            $return_string .= getListItems($gtree, $last, $current_itemid, $errors);
        }
        $return_string .= '</ul></li>' . "\n";
    }

    return $return_string;
}

/**
 * Method to give a calculation given params.
 * @param string $id Id of item to be placed the calculation.
 * @param string $courseid Course id.
 * @param array $activities with activities to add into operation.
 * @param string $operation with operation to build.
 */
function getCalculationFromParams($id, $courseid, $gradesSelected, $operation)
{
    $operation = ltrim($operation, "op:");
    var_dump($id, $courseid, $gradesSelected, $operation);
}

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

namespace local_gradebook;

/**
 * Class Functions is used to store all internal functions that has a specific use.
 *
 * Example of usage:
 *  $variable = new local_gradebook\Functions();
 *  $return = $variable->methodYouWantToUse($params);
 *
 * @package local_gradebook
 */
class Functions
{
    /**
     * Function to display all single operation buttons.
     * @return array with HTML tag buttons
     */
    function local_gradebook_get_simple_options()
    {
        $buttonNames = ['op:average', 'op:maximum', 'op:minimum', 'op:add'];
        $buttons = [];
        foreach ($buttonNames as $buttonName) {
            $buttons[] = '<button name="operation" type="submit" value="' . $buttonName . '">' . get_string($buttonName, 'local_gradebook') . '</button>';
        }

        return $buttons;
    }

    /**
     * Function to get all idnumbers of a given course.
     * @param int $courseid Course id
     */
    function local_gradebook_complete_grade_idnumbers($courseid)
    {
        $gradeByCourse = $this->getGradesByCourseId($courseid);
        foreach ($gradeByCourse as $grade) {
            if (empty($grade->idnumber) || null == $grade->idnumber) {
                $grade->add_idnumber('idnum_' . $grade->id);
            }
        }
    }

    /**
     * Function that provides all grades given a course id.
     * @param int $courseid
     * @return array
     */
    function getGradesByCourseId($courseid)
    {
        return \grade_item::fetch_all(['courseid' => $courseid]);
    }

    /**
     * Function to build grade tree list in order to select which activities would be on the operation.
     * @codeCoverageIgnore
     * @param \grade_tree $gtree Gtree instance
     * @param $element
     * @param null $current_itemid
     * @param null $errors
     * @return string
     */
    function local_gradebook_get_list_items(&$gtree, $element, $current_itemid = null, $errors = null)
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
            $name .= '<input type="checkbox" name="grades[]" value="' . $grade_item->idnumber . '">';
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
                $return_string .= $this->local_gradebook_get_list_items($gtree, $child_el, $current_itemid, $errors);
            }
            if ($last) {
                $return_string .= $this->local_gradebook_get_list_items($gtree, $last, $current_itemid, $errors);
            }
            $return_string .= '</ul></li>' . "\n";
        }

        return $return_string;
    }

    /**
     * Method to give a calculation given params.
     * @param array $gradesSelected with activities to add into operation.
     * @param string $operation with operation to build.
     * @return string $calculation with
     */
    function local_gradebook_get_calculation_from_params($gradesSelected, $operation)
    {
        $operation = ltrim($operation, "op:");
        $calculation = '=';
        $calculation .= $operation;
        $calculation .= '(';
        $iterator = new \CachingIterator(new \ArrayIterator($gradesSelected));
        foreach ($iterator as $grade) {
            $calculation .= '[[' . $grade . ']]';
            if ($iterator->hasNext()) {
                $calculation .= ';';
            }
        }
        $calculation .= ')';
        return $calculation;
    }
}

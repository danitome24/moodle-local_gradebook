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

class local_gradebook_renderer extends plugin_renderer_base
{

    /**
     * Display grade tree in a checkbox input list
     * @param int $courseid course id
     * @param int $gradeid grade id selected
     * @param array $treeitems grade tree containing all items in grade
     * @return string
     */
    public function gradesInputSelection($courseid, $gradeid, $treeitems)
    {
        $output = html_writer::start_div('row-fluid');
        $output .= html_writer::start_tag('form', ['method' => 'post', 'action' => '']);
        $output .= html_writer::start_div('span4');
        $output .= html_writer::start_div('', array('style="display: none;"'));
        $output .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'courseid', 'value' => $courseid]);
        $output .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $gradeid]);
        $output .= html_writer::end_div();
        $output .= html_writer::tag('h3', get_string('qualifier_elements', 'local_gradebook'));
        $output .= $treeitems;
        $output .= html_writer::end_div();

        return $output;
    }

    /**
     * Start of buttons table
     * @return string
     */
    public function startGradesSimpleOperations()
    {
        $output = html_writer::start_div('span4');
        $output .= html_writer::tag('h3', get_string('operations', 'local_gradebook'));

        return $output;
    }

    /**
     * Display in a table all butons with his own operations
     * @param array $buttons buttons to be displayed on simple operation
     * @return string
     */
    public function operationButtons($buttons)
    {
        $htmlTable = new html_table();
        $htmlTable->attributes['class'] = 'table';
        $htmlTable->id = 'borderless';
        $count = 0;
        $rows = [];
        $row = [];
        foreach ($buttons as $button) {
            if (!fmod($count, 2)) {
                $row[] = $button;
            } else {
                $row[] = $button;
                $rows[] = $row;
                $row = [];
            }
            $count++;
        }
        $htmlTable->data = $rows;
        $output = html_writer::table($htmlTable);

        return $output;
    }

    /**
     * End of buttons table
     * @return string
     */
    public function endGradesSimpleOptions()
    {
        $output = html_writer::end_tag('h3');
        $output .= html_writer::end_div();
        $output .= html_writer::end_tag('form');
        $output .= html_writer::end_div();

        return $output;
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
    function getGradeTreeList(&$gtree, $element, $current_itemid = null, $errors = null)
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
                $return_string .= $this->getGradeTreeList($gtree, $child_el, $current_itemid, $errors);
            }
            if ($last) {
                $return_string .= $this->getGradeTreeList($gtree, $last, $current_itemid, $errors);
            }
            $return_string .= '</ul></li>' . "\n";
        }

        return $return_string;
    }

    /**
     * Function to display all single operation buttons.
     * @return array with HTML tag buttons
     */
    function getSimpleOptionsButtons()
    {
        $buttonNames = ['op:average', 'op:maximum', 'op:minimum', 'op:add'];
        $buttons = [];
        foreach ($buttonNames as $buttonName) {
            $buttons[] = '<button name="operation" type="submit" value="' . $buttonName . '">' . get_string($buttonName, 'local_gradebook') . '</button>';
        }

        return $buttons;
    }
}

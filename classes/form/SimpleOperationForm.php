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

namespace local_gradebook\form;


class SimpleOperationForm extends \moodleform
{
    protected $checkboxElements = [];

    /**
     * @codeCoverageIgnore
     */
    public function definition()
    {
        global $CFG;

        $gtree = $this->_customdata['gtree'];
        $element = $this->_customdata['element'];
        $gradeid = $this->_customdata['gradeid'];
        $id = $this->_customdata['id'];

        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('hidden', 'id', $id);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'gradeid', $gradeid);
        $mform->setType('gradeid', PARAM_INT);

        $mform->addElement('static', 'description',
            '<h3>' . get_string('qualifier_elements', 'local_gradebook') . '</h3>');

        $gradeItems = $this->getGradeItemsList($gtree, $element, $gradeid);
        $checkboxGroup = $this->addToFormGradeItemsList($mform, $gradeItems);


        $mform->addGroup($checkboxGroup, 'grades', '', '</br>');

        $mform->addElement('static', 'description', '<h3>' . get_string('operations', 'local_gradebook'));
        $radioarray = [];
        $radioarray[] = $mform->createElement('radio', 'operation', '', get_string('op:average', 'local_gradebook'), 'op:average');
        $radioarray[] = $mform->createElement('radio', 'operation', '', get_string('op:max', 'local_gradebook'), 'op:max');
        $radioarray[] = $mform->createElement('radio', 'operation', '', get_string('op:min', 'local_gradebook'), 'op:min');
        $radioarray[] = $mform->createElement('radio', 'operation', '', get_string('op:sum', 'local_gradebook'), 'op:sum');
        $mform->addGroup($radioarray, 'radioar', null, array(' '), false);

        $actionButtons = [];
        $backLink = new \moodle_url('/local/gradebook/index.php', ['id' => $id]);
        $actionButtons[] = &$mform->createElement('link', 'cancelbutton', '', $backLink, get_string('cancel'),
            'class="btn btn-default"');
        $questionString = get_string("simple_op_delete", "local_gradebook");
        $actionButtons[] = &$mform->createElement('submit', 'resetbutton', get_string('clear'),
            'data-question="' . $questionString . '" onClick="showConfirmation()"');
        $actionButtons[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $mform->addGroup($actionButtons, 'buttonar', '', array(''), false);
        $mform->closeHeaderBefore('buttonar');
    }

    /**
     * Method to add grade_items to checkbox list.
     * @codeCoverageIgnore
     * @param $mform
     * @param $gradeItems
     * @return array
     */
    protected function &addToFormGradeItemsList($mform, $gradeItems)
    {
        foreach ($gradeItems as $element) {
            if (is_array($element)) {
                $this->addToFormGradeItemsList($mform, $element);
            } else {
                $this->putIntoArray($element);
            }
        }

        return $this->checkboxElements;
    }

    /**
     * Method to put into an array.
     * @codeCoverageIgnore
     * @param $element
     * @return mixed
     */
    protected function &putIntoArray($element)
    {
        $this->checkboxElements[] =& $element;
        return $this->checkboxElements[0];
    }

    /**
     * Method to build grade items list in checkbox.
     * @codeCoverageIgnore
     * @param $gtree
     * @param $element
     * @return array
     */
    protected function getGradeItemsList(&$gtree, $element, $current_itemid)
    {
        global $OUTPUT;

        $object = $element['object'];
        $type = $element['type'];
        $grade_item = $object->get_grade_item();
        $elements = [];
        $form = $this->_form;
        $name = $object->get_name();

        //TODO: improve outcome visualisation
        if ($type == 'item' and !empty($object->outcomeid)) {
            $elements[] = $name . ' (' . get_string('outcome', 'grades') . ')';
        }
        if ($type != 'category' && $type != 'courseitem' && $type != 'categoryitem') {
            $elements[] = $form->createElement('checkbox', $grade_item->idnumber, null,
                $icon = $gtree->get_element_icon($element, true) . $name);
        }
        if ($type == 'category') {
            if ($current_itemid == $grade_item->id) {
                $name = '<b>' . $name . '</b>';
            }
            $elements[] = $form->createElement('static', '', null, $icon = $gtree->get_element_icon($element, true) . $name);
            foreach ($element['children'] as $child_el) {
                $elements[] = $this->getGradeItemsList($gtree, $child_el, $current_itemid);
            }
        }

        return $elements;
    }
}
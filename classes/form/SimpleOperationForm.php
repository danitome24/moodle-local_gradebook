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

    public function definition()
    {
        global $CFG;

        $gtree = $this->_customdata['gtree'];
        $element = $this->_customdata['element'];
        $courseid = $this->_customdata['courseid'];
        $id = $this->_customdata['id'];

        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('hidden', 'courseid', $courseid);
        $mform->setType('courseid', PARAM_INT);
        $mform->addElement('hidden', 'id', $id);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('static', 'description',
            '<h3>' . get_string('qualifier_elements', 'local_gradebook') . '</h3>');

        $gradeItems = $this->getGradeItemsList($gtree, $element, $id);
        $checkboxGroup = $this->addToFormGradeItemsList($mform, $gradeItems);


        $mform->addGroup($checkboxGroup, 'grades', '', '</br>');

        $mform->addElement('html', '<div class="span6">');
        $mform->addElement('static', 'description', '<h3>' . get_string('operations', 'local_gradebook'));
        $radioarray = [];
        $radioarray[] = $mform->createElement('radio', 'operation', '', get_string('op:average', 'local_gradebook'), 'op:average');
        $radioarray[] = $mform->createElement('radio', 'operation', '', get_string('op:maximum', 'local_gradebook'), 'op:maximum');
        $radioarray[] = $mform->createElement('radio', 'operation', '', get_string('op:minimum', 'local_gradebook'), 'op:minimum');
        $radioarray[] = $mform->createElement('radio', 'operation', '', get_string('op:add', 'local_gradebook'), 'op:add');
        $mform->addGroup($radioarray, 'radioar', null, array(' '), false);
        $mform->addElement('html', '</div>');

        $buttonarray = [];
        $buttonarray[] = &$mform->createElement('submit', 'clearbutton', get_string('clear'));
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

    /**
     * Method to add grade_items to checkbox list.
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
            if (is_null($current_itemid) OR $grade_item->id != $current_itemid) {
                $elements[] = $form->createElement('checkbox', $grade_item->idnumber, null, $icon = $gtree->get_element_icon($element, true) . $name);
            } else {
                $icon = new \pix_icon('t/approve', $name);
                $elements = $form->createElement('static', '', $name, $OUTPUT->render($icon) . $name);
            }
        }
        if ($type == 'category') {
            $elements[] = $form->createElement('static', '', null, $icon = $gtree->get_element_icon($element, true) . $name);
            foreach ($element['children'] as $child_el) {
                $elements[] = $this->getGradeItemsList($gtree, $child_el, $current_itemid);
            }
        }

        return $elements;
    }
}
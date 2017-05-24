<?php
// This file is part of Moodle - http://moodle.org/
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
//
// @author Daniel Tome <danieltomefer@gmail.com>.

namespace local_gradebook\form;

use grade_item;
use local_gradebook\Conditional;

class AdvancedOperationForm extends \moodleform
{

    /**
     * Form definition. Abstract method - always override!
     */
    protected function definition() {
        $gradeid = $this->_customdata['gradeid'];
        $id = $this->_customdata['id'];
        $gtree = $this->_customdata['gtree'];
        $element = $this->_customdata['element'];

        $mform = $this->_form; // Don't forget the underscore!
        $mform->updateAttributes(['class' => 'form-inline']);
        $mform->addElement('hidden', 'id', $id);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'gradeid', $gradeid);
        $mform->setType('gradeid', PARAM_INT);

        $gradeselected = \grade_item::fetch(['id' => $gradeid]);
        $a = new \stdClass();
        $a->name = $gradeselected->get_name(true);
        $mform->addElement('static', 'description', '<h3>' . get_string('advanced_operation_page_title', 'local_gradebook') . '</h3>');
        $mform->addElement('static', 'description', get_string('selected_element', 'local_gradebook', $a));

        $gradeitems = $this->get_grade_items_list($gtree, $element, $gradeid);
        $dropdowngroup = $this->add_to_form_grade_items_list($mform, $gradeitems);

        foreach ($dropdowngroup as $dropdownitem) {
            $category = \grade_category::fetch(['id' => $dropdownitem->id_parent]);
            $dropdownelements[$category->get_name(true)][$dropdownitem->id_num] = '[[' .$dropdownitem->id_num . ']] - ' . $dropdownitem->name;
        }
        $mform->addElement('html', '<p>' . get_string('advanced_operation_comparation', 'local_gradebook') . '</p>');
        $mform->addElement('selectgroups', 'grade_condition_1', null, $dropdownelements);
        $mform->addElement('select', 'type', null, Conditional::in_array());
        $mform->addElement('selectgroups', 'grade_condition_2', null, $dropdownelements);

        $mform->addElement('html', '<br><br>');
        $mform->addElement('html', '');
        $mform->addElement('selectgroups', 'positive_result', get_string('advanced_operation_comparation_positive', 'local_gradebook'), $dropdownelements);

        $mform->addElement('html', '<br><br>');

        $mform->addElement('selectgroups', 'negative_result', get_string('advanced_operation_comparation_negative', 'local_gradebook'), $dropdownelements);

        $buttonarray = [];
        $backlink = new \moodle_url('/local/gradebook/index.php', ['id' => $id]);
        $buttonarray[] = &$mform->createElement('link', 'cancelbutton', '', $backlink, get_string('cancel'),
            'class="btn btn-default"');
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);

        $mform->closeHeaderBefore('buttonar');
    }


    /**
     * Method to add grade_items to checkbox list.
     * @codeCoverageIgnore
     * @param $mform
     * @param $gradeitems
     * @return array
     */
    protected function &add_to_form_grade_items_list($mform, $gradeitems) {
        foreach ($gradeitems as $element) {
            if (is_array($element)) {
                $this->add_to_form_grade_items_list($mform, $element);
            } else {
                $this->put_into_array($element);
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
    protected function &put_into_array($element) {
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
    protected function get_grade_items_list(&$gtree, $element, $currentitemid) {
        global $OUTPUT;

        $object = $element['object'];
        $type = $element['type'];
        /** @var grade_item $gradeitem */
        $gradeitem = $object->get_grade_item();
        $elements = [];
        $form = $this->_form;
        $name = $object->get_name();

        if ($type == 'item' and !empty($object->outcomeid)) {
            $elements[] = $name . ' (' . get_string('outcome', 'grades') . ')';
        }
        if ($type != 'category' && $type != 'courseitem' && $type != 'categoryitem') {
            if ($type == 'item' && $currentitemid != $gradeitem->id) {
                $elem = new \stdClass();
                $elem->id_num = $gradeitem->get_idnumber();
                $elem->name = $gradeitem->get_name();
                $elem->id_parent = (int)$gradeitem->get_parent_category()->id;
                $elements[] = $elem;
            }
        }
        if ($type == 'category') {
            if ($currentitemid != $gradeitem->id) {
                $elem = new \stdClass();
                $elem->id_num = $gradeitem->get_idnumber();
                $elem->name = $gradeitem->get_name(true);
                $elem->id_parent = (int)$gradeitem->get_parent_category()->id;
                $elements[] = $elem;
            }
            foreach ($element['children'] as $childel) {
                $elementtoadd = $this->get_grade_items_list($gtree, $childel, $currentitemid);
                if (!empty($elementtoadd)) {
                    $elements[] = $elementtoadd;
                }
            }
        }

        return $elements;
    }
}

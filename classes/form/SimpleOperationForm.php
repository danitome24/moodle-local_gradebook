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
// @author Daniel Tome <danieltomefer@gmail.com>
//

namespace local_gradebook\form;


class SimpleOperationForm extends \moodleform
{
    protected $checkboxelements = [];
    private $gradeid;

    /**
     * @codeCoverageIgnore
     */
    public function definition() {
        global $CFG;

        $gtree = $this->_customdata['gtree'];
        $element = $this->_customdata['element'];
        $gradeid = $this->_customdata['gradeid'];
        $this->gradeid = $gradeid;
        $id = $this->_customdata['id'];

        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('hidden', 'id', $id);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'gradeid', $gradeid);
        $mform->setType('gradeid', PARAM_INT);

        $gradeselected = \grade_item::fetch(['id' => $gradeid]);
        $a = new \stdClass();
        $a->name = $gradeselected->get_name(true);
        $mform->addElement('static', 'description',
            '<h3>' . get_string('qualifier_elements', 'local_gradebook') . '</h3>');
        $mform->addElement('static', 'description', get_string('selected_element', 'local_gradebook', $a));

        $gradeitems = $this->get_grade_items_list($gtree, $element, $gradeid);
        $checkboxgroup = $this->add_to_form_grade_items_list($mform, $gradeitems);

        $mform->addGroup($checkboxgroup, 'grades', '', '</br>');

        $mform->addElement('static', 'description', '<h3>' . get_string('operations', 'local_gradebook'));
        $radioarray = [];
        $radioarray[] = $mform->createElement('radio', 'operation', '', get_string('op:average', 'local_gradebook'), 'op:average');
        $radioarray[] = $mform->createElement('radio', 'operation', '', get_string('op:max', 'local_gradebook'), 'op:max');
        $radioarray[] = $mform->createElement('radio', 'operation', '', get_string('op:min', 'local_gradebook'), 'op:min');
        $radioarray[] = $mform->createElement('radio', 'operation', '', get_string('op:sum', 'local_gradebook'), 'op:sum');
        $mform->addGroup($radioarray, 'radioar', null, array(' '), false);

        // Generate calculation zone
        $mform->addElement('static', 'description', '<h3>' . get_string('generated_calc', 'local_gradebook') . '</h3>');
        $mform->addElement('button', 'generate-calc', 'Generar', 'id="generate-calculation"');
        $mform->addElement('textarea', 'generated-calculation', null, 'wrap="virtual" rows="5" cols="50"');
        $mform->addElement('static', 'calculation-text', null, get_string('generated_calc_text', 'local_gradebook'));

        // Text area with calculation
        $mform->addElement('static', 'description', '<h3>' . get_string('current_calc', 'local_gradebook') . '</h3>');
        $mform->addElement('textarea', 'calculation', null, 'wrap="virtual" rows="5" cols="50"');

        $actionbuttons = [];
        $backlink = new \moodle_url('/local/gradebook/index.php', ['id' => $id]);
        $actionbuttons[] = &$mform->createElement('link', 'cancelbutton', '', $backlink, get_string('cancel'),
            'class="btn btn-default"');
        $questionstring = get_string("simple_op_delete", "local_gradebook");
        $actionbuttons[] = &$mform->createElement('submit', 'resetbutton', get_string('clear'),
            'data-question="' . $questionstring . '" onClick="showConfirmation()"');
        $actionbuttons[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $mform->addGroup($actionbuttons, 'buttonar', '', array(''), false);
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

        return $this->checkboxelements;
    }

    /**
     * Method to put into an array.
     * @codeCoverageIgnore
     * @param $element
     * @return mixed
     */
    protected function &put_into_array($element) {
        $this->checkboxelements[] =& $element;
        return $this->checkboxelements[0];
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
        $gradeitem = $object->get_grade_item();
        $elements = [];
        $form = $this->_form;
        $name = $object->get_name();

        if ($type == 'item' and !empty($object->outcomeid)) {
            $elements[] = $name . ' (' . get_string('outcome', 'grades') . ')';
        }
        if ($type != 'category' && $type != 'courseitem' && $type != 'categoryitem' && $type != 'item') {
            $elements[] = $form->createElement('checkbox', $gradeitem->idnumber, null,
                $icon = $gtree->get_element_icon($element, true) . '[[' . $gradeitem->idnumber . ']] - ' . $name, 'data-id="' . $gradeitem->idnumber . '"');
        }
        if ($type == 'category' || $type == 'item') {
            if ($currentitemid == $gradeitem->id) {
                $name = '<b>' . $name . '</b>';
                $elements[] = $form->createElement('static', '', null, $icon = $gtree->get_element_icon($element, true) . $name);
            } else {
                $elements[] = $form->createElement('checkbox', $gradeitem->idnumber, null,
                    $icon = $gtree->get_element_icon($element, true) . '[[' . $gradeitem->idnumber . ']] - ' . $name, 'data-id="' . $gradeitem->idnumber . '"');
            }
            if (!empty($element['children'])) {
                foreach ($element['children'] as $childel) {
                    $elements[] = $this->get_grade_items_list($gtree, $childel, $currentitemid);
                }
            }
        }

        return $elements;
    }
}
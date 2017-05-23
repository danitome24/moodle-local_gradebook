<?php
/**
 * Created by PhpStorm.
 * User: dtome
 * Date: 6/05/17
 * Time: 10:28
 */

namespace local_gradebook\form;

use grade_item;
use local_gradebook\Conditional;

class AdvancedOperationForm extends \moodleform
{

    /**
     * Form definition. Abstract method - always override!
     */
    protected function definition()
    {
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

        $gradeSelected = \grade_item::fetch(['id' => $gradeid]);
        $a = new \stdClass();
        $a->name = $gradeSelected->get_name(true);
        $mform->addElement('static', 'description', '<h3>' . get_string('advanced_operation_page_title', 'local_gradebook') . '</h3>');
        $mform->addElement('static', 'description', get_string('selected_element', 'local_gradebook', $a));

        $gradeItems = $this->getGradeItemsList($gtree, $element, $gradeid);
        $dropDownGroup = $this->addToFormGradeItemsList($mform, $gradeItems);

        foreach ($dropDownGroup as $dropDownItem) {
            $category = \grade_category::fetch(['id' => $dropDownItem->id_parent]);
            $dropDownElements[$category->get_name(true)][$dropDownItem->id_num] = '[[' .$dropDownItem->id_num . ']] - ' . $dropDownItem->name;
        }
        $mform->addElement('html', '<p>' . get_string('advanced_operation_comparation', 'local_gradebook') . '</p>');
        $mform->addElement('selectgroups', 'grade_condition_1', null, $dropDownElements);
        $mform->addElement('select', 'type', null, Conditional::inArray());
        $mform->addElement('selectgroups', 'grade_condition_2', null, $dropDownElements);

        $mform->addElement('html', '<br><br>');
        $mform->addElement('html', '');
        $mform->addElement('selectgroups', 'positive_result', get_string('advanced_operation_comparation_positive', 'local_gradebook'), $dropDownElements);

        $mform->addElement('html', '<br><br>');

        $mform->addElement('selectgroups', 'negative_result', get_string('advanced_operation_comparation_negative', 'local_gradebook'), $dropDownElements);

        $buttonarray = [];
        $backLink = new \moodle_url('/local/gradebook/index.php', ['id' => $id]);
        $buttonarray[] = &$mform->createElement('link', 'cancelbutton', '', $backLink, get_string('cancel'),
            'class="btn btn-default"');
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);

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
        /** @var grade_item $grade_item */
        $grade_item = $object->get_grade_item();
        $elements = [];
        $form = $this->_form;
        $name = $object->get_name();

        //TODO: improve outcome visualisation
        if ($type == 'item' and !empty($object->outcomeid)) {
            $elements[] = $name . ' (' . get_string('outcome', 'grades') . ')';
        }
        if ($type != 'category' && $type != 'courseitem' && $type != 'categoryitem') {
            if ($type == 'item' && $current_itemid != $grade_item->id) {
                $elem = new \stdClass();
                $elem->id_num = $grade_item->get_idnumber();
                $elem->name = $grade_item->get_name();
                $elem->id_parent = (int)$grade_item->get_parent_category()->id;
                $elements[] = $elem;
            }
        }
        if ($type == 'category') {
            if ($current_itemid != $grade_item->id) {
                $elem = new \stdClass();
                $elem->id_num = $grade_item->get_idnumber();
                $elem->name = $grade_item->get_name(true);
                $elem->id_parent = (int)$grade_item->get_parent_category()->id;
                $elements[] = $elem;
            }
            foreach ($element['children'] as $child_el) {
                $elementToAdd = $this->getGradeItemsList($gtree, $child_el, $current_itemid);
                if (!empty($elementToAdd)) {
                    $elements[] = $elementToAdd;
                }
            }
        }

        return $elements;
    }
}

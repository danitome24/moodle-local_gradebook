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

        $mform = $this->_form; // Don't forget the underscore!
        $mform->updateAttributes(['class' => 'form-inline']);
        $mform->addElement('hidden', 'id', $id);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'gradeid', $gradeid);
        $mform->setType('gradeid', PARAM_INT);

        $gradeSelected = \grade_item::fetch(['id' => $gradeid]);
        $a = new \stdClass();
        $a->name = $gradeSelected->get_name(true);
        $mform->addElement('static', 'description', get_string('selected_element', 'local_gradebook', $a));
        $mform->addElement('static', 'description', '<h3>' . get_string('advanced_operation_page_title', 'local_gradebook'). '</h3>');

        $grades = grade_item::fetch_all(['courseid' => $id, 'itemtype' => 'mod']);
        foreach ($grades as $grade) {
            $gradesToForm[$grade->idnumber] = $grade->itemname;
        }
        $mform->addElement('html', '<p>' . get_string('advanced_operation_comparation', 'local_gradebook') . '</p>');
        $mform->addElement('select', 'grade_condition_1', null, $gradesToForm);
        $mform->addElement('select', 'type', null, Conditional::inArray());
        $mform->addElement('select', 'grade_condition_2', null, $gradesToForm);

        $mform->addElement('html', '<br><br>');
        $mform->addElement('html', '');
        $mform->addElement('select', 'positive_result', get_string('advanced_operation_comparation_positive', 'local_gradebook'), $gradesToForm);

        $mform->addElement('html', '<br><br>');

        $mform->addElement('select', 'negative_result', get_string('advanced_operation_comparation_negative', 'local_gradebook'), $gradesToForm);

        $buttonarray = [];
        $backLink = new \moodle_url('/local/gradebook/index.php', ['id' => $id]);
        $buttonarray[] = &$mform->createElement('link', 'cancelbutton', '', $backLink, get_string('cancel'),
            'class="btn btn-default"');
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);

        $mform->closeHeaderBefore('buttonar');
    }
}

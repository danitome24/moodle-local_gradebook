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

        $grades = grade_item::fetch_all(['courseid' => $id, 'itemtype' => 'mod']);
        foreach ($grades as $grade) {
            $gradesToForm[$grade->idnumber] = $grade->itemname;
        }
//        var_dump($grades);die;
        $mform->addElement('html', '<p>Comparació entre elements de qualificació</p>');
        $mform->addElement('select', 'grade_condition_1', null, $gradesToForm);
        $mform->addElement('select', 'type', null, Conditional::inArray());
        $mform->addElement('select', 'grade_condition_2', null, $gradesToForm);

        $mform->addElement('html', '<br><br>');
        $mform->addElement('html', '');
        $mform->addElement('select', 'positive_result', 'El resultat per la categoria o element en cas de ser comparació <b>positiva</b> serà:', $gradesToForm);

        $mform->addElement('html', '<br><br>');

        $mform->addElement('select', 'negative_result', 'El resultat per la categoria o element en cas de ser comparació <b>negativa</b> serà:', $gradesToForm);

        $buttonarray = [];
        $backLink = new \moodle_url('/local/gradebook/index.php', ['id' => $id]);
        $buttonarray[] = &$mform->createElement('link', 'cancelbutton', '', $backLink, get_string('cancel'),
            'class="btn btn-default"');
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);

        $mform->closeHeaderBefore('buttonar');
    }
}

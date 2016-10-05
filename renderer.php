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

    private $elements = [];

    public function getGradesDemoTree(&$gtree, $moving = false, $gpr)
    {
        $element = $gtree->top_element;
        $object = $element['object'];
        $type = $element['type'];
        $grade_item = $object->get_grade_item();
        $name = $object->get_name();

        $demoTable = new html_table();
        $demoTable->head = ['Activitites', 'Grades'];
        $demoTable->colclasses = ['span4', 'span2'];

        $elements = [];
        $row = new html_table_row();

        $gradeList = $this->getGradeItemsList($gtree, $element);
        foreach ($gradeList as $key => $grade) {
            $cell = new html_table_cell();
            $cell->text = $grade['object']->get_name();

            $cell2 = new html_table_cell();
            if ($grade['type'] == 'category' || $grade['type'] == 'courseitem' || $grade['type'] == 'categoryitem') {
                $cell2->text = html_writer::empty_tag('input',
                    ['type' => 'text', 'class' => 'span2 local-gradebook-demo-autogenerate-inputs', 'readonly' => 'readonly']);
            } else {
                $cell2->text = html_writer::empty_tag('input',
                    ['type' => 'text', 'class' => 'span2 local-gradebook-demo-autogenerate-inputs']);
            }
            $row->cells = [$cell, $cell2];

            $row = new html_table_row();
            $demoTable->data[] = $row;
        }

        return html_writer::table($demoTable);
    }

    protected function getGradeItemsList(&$gtree, $element)
    {
        global $OUTPUT;

        $object = $element['object'];
        $type = $element['type'];
        $grade_item = $object->get_grade_item();
        $name = $object->get_name();
        //TODO: improve outcome visualisation
        if ($type == 'item' and !empty($object->outcomeid)) {
            $this->elements[] = $element;
        }
        if ($type != 'category' && $type != 'categoryitem') {
            $this->elements[] = $element;
        }
        if ($type == 'category') {
            $this->elements[] = $element;
            foreach ($element['children'] as $child_el) {
                $this->getGradeItemsList($gtree, $child_el);
            }
        }

        return $this->elements;
    }

    /**
     * Get autogenerate and calculate buttons on demo.php page
     * @return string
     */
    public function getDemoButtons()
    {
        $output = '';
        $output = html_writer::start_div('row-fluid');
        $output .= html_writer::start_div('span2');
        $output .= html_writer::tag('button', get_string('autogenerate', 'local_gradebook'),
            ['class' => 'btn-warning', 'id' => 'local-gradebook-demo-autogenerate']);
        $output .= html_writer::end_div();
        $output .= html_writer::start_div('pull-right span2');
        $output .= html_writer::tag('button', get_string('calculate', 'local_gradebook'),
            ['class' => 'btn-success', 'id' => 'local-gradebook-demo-calculate']);
        $output .= html_writer::end_div();
        $output .= html_writer::end_div();

        return $output;
    }
}

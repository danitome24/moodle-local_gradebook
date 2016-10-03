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
    public function getGradesDemoTree(&$gtree, $moving, $gpr)
    {
        $demoTable = new html_table();
        $demoTable->head = ['Activitites', 'Grades'];
        $demoTable->colclasses = ['span4', 'span2'];

        $activitats = ['activitat1', 'activitat2', 'activitat3', 'activitat4', 'activitat5', 'activitat6'];
        $row = new html_table_row();
        foreach ($activitats as $key => $activitat) {
            $cell = new html_table_cell();
            $cell->text = $activitat;

            $cell2 = new html_table_cell();
            $cell2->text = html_writer::empty_tag('input',
                ['type' => 'text', 'class' => 'span2 local-gradebook-demo-autogenerate-inputs']);
            $row->cells = [$cell, $cell2];

            $row = new html_table_row();
            $demoTable->data[] = $row;
        }

        return html_writer::table($demoTable);
    }

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

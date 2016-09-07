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
}

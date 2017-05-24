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

defined('MOODLE_INTERNAL') || die;

class local_gradebook_renderer extends plugin_renderer_base
{

    /**
     * Get autogenerate and calculate buttons on demo.php page
     * @return string
     */
    public function get_demo_buttons() {
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

    public function build_parameters_to_send_by_ajax($courseid) {
        $output = html_writer::empty_tag('input',
            ['id' => 'local-demo-sesskey', 'type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
        $output .= html_writer::empty_tag('input',
            ['id' => 'local-demo-courseid', 'type' => 'hidden', 'name' => 'courseid', 'value' => s($courseid)]);
        $output .= html_writer::empty_tag('input',
            ['id' => 'local-demo-timepageload', 'type' => 'hidden', 'name' => 'timepageload', 'value' => time()]);
        $output .= html_writer::empty_tag('input',
            ['id' => 'local-demo-report', 'type' => 'hidden', 'name' => 'report', 'value' => 'grader']);
        $output .= html_writer::empty_tag('input',
            ['id' => 'local-demo-page', 'type' => 'hidden', 'name' => 'page', 'value' => 0]);

        return $output;
    }
}

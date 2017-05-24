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

namespace local_gradebook\grade;


class GradeCalculationFormatter
{
    /**
     * Function to get pretty calculation string
     *
     * @param string $calc
     * @return string
     */
    public static function get_pretty_calculation($calc) {
        $operation = get_string('op:' . self::get_type_operation($calc), 'local_gradebook');
        return $operation . '(' . self::get_elements_in_operation($calc) . ')';
    }
    /**
     * Function to get idnumbers involved in an operation.
     * @param string $calc
     * @return string
     */
    protected static function get_elements_in_operation($calc) {
        $matches = [];
        $regex = '~\[(.*?)\]]~';
        preg_match_all($regex, $calc, $matches);
        $string = '';
        $numberofelements = count($matches[0]);

        foreach ($matches[0] as $element) {
            $elem = ltrim($element, '[[');
            $elem = rtrim($elem, ']]');
            $name = self::get_grade_given_id($elem);
            // Remove all empty spaces
            $string .= trim($name);
            if (--$numberofelements > 0) {
                $string .= ',';
            }
        }

        return $string;
    }

    /**
     * Function to get type of operation (sum, max, min...)
     * @param string $str
     * @return array mixed
     */
    protected static function get_type_operation($str) {
        $matches = [];
        $regex = '~=(.*?)\(~';
        preg_match($regex, $str, $matches);

        return $matches[1];
    }

    /**
     * Function to get a grade given idnumber.
     * @param int $id
     * @return string with name.
     */
    protected static function get_grade_given_id($id) {
        $grade = \grade_item::fetch(['idnumber' => $id]);

        return $grade->get_name();
    }
}

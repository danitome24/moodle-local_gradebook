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

require_once($CFG->libdir . "/externallib.php");

class local_gradebook_external extends \external_api
{
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_demo_calc_parameters()
    {
        return new external_function_parameters(
            ['id' => new external_value(PARAM_INT, 'Course id', VALUE_REQUIRED)]
        );
    }

    public static function get_demo_calc()
    {
        $grade2 = ['id' => 3, 'value' => 8];
        $grade = ['id' => 1, 'value' => 3];
        $grades[] = $grade;
        $grades[] = $grade2;

        return $grades;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_demo_calc_returns()
    {
        return new external_multiple_structure(
            new external_single_structure(
                [
                    'id' => new external_value(PARAM_INT, 'id of the note', VALUE_OPTIONAL),
                    'value' => new external_value(PARAM_INT, 'id of the user the note is about', VALUE_OPTIONAL),
                ]
            ));
    }
}
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
require_once($CFG->dirroot . '/grade/lib.php');
require_once($CFG->dirroot . '/grade/report/grader/lib.php');

class local_gradebook_externallib extends \external_api
{
    /**
     * Returns description of method parameters
     * @codeCoverageIgnore
     * @return external_function_parameters
     */
    public static function get_demo_calc_parameters()
    {
        return new external_function_parameters(
            [
                'sesskey' => new external_value(PARAM_TEXT, 'Session key', VALUE_REQUIRED),
                'id' => new external_value(PARAM_INT, 'Course id', VALUE_REQUIRED),
                'timepageload' => new external_value(PARAM_TEXT, 'Page load time', VALUE_REQUIRED),
                'report' => new external_value(PARAM_TEXT, 'Reporter', VALUE_REQUIRED),
                'page' => new external_value(PARAM_TEXT, 'Page', VALUE_REQUIRED),
                'grades' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'id' => new external_value(PARAM_INT, 'id of course'),
                            'value' => new external_value(PARAM_INT, 'grade'),
                            'type' => new external_value(PARAM_STRINGID, 'type'),
                        ],
                        'Grades', VALUE_REQUIRED)
                ),
            ]
        );
    }

    /**
     *
     * @codeCoverageIgnore
     * @param $sesskey
     * @param $id
     * @param $timepageload
     * @param $report
     * @param $page
     * @param $grades
     *
     * @return array
     *
     */
    public static function get_demo_calc($sesskey, $id, $timepageload, $report, $page, $grades)
    {
        $params = self::validate_parameters(self::get_demo_calc_parameters(),
            [
                'sesskey' => $sesskey,
                'id' => $id,
                'timepageload' => $timepageload,
                'report' => $report,
                'page' => $page,
                'grades' => $grades,
            ]);

        $gradesToCalculate = [];
        foreach ($grades as $id => $grade) {
            $gradesToCalculate[$grade['id']] = $grade['value'];
        }
        try {
            $result = (new local_gradebook_demo_calculator())->calculateCategoryGrades($gradesToCalculate);
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }

        return $result;
    }

    /**
     * Returns description of method result value
     * @codeCoverageIgnore
     * @return external_description
     */
    public static function get_demo_calc_returns()
    {
        return new external_multiple_structure(
            new external_single_structure(
                [
                    'id' => new external_value(PARAM_INT, 'id of the note', VALUE_OPTIONAL),
                    'value' => new external_value(PARAM_FLOAT, 'id of the user the note is about', VALUE_OPTIONAL),
                ]
            ));
    }
}
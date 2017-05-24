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

namespace local_gradebook\grade;

/**
 * Class Grade.
 * @package local_gradebook\grade
 *
 * Example of usage:
 *  $grade = new local_gradebook\grade\Grade();
 *  $grade->whatEverMethod();
 */
class Grade
{
    /**
     * Method to get all idnumbers of a given course.
     * @param int $courseid Course id
     */
    public function complete_grade_idnumbers($courseid) {
        $gradebycourse = $this->get_grades_by_courseid($courseid);
        foreach ($gradebycourse as $grade) {
            if (empty($grade->idnumber) || null == $grade->idnumber) {
                $grade->add_idnumber('idnum_' . $grade->id);
            }
        }
    }

    /**
     * Method that provides all grades given a course id.
     * @codeCoverageIgnore
     * @param int $courseid
     * @return array
     */
    protected function get_grades_by_courseid($courseid) {
        return \grade_item::fetch_all(['courseid' => $courseid]);
    }

    /**
     * Method to give a calculation given params.
     * @param array $gradesselected with activities to add into operation.
     * @param string $operation with operation to build.
     * @return string $calculation with
     */
    public function get_calculation_from_params($gradesselected, $operation) {
        $operation = ltrim($operation, "op:");
        $calculation = '=';
        $calculation .= $operation;
        $calculation .= '(';
        $iterator = new \CachingIterator(new \ArrayIterator($gradesselected));
        foreach ($iterator as $grade) {
            $calculation .= '[[' . $grade['id'] . ']]';
            if ($iterator->hasNext()) {
                $calculation .= (current_language() == 'en') ? ',' : ';';
            }
        }
        $calculation .= ')';
        return $calculation;
    }

    /**
     * Get idnumbers of a calculation.
     * @param $calculation
     * @return array
     */
    public static function get_idnumbers_in_array_from_calculation($calculation) {
        preg_match_all('/\[\[[a-zA-Z0-9_]+\]\]/', $calculation, $idnumbersofgrade);
        $retidsnumbers = [];
        foreach ($idnumbersofgrade[0] as $idnumberofgrade) {
            $idnumber = substr($idnumberofgrade, 2, -2);
            $retidsnumbers[$idnumber] = 1;
        }

        return $retidsnumbers;
    }

    public static function get_operation_from_calculation($calculation) {
        preg_match('/=[a-zA-Z]+\(/', $calculation, $operation);
        $operation = $operation[0];

        return 'op:' . substr($operation, 1, -1);
    }
}

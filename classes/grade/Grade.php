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
    public function completeGradeIdnumbers($courseid)
    {
        $gradeByCourse = $this->getGradesByCourseId($courseid);
        foreach ($gradeByCourse as $grade) {
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
    protected function getGradesByCourseId($courseid)
    {
        return \grade_item::fetch_all(['courseid' => $courseid]);
    }

    /**
     * Method to give a calculation given params.
     * @param array $gradesSelected with activities to add into operation.
     * @param string $operation with operation to build.
     * @return string $calculation with
     */
    public function getCalculationFromParams($gradesSelected, $operation)
    {
        $operation = ltrim($operation, "op:");
        $calculation = '=';
        $calculation .= $operation;
        $calculation .= '(';
        $iterator = new \CachingIterator(new \ArrayIterator($gradesSelected));
        foreach ($iterator as $grade) {
            $calculation .= '[[' . $grade . ']]';
            if ($iterator->hasNext()) {
                $calculation .= ',';
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
    public static function getIdNumbersInArrayFromCalculation($calculation)
    {
        preg_match_all('/\[\[[a-zA-Z0-9_]+\]\]/', $calculation, $idNumbersOfGrade);
        $retIdsNumbers = [];
        foreach ($idNumbersOfGrade[0] as $idNumberOfGrade) {
            $idNumber = substr($idNumberOfGrade, 2, -2);
            $retIdsNumbers[$idNumber] = 1;
        }

        return $retIdsNumbers;
    }

    public static function getOperationFromCalculation($calculation)
    {
        preg_match('/=[a-zA-Z]+\(/', $calculation, $operation);
        $operation = $operation[0];

        return 'op:' . substr($operation, 1, -1);
    }
}

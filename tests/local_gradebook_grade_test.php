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

defined('MOODLE_INTERNAL') || die();

/**
 * Class within phpunit tests
 * @group local_gradebook
 */
class local_gradebook_grade_testcase extends advanced_testcase
{

    public function test_completeIdNumberOfGrade()
    {
        $courseid = 24;
        $idnumber = 'idnum_' . 2709;

        $gradeMock = $this->getMock('grade_item', ['add_idnumber'], [], '', false);
        $gradeMock->id = 2709;
        $gradeMock->expects($this->once())
            ->method('add_idnumber')
            ->with($idnumber);

        $gradeClassMock = $this->getMock('local_gradebook\grade\Grade', ['getGradesByCourseId'], [], '', false);
        $gradeClassMock->expects($this->once())
            ->method('getGradesByCourseId')
            ->will($this->returnValue([$gradeMock]));

        $gradeClassMock->completeGradeIdnumbers($courseid);
    }

    public function test_getCalculationFromParams()
    {
        $idnumberGrades = ['idnum_5', 'idnum_6'];
        $operation = 'op:add';
        $expectedResult = '=add([[idnum_5]],[[idnum_6]])';
        $gradeClass = new local_gradebook\grade\Grade();

        $this->assertEquals($expectedResult,
            $gradeClass->getCalculationFromParams($idnumberGrades, $operation));
    }
}

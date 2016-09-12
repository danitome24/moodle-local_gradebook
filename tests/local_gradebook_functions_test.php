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
class local_gradebook_functions_testcase extends advanced_testcase
{

    public function test_getSimpleOptions()
    {
        $buttonsExpected = [
            '<button name="operation" type="submit" value="op:average">Average</button>',
            '<button name="operation" type="submit" value="op:maximum">Maximum</button>',
            '<button name="operation" type="submit" value="op:minimum">Minimum</button>',
            '<button name="operation" type="submit" value="op:add">Sum</button>',
        ];
        $functionsClass = new local_gradebook\Functions();
        $buttonsObtained = $functionsClass->local_gradebook_get_simple_options();

        $this->assertEquals($buttonsExpected, $buttonsObtained);
    }

    public function test_completeIdNumberOfGrade()
    {
        global $CFG;
        $courseid = 24;
        $idnumber = 'idnum_' . 2709;

        $gradeMock = $this->getMock('grade_item', ['add_idnumber'], [], '', false);
        $gradeMock->id = 2709;
        $gradeMock->expects($this->once())
            ->method('add_idnumber')
            ->with($idnumber);

        $functionsMock = $this->getMock('local_gradebook\Functions', ['getGradesByCourseId'], [], '', false);
        $functionsMock->expects($this->once())
            ->method('getGradesByCourseId')
            ->will($this->returnValue([$gradeMock]));

        $functionsMock->local_gradebook_complete_grade_idnumbers($courseid);
    }

    public function test_getCalculationFromParams()
    {
        $idnumberGrades = ['idnum_5', 'idnum_6'];
        $operation = 'op:add';
        $expectedResult = '=add([[idnum_5]];[[idnum_6]])';
        $functionsClass = new local_gradebook\Functions();

        $this->assertEquals($expectedResult,
            $functionsClass->local_gradebook_get_calculation_from_params($idnumberGrades, $operation));
    }
}

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

defined('MOODLE_INTERNAL') || die();

/**
 * Class within phpunit tests
 * @group local_gradebook
 */
class local_gradebook_grade_testcase extends advanced_testcase
{

    public function test_complete_idnumber_of_grade() {
        $courseid = 24;
        $idnumber = 'idnum_' . 2709;

        $grademock = $this->getMock('grade_item', ['add_idnumber'], [], '', false);
        $grademock->id = 2709;
        $grademock->expects($this->once())->method('add_idnumber')->with($idnumber);

        $gradeclassmock = $this->getMock('local_gradebook\grade\Grade', ['get_grades_by_ourse_id'], [], '', false);
        $gradeclassmock->expects($this->once())->method('get_grades_by_ourse_id')->will($this->returnValue([$grademock]));

        $gradeclassmock->complete_grade_idnumbers($courseid);
    }

    public function test_get_calculation_from_params() {
        $idnumbergrades = [
            [
                'id' => 'idnum_5',
            ],
            [
                'id' => 'idnum_6'
            ]
        ];
        $operation = 'op:add';
        $expectedresult = '=add([[idnum_5]],[[idnum_6]])';
        $gradeclass = new local_gradebook\grade\Grade();

        $this->assertEquals($expectedresult,
            $gradeclass->get_calculation_from_params($idnumbergrades, $operation));
    }
}

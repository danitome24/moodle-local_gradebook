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

namespace local_gradebook;

defined('MOODLE_INTERNAL') || die();

class Conditional
{
    const GREATER_THAN = 1;
    const LESS_THAN = 2;
    const GREATER_OR_EQUALS_THAN = 3;
    const LESS_OR_EQUALS_THAN = 4;
    const EQUALS_THAN = 5;

    public static function in_array() {
        return [
            self::GREATER_OR_EQUALS_THAN => '>=',
            self::LESS_OR_EQUALS_THAN => '<=',
        ];
    }
}

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
$functions = [
    'local_gradebook_get_demo_calc' => [
        'classname' => 'local_gradebook_externallib',
        'methodname' => 'get_demo_calc',
        'classpath' => '/local/gradebook/externallib.php',
        'description' => 'Service to calculate the grades of a course',
        'type' => 'read',
        'ajax' => true,
    ],
];
$services = [
    'Demo_calculation' => [
        'functions' => [
            'local_gradebook_get_demo_calc',
        ],
        'restricted_user' => 0,
        'enabled' => 1,
    ]
];

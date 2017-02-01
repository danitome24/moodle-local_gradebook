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

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

class behat_local_gradebook_generator extends behat_base
{

    /**
     * @When /^I select gradebook on navigation$/
     */
    public function iSelectGradebookOnNavigation()
    {
        $this->execute('behat_navigation::i_navigate_to_node_in', [
            get_string('navbar_link', 'local_gradebook'),
            implode(' > ', [
                get_string('courseadministration', 'moodle'),
            ])
        ]);
    }

    /**
     * @When /^I am on local gradebook home with course "(?P<selected_course>(?:[^"]|\\")*)"$/
     */
    public function iAmOnLocalGradebookHome($course)
    {
        $this->getSession()->visit($this->locate_path('/course/view.php?id=' . $course));
    }
}

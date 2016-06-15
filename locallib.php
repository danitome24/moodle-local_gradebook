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
class local_gradebook_tree extends grade_edit_tree
{
    public function __construct($gtree, $moving, $gpr)
    {

        $this->gtree = $gtree;
        $this->moving = $moving;
        $this->gpr = $gpr;
        $this->deepest_level = $this->get_deepest_level($this->gtree->top_element);

        $this->columns = array(grade_edit_tree_column::factory('name', array('deepest_level' => $this->deepest_level)));

        $this->columns[] = grade_edit_tree_column::factory('weight', array('adv' => 'weight'));
        $this->columns[] = grade_edit_tree_column::factory('range');
        $this->columns[] = grade_edit_tree_column::factory('operation');
        $this->columns[] = grade_edit_tree_column::factory('selected');
        $this->columns[] = grade_edit_tree_column::factory('advanced_actions');

        $this->table = new html_table();
        $this->table->id = "grade_edit_tree_table";
        $this->table->attributes['class'] = 'generaltable simple setup-grades';
        if ($this->moving) {
            $this->table->attributes['class'] .= ' moving';
        }

        foreach ($this->columns as $column) {
            if (!($this->moving && $column->hide_when_moving)) {
                $this->table->head[] = $column->get_header_cell();
            }
        }

        $rowcount = 0;
        $this->table->data = $this->build_html_tree($this->gtree->top_element, true, array(), 0, $rowcount);
    }
}

class grade_edit_tree_column_operation extends grade_edit_tree_column
{
    public function get_header_cell()
    {
        global $OUTPUT;
        $headercell = clone($this->headercell);
        $headercell->text = get_string('apply_operations', 'local_gradebook');
        return $headercell;
    }
}

class grade_edit_tree_column_selected extends grade_edit_tree_column
{
    public function get_header_cell()
    {
        global $OUTPUT;
        $headercell = clone($this->headercell);
        $headercell->text = get_string('selected', 'local_gradebook');
        return $headercell;
    }
}

class grade_edit_tree_column_advanced_actions extends grade_edit_tree_column
{
    public function get_header_cell()
    {
        global $OUTPUT;
        $headercell = clone($this->headercell);
        $headercell->text = get_string('advanced_actions', 'local_gradebook');
        return $headercell;
    }
}

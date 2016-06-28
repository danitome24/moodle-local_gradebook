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
        $headercell = clone($this->headercell);
        $headercell->text = get_string('applied_operations', 'local_gradebook');

        return $headercell;
    }

    public function get_item_cell($item, $params)
    {
        $element = array_shift($params['element']);
        $itemcell = parent::get_item_cell($item, $params);
        if (!empty($element->parent_category)) {
            $itemcell->text = ' - ';
        }

        return $itemcell;
    }
}

class grade_edit_tree_column_selected extends grade_edit_tree_column
{
    public function get_header_cell()
    {
        $headercell = clone($this->headercell);
        $headercell->text = get_string('selected', 'local_gradebook');
        return $headercell;
    }

    public function get_category_cell($category, $levelclass, $params)
    {
        $item = $category->get_grade_item();
        $checkboxname = 'weightoverride_' . $item->id;
        $checkbox = html_writer::empty_tag('input', array('name' => $checkboxname,
            'type' => 'checkbox', 'value' => 1, 'id' => $checkboxname, 'class' => 'weightoverride',
            'checked' => ($item->weightoverride ? 'checked' : null)));
        $categorycell = parent::get_category_cell($category, $levelclass, $params);

        if ($item->is_category_item()) {
            $categorycell->text = $checkbox;
        }

        return $categorycell;
    }

    public function get_item_cell($item, $params)
    {
        global $CFG;

        if (empty($params['element'])) {
            throw new Exception('Array key (element) missing from 2nd param of grade_edit_tree_column_weightorextracredit::get_item_cell($item, $params)');
        }

        $itemcell = parent::get_item_cell($item, $params);
        $checkbox = self::getCheckbox($item, $params);

        if (!in_array($item->itemtype, array('courseitem', 'categoryitem', 'category'))
            && !in_array($item->gradetype, array(GRADE_TYPE_NONE, GRADE_TYPE_TEXT))
            && (!$item->is_outcome_item() || $item->load_parent_category()->aggregateoutcomes)
            && ($item->gradetype != GRADE_TYPE_SCALE || !empty($CFG->grade_includescalesinaggregation))
        ) {
            $itemcell->text = $checkbox;
        }

        return $itemcell;
    }

    static function getCheckbox($item, $params)
    {
        $checkboxname = 'weightoverride_' . $item->id;

        $checkbox = html_writer::empty_tag('input', array('name' => $checkboxname,
            'type' => 'checkbox', 'value' => 1, 'id' => $checkboxname, 'class' => 'weightoverride',
            'checked' => ($item->weightoverride ? 'checked' : null)));

        return $checkbox;
    }
}

class grade_edit_tree_column_advanced_actions extends grade_edit_tree_column
{
    public function get_header_cell()
    {
        $headercell = clone($this->headercell);
        $headercell->text = get_string('advanced_actions', 'local_gradebook');
        return $headercell;
    }

    public function get_item_cell($item, $params)
    {
        global $OUTPUT;

        if (empty($params['actions'])) {
            throw new Exception('Array key (actions) missing from 2nd param of grade_edit_tree_column_actions::get_item_cell($item, $params)');
        }
        $url = new moodle_url('/local/' . Constants::PLUGIN_NAME . '/index.php', ['id' => $item->courseid]);
        $button = new single_button($url, get_string('add', 'local_gradebook'));
        $element = array_shift($params['element']);
        $itemcell = parent::get_item_cell($item, $params);
        if (!empty($element->parent_category)) {
            $itemcell->text = $OUTPUT->render($button);
        }

        return $itemcell;
    }
}

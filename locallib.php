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
require_once $CFG->libdir . '/mathslib.php';
require_once $CFG->dirroot . '/grade/lib.php';


class grade_edit_tree_column_operation extends grade_edit_tree_column
{
    public function get_header_cell()
    {
        $headercell = clone($this->headercell);
        $headercell->text = get_string('applied_operations', 'local_gradebook');

        return $headercell;
    }

    public function get_category_cell($category, $levelclass, $params)
    {
        $item = $category->get_grade_item();
        $categorycell = parent::get_category_cell($category, $levelclass, $params);

        if (!$item->is_category_item()) {
            return $categorycell;
        }

        if (!empty ($item->calculation)) {
            $categorycell->text = get_string($this->getCalculationString($item), 'local_gradebook');

            return $categorycell;
        }

        $categorycell->text = '-';
        return $categorycell;
    }

    protected function getCalculationString($item)
    {
        $calculation = $item->calculation;
        $calculation = calc_formula::localize($calculation);
        $calculation = grade_item::denormalize_formula($calculation, $item->courseid);
        $operation = $this->getInbetweenStrings($calculation);

        return 'op:' . $operation;
    }

    public function getInbetweenStrings($str){
        $matches = [];
        $regex = '~=(.*?)\(~';
        preg_match($regex, $str, $matches);

        $string = ltrim($matches[0], '=');
        $string = rtrim($string, '(');

        return $string;
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

class grade_edit_tree_column_simple_op extends grade_edit_tree_column
{
    public function get_header_cell()
    {
        $headercell = clone($this->headercell);
        $headercell->text = get_string('simple_op', 'local_gradebook');
        return $headercell;
    }

    public function get_item_cell($item, $params)
    {
        if (empty($params['element'])) {
            throw new Exception('Array key (element) missing from 2nd param of grade_edit_tree_column_weightorextracredit::get_item_cell($item, $params)');
        }
        $itemcell = parent::get_item_cell($item, $params);

        if ($item->itemtype === 'mod') {
            return $itemcell;
        }
        $calc = self::getCalcUrl($item);
        $itemcell->text = $calc;


        return $itemcell;
    }

    static function getCalcUrl($item)
    {
        global $OUTPUT;

        $simpleOpUrl = new moodle_url('/local/' . local_gradebook\Constants::PLUGIN_NAME . '/simple_operation.php', ['courseid' => $item->courseid, 'id' => $item->id]);
        $pixelString = '<a href=" ' . $simpleOpUrl . '">';
        $pixIcon = new pix_icon('t/calc', get_string('name'));
        $pixelString .= $OUTPUT->render($pixIcon) . '</a>';

        return $pixelString;
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

    public function get_category_cell($category, $levelclass, $params)
    {
        global $OUTPUT;

        $item = $category->get_grade_item();
        $url = new moodle_url('/local/' . local_gradebook\Constants::PLUGIN_NAME . '/index.php', ['id' => $item->courseid]);
        $button = new single_button($url, get_string('add', 'local_gradebook'));
        $categorycell = parent::get_category_cell($category, $levelclass, $params);

        if ($item->is_category_item()) {
            $categorycell->text = $OUTPUT->render($button);
        }

        return $categorycell;
    }

    public function get_item_cell($item, $params)
    {
        global $OUTPUT;

        if (empty($params['actions'])) {
            throw new Exception('Array key (actions) missing from 2nd param of grade_edit_tree_column_actions::get_item_cell($item, $params)');
        }
        $url = new moodle_url('/local/' . local_gradebook\Constants::PLUGIN_NAME . '/index.php', ['id' => $item->courseid]);
        $button = new single_button($url, get_string('add', 'local_gradebook'));
        $element = array_shift($params['element']);
        $itemcell = parent::get_item_cell($item, $params);
        if (!empty($element->parent_category)) {
            $itemcell->text = $OUTPUT->render($button);
        }

        return $itemcell;
    }
}

class grade_edit_tree_column_weight_local extends grade_edit_tree_column_weight
{
    public function get_header_cell()
    {
        global $OUTPUT;
        $headercell = clone($this->headercell);
        $headercell->text = get_string('weights', 'grades') . $OUTPUT->help_icon('aggregationcoefweight', 'grades');
        return $headercell;
    }

    public function get_category_cell($category, $levelclass, $params)
    {

        $item = $category->get_grade_item();
        $categorycell = parent::get_category_cell($category, $levelclass, $params);
        if ($item->is_course_item()) {
            return '';
        }
        $categorycell->text = $item->aggregationcoef2 * 100.00;
        return $categorycell;
    }

    public function get_item_cell($item, $params)
    {
        if (empty($params['element'])) {
            throw new Exception('Array key (element) missing from 2nd param of grade_edit_tree_column_weightorextracredit::get_item_cell($item, $params)');
        }
        $itemcell = parent::get_item_cell($item, $params);
        $itemcell->text = '&nbsp;';
        $element = array_shift($params['element']);
        if (!empty($element->parent_category)) {
            $itemcell->text = $item->aggregationcoef2 * 100.00;
        }

        return $itemcell;
    }
}

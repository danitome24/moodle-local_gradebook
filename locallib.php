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

/**
 * Class grade_edit_tree_column_operation to display column operations applied in local_gradebook_tree.
 * @codeCoverageIgnore
 */
class grade_edit_tree_column_operation extends grade_edit_tree_column
{
    /**
     * Function to display header.
     * @return html_table_cell
     */
    public function get_header_cell()
    {
        $headercell = clone($this->headercell);
        $headercell->text = get_string('applied_operations', 'local_gradebook');

        return $headercell;
    }

    /**
     * Function to display category cell.
     * @param $category
     * @param $levelclass
     * @param $params
     * @return html_table_cell
     */
    public function get_category_cell($category, $levelclass, $params)
    {
        $item = $category->get_grade_item();
        $categorycell = parent::get_category_cell($category, $levelclass, $params);

        if (!empty ($item->calculation)) {
            $categorycell->text = $this->getCalculationString($item);

            return $categorycell;
        }

        $categorycell->text = '-';
        return $categorycell;
    }

    /**
     * Function to build operation if a calculation is set.
     * @param grade_item $item
     * @return string
     */
    protected function getCalculationString($item)
    {
        $calculation = $item->calculation;
        $calculation = calc_formula::localize($calculation);
        $calculation = grade_item::denormalize_formula($calculation, $item->courseid);
        $operation = get_string('op:' . $this->getTypeOperation($calculation), 'local_gradebook');
        $appliedOperation = $operation . '(' . $this->getElementsInOperation($calculation) . ')';

        return $appliedOperation;
    }

    /**
     * Function to get a grade given idnumber.
     * @param int $id
     * @return string with name.
     */
    protected function getGradeGivenId($id)
    {
        $grade = \grade_item::fetch(['idnumber' => $id]);

        return $grade->get_name();
    }

    /**
     * Function to get type of operation (sum, max, min...)
     * @param string $str
     * @return array mixed
     */
    protected function getTypeOperation($str)
    {
        $matches = [];
        $regex = '~=(.*?)\(~';
        preg_match($regex, $str, $matches);

        return $matches[1];
    }

    /**
     * Function to get idnumbers involved in an operation.
     * @param string $calc
     * @return string
     */
    protected function getElementsInOperation($calc)
    {
        $matches = [];
        $regex = '~\[(.*?)\]]~';
        preg_match_all($regex, $calc, $matches);
        $string = '';
        $numberOfElements = count($matches[0]);

        foreach ($matches[0] as $element) {
            $elem = ltrim($element, '[[');
            $elem = rtrim($elem, ']]');
            $name = $this->getGradeGivenId($elem);
            //Remove all empty spaces
            $string .= trim($name);
            if (--$numberOfElements > 0) {
                $string .= ',';
            }
        }

        return $string;
    }

    /**
     * Function to display on item cell.
     * @param $item
     * @param $params
     * @return html_table_cell
     */
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

/**
 * Class grade_edit_tree_column_simple_op to display column simple operation buttons in local_gradebook_tree.
 */
class grade_edit_tree_column_simple_op extends grade_edit_tree_column
{
    /**
     * Function to display at header cell.
     * @return html_table_cell
     */
    public function get_header_cell()
    {
        $headercell = clone($this->headercell);
        $headercell->text = get_string('simple_op', 'local_gradebook');
        return $headercell;
    }

    /**
     * Function to display in item cell.
     * @param $item
     * @param $params
     * @return html_table_cell
     * @throws Exception
     */
    public function get_item_cell($item, $params)
    {
        if (empty($params['element'])) {
            throw new Exception('Array key (element) missing from 2nd param of grade_edit_tree_column_weightorextracredit::get_item_cell($item, $params)');
        }
        $itemcell = parent::get_item_cell($item, $params);

        if ($item->itemtype === 'mod') {
            return $itemcell;
        }
        $calc = $this->getCalcUrl($item);
        $itemcell->text = $calc;


        return $itemcell;
    }

    /**
     * Function to build an icon with simple operation url link.
     * @param grade_item $item
     * @return string
     */
    protected function getCalcUrl($item)
    {
        global $OUTPUT;

        $simpleOpUrl = new moodle_url('/local/' . local_gradebook\Constants::PLUGIN_NAME . '/simple_operation.php', ['id' => $item->courseid, 'gradeid' => $item->id]);
        $pixelString = '<a href=" ' . $simpleOpUrl . '">';
        $pixIcon = new pix_icon('t/calc', get_string('name'));
        $pixelString .= $OUTPUT->render($pixIcon) . '</a>';

        return $pixelString;
    }
}

/**
 * Class grade_edit_tree_column_advanced_actions to display advanced operations link in local_gradebook_tree.
 */
class grade_edit_tree_column_advanced_actions extends grade_edit_tree_column
{
    /**
     * Function to display on header cell.
     * @return html_table_cell
     */
    public function get_header_cell()
    {
        $headercell = clone($this->headercell);
        $headercell->text = get_string('advanced_actions', 'local_gradebook');
        return $headercell;
    }

    /**
     * Function to display on category cell.
     * @param $category
     * @param $levelclass
     * @param $params
     * @return html_table_cell
     */
    public function get_category_cell($category, $levelclass, $params)
    {
        global $OUTPUT;

        $item = $category->get_grade_item();
        $categorycell = parent::get_category_cell($category, $levelclass, $params);

        $pixelString = $this->getIconLink($item->courseid, $item->id);
        if ($item->is_category_item()) {
            $categorycell->text = $pixelString;
        }

        return $categorycell;
    }

    /**
     * Function to display on item cell.
     * @param $item
     * @param $params
     * @return html_table_cell
     * @throws Exception
     */
    public function get_item_cell($item, $params)
    {
        global $OUTPUT;

        if (empty($params['actions'])) {
            throw new Exception('Array key (actions) missing from 2nd param of grade_edit_tree_column_actions::get_item_cell($item, $params)');
        }
        $element = array_shift($params['element']);
        $itemcell = parent::get_item_cell($item, $params);

        $pixelString = $this->getIconLink($item->courseid, $item->id);
        if (!empty($element->parent_category)) {
            $itemcell->text = $pixelString;
        }

        return $itemcell;
    }

    /**
     * Method to get into advanced operation page.
     * @return string
     */
    protected function getIconLink($courseid, $gradeId)
    {
        global $OUTPUT;

        $url = new moodle_url('/local/' . local_gradebook\Constants::PLUGIN_NAME . '/advanced_operation.php',
            [
                'id' => $courseid,
                'gradeid' => $gradeId,
            ]);
        $pixelString = html_writer::start_tag('a', ['href' => $url]);
        $pixIcon = new pix_icon('t/add', get_string('add'));
        $pixelString .= $OUTPUT->render($pixIcon);
        $pixelString .= html_writer::end_tag('a');

        return $pixelString;
    }
}

/**
 * Class grade_edit_tree_column_weight_local to display at weight column in local_gradebook_tree.
 */
class grade_edit_tree_column_weight_local extends grade_edit_tree_column_weight
{
    /**
     * Function to write on header.
     * @return html_table_cell
     */
    public function get_header_cell()
    {
        global $OUTPUT;
        $headercell = clone($this->headercell);
        $headercell->text = get_string('weights', 'grades') . $OUTPUT->help_icon('aggregationcoefweight', 'grades');
        return $headercell;
    }

    /**
     * Function to write on category cell.
     * @param $category
     * @param $levelclass
     * @param $params
     * @return html_table_cell|string
     */
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

    /**
     * Function to write on item cell.
     * @param $item
     * @param $params
     * @return html_table_cell
     * @throws Exception
     */
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

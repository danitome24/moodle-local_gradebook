<?php
/**
 * Created by PhpStorm.
 * User: dtome
 * Date: 29/04/17
 * Time: 11:34
 */

namespace local_gradebook\grade\tree;


class GradebookDemoTree extends \grade_edit_tree
{

    public function __construct($gtree, $moving, $gpr)
    {
        $this->gtree = $gtree;
        $this->moving = $moving;
        $this->gpr = $gpr;
        $this->deepest_level = $this->get_deepest_level($this->gtree->top_element);

        $this->columns = [\grade_edit_tree_column::factory('name', array('deepest_level' => $this->deepest_level))];
        $this->columns[] = \grade_edit_tree_column::factory('idnumber');
        $this->columns[] = \grade_edit_tree_column::factory('operation');
        $this->columns[] = \grade_edit_tree_column::factory('demo_input');

        $this->table = new \html_table();
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

        $this->table->data = $this->build_html_tree($this->gtree->top_element, true, array(), 0, $rowcount);
    }

}

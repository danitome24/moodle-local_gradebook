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

require_once('../../config.php');
require_once($CFG->dirroot . '/lib/mathslib.php');

defined('MOODLE_INTERNAL') || die();

class local_gradebook_demo_calculator
{

    /**
     * Calculate category grades given grade items and his values.
     *
     * @param array $gradeitems
     *
     * @return array
     * @throws \Exception
     */
    public function calculate_category_grades($gradeitems) {
        $allgrades = [];
        $gradescalculated = [];

        foreach ($gradeitems as $gradeitemid => $value) {
            /** @var \grade_item $grade */
            $grade = \grade_item::fetch(['id' => $gradeitemid]);
            $allgrades['gi' . $grade->id] = (null == $value) ? '' : $value;
        }

        foreach ($gradeitems as $gradeitemid => $value) {
            $grade = \grade_item::fetch(['id' => $gradeitemid]);
            if ($grade->itemtype != 'course' && $grade->itemtype != 'category' && $grade->itemtype !== 'manual') {
                $gradescalculated[] = [
                    'id' => $gradeitemid,
                    'gid' => 'gi' . $gradeitemid,
                    'value' => $value
                ];
                continue;
            }
            if (empty($grade->calculation)) {
                $gradescalculated[] = [
                    'id' => $gradeitemid,
                    'gid' => 'gi' . $gradeitemid,
                    'value' => 0
                ];
                continue;
            }

            $formula = preg_replace('/##(gi\d+)##/', '\1', $grade->calculation);
            $params = $this->set_params_to_formula($formula, $allgrades, $gradescalculated);
            $formula = new \calc_formula($formula, $params);
            $gradescalculated[] = [
                'id' => $gradeitemid,
                'gid' => 'gi' . $gradeitemid,
                'value' => $formula->evaluate()
            ];
            if ($error = $formula->get_error()) {
                throw new \Exception(__CLASS__ . ' error on formula: ' . $error);
            }
        }
        return $gradescalculated;
    }

    /**
     * Set params required in calc_formula
     *
     * @param string $formula
     * @param array $allgrades
     *
     * @return array
     */
    protected function set_params_to_formula($formula, $allgrades, $gradescalculated) {
        $items = preg_match_all('/(gi\d+)/', $formula, $matches);
        $params = [];
        foreach ($matches[0] as $gi) {
            $continue = false;
            foreach ($gradescalculated as $gradecalculated) {
                if ($gradecalculated['gid'] == $gi && !empty($gradecalculated['value'])) {
                    $params[$gi] = $gradecalculated['value'];
                    $continue = true;
                }
            }
            if ($continue) {
                continue;
            }

            $params[$gi] = $allgrades[$gi];
        }

        return $params;
    }
}

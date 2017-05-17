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

require_once $CFG->dirroot . '/lib/mathslib.php';

class local_gradebook_demo_calculator
{

    /**
     * Calculate category grades given grade items and his values.
     *
     * @param array $gradeItems
     *
     * @return array
     * @throws \Exception
     */
    public function calculateCategoryGrades($gradeItems)
    {
        $allGrades = [];
        $gradesCalculated = [];
        $evaluateLater = [];

        foreach ($gradeItems as $gradeItemId => $value) {
            /** @var \grade_item $grade */
            $grade = \grade_item::fetch(['id' => $gradeItemId]);
            $allGrades['gi' . $grade->id] = (null == $value) ? '' : $value;
        }

        foreach ($gradeItems as $gradeItemId => $value) {
            $grade = \grade_item::fetch(['id' => $gradeItemId]);
            if ($grade->itemtype != 'course' && $grade->itemtype != 'category' && $grade->itemtype !== 'manual') {
                $gradesCalculated[] = [
                    'id' => $gradeItemId,
                    'gid' => 'gi' . $gradeItemId,
                    'value' => $value
                ];
                continue;
            }
            if (empty($grade->calculation)) {
                $gradesCalculated[] = [
                    'id' => $gradeItemId,
                    'gid' => 'gi' . $gradeItemId,
                    'value' => 0
                ];
                continue;
            }

            $formula = preg_replace('/##(gi\d+)##/', '\1', $grade->calculation);
            $params = $this->setParamsToFormula($formula, $allGrades, $gradesCalculated);
            $formula = new \calc_formula($formula, $params);
            $gradesCalculated[] = [
                'id' => $gradeItemId,
                'gid' => 'gi' . $gradeItemId,
                'value' => $formula->evaluate()
            ];
            if ($error = $formula->get_error()) {
                throw new \Exception(__CLASS__ . ' error on formula: ' . $error);
            }
        }
        return $gradesCalculated;
    }

    /**
     * Set params required in calc_formula
     *
     * @param string $formula
     * @param array $allGrades
     *
     * @return array
     */
    protected function setParamsToFormula($formula, $allGrades, $gradesCalculated)
    {
        //MODIFY
        $items = preg_match_all('/(gi\d+)/', $formula, $matches);
        $params = [];
        foreach ($matches[0] as $gi) {
            $continue = false;
            foreach ($gradesCalculated as $gradeCalculated) {
                if ($gradeCalculated['gid'] == $gi && !empty($gradeCalculated['value'])) {
                    $params[$gi] = $gradeCalculated['value'];
                    $continue = true;
                }
            }
            if ($continue) {
                continue;
            }

            $params[$gi] = $allGrades[$gi];
        }

        return $params;
    }
}

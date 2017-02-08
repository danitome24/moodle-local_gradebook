<?php

namespace local_gradebook;

class DemoCalculator
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
        foreach ($gradeItems as $gradeItemId => $value) {
            /** @var \grade_item $grade */
            $grade = \grade_item::fetch(['id' => $gradeItemId]);
            $allGrades['gi' . $grade->id] = $value;
            if ($grade->itemtype != 'course') {
                $gradesCalculated[$gradeItemId] = $value;
                continue;
            }
            $formula = preg_replace('/##(gi\d+)##/', '\1', $grade->calculation);
            $params = $this->setParamsToFormula($formula, $allGrades);
            $formula = new \calc_formula($formula);
            $formula->set_params($params);
            $gradesCalculated[$gradeItemId] = $formula->evaluate();
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
    protected function setParamsToFormula($formula, $allGrades)
    {
        $items = explode(',', substr((substr($formula, strpos($formula, '('))), 1, -1));
        $params = [];
        foreach ($items as $gi) {
            $params[$gi] = $allGrades[$gi];
        }

        return $params;
    }
}

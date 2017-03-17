<?php
/**
 * Created by PhpStorm.
 * User: dtome
 * Date: 17/03/17
 * Time: 12:44
 */

namespace local_gradebook\grade;


class GradeCalculationFormatter
{
    /**
     * Function to get pretty calculation string
     *
     * @param string $calc
     * @return string
     */
    public static function getPrettyCalculation($calc)
    {
        $operation = get_string('op:' . self::getTypeOperation($calc), 'local_gradebook');
        return $operation . '(' . self::getElementsInOperation($calc) . ')';
    }
    /**
     * Function to get idnumbers involved in an operation.
     * @param string $calc
     * @return string
     */
    protected static function getElementsInOperation($calc)
    {
        $matches = [];
        $regex = '~\[(.*?)\]]~';
        preg_match_all($regex, $calc, $matches);
        $string = '';
        $numberOfElements = count($matches[0]);

        foreach ($matches[0] as $element) {
            $elem = ltrim($element, '[[');
            $elem = rtrim($elem, ']]');
            $name = self::getGradeGivenId($elem);
            //Remove all empty spaces
            $string .= trim($name);
            if (--$numberOfElements > 0) {
                $string .= ',';
            }
        }

        return $string;
    }

    /**
     * Function to get type of operation (sum, max, min...)
     * @param string $str
     * @return array mixed
     */
    protected static function getTypeOperation($str)
    {
        $matches = [];
        $regex = '~=(.*?)\(~';
        preg_match($regex, $str, $matches);

        return $matches[1];
    }

    /**
     * Function to get a grade given idnumber.
     * @param int $id
     * @return string with name.
     */
    protected static function getGradeGivenId($id)
    {
        $grade = \grade_item::fetch(['idnumber' => $id]);

        return $grade->get_name();
    }
}

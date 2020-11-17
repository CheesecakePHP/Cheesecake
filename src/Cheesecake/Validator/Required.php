<?php


namespace Cheesecake\Validator;

/**
 * Validator to validate if a value is not empty.
 *
 * @package Cheesecake\Validator
 * @author Christian Meyer <ak56Lk@gmx.net>
 * @version 1.0
 * @since 0.1
 */
class Required implements ValidatorInterface
{

    /**
     * Validates the given value if it is not empty.
     * If it is not empty then it returns TRUE otherwise
     * it returns FALSE. Unlike PHP's empty function this
     * validator returns TRUE with a numeric 0.
     *
     * @param $value
     * @return bool
     * @see empty()
     */
    public static function validate($value)
    {
        $return = false;

        if(!empty($value) || is_numeric($value)) {
            $return = true;
        }

        return $return;
    }

}
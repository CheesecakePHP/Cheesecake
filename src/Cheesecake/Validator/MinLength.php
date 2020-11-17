<?php


namespace Cheesecake\Validator;

/**
 * Validator to validate if a value has a minimum length.
 *
 * @package Cheesecake\Validator
 * @author Christian Meyer <ak56Lk@gmx.net>
 * @version 1.0
 * @since 0.1
 */
class MinLength implements ValidatorInterface
{

    /**
     * Validates the given value if it has a given minimum length.
     * If it meets the minimum length then it returns TRUE otherwise
     * it returns FALSE.
     *
     * @param $value
     * @param $minLength
     * @return bool
     */
    public static function validate($value, $minLength)
    {
        $return = false;

        if(mb_strlen($value) >= $minLength) {
            $return = true;
        }

        return $return;
    }

}
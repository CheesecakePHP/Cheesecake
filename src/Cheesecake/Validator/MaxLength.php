<?php


namespace Cheesecake\Validator;

/**
 * Validator to validate if a value has a maximum length.
 *
 * @package Cheesecake\Validator
 * @author Christian Meyer <ak56Lk@gmx.net>
 * @version 1.0
 * @since 0.1
 */
class MaxLength implements ValidatorInterface
{

    /**
     * Validates the given value if it has a given maximum length.
     * If it meets the maximum length then it returns TRUE otherwise
     * it returns FALSE.
     *
     * @param $value
     * @param $minLength
     * @return bool
     */
    public static function validate($value, $maxLength)
    {
        $return = false;

        if(mb_strlen($value) <= $maxLength) {
            $return = true;
        }

        return $return;
    }

}
<?php


namespace Cheesecake\Validator;

/**
 * Validator to validate if a value is a valid email address or not.
 *
 * @package Cheesecake\Validator
 * @author Christian Meyer <ak56Lk@gmx.net>
 * @version 1.0
 * @since 0.1
 */
class Email implements ValidatorInterface
{

    /**
     * Validates if the given value is a valid email address or not.
     * If it is a valid email address it returns TRUE otherwise it
     * returns FALSE.
     *
     * @param $value
     * @return bool
     */
    public static function validate($value)
    {
        $return = false;

        if(filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $return = true;
        }

        return $return;
    }

}
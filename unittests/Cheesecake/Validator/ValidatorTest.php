<?php

namespace Tests;

use Cheesecake\Validator\Email;
use Cheesecake\Validator\MaxLength;
use Cheesecake\Validator\MinLength;
use Cheesecake\Validator\Number;
use Cheesecake\Validator\Required;
use Cheesecake\Validator\Url;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{

    /**
     * @dataProvider requiredProvider
     */
    public function testValidatorRequired($value, $expected)
    {
        $Validator = Required::class;

        self::assertEquals($expected, $Validator::validate($value));
    }

    public function requiredProvider()
    {
        return [
            ['', false],
            [null, false],
            [false, false],
            [true, true],
            ['not empty', true],
            [1, true],
            [0, true],
            ['0', true],
            [0.0, true]
        ];
    }

    /**
     * @dataProvider minLengthProvider
     */
    public function testValidatorMinLength($value, $minLength, $expected)
    {
        $Validator = MinLength::class;

        self::assertEquals($expected, $Validator::validate($value, $minLength));
    }

    public function minLengthProvider()
    {
        return [
            ['', 1, false],
            ['a', 1, true],
            ['ab', 1, true],
            [null, 1, false],
            ['', 0, true],
            ['abc123', 8, false],
            ['abc123', 6, true],
            [false, 1, false],
            [true, 1, true],
            [true, 2, false]
        ];
    }

    /**
     * @dataProvider maxLengthProvider
     */
    public function testValidatorMaxLength($value, $maxLength, $expected)
    {
        $Validator = MaxLength::class;

        self::assertEquals($expected, $Validator::validate($value, $maxLength));
    }

    public function maxLengthProvider()
    {
        return [
            ['', 1, true],
            ['a', 1, true],
            ['ab', 1, false],
            [null, 1, true],
            ['', 0, true],
            ['abc123', 8, true],
            ['abc123', 6, true],
            ['abc123', 5, false],
            [false, 1, true],
            [true, 1, true],
            [true, 2, true]
        ];
    }

    /**
     * @dataProvider emailProvider
     */
    public function testValidatorEmail($value, $expected)
    {
        $Validator = Email::class;

        self::assertEquals($expected, $Validator::validate($value));
    }

    public function emailProvider()
    {
        return [
            ['name@host.tld', true],
            ['name', false],
            ['@host', false],
            ['@host.tld', false],
            ['name@', false],
            [null, false],
            [false, false],
            [true, false],
            [0, false],
            [1, false]
        ];
    }

    /**
     * @dataProvider urlProvider
     */
    public function testValidatorUrl($value, $expected)
    {
        $Validator = Url::class;

        self::assertEquals($expected, $Validator::validate($value));
    }

    public function urlProvider()
    {
        return [
            ['http://domain.in', true],
            ['domain.tld', false],
            ['http://', false],
            ['domain', false],
            ['tld', false],
            ['http://domain.tld/path', true],
            ['protocol://domain.tld', true],
            ['//domain.tld', false],
            ['http://https://domain.tld', true],
            ['http://domain..tld', false],
            ['ftp://username:passwd@domain.tld/path/filename.php?query=value', true]
        ];
    }

    /**
     * @dataProvider numberProvider
     */
    public function testValidatorNumber($value, $expected)
    {
        $Validator = Number::class;

        self::assertEquals($expected, $Validator::validate($value));
    }

    public function numberProvider()
    {
        return [
            [0, true],
            [false, false],
            [true, false],
            [1, true],
            [(35 - 34.99), true],
            [('35' - '34.99'), true],
            [1.1, true],
            ['1,1', false],
            [null, false]
        ];
    }

}

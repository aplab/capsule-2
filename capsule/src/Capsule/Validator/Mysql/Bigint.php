<?php
/**
 * Created by Alexander Polyanin polyanin@gmail.com.
 * User: polyanin
 * Date: 07.11.2015
 * Time: 14:17
 */

namespace Capsule\Validator\Mysql;


use Capsule\Validator\Validator;

/**
 * Class Bigint
 * @package Capsule\Validator\Mysql
 * @property boolean $unsigned
 */
class Bigint extends Validator
{
    /**
     * @var string
     */
    const INVALID_TYPE = 'invalid_type';

    /**
     * @var string
     */
    const INVALID_VALUE = 'invalid_value';

    /**
     * @var string
     */
    const INVALID_VALUE_UNSIGNED = 'invalid_type_unsigned';

    /**
     * @var string
     */
    const OUT_OF_RANGE = 'out_of_range';

    /**
     * @var string
     */
    const OUT_OF_RANGE_UNSIGNED = 'out_of_range_unsigned';

    /**
     * @var string
     */
    const SIGNED_MIN = '-9223372036854775808';

    /**
     * @var string
     */
    const SIGNED_MAX = '9223372036854775807';

    /**
     * @var string
     */
    const UNSIGNED_MAX = '18446744073709551615';


    /**
     * Constructor
     *
     * @param void
     */
    public function __construct()
    {
        parent::__construct();
        $this->unsigned = true;
        $this->messageTemplates = array(
            self::INVALID_TYPE => 'The parameter %name% must be a integer, %type% given.',
            self::INVALID_VALUE => 'The parameter %name% must be a integer, %value% given.',
            self::INVALID_VALUE_UNSIGNED => 'The parameter %name% must be a unsigned integer, %value% given.',
            self::OUT_OF_RANGE => 'The parameter %name% is out of range between ' . self::SIGNED_MIN . ' and ' . self::SIGNED_MAX,
            self::OUT_OF_RANGE_UNSIGNED => 'The parameter %name% is out of range between 0 and ' . self::UNSIGNED_MAX
        );
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        parent::isValid($value);
        if (!is_scalar($this->value)) {
            $this->message(self::INVALID_TYPE);
            return $this->isValid = false;
        }
        $this->value = (string)$this->value;
        $length = strlen(self::UNSIGNED_MAX) + 4;
        if ($this->unsigned) {
            $regexp = '/^-?\\d{1,' . $length . '}$/';
        } else {
            $regexp = '/^\\d+${1,' . $length . '}/';
        }
        if (!preg_match($regexp, $value)) {
            $this->message($this->unsigned ? self::INVALID_VALUE_UNSIGNED : self::INVALID_VALUE);
            return $this->isValid = false;
        }
        if ($this->unsigned) {
            if (1 === bccomp('0', $this->value)) {
                $this->message(self::OUT_OF_RANGE_UNSIGNED);
                return $this->isValid = false;
            }
            if (-1 === bccomp(self::UNSIGNED_MAX, $this->value)) {
                $this->message(self::OUT_OF_RANGE_UNSIGNED);
                return $this->isValid = false;
            }
        } else {
            if (1 === bccomp(self::SIGNED_MIN, $this->value)) {
                $this->message(self::OUT_OF_RANGE);
                return $this->isValid = false;
            }
            if (-1 === bccomp(self::SIGNED_MAX, $this->value)) {
                $this->message(self::OUT_OF_RANGE);
                return $this->isValid = false;
            }
        }
        $this->isValid = true;
        return $this->isValid;
    }

    /**
     * @param boolean $value
     * @param string $name
     */
    public function setUnsigned($value, $name)
    {
        $this->data[$name] = !!$value;
    }
}
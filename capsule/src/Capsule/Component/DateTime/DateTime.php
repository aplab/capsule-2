<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 16.04.2013 14:33:42 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Api
 */

namespace Capsule\Component\DateTime;

use Capsule\Common\Utf8String;
use Capsule\Component\DateTime\Exception;

/**
 * Различные функции для даты и времени.
 * The DATETIME type is used when you need values that contain both date
 * and time information. Retrieves and displays DATETIME
 * values in 'YYYY-MM-DD HH:MM:SS' format. The supported range is
 * '1000-01-01 00:00:00' to '9999-12-31 23:59:59'.
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class DateTime
{
    /**
     * Минимальный год
     *
     * @var int
     */
    const MIN_YEAR = 1000;

    /**
     * Максимальный год
     *
     * @var int
     */
    const MAX_YEAR = 9999;

    /**
     * Год
     *
     * @var int
     */
    protected $year;

    /**
     * Получить год
     *
     * @param void
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Установить год.
     * Если $fitDay = true тогда если день в заданном месяце исходного года
     * отсутствует в этом же месяце устанавливаемого года, будет установлен
     * ближайший имеющийся день.
     *
     * Например 29 февраля 2008 года - исходное
     * Устанавливаем 2010 год. В нем нет 29 февраля. Будет установлено 28 число.
     *
     * В противном случае получаем ошибку Fatal error: February 2010 was limited
     * to 28 days. Current day number 29. First, set the correct day.
     *
     * @param null $value
     * @param boolean $fitDay
     * @return DateTime
     * @throws Exception
     */
    public function setYear($value = null, $fitDay = false)
    {
        if (is_null($value)) {
            $value = $this->getCurrentYear();
        }
        if (!preg_match('/^[[:digit:]]{4}$/', $value)) {
            $msg = 'Year must be positive integer, range was limited from '
                . self::MIN_YEAR . ' to ' . self::MAX_YEAR;
            throw new Exception($msg);
        }
        if ($value < self::MIN_YEAR) {
            $msg = 'Year range was limited from ' . self::MIN_YEAR . ' to ' . self::MAX_YEAR;
            throw new Exception($msg);
        }
        if ($value > self::MAX_YEAR) {
            $msg = 'Year range was limited from ' . self::MIN_YEAR . ' to ' . self::MAX_YEAR;
            throw new Exception($msg);
        }
        $this->year = $value;
        $this->initIsLeapYear();
        $this->setMonth($this->month, $fitDay);
        return $this;
    }

    /**
     * Месяц
     *
     * @var int
     */
    protected $month;

    /**
     * Получить месяц
     *
     * @param void
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Установить месяц.
     * Если $fitDay = true тогда если день в заданном месяце
     * отсутствует в устанавливаемом месяце, будет установлен
     * ближайший имеющийся день.
     *
     * Например 31 января - исходное
     * Устанавливаем февраль. В нем нет 31 числа. Будет установлено 28(29 для
     * високосного года) число.
     *
     * В противном случае получаем ошибку Fatal error: February 2008 was limited
     * to 29 days. Current day number 31. First, set the correct day.
     *
     * @param null $value
     * @param boolean $fitDay
     * @return DateTime
     * @throws Exception
     */
    public function setMonth($value = null, $fitDay = false)
    {
        if (is_null($value)) {
            $value = $this->getCurrentMonth();
        }
        if (!preg_match('/^[[:digit:]]{1,2}$/', $value)) {
            $msg = 'Month must be positive integer, range was limited from 1 to 12';
            throw new Exception($msg);
        }
        if ($value < 1) {
            $msg = 'Month range was limited from 1 to 12';
            throw new Exception($msg);
        }
        if ($value > 12) {
            $msg = 'Month range was limited from 1 to 12';
            throw new Exception($msg);
        }
        $this->month = $value;
        $this->initNumberDaysInMonth();
        $this->setDay($this->day, $fitDay);
        return $this;
    }

    /**
     * День
     *
     * @var int
     */
    protected $day;

    /**
     * Получить день
     *
     * @param void
     * @return int
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Установить день.
     * Если второй параметр истина, то при установке несуществующего дня,
     * например 32 февраля, происходит корректировка дня.
     *
     * @param int $value
     * @param boolean $fit
     * @return DateTime
     * @throws Exception
     */
    public function setDay($value = null, $fit = false)
    {
        if (is_null($value)) {
            $value = $this->getCurrentDay();
        }
        if (!preg_match('/^[[:digit:]]{1,2}$/', $value)) {
            $msg = Utf8String::ucfirst($this->getMonthNameByNumber($this->month))
                . ' ' . $this->year . ' was limited from 1 to '
                . $this->numberDaysInMonth . ' days. Attempt to establish '
                . $value . ' day.';
            throw new Exception($msg);
        }
        if ($value < 1) {
            if ($fit) {
                $value = 1;
            } else {
                $msg = Utf8String::ucfirst($this->getMonthNameByNumber($this->month))
                    . ' ' . $this->year . ' was limited from 1 to '
                    . $this->numberDaysInMonth . ' days. Attempt to establish '
                    . $value . ' day.';
                throw new Exception($msg);
            }
        }
        if ($value > $this->numberDaysInMonth) {
            if ($fit) {
                $value = $this->numberDaysInMonth;
            } else {
                $msg = Utf8String::ucfirst($this->getMonthNameByNumber($this->month))
                    . ' ' . $this->year . ' was limited from 1 to '
                    . $this->numberDaysInMonth . ' days. Attempt to establish '
                    . $value . ' day.';
                throw new Exception($msg);
            }
        }
        $this->day = $value;
        $this->initDayOfWeek();
        $this->initNumberDayOfYear();
        return $this;
    }

    /**
     * Час
     *
     * @var int
     */
    protected $hour;

    /**
     * Получить час
     *
     * @param void
     * @return int
     */
    public function getHour()
    {
        return $this->hour;
    }

    /**
     * Установить час
     *
     * @param int $value
     * @return DateTime
     * @throws Exception
     */
    public function setHour($value = null)
    {
        if (is_null($value)) {
            $value = $this->getCurrentHour();
        }
        if (!preg_match('/^[[:digit:]]{1,2}$/', $value)) {
            $msg = 'Hour must be positive integer, range was limited from 0 to 23';
            throw new Exception($msg);
        }
        if ($value < 0) {
            $msg = 'Hour range was limited from 0 to 23';
            throw new Exception($msg);
        }
        if ($value > 24) {
            $msg = 'Hour range was limited from 0 to 23';
            throw new Exception($msg);
        }
        $this->hour = $value;
        return $this;
    }

    /**
     * Минута
     *
     * @var int
     */
    protected $minute;

    /**
     * Получить минуту
     *
     * @param void
     * @return int
     */
    public function getMinute()
    {
        return $this->minute;
    }

    /**
     * Установить минуты
     *
     * @param int $value
     * @return DateTime
     * @throws Exception
     */
    public function setMinute($value = null)
    {
        if (is_null($value)) {
            $value = $this->getCurrentMinute();
        }
        if (!preg_match('/^[[:digit:]]{1,2}$/', $value)) {
            $msg = 'Minute must be positive integer, range was limited from 0 to 59';
            throw new Exception($msg);
        }
        if ($value < 0) {
            $msg = 'Minute range was limited from 0 to 59';
            throw new Exception($msg);
        }
        if ($value > 59) {
            $msg = 'Minute range was limited from 0 to 59';
            throw new Exception($msg);
        }
        $this->minute = $value;
        return $this;
    }

    /**
     * Секунда
     *
     * @var int
     */
    protected $second;

    /**
     * Получить секунду
     *
     * @param void
     * @return int
     */
    public function getSecond()
    {
        return $this->second;
    }

    /**
     * Установить секунды
     *
     * @param int $value
     * @return DateTime
     * @throws Exception
     */
    public function setSecond($value = null)
    {
        if (is_null($value)) {
            $value = $this->getCurrentSecond();
        }
        if (!preg_match('/^[[:digit:]]{1,2}$/', $value)) {
            $msg = 'Second must be positive integer, range was limited from 0 to 59';
            throw new Exception($msg);
        }
        if ($value < 0) {
            $msg = 'Second range was limited from 0 to 59';
            throw new Exception($msg);
        }
        if ($value > 59) {
            $msg = 'Second range was limited from 0 to 59';
            throw new Exception($msg);
        }
        $this->second = $value;
        return $this;
    }

    /**
     * Високосный год
     *
     * @var boolean
     */
    protected $leapYear;

    /**
     * Инициализация признака високосного года
     *
     * @param void
     * @return void
     */
    protected function initIsLeapYear()
    {
        $this->leapYear = ($this->year % 4 == 0 && $this->year % 100 != 0) ||
        ($this->year % 400 == 0) ? true : false;
    }

    /**
     * Проверка на високосный год
     *
     * @param int
     * @return boolean
     */
    public function isLeapYear()
    {
        return $this->leapYear;
    }

    /**
     * Количество дней в месяце
     *
     * @var int
     */
    protected $numberDaysInMonth;

    /**
     * Получить количество дней в месяцах в виде массива.
     * Ключи - номера месяцев.
     * Значения - количество дней.
     *
     * @param void
     * @return array
     */
    protected function getNumberDaysInMonths()
    {
        return array(
            1 => 31,
            2 => $this->leapYear ? 29 : 28,
            3 => 31,
            4 => 30,
            5 => 31,
            6 => 30,
            7 => 31,
            8 => 31,
            9 => 30,
            10 => 31,
            11 => 30,
            12 => 31);
    }

    /**
     * Инициализация количества дней в месяце
     *
     * @param void
     * @return void
     */
    protected function initNumberDaysInMonth()
    {
        $numberDaysInMonths = $this->getNumberDaysInMonths();
        $this->numberDaysInMonth = $numberDaysInMonths[(int)$this->month];
    }

    /**
     * Получить количество дней в месяце
     *
     * @param void
     * @return int
     */
    public function getNumberDaysInMonth()
    {
        return $this->numberDaysInMonth;
    }

    /**
     * Номер дня в году 1-366
     *
     * @var int
     */
    protected $numberDayOfYear;

    /**
     * Инициализация номера дня в году
     *
     * @param void
     * @return void
     */
    protected function initNumberDayOfYear()
    {
        if ($this->month < 2) {
            $this->numberDayOfYear = $this->day;
        } else {
            $full_months = $this->month - 1;
            $data = $this->getNumberDaysInMonths();
            $this->numberDayOfYear = $this->day + array_sum(array_slice($data, 0, $full_months));
        }
    }

    /**
     * Номер дня недели от 1 до 7
     *
     * @var int
     */
    protected $dayOfWeek;

    /**
     * Инициализация номера дня недели
     *
     * @param void
     * @return void
     */
    protected function initDayOfWeek()
    {
        $month = $this->month;
        $year = $this->year;
        $day = $this->day;
        if ($month < 3) {
            $month += 10;
        } else {
            $month -= 2;
        }
        if ($month > 10) {
            $year--;
        }
        $cent = floor($year / 100);
        $year %= 100;
        $dday = floor(2.6 * $month - 0.2) + $day + $year + floor($year / 4) + floor($cent / 4) - 2 * $cent;
        $dday = floor(($dday + 777) % 7);
        $this->dayOfWeek = intval((($dday == 0) ? 7 : $dday));
    }

    public function getDayOfWeek()
    {
        return $this->dayOfWeek;
    }

    /**
     * Конструктор
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @param int $second
     * @throws Exception
     */
    public function __construct($year = null, $month = null, $day = null, $hour = null, $minute = null, $second = null)
    {
        // Первичная инициализация
        if (is_null($year)) {
            $year = $this->getCurrentYear();
        }
        if (is_null($month)) {
            $month = $this->getCurrentMonth();
        }
        if (is_null($day)) {
            $day = $this->getCurrentDay();
        }
        if (is_null($hour)) {
            $hour = $this->getCurrentHour();
        }
        if (is_null($minute)) {
            $minute = $this->getCurrentMinute();
        }
        if (is_null($second)) {
            $second = $this->getCurrentSecond();
        }
        // Первичная валидация
        // Год
        $year = self::digit($year);
        if (false === $year) {
            $msg = 'Year value must be unsigned integer. ';
            $msg .= 'Year range was limited from ' . self::MIN_YEAR . ' to ' . self::MAX_YEAR;
            throw new Exception($msg);
        }
        if ($year > self::MAX_YEAR) {
            $msg = 'Year range was limited from ' . self::MIN_YEAR . ' to ' . self::MAX_YEAR;
            throw new Exception($msg);
        }
        if ($year < self::MIN_YEAR) {
            $msg = 'Year range was limited from ' . self::MIN_YEAR . ' to ' . self::MAX_YEAR;
            throw new Exception($msg);
        }
        $this->year = $year;
        // Високосный год или нет
        $this->initIsLeapYear();
        // Месяц
        $month = self::digit($month);
        if (false === $month) {
            $msg = 'Month value must be unsigned integer. ';
            $msg .= 'Month range was limited from 1 to 12';
            throw new Exception($msg);
        }
        if ($month > 12) {
            $msg = 'Month range was limited from 1 to 12';
            throw new Exception($msg);
        }
        if ($month < 1) {
            $msg = 'Month range was limited from 1 to 12';
            throw new Exception($msg);
        }
        $this->month = $month;
        // Количество дней в месяце
        $this->initNumberDaysInMonth();
        // День
        $day = self::digit($day);
        if (false === $day) {
            $msg = 'Day value must be unsigned integer. ';
            $msg .= 'Day range was limited from 1 to ' . $this->numberDaysInMonth;
            throw new Exception($msg);
        }
        if ($day > $this->numberDaysInMonth) {
            $msg = 'Day range was limited from 1 to ' . $this->numberDaysInMonth;
            throw new Exception($msg);
        }
        if ($day < 1) {
            $msg = 'Day range was limited from 1 to ' . $this->numberDaysInMonth;
            throw new Exception($msg);
        }
        $this->day = $day;
        // Номер дня в году
        $this->initNumberDayOfYear();
        $this->initDayOfWeek();
        // Час
        $hour = self::digit($hour);
        if (false === $hour) {
            $msg = 'Hour value must be unsigned integer. ';
            $msg .= 'Hour range was limited from 0 to 23';
            throw new Exception($msg);
        }
        if ($hour > 23) {
            $msg = 'Hour range was limited from 0 to 23';
            throw new Exception($msg);
        }
        if ($hour < 0) {
            $msg = 'Hour range was limited from 0 to 23';
            throw new Exception($msg);
        }
        $this->hour = $hour;
        // Минута
        $minute = self::digit($minute);
        if (false === $minute) {
            $msg = 'Minute value must be unsigned integer. ';
            $msg .= 'Minute range was limited from 0 to 59';
            throw new Exception($msg);
        }
        if ($minute > 60) {
            $msg = 'Minute range was limited from 0 to 59';
            throw new Exception($msg);
        }
        if ($minute < 0) {
            $msg = 'Minute range was limited from 0 to 59';
            throw new Exception($msg);
        }
        $this->minute = $minute;
        // Секунда
        $second = self::digit($second);
        if (false === $second) {
            $msg = 'Second value must be unsigned integer.';
            $msg .= 'Second range was limited from 0 to 59';
            throw new Exception($msg);
        }
        if ($second > 60) {
            $msg = 'Second range was limited from 0 to 59';
            throw new Exception($msg);
        }
        if ($second < 0) {
            $msg = 'Second range was limited from 0 to 59';
            throw new Exception($msg);
        }
        $this->second = $second;
    }

    /**
     * Проверка на цифры
     *
     * @param mixed $value
     * @return bool
     * @throws Exception
     */
    public static function digit($value)
    {
        if (!is_scalar($value)) {
            $msg = 'Value must be unsigned int.';
            throw new Exception($msg);
        }
        if (!preg_match('/^[[:digit:]]+$/', $value)) {
            $msg = 'Value must be unsigned int.';
            throw new Exception($msg);
        }
        return $value;
    }

    /**
     * Получить текущий год в виде целого числа
     *
     * @param void
     * @return int
     */
    protected function getCurrentYear()
    {
        return intval(date('Y'));
    }

    /**
     * Получить текущий год в виде целого числа
     *
     * @param void
     * @return int
     */
    protected function getCurrentMonth()
    {
        return intval(date('m'));
    }

    /**
     * Получить текущий год в виде целого числа
     *
     * @param void
     * @return int
     */
    protected function getCurrentDay()
    {
        return intval(date('d'));
    }

    /**
     * Получить текущий год в виде целого числа
     *
     * @param void
     * @return int
     */
    protected function getCurrentHour()
    {
        return intval(date('H'));
    }

    /**
     * Получить текущий год в виде целого числа
     *
     * @param void
     * @return int
     */
    protected function getCurrentMinute()
    {
        return intval(date('i'));
    }

    /**
     * Получить текущий год в виде целого числа
     *
     * @param void
     * @return int
     */
    protected function getCurrentSecond()
    {
        return intval(date('s'));
    }

    /**
     * Получить массив названий дней недели на английском языке.
     * От 0 (воскресенье) до 6 (суббота)
     *
     * @param void
     * @return array
     */
    public static function getWeekDays()
    {
        return array(
            0 => 'sunday',
            1 => 'monday',
            2 => 'tuesday',
            3 => 'wednesday',
            4 => 'thursday',
            5 => 'friday',
            6 => 'saturday');
    }

    /**
     * Получить массив названий дней недели на английском языке.
     * От 1 (понедельник) до 7 (воскресенье).
     * По ISO-8601, первый день недели - понедельник.
     *
     * @param void
     * @return array
     */
    public static function getWeekDaysIso8601()
    {
        return array(
            1 => 'monday',
            2 => 'tuesday',
            3 => 'wednesday',
            4 => 'thursday',
            5 => 'friday',
            6 => 'saturday',
            7 => 'sunday');
    }

    /**
     * Получить массив названий дней недели на английском языке.
     * От 1 (воскресенье) до 7 (суббота).
     * Как в функции DAYOFWEEK() в MySQL.
     *
     * @param void
     * @return array
     */
    public static function getWeekDaysMySQL()
    {
        return array(
            1 => 'sunday',
            2 => 'monday',
            3 => 'tuesday',
            4 => 'wednesday',
            5 => 'thursday',
            6 => 'friday',
            7 => 'saturday');
    }

    /**
     * Получить массив названий месяцев на английском языке.
     *
     * @param void
     * @return array
     */
    public static function getMonths()
    {
        return array(
            1 => 'january',
            2 => 'february',
            3 => 'march',
            4 => 'april',
            5 => 'may',
            6 => 'june',
            7 => 'july',
            8 => 'august',
            9 => 'september',
            10 => 'october',
            11 => 'november',
            12 => 'december');
    }

    /**
     * Получить строковое представление для вставки в поле DATETIME MySQL
     *
     * @param boolean
     * @return string
     */
    public function getMysqlDatetime($delimiter = true)
    {
        return $delimiter ?
            sprintf('%04d-%02d-%02d %02d:%02d:%02d',
                $this->year, $this->month, $this->day,
                $this->hour, $this->minute, $this->second) :
            sprintf('%04d%02d%02d%02d%02d%02d',
                $this->year, $this->month, $this->day,
                $this->hour, $this->minute, $this->second);
    }

    /**
     * Неявное преобразование в строку
     * Хорошо подходит для вывода в админку
     *
     * @param void
     * @return string
     */
    public function __toString()
    {
        return $this->getString();
    }

    /**
     * Явное преобразование в строку
     * Хорошо подходит для вывода в админку
     *
     * @param void
     * @return string
     */
    public function getString()
    {
        return sprintf('%02d.%02d.%04d %02d:%02d:%02d',
            $this->day, $this->month, $this->year,
            $this->hour, $this->minute, $this->second);
    }

    /**
     * Получить название месяца на английском языке по его порядковому номеру.
     * По умолчанию - название текущего месяца объекта.
     *
     * @param int $number
     * @return string
     * @throws Exception
     */
    public function getMonthNameByNumber($number = null)
    {
        if (is_null($number)) {
            $number = $this->month;
        }
        if (!preg_match('/^[[:digit:]]{1,2}$/', $number)) {
            $msg = 'Month must be positive integer, range was limited from 1 to 12';
            throw new Exception($msg);
        }
        $months = self::getMonths();
        if (isset($months[$number])) {
            return $months[$number];
        }
        $msg = 'Undefined month number';
        throw new Exception($msg);
    }

    /**
     * Получить первый день месяца
     *
     * @param void
     * @return DateTime
     */
    public function getFirstDayOfMonth()
    {
        $o = clone($this);
        $o->setDay(1);
        return $o;
    }

    /**
     * Получить последний день месяца
     *
     * @param void
     * @return DateTime
     */
    public function getLatestDayOfMonth()
    {
        $o = clone($this);
        $o->setDay($this->getNumberDaysInMonth());
        return $o;
    }

    /**
     * Получить месяц в виде массива объектов.
     * Если $additionalDays = true то первая и последняя недели дополнятся до 7
     * дней днями из предыдущего и следующего месяцев соответственно.
     *
     * @param boolean $additionalDays
     * @return array
     */
    public function getMonthCalendar($additionalDays = false)
    {
        $year = $this->getYear();
        $month = $this->getMonth();
        $first_day = $this->getFirstDayOfMonth();
        $latest_day = $this->getLatestDayOfMonth();
        $first_day_number = $first_day->getDay();
        $latest_day_number = $latest_day->getDay();
        $first_day_week_day_number = $first_day->getDayOfWeek();
        $latest_day_week_day_number = $latest_day->getDayOfWeek();
        #$week_day_number = $first_day_week_day_number;
        $class = __CLASS__;
        $ret = array();
        if ($additionalDays) {
            if ($first_day_week_day_number > 1) {
                $counter = $first_day_week_day_number;
                $previous_day = $first_day->getPreviousDay();
                while ($counter > 1) {
                    $ret[] = $previous_day;
                    $previous_day = $previous_day->getPreviousDay();
                    $counter--;
                }
                $ret = array_reverse($ret);
            }
        }
        for ($i = $first_day_number; $i <= $latest_day_number; $i++) {
            $ret[] = new $class($year, $month, $i, 0, 0, 0);
        }
        if ($additionalDays) {
            $next_day = $latest_day->getNextDay();
            for ($i = $latest_day_week_day_number + 1; $i <= 7; $i++) {
                $ret[] = $next_day;
                $next_day = $next_day->getNextDay();
            }
        }
        return $ret;
    }

    /**
     * Получить месяц в виде двумерного массива объектов.
     * Если $additionalDays = true то первая и последняя недели дополнятся до 7
     * дней днями из предыдущего и следующего месяцев соответственно.
     *
     * @param boolean $additionalDays
     * @return array
     */
    public function getMonthCalendar2D($additionalDays = false)
    {
        $source_data = $this->getMonthCalendar($additionalDays);
        $week_number = 0;
        $ret = array();
        foreach ($source_data as $data_item) {
            $week_day_number = $data_item->getDayOfWeek();
            $ret[$week_number][$week_day_number] = $data_item;
            if ($week_day_number >= 7) {
                $week_number++;
            }
        }
        return $ret;
    }

    /**
     * Создать элемент из данных, полученных из поля DATETIME субд MySQL
     *
     * @param string
     * @return DateTime|null
     */
    public static function createElementFromMysqlDatetime($value)
    {
        $matches = array();
        if (preg_match('/^([[:digit:]]{4})\D?([[:digit:]]{2})\D?([[:digit:]]{2})\D?([[:digit:]]{2})\D?([[:digit:]]{2})\D?([[:digit:]]{2})/', $value, $matches)) {
            $class = __CLASS__;
            return new $class($matches[1], $matches[2], $matches[3], $matches[4], $matches[5], $matches[6]);
        } else {
            return null;
        }
    }

    /**
     * Создать элемент из данных, полученных из поля DATE субд MySQL
     *
     * @param string
     * @return DateTime|null
     */
    public static function createElementFromMysqlDate($value)
    {
        $matches = array();
        if (preg_match('/^([[:digit:]]{4})\D?([[:digit:]]{2})\D?([[:digit:]]{2})/', $value, $matches)) {
            $class = __CLASS__;
            return new $class($matches[1], $matches[2], $matches[3]);
        } else {
            return null;
        }
    }

    /**
     * Создать элемент из данных, полученных из строки вида
     * DD-MM-YYYY или DDMMYYYY
     *
     * @param string
     * @return DateTime|null
     */
    public static function createElementFromDate($value)
    {
        $matches = array();
        if (preg_match('/^([[:digit:]]{2})\D?([[:digit:]]{2})\D?([[:digit:]]{4})/', $value, $matches)) {
            $class = __CLASS__;
            return new $class($matches[3], $matches[2], $matches[1]);
        } else {
            return null;
        }
    }

    /**
     * Создать элемент из данных, полученных из строки вида
     * DD-MM-YYYY HH:II:SS или DDMMYYYYHHIISS
     *
     * @param string
     * @return DateTime|null
     */
    public static function createElementFromString($value)
    {
        $matches = array();
        if (preg_match('/^([[:digit:]]{2})\D?([[:digit:]]{2})\D?([[:digit:]]{4})\D?([[:digit:]]{2})\D?([[:digit:]]{2})\D?([[:digit:]]{2})/', $value, $matches)) {
            $class = __CLASS__;
            return new $class($matches[3], $matches[2], $matches[1], $matches[4], $matches[5], $matches[6]);
        } else {
            return null;
        }
    }

    /**
     * Создать элемент из данных, полученных из строки
     *
     * @param string
     * @return DateTime|null
     */
    public static function tryCreateElementFromString($value)
    {
        $matches = array();
        $regexp = '/^\D*([[:digit:]]{4})\D*([[:digit:]]{2})\D*([[:digit:]]{2})\D*([[:digit:]]{2})\D*([[:digit:]]{2})\D*([[:digit:]]{2})\D*/';
        if (preg_match($regexp, $value, $matches)) {
            return new self($matches[1], $matches[2], $matches[3], $matches[4], $matches[5], $matches[6]);
        }
        $regexp = '/^\D*([[:digit:]]{2})\D*([[:digit:]]{2})\D*([[:digit:]]{4})\D*([[:digit:]]{2})\D*([[:digit:]]{2})\D*([[:digit:]]{2})\D*/';
        if (preg_match($regexp, $value, $matches)) {
            return new self($matches[3], $matches[2], $matches[1], $matches[4], $matches[5], $matches[6]);
        }
        return null;
    }

    public static function createElementFromDatetime($value)
    {
        return self::tryCreateElementFromString($value);
    }

    /**
     * Создать элемент из данных, полученных из поля DATETIME субд MySQL
     *
     * @param string
     * @return DateTime|null
     */
    public static function createElementFromUnixTimestamp($value = null)
    {
        $value = self::digit($value);
        $class = __CLASS__;
        if (false === $value) {
            $value = time();
            return new $class(
                date('Y', $value),
                date('m', $value),
                date('d', $value),
                date('H', $value),
                date('i', $value),
                date('s', $value));
        }
        return new $class(
            date('Y', $value),
            date('m', $value),
            date('d', $value),
            date('H', $value),
            date('i', $value),
            date('s', $value));
    }

    /**
     * Получить дату (без времени), подготовленную к сохранению в поле с типом
     * DATE субд MySQL
     *
     * @param void
     * @return string
     */
    public function getMysqlDate()
    {
        return sprintf('%04d-%02d-%02d',
            $this->year, $this->month, $this->day);
    }

    /**
     * Получить дату (без времени), подготовленную к сохранению в поле с типом
     * DATE субд MySQL
     *
     * @param void
     * @return string
     */
    public function getMysqlTime()
    {
        return sprintf('%02d:%02d:%02d',
            $this->hour, $this->minute, $this->second);
    }

    /**
     * Получить дату (без времени), подготовленную к выводу
     *
     * @param void
     * @return string
     */
    public function getDate($delimiter = null)
    {
        if (is_null($delimiter)) {
            $delimiter = '-';
        }
        return sprintf('%02d' . $delimiter . '%02d' . $delimiter . '%04d',
            $this->day, $this->month, $this->year);
    }

    /**
     * Получить год
     *
     * @param void
     * @return string
     */
    public function getMysqlYear()
    {
        return sprintf('%04d', $this->year);
    }

    /**
     * Получить предыдущий день
     *
     * @param void
     * @return DateTime
     */
    public function getPreviousDay()
    {
        $day = $this->getDay();
        $month = $this->getMonth();
        $year = $this->getYear();
        if ($day > 1) {
            $ret = clone($this);
            $ret->setDay($day - 1);
            return $ret;
        }
        if ($month > 1) {
            $ret = clone($this);
            $ret->setMonth($month - 1);
            return $ret->getLatestDayOfMonth();
        }
        $month = 12;
        $year--;
        $class = __CLASS__;
        /**
         * @var static $o
         */
        $o = new $class($year, $month);
        return $o->getLatestDayOfMonth();
    }

    /**
     * Получить следующий день
     *
     * @param void
     * @return DateTime
     */
    public function getNextDay()
    {
        $day = $this->getDay();
        $month = $this->getMonth();
        $year = $this->getYear();
        $latest_day = $this->getLatestDayOfMonth()->getDay();
        if ($day < $latest_day) {
            $ret = clone($this);
            $ret->setDay($day + 1);
            return $ret;
        }
        if ($month < 12) {
            $ret = clone($this);
            $ret->setDay(1);
            $ret->setMonth($month + 1);
            return $ret;
        }
        $month = 1;
        $year++;
        $class = __CLASS__;
        $o = new $class($year, $month, 1);
        return $o;
    }

    /**
     * Получить предыдущий месяц
     * Если fit = true то происходит корректировка дня в случае, если
     * установленный день не существует в месяце результата.
     * Например 30 февраля
     *
     * @param bool $fit
     * @return DateTime
     * @throws Exception
     */
    public function getPreviousMonth($fit = true)
    {
        $day = $this->getDay();
        $month = $this->getMonth();
        $year = $this->getYear();
        $month = $month - 1;
        if ($month < 1) {
            $month = 12;
            $year = $year - 1;
            if ($year < self::MIN_YEAR) {
                $msg = 'Year range was limited from ' . self::MIN_YEAR . ' to ' . self::MAX_YEAR;
                throw new Exception($msg);
            }
        }
        $ret = clone($this);
        $ret->setYear($year, $fit);
        $ret->setMonth($month, $fit);
        $ret->setDay($day, $fit);
        return $ret;
    }

    /**
     * Получить следующий месяц
     * Если fit = true то происходит корректировка дня в случае, если
     * установленный день не существует в месяце результата.
     * Например 30 февраля
     *
     * @param bool $fit
     * @return DateTime
     * @throws Exception
     */
    public function getNextMonth($fit = true)
    {
        $day = $this->getDay();
        $month = $this->getMonth();
        $year = $this->getYear();
        $month = $month + 1;
        if ($month > 12) {
            $month = 1;
            $year = $year + 1;
            if ($year > self::MAX_YEAR) {
                $msg = 'Year range was limited from ' . self::MIN_YEAR . ' to ' . self::MAX_YEAR;
                throw new Exception($msg);
            }
        }
        $ret = clone($this);
        $ret->setYear($year, $fit);
        $ret->setMonth($month, $fit);
        $ret->setDay($day, $fit);
        return $ret;
    }

    /**
     * Получить предыдущий год
     * Если fit = true то происходит корректировка дня в случае, если
     * установленный день не существует в месяце результата.
     * Например 30 февраля
     *
     * @param bool $fit
     * @return DateTime
     * @throws Exception
     */
    public function getPreviousYear($fit = true)
    {
        $year = $this->getYear();
        $year = $year - 1;
        if ($year < self::MIN_YEAR) {
            $msg = 'Year range was limited from ' . self::MIN_YEAR . ' to ' . self::MAX_YEAR;
            throw new Exception($msg);
        }
        $ret = clone($this);
        $ret->setYear($year, $fit);
        return $ret;
    }

    /**
     * Получить следующий год
     * Если fit = true то происходит корректировка дня в случае, если
     * установленный день не существует в месяце результата.
     * Например 30 февраля
     *
     * @param bool $fit
     * @return DateTime
     * @throws Exception
     */
    public function getNextYear($fit = true)
    {
        $year = $this->getYear();
        $year = $year + 1;
        if ($year > self::MAX_YEAR) {
            $msg = 'Year range was limited from ' . self::MIN_YEAR . ' to ' . self::MAX_YEAR;
            throw new Exception($msg);
        }
        $ret = clone($this);
        $ret->setYear($year, $fit);
        return $ret;
    }

    const COMPARE_MODE_GREATER = true;
    const COMPARE_MODE_SMALLER = false;

    /**
     * Сравнить значения. Внутренняя функция, вызывается более мелкими с различными параметрами.
     *
     * @param DateTime $value
     * @param boolean $compare_mode
     * @param boolean $or_equal
     * @param boolean $compare_date
     * @param boolean $compare_time
     * @return boolean
     */
    protected function compare(DateTime $value, $compare_mode, $or_equal, $compare_date, $compare_time)
    {
        if ($compare_mode) {
            if ($compare_date) {
                if ($this->getYear() > $value->getYear()) {
                    return true;
                }
                if ($this->getYear() < $value->getYear()) {
                    return false;
                }
                if ($this->getMonth() > $value->getMonth()) {
                    return true;
                }
                if ($this->getMonth() < $value->getMonth()) {
                    return false;
                }
                if ($this->getDay() > $value->getDay()) {
                    return true;
                }
                if ($this->getDay() < $value->getDay()) {
                    return false;
                }
            }
            if ($compare_time) {
                if ($this->getHour() > $value->getHour()) {
                    return true;
                }
                if ($this->getHour() < $value->getHour()) {
                    return false;
                }
                if ($this->getMinute() > $value->getMinute()) {
                    return true;
                }
                if ($this->getMinute() < $value->getMinute()) {
                    return false;
                }
                if ($this->getSecond() > $value->getSecond()) {
                    return true;
                }
                if ($this->getSecond() < $value->getSecond()) {
                    return false;
                }
            }
            return $or_equal ? true : false;
        } else {
            if ($compare_date) {
                if ($this->getYear() < $value->getYear()) {
                    return true;
                }
                if ($this->getYear() > $value->getYear()) {
                    return false;
                }
                if ($this->getMonth() < $value->getMonth()) {
                    return true;
                }
                if ($this->getMonth() > $value->getMonth()) {
                    return false;
                }
                if ($this->getDay() < $value->getDay()) {
                    return true;
                }
                if ($this->getDay() > $value->getDay()) {
                    return false;
                }
            }
            if ($compare_time) {
                if ($this->getHour() < $value->getHour()) {
                    return true;
                }
                if ($this->getHour() > $value->getHour()) {
                    return false;
                }
                if ($this->getMinute() < $value->getMinute()) {
                    return true;
                }
                if ($this->getMinute() > $value->getMinute()) {
                    return false;
                }
                if ($this->getSecond() < $value->getSecond()) {
                    return true;
                }
                if ($this->getSecond() > $value->getSecond()) {
                    return false;
                }
            }
            return $or_equal ? true : false;
        }
    }

    /**
     * Сравнить значения.
     * Принимает в качестве параметра объект DateTime.
     * Если значение больше, чем у параметра, то возвращает true,
     * если значение меньше или равно значению параметра, то возвращает false.
     *
     * @param DateTime $value
     * @return boolean
     */
    public function greaterThan(DateTime $value)
    {
        return $this->compare($value, self::COMPARE_MODE_GREATER, false, true, true);
    }

    /**
     * Сравнить значения.
     * Принимает в качестве параметра объект DateTime.
     * Если значение меньше, чем у параметра, то возвращает true,
     * если значение больше или равно значению параметра, то возвращает false.
     *
     * @param DateTime $value
     * @return boolean
     */
    public function smallerThan(DateTime $value)
    {
        return $this->compare($value, self::COMPARE_MODE_SMALLER, false, true, true);
    }

    /**
     * Сравнить значения.
     * Принимает в качестве параметра объект DateTime.
     * Если значение больше либо равно значению параметра, то возвращает true,
     * если значение меньше значения параметра, то возвращает false.
     *
     * @param DateTime $value
     * @return boolean
     */
    public function greaterOrEqual(DateTime $value)
    {
        return $this->compare($value, self::COMPARE_MODE_GREATER, true, true, true);
    }

    /**
     * Сравнить значения.
     * Принимает в качестве параметра объект DateTime.
     * Если значение меньше либо равно значению параметра, то возвращает true,
     * если значение больше значения параметра, то возвращает false.
     *
     * @param DateTime $value
     * @return boolean
     */
    public function smallerOrEqual(DateTime $value)
    {
        return $this->compare($value, self::COMPARE_MODE_SMALLER, true, true, true);
    }

    /**
     * Сравнить значение с текущей датой и временем.
     * Если значение меньше либо равно значению параметра, то возвращает true,
     * если значение больше значения параметра, то возвращает false.
     *
     * @param void
     * @return boolean
     */
    public function smallerOrEqualNow()
    {
        $value = new DateTime();
        return $this->compare($value, self::COMPARE_MODE_SMALLER, true, true, true);
    }

    /**
     * Сравнить значение с текущей датой и временем.
     * Если значение больше либо равно значению параметра, то возвращает true,
     * если значение больше значения параметра, то возвращает false.
     *
     * @param void
     * @return boolean
     */
    public function greaterOrEqualNow()
    {
        $value = new DateTime();
        return $this->compare($value, self::COMPARE_MODE_GREATER, true, true, true);
    }

    /**
     * Сравнить значения даты, не учитывая время.
     * Принимает в качестве параметра объект DateTime.
     * Если значение больше либо равно значению параметра, то возвращает true,
     * если значение меньше значения параметра, то возвращает false.
     *
     * @param DateTime $value
     * @return boolean
     */
    public function dateGreaterOrEqual(DateTime $value)
    {
        return $this->compare($value, self::COMPARE_MODE_GREATER, true, true, false);
    }

    /**
     * Сравнить значения даты, не учитывая время.
     * Принимает в качестве параметра объект DateTime.
     * Если значение меньше либо равно значению параметра, то возвращает true,
     * если значение больше значения параметра, то возвращает false.
     *
     * @param DateTime $value
     * @return boolean
     */
    public function dateSmallerOrEqual(DateTime $value)
    {
        return $this->compare($value, self::COMPARE_MODE_SMALLER, true, true, false);
    }

    /**
     * Сравнить значения даты, не учитывая время.
     * Принимает в качестве параметра объект DateTime.
     * Если значение больше, чем у параметра, то возвращает true,
     * если значение меньше или равно значению параметра, то возвращает false.
     *
     * @param DateTime $value
     * @return boolean
     */
    public function dateGreaterThan(DateTime $value)
    {
        return $this->compare($value, self::COMPARE_MODE_GREATER, false, true, false);
    }

    /**
     * Сравнить значения даты, не учитывая время.
     * Принимает в качестве параметра объект DateTime.
     * Если значение меньше, чем у параметра, то возвращает true,
     * если значение больше или равно значению параметра, то возвращает false.
     *
     * @param DateTime $value
     * @return boolean
     */
    public function dateSmallerThan(DateTime $value)
    {
        return $this->compare($value, self::COMPARE_MODE_SMALLER, false, true, false);
    }

    /**
     * Сравнить значения даты, не учитывая время.
     * Принимает в качестве параметра объект DateTime.
     * Если значение равно значению параметра, то возвращает true.
     *
     * @param DateTime $value
     * @return boolean
     */
    public function dateEqual(DateTime $value)
    {
        if ($this->getYear() === $value->getYear() &&
            $this->getMonth() === $value->getMonth() &&
            $this->getDay() === $value->getDay()
        ) {
            return true;
        }
        return false;
    }

    /**
     * Сравнить значения.
     * Принимает в качестве параметра объект DateTime.
     * Если значение равно значению параметра, то возвращает true.
     *
     * @param DateTime $value
     * @return boolean
     */
    public function equal(DateTime $value)
    {
        if ($this->dateEqual($value) &&
            $this->getHour() === $value->getHour() &&
            $this->getMinute() === $value->getMinute() &&
            $this->getSecond() === $value->getSecond()
        ) {
            return true;
        }
        return false;
    }

    /**
     * Получить предыдущую неделю
     *
     * @param void
     * @return DateTime
     */
    public function getPreviousWeek()
    {
        $day = $this->getDay();
        $month = $this->getMonth();
        $year = $this->getYear();
        if ($day > 7) {
            $ret = clone($this);
            $ret->setDay($day - 7);
            return $ret;
        }
        if ($month > 1) {
            $ret = clone($this);
            $ret->setMonth($month - 1);
            $latest_day = $ret->getLatestDayOfMonth()->getDay();
            $ret->setDay($latest_day - (7 - $day));
            return $ret;
        }
        $month = 12;
        $year--;
        $class = __CLASS__;
        /**
         * @var static $ret
         */
        $ret = new $class($year, $month);
        $latest_day = $ret->getLatestDayOfMonth()->getDay();
        $ret->setDay($latest_day - (7 - $day));
        return $ret;
    }

    /**
     * Получить первый день предыдущей недели
     *
     * @param void
     * @return DateTime
     */
    public function getFirstDayOfPreviousWeek()
    {
        return $this->getPreviousWeek()->getFirstDayOfWeek();
    }

    /**
     * Получить следующую неделю
     *
     * @param void
     * @return DateTime
     */
    public function getNextWeek()
    {
        $day = $this->getDay();
        $month = $this->getMonth();
        $year = $this->getYear();
        $latest_day = $this->getLatestDayOfMonth()->getDay();
        if ($day <= ($latest_day - 7)) {
            $ret = clone($this);
            $ret->setDay($day + 7);
            return $ret;
        }
        if ($month < 12) {
            $ret = clone($this);
            $ret->setDay();
            $ret->setMonth($month + 1);
            $ret->setDay($day + 7 - $latest_day);
            return $ret;
        }
        $month = 1;
        $year++;
        $class = __CLASS__;
        /**
         * @var static $ret
         */
        $ret = new $class($year, $month);
        $ret->setDay($day + 7 - $latest_day);
        return $ret;
    }

    /**
     * Получить первый день недели (понедельник)
     *
     * @param void
     * @return DateTime
     */
    public function getFirstDayOfWeek()
    {
        $day = $this->getDay();
        $month = $this->getMonth();
        $year = $this->getYear();
        $diff = $this->getDayOfWeek();
        $diff--;
        if ($day > $diff) {
            $ret = clone($this);
            $ret->setDay($day - $diff);
            return $ret;
        }
        if ($month > 1) {
            $ret = clone($this);
            $ret->setMonth($month - 1);
            $latest_day = $ret->getLatestDayOfMonth()->getDay();
            $ret->setDay($latest_day - ($diff - $day));
            return $ret;
        }
        $month = 12;
        $year--;
        $class = __CLASS__;
        /**
         * @var static $ret
         */
        $ret = new $class($year, $month);
        $latest_day = $ret->getLatestDayOfMonth()->getDay();
        $ret->setDay($latest_day - ($diff - $day));
        return $ret;
    }

    /**
     * Получить последний день недели (воскресенье)
     *
     * @param void
     * @return DateTime
     */
    public function getLatestDayOfWeek()
    {
        $day = $this->getDay();
        $month = $this->getMonth();
        $year = $this->getYear();
        $diff = 7 - $this->getDayOfWeek();
        $latest_day = $this->getLatestDayOfMonth()->getDay();
        if ($day <= ($latest_day - $diff)) {
            $ret = clone($this);
            $ret->setDay($day + $diff);
            return $ret;
        }
        if ($month < 12) {
            $ret = clone($this);
            $ret->setDay();
            $ret->setMonth($month + 1);
            $ret->setDay($day + $diff - $latest_day);
            return $ret;
        }
        $month = 1;
        $year++;
        $class = __CLASS__;
        /**
         * @var static $ret
         */
        $ret = new $class($year, $month);
        $ret->setDay($day + $diff - $latest_day);
        return $ret;
    }

    /**
     * Получить дату как часть ссылки в виде YYYY/MM/DD
     *
     * @param void
     * @return string
     */
    public function getDateAsLinkPart()
    {
        return sprintf('%04d/%02d/%02d',
            $this->year, $this->month, $this->day);
    }

    /**
     * Возвращает название дня недели на русском языке
     *
     * @param void
     * @return string
     */
    public function getWeekDayNameRussian()
    {
        $data = $this->getWeekDaysArrayRussian();
        return $data[$this->getDayOfWeek()];
    }

    public function getWeekDaysArrayRussian()
    {
        return array(
            0 => 'воскресенье',
            1 => 'понедельник',
            2 => 'вторник',
            3 => 'среда',
            4 => 'четверг',
            5 => 'пятница',
            6 => 'суббота',
            7 => 'воскресенье');
    }
}
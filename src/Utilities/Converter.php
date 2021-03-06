<?php
declare (strict_types=1);

namespace Project\Utilities;

/**
 * Class Converter
 * @package Project\Utilities
 */
class Converter
{
    protected const GERMAN_WEEKDAYS = [
        'long' => [
            0 => 'Sonntag',
            1 => 'Montag',
            2 => 'Dienstag',
            3 => 'Mittwoch',
            4 => 'Donnerstag',
            5 => 'Freitag',
            6 => 'Samstag'
        ],
        'short' => [
            0 => 'So',
            1 => 'Mo',
            2 => 'Di',
            3 => 'Mi',
            4 => 'Do',
            5 => 'Fr',
            6 => 'Sa'
        ]
    ];

    /**
     * @param int $day
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function convertIntToWeekday(int $day): string
    {
        if ($day < 0 && $day > 6) {
            throw new \InvalidArgumentException('Der Wochentag liegt außerhalb des Bereiches.');
        }

        return self::GERMAN_WEEKDAYS['long'][$day];
    }

    /**
     * @param int $day
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function convertIntToWeekdayShort(int $day): string
    {
        if ($day < 0 && $day > 6) {
            throw new \InvalidArgumentException('Der Wochentag liegt außerhalb des Bereiches.');
        }

        return self::GERMAN_WEEKDAYS['short'][$day];
    }

    /**
     * @param int $seconds
     *
     * @return string
     */
    public static function convertDiff(int $seconds): string
    {
        // Hour
        $hour = floor($seconds / 3600);
        if ($hour < 10) {
            $hour = '0' . $hour;
        }

        // Minute
        $minute = floor(($seconds % 3600) / 60);
        if ($minute < 10) {
            $minute = '0' . $minute;
        }

        // Second
        $second = $seconds - ($hour * 3600 + $minute * 60);
        if ($second < 10) {
            $second = '0' . $second;
        }

        return $hour . ':' . $minute . ':' . $second;
    }
}
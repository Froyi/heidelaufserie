<?php declare(strict_types=1);

namespace Project\Module\Competition;

use Project\Module\GenericValueObject\Date;
use Project\Module\GenericValueObject\Id;
use Project\Module\GenericValueObject\Title;


/**
 * Class CompetitionFactory
 * @package Project\Module\Competition
 */
class CompetitionFactory
{
    /**
     * @param $object
     *
     * @return null|Competition
     */
    public function getCompetitionByObject($object): ?Competition
    {
        try {
            if ($this->checkCompetitionProperties($object) === false) {
                return null;
            }

            if (empty($object->competitionId) === true) {
                $competitionId = Id::generateId();
            } else {
                $competitionId = Id::fromString($object->competitionId);
            }

            /** @var Date $date */
            $date = Date::fromValue($object->date);
            $competitionNumber = (int)$object->competitionNumber;

            return new Competition($competitionId, $date, $competitionNumber);
        } catch (\InvalidArgumentException $exception) {
            return null;
        }
    }

    /**
     * @param $object
     *
     * @return null|CompetitionDay
     */
    public function getCompetitionDayByObject($object): ?CompetitionDay
    {
        try {
            if ($this->checkCompetitionDayProperties($object) === false) {
                return null;
            }

            if (empty($object->competitionDayId) === true) {
                $competitionDayId = Id::generateId();
            } else {
                $competitionDayId = Id::fromString($object->competitionDayId);
            }

            /** @var Date $date */
            $date = Date::fromValue($object->date);
            $title = Title::fromString($object->title);

            return new CompetitionDay($competitionDayId, $title, $date);
        } catch (\InvalidArgumentException $exception) {
            return null;
        }
    }

    /**
     * @param $properties
     *
     * @return bool
     */
    protected function checkCompetitionProperties($properties): bool
    {
        return empty($properties->competitionNumber) !== true && empty($properties->date) !== true;
    }

    /**
     * @param $properties
     *
     * @return bool
     */
    protected function checkCompetitionDayProperties($properties): bool
    {
        return empty($properties->date) !== true && empty($properties->title) !== true;
    }
}
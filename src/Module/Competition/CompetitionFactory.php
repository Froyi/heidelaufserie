<?php declare(strict_types=1);

namespace Project\Module\Competition;

use Project\Module\CompetitionResults\Round;
use Project\Module\GenericValueObject\Date;
use Project\Module\GenericValueObject\Datetime;
use Project\Module\GenericValueObject\Distance;
use Project\Module\GenericValueObject\Id;
use Project\Module\GenericValueObject\Name;
use Project\Module\GenericValueObject\Title;


/**
 * Class CompetitionFactory
 * @package Project\Module\Competition
 */
class CompetitionFactory
{
    public function getCompetitionTypeByObject($object): ?CompetitionType
    {
        try {
            $competitionTypeId = (int)$object->competitionTypeId;
            $competitionName = Name::fromString($object->competitionName);
            $distance = Distance::fromValue($object->distance);
            $rounds = Round::fromValue($object->rounds);
            $standardSet = (bool)$object->standardSet;
            $startTimeGroup = StartTimeGroup::fromValue($object->startTimeGroup);

            return new CompetitionType($competitionTypeId, $competitionName, $distance, $rounds, $standardSet, $startTimeGroup);

        } catch (\InvalidArgumentException $exception) {
            return null;
        }
    }

    /**
     * @param $object
     * @param CompetitionType $competitionType
     *
     * @return null|Competition
     */
    public function getCompetitionByObject(\stdClass $object, CompetitionType $competitionType): ?Competition
    {
        try {
            if (empty($object->competitionId) === true) {
                $competitionId = Id::generateId();
            } else {
                $competitionId = Id::fromString($object->competitionId);
            }

            /** @var Date $date */
            $date = Date::fromValue($object->date);

            /** @var Title $title */
            $title = Title::fromString($object->title);

            /** @var Datetime $startTime */
            $startTime = Datetime::fromValue($object->startTime);

            return new Competition($competitionId, $competitionType, $title, $date, $startTime);
        } catch (\InvalidArgumentException $exception) {
            return null;
        }
    }
}
<?php
declare (strict_types=1);


namespace Project\Module\CompetitionResults;

use Project\Module\GenericValueObject\Id;

class CompetitionResultsFactory
{
    public function getCompetitionResultsByObject($object): ?CompetitionResults
    {
        try {
            if (empty($object->competitionResultsId) === true) {
                $competitionResultsId = Id::generateId();
            } else {
                $competitionResultsId = Id::fromString($object->competitionResultsId);
            }
            $competitionDataId = Id::fromString($object->competitionDataId);
            $runnerId = Id::fromString($object->runnerId);

            $competitionResults = new CompetitionResults($competitionResultsId, $competitionDataId, $runnerId);

            if (empty($object->timeOverall) === false) {
                $timeOverall = TimeOverall::fromValue($object->timeOverall);

                $competitionResults->setTimeOverall($timeOverall);
            }

            if (empty($object->points) === false) {
                $points = Points::fromValue($object->points);

                $competitionResults->setPoints($points);
            }

            if (empty($object->firstRound) === false) {
                $firstRound = Round::fromValue($object->firstRound);

                $competitionResults->setFirstRound($firstRound);
            }

            if (empty($object->secondRound) === false) {
                $secondRound = Round::fromValue($object->SecondRound);

                $competitionResults->setSecondRound($secondRound);
            }

            if (empty($object->thirdRound) === false) {
                $thirdRound = Round::fromValue($object->thirdRound);

                $competitionResults->setThirdRound($thirdRound);
            }

            return $competitionResults;

        } catch (\InvalidArgumentException $exception) {
            return null;
        }
    }


}
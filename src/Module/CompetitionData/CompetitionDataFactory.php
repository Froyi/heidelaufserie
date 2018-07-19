<?php
declare(strict_types=1);

namespace Project\Module\CompetitionData;

use Project\Module\Competition\Competition;
use Project\Module\GenericValueObject\Id;

/**
 * Class CompetitionDataFactory
 * @package Project\Module\CompetitionData
 */
class CompetitionDataFactory
{
    /**
     * @param $competitionDataData
     * @param Competition $competition
     * @param array $transponderData
     *
     * @return null|CompetitionData
     */
    public function getCompetitionDataByObject($competitionDataData, Competition $competition, array $transponderData): ?CompetitionData
    {
        if (\is_array($competitionDataData) === true) {
            $competitionDataData = (object)$competitionDataData;
        }

        try {
            if (empty($competitionDataData->competitionDataId) === true) {
                $competitionDataId = Id::generateId();
            } else {
                $competitionDataId = Id::fromString($competitionDataData->competitionId);
            }

            $competitionId = $competition->getCompetitionId();
            $date = $competition->getDate();
            $runnerId = Id::fromString($competitionDataData->runnerId);
            $startNumber = StartNumber::fromValue($competitionDataData->startNumber);
            $transponderNumber = TransponderNumber::fromValue($transponderData[$startNumber->getStartNumber()]['transponderNumber']);

            $competitionData = new CompetitionData($competitionDataId, $competitionId, $runnerId, $date, $startNumber, $transponderNumber);

            if (empty($competitionDataData->club) === false) {
                $club = Club::fromString($competitionDataData->club);
                $competitionData->setClub($club);
            }

            return $competitionData;
        } catch (\InvalidArgumentException $exception) {
            return null;
        }
    }
}
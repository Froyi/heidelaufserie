<?php
declare(strict_types=1);

namespace Project\Module\CompetitionResults;

/**
 * Class CompetitionResultsViewHelper
 * @package Project\Module\CompetitionResults
 */
class CompetitionResultsViewHelper
{
    /**
     * @param array $competitionResultsArray
     *
     * @return array
     */
    public function sortCompetitionResultsByCompetitionTypeAndPoints(array $competitionResultsArray): array
    {
        $results = [];


        /** @var CompetitionResults $competitionResults */
        foreach ($competitionResultsArray as $competitionResults) {
            if ($competitionResults->getCompetitionData() !== null && $competitionResults->getCompetitionData()->getCompetition() !== null) {
                $results[$competitionResults->getCompetitionData()->getCompetition()->getCompetitionType()->getCompetitionTypeId()->getCompetitionTypeId()][] = $competitionResults;
            }
        }

        foreach ($results as $competitionTypeId => $result) {
            usort($result, [$this, 'sortByPoints']);

            $results[$competitionTypeId] = $result;
        }

        return $results;
    }

    /**
     * @param CompetitionResults $competitionResults
     * @param CompetitionResults $competitionResults2
     *
     * @return int
     */
    protected function sortByPoints(CompetitionResults $competitionResults, CompetitionResults $competitionResults2): int
    {
        if ($competitionResults->getPoints() === null) {
            return 1;
        }

        if ($competitionResults2->getPoints() === null) {
            return -1;
        }

        if ($competitionResults->getPoints()->getPoints() === $competitionResults2->getPoints()->getPoints()) {
            return 0;
        }

        return ($competitionResults->getPoints()->getPoints() < $competitionResults2->getPoints()->getPoints()) ? 1 : -1;
    }
}
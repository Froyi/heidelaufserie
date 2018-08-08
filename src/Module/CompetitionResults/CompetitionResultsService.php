<?php
declare (strict_types=1);


namespace Project\Module\CompetitionResults;

use Project\Module\Competition\Competition;
use Project\Module\CompetitionData\CompetitionData;
use Project\Module\Database\Database;
use Project\Module\GenericValueObject\Id;

class CompetitionResultsService
{
    /** @var  CompetitionResultsFactory $competitionResultsFactory */
    protected $competitionResultsFactory;

    /** @var  CompetitionResultsRepository $competitionResultsRepository */
    protected $competitionResultsRepository;

    /**
     * CompetitionResultsService constructor.
     *
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->competitionResultsFactory = new CompetitionResultsFactory();
        $this->competitionResultsRepository = new CompetitionResultsRepository($database);
    }

    /**
     * @param Id $competitionDataId
     *
     * @return null|CompetitionResults
     */
    public function getCompetitionResultsByCompetitionDataId(Id $competitionDataId): ?CompetitionResults
    {
        $competitionResultsData = $this->competitionResultsRepository->getCompetitionResultsByCompetitionDataId($competitionDataId);
        if (empty($competitionResultsData) === true) {
            return null;
        }

        return $this->competitionResultsFactory->getCompetitionResultsByObject($competitionResultsData);
    }

    /**
     * @param $competitionResultsData
     * @param CompetitionData $competitionData
     *
     * @return null|CompetitionResults
     */
    public function getCompetitionResultsByUploadData($competitionResultsData, CompetitionData $competitionData): ?CompetitionResults
    {
        $competitionResultsData = (object)$competitionResultsData;

        if (empty($competitionResultsData->competitionDataId) === true) {
            $competitionResultsData->competitionDataId = $competitionData->getCompetitionDataId()->toString();
            $competitionResultsData->runnerId = $competitionData->getRunnerId()->toString();
        }

        return $this->competitionResultsFactory->getCompetitionResultsByObject($competitionResultsData);
    }

    /**
     * @param CompetitionResults $competitionResults
     *
     * @return bool
     */
    public function saveCompetitionResults(CompetitionResults $competitionResults): bool
    {
        return $this->competitionResultsRepository->saveCompetitionResults($competitionResults);
    }

    /**
     * @param CompetitionResults $competitionResults
     *
     * @return bool
     */
    public function provePoints(CompetitionResults $competitionResults, Competition $competition): bool
    {
        $resultPoints = $this->getPointsByResult($competitionResults, $competition);

        if ($resultPoints === null && $competitionResults->getPoints() === null) {
            return true;
        }

        if ($resultPoints === null || $competitionResults->getPoints() === null) {
            return false;
        }

        return ($resultPoints->getPoints() === $competitionResults->getPoints()->getPoints());
    }

    /**
     * @param CompetitionResults $competitionResults
     * @param Competition $competition
     *
     * @return null|Points
     */
    public function getPointsByResult(CompetitionResults $competitionResults, Competition $competition): ?Points
    {
        if ($competitionResults->getTimeOverall() === null || $competitionResults->getRoundsRun() === null) {
            return null;
        }

        return Points::fromTimeAndRounds($competitionResults->getTimeOverall(), $competitionResults->getRoundsRun(), $competition->getCompetitionType()->getCompetitionTypeId());
    }
}
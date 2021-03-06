<?php
declare (strict_types=1);


namespace Project\Module\CompetitionResults;

use Project\Module\Competition\Competition;
use Project\Module\Competition\CompetitionService;
use Project\Module\CompetitionData\CompetitionData;
use Project\Module\CompetitionData\CompetitionDataService;
use Project\Module\Database\Database;
use Project\Module\FinishMeasure\FinishMeasureService;
use Project\Module\GenericValueObject\Date;
use Project\Module\GenericValueObject\Id;
use Project\Module\Runner\RunnerService;

class CompetitionResultsService
{
    /** @var  CompetitionResultsFactory $competitionResultsFactory */
    protected $competitionResultsFactory;

    /** @var  CompetitionResultsRepository $competitionResultsRepository */
    protected $competitionResultsRepository;

    /** @var array $wrongData */
    protected $wrongData = [];

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
     * @param Date $date
     * @param CompetitionDataService $competitionDataService
     * @param FinishMeasureService $finishMeasureService
     * @param CompetitionService $competitionService
     *
     * @return array
     */
    public function generateCompetitionResultsAfterCompetitionEnd(Date $date, CompetitionDataService $competitionDataService, FinishMeasureService $finishMeasureService, CompetitionService $competitionService): array
    {
        $competitionResultsArray = [];
        $competitionResultData = new \stdClass();
        $competitionDatas = $competitionDataService->getCompetitionDataByDate($date, null, null, $competitionService, $finishMeasureService);

        /** @var CompetitionData $singleCompetitionData */
        foreach ($competitionDatas as $singleCompetitionData) {
            if ($singleCompetitionData->getCompetition() === null || $singleCompetitionData->isRunValid() === false) {
                $this->wrongData[] = $singleCompetitionData;
                continue;
            }

            $roundTimes = $singleCompetitionData->getRoundTimes(true);

            $competitionResultData->competitionDataId = $singleCompetitionData->getCompetitionDataId()->toString();
            $competitionResultData->runnerId = $singleCompetitionData->getRunnerId()->toString();

            foreach ($roundTimes as $roundNumber => $roundTime) {
                if ($roundNumber === 1) {
                    $competitionResultData->firstRound = $roundTime['round'];
                }

                if ($roundNumber === 2) {
                    $competitionResultData->secondRound = $roundTime['round'];
                }

                if ($roundNumber === 3) {
                    $competitionResultData->thirdRound = $roundTime['round'];
                }
            }

            $competitionResultData->timeOverall = $singleCompetitionData->getLastTimeOverall(true);

            $competitionType = $singleCompetitionData->getCompetition()->getCompetitionType();
            $rounds = $competitionType->getRounds();
            $competitionTypeId = $competitionType->getCompetitionTypeId();
            $timeOverall = TimeOverall::fromValue($competitionResultData->timeOverall);
            $points = Points::fromTimeAndRounds($timeOverall, $rounds, $competitionTypeId);

            if ($points === null) {
                $this->wrongData[] = $singleCompetitionData;
                continue;
            }
            $competitionResultData->points = $points->getPoints();

            $competitionResults = $this->competitionResultsFactory->getCompetitionResultsByObject($competitionResultData);

            if ($competitionResults !== null) {
                $competitionResultsArray[] = $competitionResults;
            }
        }

        return $competitionResultsArray;
    }

    /**
     * @param array $competitionResultsArray
     *
     * @return bool
     */
    public function saveAllCompetitionResults(array $competitionResultsArray): bool
    {
        return $this->competitionResultsRepository->saveAllCompetitionResults($competitionResultsArray);
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
     * @param array $competitionResultsArray
     *
     * @return bool
     */
    public function updateAllCompetitionResults(array $competitionResultsArray): bool
    {
        return $this->competitionResultsRepository->updateAllCompetitionResults($competitionResultsArray);
    }

    /**
     * @param Id $competitionDataId
     *
     * @return bool
     */
    public function deleteCompetitionResultsByCompetitionDataId(Id $competitionDataId): bool
    {
        if ($this->getCompetitionResultsByCompetitionDataId($competitionDataId) === null) {
            return true;
        }
        
        return $this->competitionResultsRepository->deleteCompetitionResultsByCompetitionDataId($competitionDataId);
    }

    /**
     * @param Id $competitionResultsId
     *
     * @return bool
     */
    public function deleteCompetitionResultsByCompetitionResultsId(Id $competitionResultsId): bool
    {
        return $this->competitionResultsRepository->deleteCompetitionResultsByCompetitionResultsId($competitionResultsId);
    }

    /**
     * @param CompetitionResults $competitionResults
     * @param Competition $competition
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

    /**
     * @param Id $runnerId
     * @return array
     */
    public function getCompetitionResultsByRunnerId(Id $runnerId): array
    {
        $competitionResultsData = $this->competitionResultsRepository->getCompetitionResultsByRunnerId($runnerId);
       
        return $this->getCompetitionResultsArrayByData($competitionResultsData);
    }

    /**
     * @param Date $date
     * @param CompetitionDataService $competitionDataService
     * @param RunnerService|null $runnerService
     * @param CompetitionService|null $competitionService
     *
     * @return array
     */
    public function getCompetitionResultsByDate(Date $date, CompetitionDataService $competitionDataService, RunnerService $runnerService = null, CompetitionService $competitionService = null): array
    {
        $competitionResultsData = [];
        $competitionDatas = $competitionDataService->getCompetitionDataByDate($date);

        /** @var CompetitionData $competitionData */
        foreach ($competitionDatas as $competitionData) {
            $competitionResults = $this->competitionResultsRepository->getCompetitionResultsByCompetitionDataId($competitionData->getCompetitionDataId());

            if (empty($competitionResults) === false) {
                $competitionResultsData[] = $competitionResults;
            }
        }

        return $this->getCompetitionResultsArrayByData($competitionResultsData, $competitionDataService, $runnerService, $competitionService);
    }

    /**
     * @return array
     */
    public function getWrongData(): array
    {
        return $this->wrongData;
    }

    /**
     * @param array $competitionResultsData
     * @param CompetitionDataService|null $competitionDataService
     * @param RunnerService|null $runnerService
     *<
     * @param CompetitionService|null $competitionService
     *
     * @return array
     */
    protected function getCompetitionResultsArrayByData(array $competitionResultsData, CompetitionDataService $competitionDataService = null, RunnerService $runnerService = null, CompetitionService $competitionService = null): array
    {
        $competitionResults = [];

        foreach ($competitionResultsData as $oneCompetitionResultsData){
            $singleCompetitionResults = $this->competitionResultsFactory->getCompetitionResultsByObject($oneCompetitionResultsData);

            if ($singleCompetitionResults !== null) {
                if ($competitionDataService !== null) {
                    $competitionData = $competitionDataService->getCompetitionDataByCompetitionDataId($singleCompetitionResults->getCompetitionDataId(), null, null, $competitionService);

                    if ($competitionData !== null) {
                        $singleCompetitionResults->setCompetitionData($competitionData);
                    }
                }

                if ($runnerService !== null) {
                    $runner = $runnerService->getRunnerByRunnerId($singleCompetitionResults->getRunnerId());

                    if ($runner !== null) {
                        $singleCompetitionResults->setRunner($runner);
                    }
                }

                $competitionResults[] = $singleCompetitionResults;
            }
        }

        return $competitionResults;
    }
}
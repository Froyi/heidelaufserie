<?php
declare(strict_types=1);

namespace Project\Module\CompetitionData;

use Project\Module\Club\ClubName;
use Project\Module\Club\ClubService;
use Project\Module\Competition\Competition;
use Project\Module\Competition\CompetitionService;
use Project\Module\Competition\CompetitionTypeId;
use Project\Module\CompetitionStatistic\CompetitionStatisticService;
use Project\Module\Database\Database;
use Project\Module\FinishMeasure\FinishMeasureService;
use Project\Module\GenericValueObject\Date;
use Project\Module\GenericValueObject\Gender;
use Project\Module\GenericValueObject\Id;
use Project\Module\GenericValueObject\Year;
use Project\Module\Runner\RunnerService;
use Project\TimeMeasure\TimeMeasureService;

/**
 * Class CompetitionDataService
 * @package Project\Module\CompetitionData
 */
class CompetitionDataService
{
    /** @var CompetitionDataRepository $competitionDataRepository */
    protected $competitionDataRepository;

    /** @var CompetitionDataFactory $competitionDataFactory */
    protected $competitionDataFactory;

    /** @var ClubService $clubService */
    protected $clubService;

    /**
     * CompetitionDataService constructor.
     *
     * @param Database $database
     * @param ClubService $clubService
     */
    public function __construct(Database $database, ClubService $clubService)
    {
        $this->competitionDataRepository = new CompetitionDataRepository($database);
        $this->competitionDataFactory = new CompetitionDataFactory();

        $this->clubService = $clubService;
    }

    /**
     * @param array $uploadData
     * @param array $competitions
     * @param array $transponderData
     *
     * @return array
     */
    public function getCompetitionDataAfterRunnerUpload(array $uploadData, array $competitions, array $transponderData): array
    {
        $competitionDataArray = [];

        /** @var array $competitionDataData */
        foreach ($uploadData as $competitionDataData) {
            $club = null;
            $competitionTypeId = CompetitionTypeId::fromValue($competitionDataData['competitionTypeId']);

            $competition = $this->getCompetitionByCompetitionTypeId($competitions, $competitionTypeId);

            if (isset($competitionDataData['clubName'])) {
                try {
                    $clubName = ClubName::fromString($competitionDataData['clubName']);

                    $club = $this->clubService->getOrCreateClubByClubName($clubName);
                } catch (\InvalidArgumentException $exception) {}
            }

            if ($competition !== null) {
                $competitionData = $this->competitionDataFactory->getCompetitionDataByObject($competitionDataData, $competition, $transponderData, $club);

                if ($competitionData !== null && $this->competitionDataExist($competitionData) === false) {
                    $competitionDataArray[$competitionData->getCompetitionDataId()->toString()] = $competitionData;
                }
            }
        }

        return $competitionDataArray;
    }

    /**
     * @param Date $date
     * @param TimeMeasureService|null $timeMeasureService
     * @param RunnerService|null $runnerService
     * @param CompetitionService|null $competitionService
     * @param FinishMeasureService|null $finishMeasureService
     *
     * @return array
     */
    public function getCompetitionDataByDate(Date $date, TimeMeasureService $timeMeasureService = null, RunnerService $runnerService = null, CompetitionService $competitionService = null, FinishMeasureService $finishMeasureService = null): array
    {
        $competitionDataData = $this->competitionDataRepository->getCompetitionDataByDate($date);

        return $this->createCompetitionData($competitionDataData, $timeMeasureService, $runnerService, $competitionService, null, $finishMeasureService);
    }

    /**
     * @param Date $date
     * @param TimeMeasureService|null $timeMeasureService
     * @param RunnerService|null $runnerService
     * @param CompetitionService|null $competitionService
     * @param FinishMeasureService|null $finishMeasureService
     * @param int|null $limit
     *
     * @return array
     */
    public function getRandomCompetitionDataByDate(Date $date, TimeMeasureService $timeMeasureService = null, RunnerService $runnerService = null, CompetitionService $competitionService = null, FinishMeasureService $finishMeasureService = null, int $limit = null): array
    {

        $competitionDataData = $this->competitionDataRepository->getRandomCompetitionDataByDate($date, $limit);

        return $this->createCompetitionData($competitionDataData, $timeMeasureService, $runnerService, $competitionService, null, $finishMeasureService);
    }

    /**
     * @param Date $date
     * @param StartNumber $startNumber
     * @param TimeMeasureService|null $timeMeasureService
     * @param RunnerService|null $runnerService
     * @param CompetitionService|null $competitionService
     *
     * @return null|CompetitionData
     */
    public function getCompetitionDataByDateAndStartNumber(Date $date, StartNumber $startNumber, TimeMeasureService $timeMeasureService = null, RunnerService $runnerService = null, CompetitionService $competitionService = null): ?CompetitionData
    {
        $competitionDataData = $this->competitionDataRepository->getCompetitionDataByDateAndStartNumber($date, $startNumber);

        if (empty($competitionDataData) === true) {
            return null;
        }

        return $this->createSingleCompetitionData($competitionDataData, $timeMeasureService, $runnerService, $competitionService);
    }

    /**
     * @param CompetitionData $competitionData
     *
     * @return bool
     */
    public function saveCompetitionData(CompetitionData $competitionData): bool
    {
        return $this->competitionDataRepository->saveCompetitionData($competitionData);
    }

    /**
     * @param array $allCompetitionData
     * @return bool
     */
    public function saveAllCompetitionData(array $allCompetitionData): bool
    {
        /** @var CompetitionData $competitionData */
        foreach ($allCompetitionData as $competitionData) {
            $this->clubService->saveOrUpdateClub($competitionData->getClub());
        }

        return $this->competitionDataRepository->saveAllCompetitionData($allCompetitionData);
    }

    /**
     * @param array $allCompetitionData
     *
     * @return bool
     */
    public function updateAllCompetitionData(array $allCompetitionData): bool
    {
        return $this->competitionDataRepository->updateAllCompetitionData($allCompetitionData);
    }

    /**
     * @param Id $runnerId
     *
     * @return array
     */
    public function getCompetitionDataByRunnerId(Id $runnerId): array
    {
        $competitionDataData = $this->competitionDataRepository->getCompetitionDataByRunnerId($runnerId);

        return $this->createCompetitionData($competitionDataData);
    }

    /**
     * @param Id $competitionDataId
     * @param TimeMeasureService $timeMeasureService
     * @param RunnerService $runnerService
     * @param CompetitionService $competitionService
     * @return null|CompetitionData
     */
    public function getCompetitionDataByCompetitionDataId(Id $competitionDataId, TimeMeasureService $timeMeasureService = null, RunnerService $runnerService = null, CompetitionService $competitionService = null): ?CompetitionData
    {
        $competitionDataData = $this->competitionDataRepository->getCompetitionDataByCompetitionDataId($competitionDataId);

        return $this->createSingleCompetitionData($competitionDataData, $timeMeasureService, $runnerService, $competitionService);
    }

    /**
     * @param Date $date
     * @param TimeMeasureService $timeMeasureService
     * @param RunnerService $runnerService
     * @param CompetitionService $competitionService
     * @param CompetitionStatisticService $competitionStatisticService
     *
     * @return array
     */
    public function getSpeakerCompetitionData(Date $date, TimeMeasureService $timeMeasureService, RunnerService $runnerService, CompetitionService $competitionService, CompetitionStatisticService $competitionStatisticService): array
    {
        $competitionDataData = $this->competitionDataRepository->getSpeakerCompetitionDataByCompetitionDate($date);
        return $this->createCompetitionData($competitionDataData, $timeMeasureService, $runnerService, $competitionService, $competitionStatisticService);
    }

    /**
     * @param array $genderConfig
     * @param Date $date
     * @param TimeMeasureService $timeMeasureService
     * @param RunnerService $runnerService
     * @param CompetitionService $competitionService
     *
     * @return array
     */
    public function getSpeakerRankingUpdateByGender(array $genderConfig, Date $date, TimeMeasureService $timeMeasureService, RunnerService $runnerService, CompetitionService $competitionService): array
    {
        $gender = Gender::fromString($genderConfig['gender']);
        $competitionTypeId = CompetitionTypeId::fromValue($genderConfig['competitionTypeId']);
        $competitionDataIds = $this->competitionDataRepository->getSpeakerRankingUpdateData($date, $gender, $competitionTypeId);

        $genderCompetitionData = [];
        foreach ($competitionDataIds as $competitionDataId) {
            $competitionData = $this->getCompetitionDataByCompetitionDataId(Id::fromString($competitionDataId->competitionDataId), $timeMeasureService, $runnerService, $competitionService);

            if ($competitionData !== null) {
                $genderCompetitionData[$competitionData->getCompetitionDataId()->toString()] = $competitionData;
            }
        }

        usort($genderCompetitionData, [$this, 'sortByRounds']);

        $genderArray = [];
        $counter = 0;
        foreach ($genderCompetitionData as $genderData) {
            if ($counter >= $genderConfig['amount']) {
                break;
            }

            $genderArray[] = $genderData;
            $counter++;
        }

        return $genderArray;
    }

    /**
     * @param CompetitionData $competitionData1
     * @param CompetitionData $competitionData2
     *
     * @return int
     */
    public function sortByRounds(CompetitionData $competitionData1, CompetitionData $competitionData2): int
    {
        if ($competitionData1->getActualRound() === $competitionData2->getActualRound()) {
            if ($competitionData1->getLastTimeOverall() === $competitionData2->getLastTimeOverall()) {
                return 0;
            }

            return ($competitionData1->getLastTimeOverall() < $competitionData2->getLastTimeOverall()) ? -1 : 1;
        }

        return ($competitionData1->getActualRound() > $competitionData2->getActualRound()) ? -1 : 1;
    }

    /**
     * @param Date $date
     *
     * @return bool
     */
    public function deleteCompetitionDataByDate(Date $date): bool
    {
        if (empty($this->getCompetitionDataByDate($date)) === true) {
            return true;
        }

        return $this->competitionDataRepository->deleteCompetitionDataByDate($date);
    }

    /**
     * @param Id $clubId
     *
     * @return array
     */
    public function getCompetitionDatasByClubId(Id $clubId): array
    {
        $competitionDataData = $this->competitionDataRepository->getCompetitionDataByClubId($clubId);

        return $this->createCompetitionData($competitionDataData);
    }

    /**
     * @return array
     */
    public function getAllCompetitionData(): array
    {
        $competitionDataData = $this->competitionDataRepository->getAllCompetitionData();

        return $this->createCompetitionData($competitionDataData);
    }

    /**
     * @param Id $getCompetitionDataId
     *
     * @return mixed
     */
    public function getClubStringByCompetitionDataId(Id $getCompetitionDataId)
    {
        $competitionDataData = $this->competitionDataRepository->getCompetitionDataByCompetitionDataId($getCompetitionDataId);

        return $competitionDataData->clubId;
    }

    /**
     * @param CompetitionData $competitionData
     *
     * @return bool
     */
    public function saveOrUpdateCompetitionData(CompetitionData $competitionData): bool
    {
        if ($this->getCompetitionDataByCompetitionDataId($competitionData->getCompetitionDataId()) === null) {
            return $this->competitionDataRepository->saveCompetitionData($competitionData);
        }

        return $this->competitionDataRepository->updateCompetitionData($competitionData);
    }

    /**
     * @param array $competitions
     * @param CompetitionTypeId $competitionTypeId
     *
     * @return null|Competition
     */
    protected function getCompetitionByCompetitionTypeId(array $competitions, CompetitionTypeId $competitionTypeId): ?Competition
    {
        /** @var Competition $competition */
        foreach ($competitions as $competition) {
            if ($competition->getCompetitionType()->getCompetitionTypeId()->getCompetitionTypeId() === $competitionTypeId->getCompetitionTypeId()) {
                return $competition;
            }
        }

        return null;
    }

    /**
     * @param array $competitionDataData
     * @param TimeMeasureService|null $timeMeasureService
     * @param RunnerService|null $runnerService
     * @param CompetitionService|null $competitionService
     * @param CompetitionStatisticService|null $competitionStatisticService
     * @param FinishMeasureService|null $finishMeasureService
     *
     * @return array
     */
    protected function createCompetitionData(array $competitionDataData, TimeMeasureService $timeMeasureService = null, RunnerService $runnerService = null, CompetitionService $competitionService = null, CompetitionStatisticService $competitionStatisticService = null, FinishMeasureService $finishMeasureService = null): array
    {
        $competitionDataArray = [];

        foreach ($competitionDataData as $singleCompetitionData) {
            $competitionData = $this->createSingleCompetitionData($singleCompetitionData, $timeMeasureService, $runnerService, $competitionService, $competitionStatisticService, $finishMeasureService);

            if ($competitionData !== null) {
                $competitionDataArray[$competitionData->getCompetitionDataId()->toString()] = $competitionData;
            }
        }

        return $competitionDataArray;
    }

    /**
     * @param $singleCompetitionData
     * @param TimeMeasureService|null $timeMeasureService
     * @param RunnerService|null $runnerService
     * @param CompetitionService|null $competitionService
     * @param CompetitionStatisticService|null $competitionStatisticService
     * @param FinishMeasureService|null $finishMeasureService
     *
     * @return null|CompetitionData
     */
    protected function createSingleCompetitionData($singleCompetitionData, TimeMeasureService $timeMeasureService = null, RunnerService $runnerService = null, CompetitionService $competitionService = null, CompetitionStatisticService $competitionStatisticService = null, FinishMeasureService $finishMeasureService = null): ?CompetitionData
    {
        /** @var CompetitionData $competitionData */
        $competitionData = $this->competitionDataFactory->getCompetitionData($singleCompetitionData);

        if ($competitionData !== null) {
            if (isset($singleCompetitionData->clubId)) {
                try {
                    $club = $this->clubService->getClubByClubId(Id::fromString($singleCompetitionData->clubId));
                    if ($club !== null) {
                        $competitionData->setClub($club);
                    }
                } catch (\InvalidArgumentException $exception) {}
            }

            if ($timeMeasureService !== null) {
                $timeMeasureList = $timeMeasureService->getAllTimeMeasuresByTransponderNumber($competitionData->getTransponderNumber());

                $competitionData->setTimeMeasureList($timeMeasureList);
            }

            if ($finishMeasureService !== null) {
                $finishMeasureList = $finishMeasureService->getFinishMeasureByTransponderNumber($competitionData->getTransponderNumber());

                $competitionData->setFinishMeasureList($finishMeasureList);
            }

            if ($runnerService !== null) {
                $runner = $runnerService->getRunnerByRunnerId($competitionData->getRunnerId());

                if ($runner !== null) {
                    $competitionData->setRunner($runner);
                }
            }

            if ($competitionService !== null) {
                $competition = $competitionService->getCompetitionByCompetitionId($competitionData->getCompetitionId());

                if ($competition !== null) {
                    $competitionData->setCompetition($competition);
                }
            }
            
            if ($competitionStatisticService !== null) {
                $competitionStatistic = $competitionStatisticService->getCompetitionStatisticByRunnerIdAndYear($competitionData->getRunnerId(), Year::fromValue(date('Y',strtotime('-1 year'))));

                if ($competitionStatistic !== null) {
                    $competitionData->setCompetitionStatistic($competitionStatistic);
                }
            }

            return $competitionData;
        }

        return null;
    }

    /**
     * @param CompetitionData $competitionData
     *
     * @return bool
     */
    protected function competitionDataExist(CompetitionData $competitionData): bool
    {
        return (empty($this->competitionDataRepository->competitionDataExist($competitionData)) === false);
    }
}
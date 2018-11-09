<?php declare(strict_types=1);

namespace Project\Module\Competition;

use Project\Module\Database\Database;
use Project\Module\Database\Query;
use Project\Module\GenericValueObject\Date;
use Project\Module\GenericValueObject\Id;

/**
 * Class CompetitionService
 * @package Project\Module\Competition
 */
class CompetitionService
{
    /** @var CompetitionRepository $competitionRepository */
    protected $competitionRepository;

    /** @var CompetitionFactory $competitionFactory */
    protected $competitionFactory;

    /** @var array $competitionTypes */
    protected $competitionTypes;

    /**
     * CompetitionService constructor.
     *
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->competitionRepository = new CompetitionRepository($database);
        $this->competitionFactory = new CompetitionFactory();

        $this->competitionTypes = $this->getAllCompetitionTypes();
    }

    /**
     * @return array
     */
    public function getCompetitionTypes(): array
    {
        return $this->competitionTypes;
    }

    /**
     * @return array
     */
    public function getAllCompetitions(): array
    {
        $competitionData = $this->competitionRepository->getAllCompetitions(Query::DESC);

        return $this->getCompetitionsByData($competitionData);
    }

    /**
     * @return array
     */
    public function getAllStartTimeGroups(): array
    {
        $startTimeGroups = [];

        /** @var CompetitionType $competitionType */
        foreach ($this->competitionTypes as $competitionType) {
            $startTimeGroups[$competitionType->getStartTimeGroup()->getStartTimeGroup()][] = $competitionType;
        }

        return $startTimeGroups;
    }

    /**
     * Return an array with startTimes to startTimeGroup as key
     *
     * @param Date $date
     *
     * @return array
     */
    public function getStartTimesByDate(Date $date): array
    {
        $startTimes = [];

        $competitions = $this->getCompetitionsByDate($date);

        /** @var Competition $competition */
        foreach ($competitions as $competition) {
            $startTimes[$competition->getCompetitionType()->getStartTimeGroup()->getStartTimeGroup()] = $competition->getStartTime();
        }

        return $startTimes;
    }

    /**
     * @param Id $competitionId
     *
     * @return null|Competition
     */
    public function getCompetitionByCompetitionId(Id $competitionId): ?Competition
    {
        $competitionData = $this->competitionRepository->getCompetitionByCompetitionId($competitionId);

        return $this->createSingleCompetitionByData($competitionData);
    }

    /**
     * @param array $parameter
     *
     * @return array
     */
    public function getCompetitionsByParameter(array $parameter): array
    {
        $allCompetitionsArray = [];
        /** @var \stdClass $competitionData */
        $competitionData = (object)$parameter;

        if ($competitionData === null) {
            return $allCompetitionsArray;
        }

        // if competitionData has no valid starttime
        if (strpos($competitionData->startTime, $competitionData->date) === false) {
            $competitionData->startTime = $competitionData->date . ' ' . $competitionData->startTime;
        }

        // create standard competitions from formular
        if (empty($competitionData->createStandardCompetitions) === false) {
            foreach ($this->getAllStandardCompetitionTypes() as $standardCompetitionType) {
                $competition = $this->competitionFactory->getCompetitionByObject($competitionData, $standardCompetitionType);

                if ($competition !== null) {
                    $allCompetitionsArray[$competition->getCompetitionId()->toString()] = $competition;
                }
            }

            return $allCompetitionsArray;
        }

        // create specific competitions from formular
        if (empty($competitionData->competitionTypes) === false && \is_array($competitionData->competitionTypes)) {
            foreach ($competitionData->competitionTypes as $competitionTypeId) {
                $competition = $this->competitionFactory->getCompetitionByObject($competitionData, $this->competitionTypes[$competitionTypeId]);

                if ($competition !== null) {
                    $allCompetitionsArray[$competition->getCompetitionId()->toString()] = $competition;
                }
            }

            return $allCompetitionsArray;
        }

        return $allCompetitionsArray;
    }

    /**
     * @param Competition $competition
     *
     * @return bool
     */
    public function saveCompetition(Competition $competition): bool
    {
        return $this->competitionRepository->saveCompetition($competition);
    }

    /**
     * @param array $competitions
     *
     * @return bool
     */
    public function updateAllCompetitions(array $competitions): bool
    {
        return $this->competitionRepository->updateAllCompetitions($competitions);
    }

    /**
     * @param Date $date
     *
     * @return array
     */
    public function getCompetitionsByDate(Date $date): array
    {
        $competitionData = $this->competitionRepository->getCompetitionsByDate($date);

        return $this->getCompetitionsByData($competitionData);
    }

    /**
     * @param Date $date
     * @param StartTimeGroup $startTimeGroup
     *
     * @return array
     */
    public function getCompetitionsByDateAndStartTimeGroup(Date $date, StartTimeGroup $startTimeGroup): array
    {
        $competitions = [];
        $competitionArray = $this->getCompetitionsByDate($date);

        /** @var Competition $competition */
        foreach ($competitionArray as $competition) {
            if ($competition->getCompetitionType()->getStartTimeGroup()->getStartTimeGroup() === $startTimeGroup->getStartTimeGroup()) {
                $competitions[] = $competition;
            }
        }

        return $competitions;
    }

    /**
     * @param Date $date
     *
     * @return bool
     */
    public function deleteCompetitionByDate(Date $date): bool
    {
        if (empty($this->getCompetitionsByDate($date)) === true) {
            return true;
        }

        return $this->competitionRepository->deleteCompetitionByDate($date);
    }

    /**
     * @return array
     */
    protected function getAllCompetitionTypes(): array
    {
        $allCompetitionsArray = [];

        $allCompetitionTypesData = $this->competitionRepository->getAllCompetitionTypes();

        if (empty($allCompetitionTypesData) === true) {
            return $allCompetitionsArray;
        }

        foreach ($allCompetitionTypesData as $allCompetitionTypeData) {
            $allCompetitionType = $this->competitionFactory->getCompetitionTypeByObject($allCompetitionTypeData);

            if ($allCompetitionType !== null) {
                $allCompetitionsArray[$allCompetitionType->getCompetitionTypeId()->getCompetitionTypeId()] = $allCompetitionType;
            }
        }

        return $allCompetitionsArray;
    }

    /**
     * @param array $competitionsData
     *
     * @return array
     */
    protected function getCompetitionsByData(array $competitionsData): array
    {
        $competitionArray = [];

        foreach ($competitionsData as $singleCompetitionData) {
            $competition = $this->createSingleCompetitionByData($singleCompetitionData);

            if ($competition !== null) {
                $competitionArray[$competition->getCompetitionId()->toString()] = $competition;
            }
        }

        return $competitionArray;
    }

    /**
     * @param $singleCompetitionData
     *
     * @return null|Competition
     */
    protected function createSingleCompetitionByData($singleCompetitionData): ?Competition
    {
        return $this->competitionFactory->getCompetitionByObject($singleCompetitionData, $this->competitionTypes[$singleCompetitionData->competitionTypeId]);
    }

    /**
     * @return array
     */
    protected function getAllStandardCompetitionTypes(): array
    {
        $standardCompetitiontypes = [];

        /** @var CompetitionType $competitionType */
        foreach ($this->competitionTypes as $competitionType) {
            if ($competitionType->isStandardSet() === true) {
                $standardCompetitiontypes[$competitionType->getCompetitionTypeId()->getCompetitionTypeId()] = $competitionType;
            }
        }

        return $standardCompetitiontypes;
    }
}
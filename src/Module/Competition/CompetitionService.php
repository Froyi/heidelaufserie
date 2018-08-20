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

    /**
     * CompetitionService constructor.
     *
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->competitionRepository = new CompetitionRepository($database);
        $this->competitionFactory = new CompetitionFactory();
    }

    /**
     * @return array
     */
    public function getAllCompetitionTypes(): array
    {
        $allCompetitionsArray = [];

        $allCompetitionTypesData = $this->competitionRepository->getAllCompetitionTypes();

        if (empty($allCompetitionTypesData) === true) {
            return $allCompetitionsArray;
        }

        foreach ($allCompetitionTypesData as $allCompetitionTypeData) {
            $allCompetitionType = $this->competitionFactory->getCompetitionTypeByObject($allCompetitionTypeData);

            if ($allCompetitionType !== null) {
                $allCompetitionsArray[$allCompetitionType->getCompetitionTypeId()] = $allCompetitionType;
            }
        }

        return $allCompetitionsArray;
    }

    /**
     * @return array
     */
    public function getAllCompetitions(): array
    {
        $competitionArray = [];

        $competitionData = $this->competitionRepository->getAllCompetitions(Query::DESC);

        if (empty($competitionData) === true) {
            return $competitionArray;
        }

        $allCompetitionTypes = $this->getAllCompetitionTypes();

        foreach ($competitionData as $singleCompetitionData) {
            $competition = $this->competitionFactory->getCompetitionByObject($singleCompetitionData, $allCompetitionTypes[$singleCompetitionData->competitionTypeId]);

            if ($competition !== null) {
                $competitionArray[$competition->getCompetitionId()->toString()] = $competition;
            }
        }

        return $competitionArray;
    }

    /**
     * @return array
     */
    public function getAllStartTimeGroups(): array
    {
        $startTimeGroups = [];

        $competitionTypes = $this->getAllCompetitionTypes();

        /** @var CompetitionType $competitionType */
        foreach ($competitionTypes as $competitionType) {
            $startTimeGroups[$competitionType->getStartTimeGroup()->getStartTimeGroup()][] = $competitionType;
        }

        return $startTimeGroups;
    }

    /**
     * @param int $competitionTypeId
     *
     * @return null|CompetitionType
     */
    public function getCompetitionTypeByCompetitionTypeId(int $competitionTypeId): ?CompetitionType
    {
        $competitionTypeData = $this->competitionRepository->getCompetitionTypeByCompetitionTypeId($competitionTypeId);

        if (empty($competitionTypeData) === true) {
            return null;
        }

        return $this->competitionFactory->getCompetitionTypeByObject($competitionTypeData);
    }

    /**
     * @param Id $competitionId
     *
     * @return null|Competition
     */
    public function getCompetitionByCompetitionId(Id $competitionId): ?Competition
    {
        $competitionData = $this->competitionRepository->getCompetitionByCompetitionId($competitionId);

        if (empty($competitionData) === true || empty($competitionData->competitionTypeId) === true) {
            return null;
        }

        $competitionType = $this->getCompetitionTypeByCompetitionTypeId((int)$competitionData->competitionTypeId);

        if ($competitionType === null) {
            return null;
        }

        return $this->competitionFactory->getCompetitionByObject($competitionData, $competitionType);
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

        if (strpos($competitionData->startTime, $competitionData->date) === false) {
            $competitionData->startTime = $competitionData->date . ' ' . $competitionData->startTime;
        }

        $allCompetitionTypes = $this->getAllCompetitionTypes();

        if (empty($competitionData->createStandardCompetitions) === false) {
            $standardCompetitionTypes = $this->getAllStandardCompetitionTypes($allCompetitionTypes);

            foreach ($standardCompetitionTypes as $standardCompetitionType) {
                $competition = $this->competitionFactory->getCompetitionByObject($competitionData, $standardCompetitionType);

                if ($competition !== null) {
                    $allCompetitionsArray[$competition->getCompetitionId()->toString()] = $competition;
                }
            }

            return $allCompetitionsArray;
        }

        if (empty($competitionData->competitionTypes) === false && \is_array($competitionData->competitionTypes)) {
            foreach ($competitionData->competitionTypes as $competitionTypeId) {
                $competition = $this->competitionFactory->getCompetitionByObject($competitionData, $allCompetitionTypes[$competitionTypeId]);

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
        $competitionArray = [];

        $competitionData = $this->competitionRepository->getCompetitionsByDate($date);

        if (empty($competitionData) === true) {
            return $competitionArray;
        }

        $allCompetitionTypes = $this->getAllCompetitionTypes();

        foreach ($competitionData as $singleCompetitionData) {
            $competition = $this->competitionFactory->getCompetitionByObject($singleCompetitionData, $allCompetitionTypes[$singleCompetitionData->competitionTypeId]);

            if ($competition !== null) {
                $competitionArray[$competition->getCompetitionId()->toString()] = $competition;
            }
        }

        return $competitionArray;
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
     * @param array $allCompetitionTypes
     *
     * @return array
     */
    protected function getAllStandardCompetitionTypes(array $allCompetitionTypes): array
    {
        $standardCompetitiontypes = [];

        /** @var CompetitionType $competitionType */
        foreach ($allCompetitionTypes as $competitionType) {
            if ($competitionType->isStandardSet() === true) {
                $standardCompetitiontypes[$competitionType->getCompetitionTypeId()] = $competitionType;
            }
        }

        return $standardCompetitiontypes;
    }
}
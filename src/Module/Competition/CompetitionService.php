<?php declare(strict_types=1);

namespace Project\Module\Competition;

use Project\Module\Database\Database;
use Project\Module\GenericValueObject\Date;

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
     * @param array $parameter
     *
     * @return null|Competition
     */
    public function getCompetitionByParameter(array $parameter): ?Competition
    {
        $competitionData = (object)$parameter;

        if (empty($competitionData) === true) {
            return null;
        }

        return $this->competitionFactory->getCompetitionByObject($competitionData);
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
     * @param Date $date
     *
     * @return null|CompetitionDay
     */
    public function getCompetitionDayByDate(Date $date): ?CompetitionDay
    {
        $competitionDayData = $this->competitionRepository->getCompetitionDayByDate($date);

        if (empty($competitionDayData) === true) {
            return null;
        }

        return $this->competitionFactory->getCompetitionDayByObject($competitionDayData);
    }

    /**
     * @param array $parameter
     *
     * @return null|CompetitionDay
     */
    public function getCompetitionDayByParameter(array $parameter): ?CompetitionDay
    {
        $competitionDayData = (object)$parameter;

        if (empty($competitionDayData) === true) {
            return null;
        }

        return $this->competitionFactory->getCompetitionDayByObject($competitionDayData);
    }

    /**
     * @param CompetitionDay $competitionDay
     *
     * @return bool
     */
    public function saveCompetitionDay(CompetitionDay $competitionDay): bool
    {
        return $this->competitionRepository->saveCompetitionDay($competitionDay);
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

        foreach ($competitionData as $singleCompetitionData) {
            $competition = $this->competitionFactory->getCompetitionByObject($singleCompetitionData);

            if ($competition !== null) {
                $competitionArray[] = $competition;
            }
        }

        return $competitionArray;
    }

    /**
     * @return array
     */
    public function getAllCompetitionDaysWithCompetitions(): array
    {
        $competitionDayArray = [];

        $competitionDayData = $this->competitionRepository->getAllCompetitionDays();

        if (empty($competitionDayData) === true) {
            return $competitionDayArray;
        }

        foreach ($competitionDayData as $singleCompetitionDayData) {
            $competitionDay = $this->competitionFactory->getCompetitionDayByObject($singleCompetitionDayData);

            if ($competitionDay !== null) {
                $competitions = $this->getCompetitionsByDate($competitionDay->getDate());

                if ($competitions !== null) {
                    $competitionDay->setCompetitionList($competitions);
                }

                $competitionDayArray[] = $competitionDay;
            }
        }

        return $competitionDayArray;
    }

    /**
     * @param Date $date
     *
     * @return bool
     */
    public function createStandardCompetitions(Date $date): bool
    {
        $savedAll = true;
        $standardCompetitions = Competition::getStandardCompetitions();

        foreach ($standardCompetitions as $standardCompetitionNumber => $standardCompetition) {
            $object = new \stdClass();
            $object->date = $date->toString();
            $object->competitionNumber = $standardCompetitionNumber;

            $competition = $this->competitionFactory->getCompetitionByObject($object);

            if ($competition !== null && $this->saveCompetition($competition) === false) {
                $savedAll = false;
            }
        }

        return $savedAll;
    }
}
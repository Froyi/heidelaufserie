<?php declare(strict_types=1);

namespace Project\Module\Competition;
use Project\Module\Database\Database;

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
        $competitionData = (object) $parameter;

        if (empty($competitionData)  === true) {
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
}
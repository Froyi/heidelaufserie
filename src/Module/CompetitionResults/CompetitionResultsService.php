<?php
declare (strict_types=1);


namespace Project\Module\CompetitionResults;

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
     */
    public function __construct(Database $database)
    {
        $this->competitionResultsFactory = new CompetitionResultsFactory();
        $this->competitionResultsRepository = new CompetitionResultsRepository($database);
    }

    public function getCompetitionResultsByCompetitionDataId(Id $competitionDataId): ?CompetitionResults
    {
        $competitionResultsData = $this->competitionResultsRepository->getCompetitionResultsByCompetitionDataId($competitionDataId);
        if (empty($competitionResultsData) === true) {
            return null;
        }

        return $this->competitionResultsFactory->getCompetitionResultsByObject($competitionResultsData);
    }


}
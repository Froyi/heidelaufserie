<?php
declare(strict_types=1);

namespace Project\Module\CompetitionData;

use Project\Module\Competition\Competition;
use Project\Module\Database\Database;

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

    /**
     * CompetitionDataService constructor.
     *
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->competitionDataRepository = new CompetitionDataRepository($database);
        $this->competitionDataFactory = new CompetitionDataFactory();
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

        foreach ($uploadData as $competitionDataData) {
            $competition = $this->getCompetitionByCompetitionTypeId($competitions, (int)$competitionDataData['competitionTypeId']);

            if ($competition !== null) {
                $competitionData = $this->competitionDataFactory->getCompetitionDataByObject($competitionDataData, $competition, $transponderData);

                if ($competitionData !== null) {
                    $competitionDataArray[$competitionData->getCompetitionDataId()->toString()] = $competitionData;
                }
            }
        }

        return $competitionDataArray;
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
     * @param array $competitions
     * @param int $competitionTypeId
     *
     * @return null|Competition
     */
    protected function getCompetitionByCompetitionTypeId(array $competitions, int $competitionTypeId): ?Competition
    {
        /** @var Competition $competition */
        foreach ($competitions as $competition) {
            if ($competition->getCompetitionType()->getCompetitionTypeId() === $competitionTypeId) {
                return $competition;
            }
        }

        return null;
    }
}
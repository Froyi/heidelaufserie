<?php
declare(strict_types=1);

namespace Project\TimeMeasure;

use Project\Module\CompetitionData\CompetitionData;
use Project\Module\CompetitionData\TransponderNumber;
use Project\Module\Database\Database;

/**
 * Class TimeMeasureService
 * @package Project\TimeMeasure
 */
class TimeMeasureService
{
    /** @var TimeMeasureFactory $timeMeasureFactory */
    protected $timeMeasureFactory;

    /** @var TimeMeasureRepository $timeMeasureRepository */
    protected $timeMeasureRepository;

    /**
     * TimeMeasureService constructor.
     */
    public function __construct(Database $database)
    {
        $this->timeMeasureRepository = new TimeMeasureRepository($database);
        $this->timeMeasureFactory = new TimeMeasureFactory();
    }

    /**
     * @param TransponderNumber $transponderNumber
     *
     * @return array
     */
    public function getAllTimeMeasuresByTransponderNumber(TransponderNumber $transponderNumber): array
    {
        $timeMeasureData = $this->timeMeasureRepository->getTimeMeasureByTransponderNumber($transponderNumber);

        if (empty($timeMeasureData) === true) {
            return [];
        }

        return $this->createTimeMeasureArray($timeMeasureData);
    }

    /**
     * @return array
     */
    public function getAllNewTimeMeasures(): array
    {
        $timeMeasureData = $this->timeMeasureRepository->getNewTimeMeasures();

        if (empty($timeMeasureData) === true) {
            return [];
        }

        return $this->createTimeMeasureArray($timeMeasureData);
    }

    public function markTimeMeasureListAsShown(array $timeMeasureList): bool
    {
        $allMarked = true;

        /** @var TimeMeasure $timeMeasure */
        foreach ($timeMeasureList as $timeMeasure) {
            $timeMeasure->setShown(true);

            if ($this->updateTimeMeasure($timeMeasure) === false) {
                $allMarked = false;
            }
        }

        return $allMarked;
    }

    /**
     * @param CompetitionData $competitionData
     *
     * @return null|TimeMeasure
     */
    public function generateTimeMeasureByData(CompetitionData $competitionData): ?TimeMeasure
    {
        return $this->timeMeasureFactory->generateTimeMeasureByData($competitionData);
    }

    /**
     * @param TimeMeasure $timeMeasure
     *
     * @return bool
     */
    public function saveTimeMeasure(TimeMeasure $timeMeasure): bool
    {
        return $this->timeMeasureRepository->saveTimeMeasure($timeMeasure);
    }

    /**
     * @param TimeMeasure $timeMeasure
     *
     * @return bool
     */
    public function updateTimeMeasure(TimeMeasure $timeMeasure): bool
    {
        return $this->timeMeasureRepository->updateTimeMeasure($timeMeasure);
    }

    /**
     * @param array $timeMeasureData
     *
     * @return array
     */
    protected function createTimeMeasureArray(array $timeMeasureData): array
    {
        $timeMeasureArray = [];

        foreach ($timeMeasureData as $timeMeasureSingleData) {
            $timeMeasure = $this->timeMeasureFactory->getTimeMeasureByObject($timeMeasureSingleData);

            if ($timeMeasure !== null) {
                $timeMeasureArray[$timeMeasure->getTimeMeasureId()->toString()] = $timeMeasure;
            }
        }

        return $timeMeasureArray;
    }
}
<?php declare(strict_types=1);

namespace Project\Module\Reader;

use Project\Module\GenericValueObject\Id;

/**
 * Class ReaderService
 * @package     Project\Module\Reader
 */
class ReaderService
{
    /** @var string RUNNER_FILE */
    public const RUNNER_FILE = 'runner.txt';

    /** @var array RUNNER_FILE_INDEX */
    public const RUNNER_FILE_INDEX = [
        'surname' => 0,
        'firstname' => 1,
        'gender' => 2,
        'birthYear' => 3,
        'clubName' => 4,
        'startNumber' => 6,
        'competitionTypeId' => 8,
    ];

    public const COMPETITION_RESULTS_FILE_INDEX = [
        'startNumber' => 0,
        'firstname' => 1,
        'surname' => 2,
        'timeOverall' => 3,
        'points' => 4,
        'date' => 5,
        'firstRound' => 6,
        'secondRound' => 7,
        'thirdRound' => 8,
    ];


    /** @var bool TRANSPONDER_HAS_LEGEND */
    protected const RUNNER_HAS_LEGEND = true;

    /** @var bool COMPETITION_RESULTS_HAS_LEGEND */
    protected const COMPETITION_RESULTS_HAS_LEGEND = true;

    /** @var string TRANSPONDER_FILE */
    public const TRANSPONDER_FILE = 'transponder.csv';

    /** @var array TRANSPONDER_FILE_INDEX */
    public const TRANSPONDER_FILE_INDEX = [
        'startNumber' => 0,
        'transponderNumber' => 3,
    ];

    /** @var bool TRANSPONDER_HAS_LEGEND */
    protected const TRANSPONDER_HAS_LEGEND = true;

    /**
     * @param null|string $runnerFile
     *
     * @return array
     */
    public function readRunnerFile(?string $runnerFile = null): array
    {
        $runnerArray = [];

        if ($runnerFile !== null) {
            $file = file($runnerFile);
            $count = \count($file);
        } else {
            if (filesize(self::RUNNER_FILE) === 0) {
                return $runnerArray;
            }

            $file = file(self::RUNNER_FILE);
            $count = \count($file);
        }

        $startEntry = 0;
        if (self::RUNNER_HAS_LEGEND === true) {
            $startEntry = 1;
        }

        for ($i = $startEntry; $i < $count; $i++) {
            $runnerData = explode(';', $file[$i]);

            $runnerSingleData = [];
            $runnerSingleData['runnerId'] = Id::generateId()->toString();
            $runnerSingleData['surname'] = $runnerData[self::RUNNER_FILE_INDEX['surname']];
            $runnerSingleData['firstname'] = $runnerData[self::RUNNER_FILE_INDEX['firstname']];
            $runnerSingleData['gender'] = $runnerData[self::RUNNER_FILE_INDEX['gender']];
            $runnerSingleData['birthYear'] = $runnerData[self::RUNNER_FILE_INDEX['birthYear']];
            $runnerSingleData['clubName'] = $runnerData[self::RUNNER_FILE_INDEX['clubName']];
            $runnerSingleData['startNumber'] = $runnerData[self::RUNNER_FILE_INDEX['startNumber']];
            $runnerSingleData['competitionTypeId'] = $runnerData[self::RUNNER_FILE_INDEX['competitionTypeId']];

            $runnerArray[$runnerSingleData['runnerId']] = $runnerSingleData;
        }

        return $runnerArray;
    }

    /**
     * @return array
     */
    public function readTransponderFile(): array
    {
        $transponderArray = [];

        $file = file(self::TRANSPONDER_FILE);
        $count = \count($file);

        $startEntry = 0;
        if (self::TRANSPONDER_HAS_LEGEND === true) {
            $startEntry = 1;
        }

        for ($i = $startEntry; $i < $count; $i++) {
            $transponderData = explode(';', $file[$i]);

            if (empty($transponderData[self::TRANSPONDER_FILE_INDEX['startNumber']]) === true || empty($transponderData[self::TRANSPONDER_FILE_INDEX['transponderNumber']]) === true) {
                continue;
            }

            $transponderSingleData = [];
            $transponderSingleData['startNumber'] = $transponderData[self::TRANSPONDER_FILE_INDEX['startNumber']];
            $transponderSingleData['transponderNumber'] = $transponderData[self::TRANSPONDER_FILE_INDEX['transponderNumber']];

            $transponderArray[$transponderSingleData['startNumber']] = $transponderSingleData;
        }

        return $transponderArray;
    }

    public function readCompetitionResultsFile(?string $resultsFile = null): array
    {
        $competitionResultsArray = [];

        if ($resultsFile === null) {
            return $competitionResultsArray;
        }

        $file = file($resultsFile);
        $count = \count($file);

        $startEntry = 0;
        if (self::COMPETITION_RESULTS_HAS_LEGEND === true) {
            $startEntry = 1;
        }

        for ($i = $startEntry; $i < $count; $i++) {
            $competitionResultsData = explode(';', $file[$i]);

            $competitionResultsSingleData = [];
            $competitionResultsSingleData['competitionResultsId'] = Id::generateId()->toString();
            $competitionResultsSingleData['startNumber'] = $competitionResultsData[self::COMPETITION_RESULTS_FILE_INDEX['startNumber']];
            $competitionResultsSingleData['firstname'] = $competitionResultsData[self::COMPETITION_RESULTS_FILE_INDEX['firstname']];
            $competitionResultsSingleData['surname'] = $competitionResultsData[self::COMPETITION_RESULTS_FILE_INDEX['surname']];
            $competitionResultsSingleData['timeOverall'] = $competitionResultsData[self::COMPETITION_RESULTS_FILE_INDEX['timeOverall']];
            $competitionResultsSingleData['points'] = $competitionResultsData[self::COMPETITION_RESULTS_FILE_INDEX['points']];
            $competitionResultsSingleData['date'] = $competitionResultsData[self::COMPETITION_RESULTS_FILE_INDEX['date']];
            $competitionResultsSingleData['firstRound'] = $competitionResultsData[self::COMPETITION_RESULTS_FILE_INDEX['firstRound']];
            $competitionResultsSingleData['secondRound'] = $competitionResultsData[self::COMPETITION_RESULTS_FILE_INDEX['secondRound']];
            $competitionResultsSingleData['thirdRound'] = $competitionResultsData[self::COMPETITION_RESULTS_FILE_INDEX['thirdRound']];

            $competitionResultsArray[$competitionResultsSingleData['competitionResultsId']] = $competitionResultsSingleData;
        }

        return $competitionResultsArray;
    }

    /**
     * @return array
     */
    public function readFinishMeasureFile(): array
    {
        $file = file('Log2018-8-26.txt');
        $count = \count($file);
        $measureData = [];
        /** @var null|\stdClass $lastMeasure */
        $lastMeasure = null;

        for ($i = 0; $i < $count; $i ++) {
            $finishMeasureData = explode(' ', $file[$i]);

            if ($finishMeasureData[1] !== 'Chip:' || \count($finishMeasureData) <= 2) {
                continue;
            }

            $finishMeasureObject = new \stdClass();
            $finishMeasureObject->timestamp = substr($finishMeasureData[0], 0, -1);
            $finishMeasureObject->transponderNumber = substr($finishMeasureData[2], 0, -1);

            if ($lastMeasure === null || $finishMeasureObject->transponderNumber !== $lastMeasure->transponderNumber) {
                $measureData[] = $finishMeasureObject;
            }
            $lastMeasure = $finishMeasureObject;
        }

        return $measureData;
    }
}
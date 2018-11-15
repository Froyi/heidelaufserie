<?php
declare(strict_types=1);

namespace Project\Module\Runner;

use Project\Configuration;
use Project\Module\CompetitionData\CompetitionDataService;
use Project\Module\Database\Database;
use Project\Module\GenericValueObject\Id;

/**
 * Class RunnerDuplicateService
 * @package Project\Module\Runner
 */
class RunnerDuplicateService
{
    /** @var RunnerService $runnerService */
    protected $runnerService;

    /** @var CompetitionDataService $competitionDataService */
    protected $competitionDataService;

    /** @var Database $database */
    protected $database;

    /** @var array $allRunner */
    protected $allRunner = [];

    protected $notProvedRunner = [];

    /**
     * RunnerDuplicateService constructor.
     *
     * @param Database $database
     * @param Configuration $configuration
     * @param CompetitionDataService $competitionDataService
     */
    public function __construct(Database $database, Configuration $configuration, CompetitionDataService $competitionDataService)
    {
        $this->database = $database;
        $this->competitionDataService = $competitionDataService;
        $this->runnerService = new RunnerService($this->database, $configuration);

        // generate all runner
        $this->allRunner = $this->runnerService->getAllCompleteRunner($this->competitionDataService);

        // generate only not proven runner
        $this->notProvedRunner = $this->getAllNotProvedRunnerByRunnerList($this->allRunner);

    }

    /**
     * proof of duplicates to all runner
     * this test is only for a complete run which does not filter the proved runner
     * @return array
     */
    public function findAllDuplicates(): array
    {
        return $this->findDuplicates(false);
    }

    /**
     * @param $database
     *
     * @return array
     */
    public function findNotProvedDuplicates($database): array
    {
        return $this->findDuplicates($database);
    }

    /**
     * @param Runner $runner
     *
     * @return array
     */
    public function findDuplicateToRunner(Runner $runner): array
    {
        $duplicates = [];

        foreach ($this->allRunner as $runner2) {
            if ($this->isDuplicate($runner, $runner2) === true) {
                $duplicates[] = $runner2;
            }
        }

        return $duplicates;
    }

    /**
     * @param Id $runnerId
     *
     * @return array
     */
    public function findDuplicateToRunnerByRunnerId(Id $runnerId): array
    {
        /** @var Runner $runner */
        $runner = $this->runnerService->getRunnerByRunnerId($runnerId);

        return $this->findDuplicateToRunner($runner);
    }

    /**
     * @param array $runnerList
     *
     * @return array
     */
    protected function getAllNotProvedRunnerByRunnerList(array $runnerList): array
    {
        $notProvedRunnerList = [];

        /** @var Runner $runner */
        foreach ($runnerList as $runner) {
            if ($runner->isProved() === false) {
                $notProvedRunnerList[] = $runner;
            }
        }

        return $notProvedRunnerList;
    }

    /**
     * @param bool $notProved
     *
     * @return array
     */
    protected function findDuplicates(bool $notProved = true): array
    {
        $duplicates = [];

        $toCheckRunner = $this->allRunner;

        $runnerArray = $this->notProvedRunner;
        if ($notProved === false) {
            $runnerArray = $this->allRunner;
        }

        /** @var Runner $runner */
        foreach ($runnerArray as $runner) {
            $competitionData = $this->competitionDataService->getCompetitionDataByRunnerId($runner->getRunnerId());
            if (empty($competitionData) === false) {
                $runner->setCompetitionDataList($competitionData);
            }

            $testedRunner['runner'] = $runner;
            $testedRunner['duplicates'] = [];

            /** @var Runner $otherRunner */
            foreach ($toCheckRunner as $otherRunner) {
                if ($this->isDuplicate($runner, $otherRunner) === true) {
                    $competitionData = $this->competitionDataService->getCompetitionDataByRunnerId($otherRunner->getRunnerId());

                    if (empty($competitionData) === false) {
                        $otherRunner->setCompetitionDataList($competitionData);
                    }
                    $testedRunner['duplicates'][] = $otherRunner;
                }
                if (\count($testedRunner['duplicates']) >= 3){
                    break;
                }
            }

            if (empty($testedRunner['duplicates']) === true) {
                $this->runnerService->markRunnerAsProved($runner);
                continue;
            }

            $duplicates[] = $testedRunner;
        }

        return $duplicates;
    }

    /**
     * @param Runner $runner1
     * @param Runner $runner2
     *
     * @return bool
     */
    protected function isDuplicate(Runner $runner1, Runner $runner2): bool
    {
        if ($runner1->getRunnerId()->toString() === $runner2->getRunnerId()->toString()) {
            return false;
        }

        if ($runner1->getAgeGroup()->getGender()->getGender() !== $runner2->getAgeGroup()->getGender()->getGender()) {
            return false;
        }

        if (abs($runner1->getAgeGroup()->getBirthYear()->getBirthYear() - $runner2->getAgeGroup()->getBirthYear()->getBirthYear()) > 2) {
            return false;
        }

        $surnameDiff = levenshtein($runner1->getSurname()->getName(), $runner2->getSurname()->getName());
        if ($surnameDiff === -1 || $surnameDiff > 2) {
            return false;
        }

        $firstnameDiff = levenshtein($runner1->getFirstname()->getName(), $runner2->getFirstname()->getName());
        if ($firstnameDiff === -1 || $firstnameDiff > 3) {
            return false;
        }

        if (($surnameDiff + $firstnameDiff) > 3) {
            return false;
        }

        return true;
    }
}
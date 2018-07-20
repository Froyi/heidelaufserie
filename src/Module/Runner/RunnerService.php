<?php declare(strict_types=1);

namespace Project\Module\Runner;

use Project\Configuration;
use Project\Module\CompetitionData\CompetitionDataService;
use Project\Module\Database\Database;
use Project\Module\GenericValueObject\Id;

/**
 * Class RunnerService
 * @package Project\Module\Runner
 */
class RunnerService
{
    /** @var RunnerFactory $runnerFactory */
    protected $runnerFactory;

    /** @var RunnerRepository $runnerRepository */
    protected $runnerRepository;

    /** @var Configuration $configuration */
    protected $configuration;

    /**
     * RunnerService constructor.
     *
     * @param Database $database
     * @param Configuration $configuration
     */
    public function __construct(Database $database, Configuration $configuration)
    {
        $this->runnerRepository = new RunnerRepository($database);
        $this->runnerFactory = new RunnerFactory();
        $this->configuration = $configuration;
    }

    /**
     * @param Id $runnerId
     *
     * @return null|Runner
     */
    public function getRunnerByRunnerId(Id $runnerId): ?Runner
    {
        $runnerData = $this->runnerRepository->getRunnerByRunnerId($runnerId);

        if (empty($runnerData) === true) {
            return null;
        }

        return $this->runnerFactory->getRunnerByObject($runnerData, $this->configuration);
    }

    /**
     * @param CompetitionDataService $competitionDataService
     *
     * @return array
     */
    public function getAllRunner(CompetitionDataService $competitionDataService = null): array
    {
        $runnerArray = [];

        $runnerData = $this->runnerRepository->getAllRunner();

        foreach ($runnerData as $singleRunnerData) {
            $runner = $this->runnerFactory->getRunnerByObject($singleRunnerData, $this->configuration);
            if ($runner !== null) {
                if ($competitionDataService !== null) {
                    $competitionDataList = $competitionDataService->getCompetitionDataByRunnerId($runner->getRunnerId());
                    $runner->setCompetitionDataList($competitionDataList);
                }

                $runnerArray[] = $runner;
            }
        }

        return $runnerArray;
    }

    /**
     * @param array $parameter
     *
     * @return null|Runner
     */
    public function getRunnerByParameter(array $parameter): ?Runner
    {
        $runnerData = (object)$parameter;

        if (empty($runnerData) === true) {
            return null;
        }

        return $this->runnerFactory->getRunnerByObject($runnerData, $this->configuration);
    }

    /**
     * @param array $parameter
     *
     * @return array
     */
    public function getAllRunnerByParameter(array $parameter): array
    {
        $runnerArray = [];

        foreach ($parameter as $runnerData) {
            $runner = $this->getRunnerByParameter($runnerData);

            if ($runner !== null) {
                $runnerArray[] = $runner;
            }
        }

        return $runnerArray;
    }

    /**
     * @param Runner $runner
     *
     * @return bool
     */
    public function saveRunner(Runner $runner): bool
    {
        return $this->runnerRepository->saveRunner($runner);
    }

    /**
     * @return array
     */
    public function findRunnerDuplicates(): array
    {
        $duplicates = [];

        $allRunner = $this->getAllRunner();
        $toCheckRunner = $allRunner;
        /** @var Runner $runner */
        foreach ($allRunner as $runner) {
            $testedRunner['runner'] = $runner;
            $testedRunner['duplicates'] = [];
            /** @var Runner $otherRunner */
            foreach ($toCheckRunner as $otherRunner) {
                if ($runner === $otherRunner) {
                    continue;
                }

                if ($runner->getSurname()->getName() === $otherRunner->getSurname()->getName() && $runner->getFirstname()->getName() === $otherRunner->getFirstname()->getName()) {
                    $testedRunner['duplicates'][] = $otherRunner;
                    continue;
                }

                if ($runner->getFirstname()->getName() === $otherRunner->getFirstname()->getName() && $runner->getAgeGroup()->getBirthYear()->getBirthYear() === $otherRunner->getAgeGroup()->getBirthYear()->getBirthYear()) {
                    $testedRunner['duplicates'][] = $otherRunner;
                    continue;
                }

                if ($runner->getSurname()->getName() === $otherRunner->getSurname()->getName() && $runner->getAgeGroup()->getBirthYear()->getBirthYear() === $otherRunner->getAgeGroup()->getBirthYear()->getBirthYear()) {
                    $testedRunner['duplicates'][] = $otherRunner;
                    continue;
                }
            }

            if (empty($testedRunner['duplicates']) === false) {
                $duplicates[] = $testedRunner;
            }
        }

        return $duplicates;
    }

    /**
     * @param CompetitionDataService $competitionDataService
     *
     * @return array
     */
    public function findDuplicatesByLevenshtein(CompetitionDataService $competitionDataService): array
    {
        $duplicates = [];

        $allRunner = $this->getAllRunner($competitionDataService);
        $toCheckRunner = $allRunner;
        $duplicatedKeys = [];

        /** @var Runner $runner */
        foreach ($allRunner as $key => $runner) {
            if (\in_array($key, $duplicatedKeys, true)) {
                continue;
            }

            $testedRunner['runner'] = $runner;
            $testedRunner['duplicates'] = [];

            /** @var Runner $otherRunner */
            foreach ($toCheckRunner as $checkKey => $otherRunner) {
                if ($runner === $otherRunner) {
                    continue;
                }

                if ($runner->getAgeGroup()->getGender()->getGender() !== $otherRunner->getAgeGroup()->getGender()->getGender()) {
                    continue;
                }

                if (abs($runner->getAgeGroup()->getBirthYear()->getBirthYear() - $otherRunner->getAgeGroup()->getBirthYear()->getBirthYear()) > 2) {
                    continue;
                }

                $surnameDiff = levenshtein($runner->getSurname()->getName(), $otherRunner->getSurname()->getName());
                if ($surnameDiff === -1 || $surnameDiff > 2) {
                    continue;
                }

                $firstnameDiff = levenshtein($runner->getFirstname()->getName(), $otherRunner->getFirstname()->getName());
                if ($firstnameDiff === -1 || $firstnameDiff > 3) {
                    continue;
                }

                if (($surnameDiff + $firstnameDiff) < 4) {
                    $duplicatedKeys[] = $checkKey;
                    $testedRunner['duplicates'][] = $otherRunner;
                }
            }

            if (empty($testedRunner['duplicates']) === false) {
                $duplicates[] = $testedRunner;
            }
        }

        return $duplicates;
    }

    /**
     * @param Runner $runner
     *
     * @return null|Runner
     */
    public function runnerExists(Runner $runner): ?Runner
    {
        $runnerData = $this->runnerRepository->runnerExists($runner);

        if (empty($runnerData) === true) {
            return null;
        }

        return $this->runnerFactory->getRunnerByObject($runnerData, $this->configuration);
    }
}
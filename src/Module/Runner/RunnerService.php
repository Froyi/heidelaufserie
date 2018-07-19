<?php declare(strict_types=1);

namespace Project\Module\Runner;

use Project\Configuration;
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
     * @return array
     */
    public function getAllRunner(): array
    {
        $runnerArray = [];

        $runnerData = $this->runnerRepository->getAllRunner();

        foreach ($runnerData as $singleRunnerData) {
            $runner = $this->runnerFactory->getRunnerByObject($singleRunnerData, $this->configuration);

            if ($runner !== null) {
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
}
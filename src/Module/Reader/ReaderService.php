<?php declare(strict_types=1);

namespace Project\Module\Reader;


use Project\Module\GenericValueObject\Id;
use Project\Module\Runner\RunnerService;


/**
 * Class ReaderService
 * @package     Project\Module\Reader
 */
class ReaderService
{
    /** @var string RUNNER_FILE */
    public const RUNNER_FILE = 'runner.txt';

    protected const RUNNER_HAS_LEGEND = true;

    public function readRunnerFile(RunnerService $runnerService): array
    {

        $runnerArray = [];

        if (filesize(self::RUNNER_FILE) === 0) {
            return $runnerArray;
        }

        $file = file(self::RUNNER_FILE);
        $count = \count($file);

        $startEntry = 0;
        if (self::RUNNER_HAS_LEGEND === true) {
            $startEntry = 1;
        }

        for ($i = $startEntry; $i < $count; $i++) {
            $runnerData = explode(';', $file[$i]);

            $runnerSingleData = [];
            $runnerSingleData['runnerId'] = Id::generateId()->toString();
            $runnerSingleData['surname'] = $runnerData[0];
            $runnerSingleData['firstname'] = $runnerData[1];
            $runnerSingleData['gender'] = $runnerData[2];
            $runnerSingleData['birthYear'] = (int)$runnerData[3];
            $runnerSingleData['club'] = $runnerData[4];

            $runner = $runnerService->getRunnerByParameter($runnerSingleData);

            if ($runner !== null) {
                $runnerArray[] = $runner;
            }
        }

        return $runnerArray;
    }
}
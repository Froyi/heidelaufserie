<?php
declare (strict_types=1);

namespace Project\Controller;

use Project\Module\Runner\Runner;
use Project\Module\Runner\RunnerService;
use Project\Module\Runner\ShortCode;

/**
 * Class MigrateController
 * @package Project\Controller
 */
class MigrateController extends DefaultController
{
    public function migrateShortCodeAction(): void
    {
        $runnerService = new RunnerService($this->database, $this->configuration);

        $allRunner = $runnerService->getAllCompleteRunner();

        /** @var Runner $runner */
        foreach ($allRunner as $runner) {
            if ($runner->getShortCode() === null || $runner->getShortCode()->getShortCode() === 'AAAA') {
                do {
                    $shortCode = ShortCode::generateShortCode();
                } while ($runnerService->getRunnerByShortCode($shortCode) !== null);

                $runner->setShortCode($shortCode);
                $runnerService->updateRunner($runner);
            }
        }
    }
}
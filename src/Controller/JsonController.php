<?php declare (strict_types=1);

namespace Project\Controller;

use Project\Configuration;
use Project\Module\GenericValueObject\Id;
use Project\Module\Runner\RunnerDuplicateService;
use Project\Module\Runner\RunnerService;
use Project\Utilities\Tools;
use Project\View\JsonModel;

/**
 * Class JsonController
 * @package     Project\Controller
 * @copyright   Copyright (c) 2018 Maik Schößler
 */
class JsonController extends DefaultController
{
    /** @var JsonModel $jsonModel */
    protected $jsonModel;

    /**
     * JsonController constructor.
     *
     * @param Configuration $configuration
     * @param string        $routeName
     */
    public function __construct(Configuration $configuration, string $routeName)
    {
        parent::__construct($configuration, $routeName);

        $this->jsonModel = new JsonModel();
    }

    /**
     *
     */
    public function noDuplicateAction(): void
    {
        $runnerDuplicateService = new RunnerDuplicateService($this->database, $this->configuration);
        $runnerService = new RunnerService($this->database, $this->configuration);

        try {
            $runnerId = Id::fromString(Tools::getValue('runnerId'));

            $runnerWithDuplicates = $runnerDuplicateService->findDuplicateToRunnerByRunnerId($runnerId);

            foreach ($runnerWithDuplicates as $runner) {
                $runnerService->markRunnerAsProved($runner);
            }
        } catch (\InvalidArgumentException $exception) {
            $this->jsonModel->send('error');
        }

        $this->jsonModel->send();
    }
}
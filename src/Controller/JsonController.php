<?php declare (strict_types=1);

namespace Project\Controller;

use Project\Configuration;
use Project\Module\Competition\CompetitionService;
use Project\Module\CompetitionData\CompetitionData;
use Project\Module\CompetitionData\CompetitionDataService;
use Project\Module\GenericValueObject\Date;
use Project\Module\GenericValueObject\Id;
use Project\Module\Runner\RunnerDuplicateService;
use Project\Module\Runner\RunnerService;
use Project\TimeMeasure\TimeMeasureService;
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
     * @param string $routeName
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
            $runner = $runnerService->getRunnerByRunnerId($runnerId);

            if ($runner !== null) {
                $duplicates = $runnerDuplicateService->findDuplicateToRunner($runner);

                $runnerService->markRunnerAsProved($runner);

                foreach ($duplicates as $runner) {
                    $runnerService->markRunnerAsProved($runner);
                }
            } else {
                $this->jsonModel->send('error');
            }

        } catch (\InvalidArgumentException $exception) {
            $this->jsonModel->send('error');
        }

        $this->jsonModel->send();
    }

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function refreshSpeakerDataAction(): void
    {
        /** @var Date $date */
        $date = Date::fromValue('2018-07-27');

        $timeMeasureService = new TimeMeasureService($this->database);
        $runnerService = new RunnerService($this->database, $this->configuration);
        $competitionService = new CompetitionService($this->database);

        $competitionDataService = new CompetitionDataService($this->database);
        $competitionDatas = $competitionDataService->getSpeakerCompetitionData($date, $timeMeasureService, $runnerService, $competitionService);

        if (empty($competitionDatas) === true) {
            $this->jsonModel->send('noRefresh');
        }

        /** @var CompetitionData $competitionData */
        foreach ($competitionDatas as $competitionData) {
            $timeMeasureService->markTimeMeasureListAsShown($competitionData->getTimeMeasureList());
        }

        $this->viewRenderer->addViewConfig('competitionDatas', $competitionDatas);
        $this->jsonModel->addJsonConfig('view', $this->viewRenderer->renderJsonView('module/runnerSpeakerUpdate.twig'));
        $this->jsonModel->send();
    }

    public function generateTimeMeasureDataAction(): void
    {
        /** @var Date $date */
        $date = Date::fromValue('2018-07-27');

        if (Tools::shallWeRefresh(20) === true) {
            $competitionDataService = new CompetitionDataService($this->database);
            $timeMeasureService = new TimeMeasureService($this->database);

            $competitionData = $competitionDataService->getCompetitionDataByDate($date);

            $randomCompetitionKey = array_rand($competitionData);

            $timeMeasure = $timeMeasureService->generateTimeMeasureByData($competitionData[$randomCompetitionKey]);

            if ($timeMeasure !== null) {
                $timeMeasureService->saveTimeMeasure($timeMeasure);
            }

            $this->jsonModel->addJsonConfig('timeMeasure', $timeMeasure);
        }

        $this->jsonModel->send();
    }
}
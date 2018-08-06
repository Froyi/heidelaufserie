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
use SebastianBergmann\Timer\Timer;

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
        Timer::start();
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
        $time = Timer::stop();
        $this->jsonModel->addJsonConfig('time', $time);

        $this->jsonModel->send();
    }

    public function refreshFinishedRunnerAction(): void
    {
        /** @var Date $date */
        $date = Date::fromValue('2018-07-27');

        $timeMeasureService = new TimeMeasureService($this->database);
        $runnerService = new RunnerService($this->database, $this->configuration);
        $competitionService = new CompetitionService($this->database);

        $competitionDataService = new CompetitionDataService($this->database);
        $allCompetitionDatas = $competitionDataService->getCompetitionDataByDate($date, $timeMeasureService, $runnerService, $competitionService);
        $completeRunnerCount = 0;
        $allRunnerCount = \count($allCompetitionDatas);

        /** @var CompetitionData $competitionData */
        foreach ($allCompetitionDatas as $competitionData) {
            if ($competitionData->isLastRound() === true || $competitionData->hasMoreRounds()) {
                $completeRunnerCount++;
            }
        }

        $this->jsonModel->addJsonConfig('allRunnerCount', $allRunnerCount);
        $this->jsonModel->addJsonConfig('completeRunnerCount', $completeRunnerCount);

        $this->jsonModel->send();
    }

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function refreshRankingDataAction(): void
    {
        /** @var Date $date */
        $date = Date::fromValue('2018-07-27');
        $genderConfig = $this->configuration->getEntryByName('ranking');

        $timeMeasureService = new TimeMeasureService($this->database);
        $runnerService = new RunnerService($this->database, $this->configuration);
        $competitionService = new CompetitionService($this->database);
        $competitionDataService = new CompetitionDataService($this->database);

        $womanCompetitionData = $competitionDataService->getSpeakerRankingUpdateByGender($genderConfig['woman'], $date, $timeMeasureService, $runnerService, $competitionService);

        $manCompetitionData = $competitionDataService->getSpeakerRankingUpdateByGender($genderConfig['man'], $date, $timeMeasureService, $runnerService, $competitionService);

        $this->viewRenderer->addViewConfig('womanCompetitionData', $womanCompetitionData);
        $this->viewRenderer->addViewConfig('manCompetitionData', $manCompetitionData);


        $this->jsonModel->addJsonConfig('view', $this->viewRenderer->renderJsonView('module/rankingUpdate.twig'));
        $this->jsonModel->send();
    }

    /**
     * @throws \Exception
     */
    public function generateTimeMeasureDataAction(): void
    {
        /** @var Date $date */
        $date = Date::fromValue('2018-07-27');

        if (Tools::shallWeRefresh(20) === true) {
            $competitionDataService = new CompetitionDataService($this->database);
            $timeMeasureService = new TimeMeasureService($this->database);
            $runnerService = new RunnerService($this->database, $this->configuration);
            $competitionService = new CompetitionService($this->database);

            $competitionDatas = $competitionDataService->getCompetitionDataByDate($date, $timeMeasureService, $runnerService, $competitionService);

            for ($i = 0; $i <= 5; $i++) {
                $randomCompetitionKey = array_rand($competitionDatas);

                /** @var CompetitionData $chosenCompetitionData */
                $chosenCompetitionData = $competitionDatas[$randomCompetitionKey];

                if ($chosenCompetitionData->getActualRound() === 3 || ($chosenCompetitionData->isLastRound() === true && random_int(0, 10) !== 3)) {
                    continue;
                }

                $timeMeasure = $timeMeasureService->generateTimeMeasureByData($chosenCompetitionData);

                if ($timeMeasure !== null) {
                    $timeMeasureService->saveTimeMeasure($timeMeasure);
                }

                $this->jsonModel->addJsonConfig('timeMeasure', $timeMeasure);

                $this->jsonModel->send();
            }
        }

        $this->jsonModel->send();
    }
}
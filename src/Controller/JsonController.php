<?php declare (strict_types=1);

namespace Project\Controller;

use Project\Configuration;
use Project\Module\Competition\CompetitionService;
use Project\Module\CompetitionData\CompetitionData;
use Project\Module\CompetitionData\CompetitionDataService;
use Project\Module\CompetitionStatistic\CompetitionStatisticService;
use Project\Module\GenericValueObject\Date;
use Project\Module\GenericValueObject\Id;
use Project\Module\GenericValueObject\Year;
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
     * @todo Mark time measures with transaction.
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function refreshSpeakerDataAction(): void
    {
        /** @var Date $date */
        //$date = Date::fromValue('today');
        $date = Date::fromValue('2018-07-28');

        $timeMeasureService = new TimeMeasureService($this->database);
        $runnerService = new RunnerService($this->database, $this->configuration);
        $competitionService = new CompetitionService($this->database);
        $competitionStatisticService = new CompetitionStatisticService($this->database);

        $competitionDataService = new CompetitionDataService($this->database);
        $allCompetitionData = $competitionDataService->getSpeakerCompetitionData($date, $timeMeasureService, $runnerService, $competitionService, $competitionStatisticService);


        if (empty($allCompetitionData) === true) {
            $this->jsonModel->send('noRefresh');
        }

        $timeMeasureService->markAllTimeMeasureListsAsShown($allCompetitionData);


        $this->viewRenderer->addViewConfig('allCompetitionData', $allCompetitionData);
        $this->viewRenderer->addViewConfig('year', Year::fromValue(date('Y', strtotime('-1 year')))->getYearShort());

        $this->jsonModel->addJsonConfig('view', $this->viewRenderer->renderJsonView('module/runnerSpeakerUpdate.twig'));

        $this->jsonModel->send();
    }

    /**
     * Counts the runner which are finished.
     * @todo Take the actual date, not a random one.
     */
    public function refreshFinishedRunnerAction(): void
    {
        /** @var Date $date */
        $date = Date::fromValue('2018-07-28');

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
     * @todo Take the actual date, not a random one.
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function refreshRankingDataAction(): void
    {
        /** @var Date $date */
        $date = Date::fromValue('2018-07-28');
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
     * This action is only for testing. In production this one is not used!!!
     * @todo Take the actual date, not a random one.
     * @todo Look why this generating process is too slow.
     * @throws \Exception
     */
    public function generateTimeMeasureDataAction(): void
    {
        /** @var Date $date */
        $date = Date::fromValue('2018-07-28');

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
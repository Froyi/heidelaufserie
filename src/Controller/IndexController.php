<?php
declare (strict_types=1);

namespace Project\Controller;

use Project\Module\Club\ClubService;
use Project\Module\Competition\CompetitionService;
use Project\Module\CompetitionData\CompetitionDataService;
use Project\Module\CompetitionResults\CompetitionResultsService;
use Project\Module\CompetitionResults\CompetitionResultsViewHelper;
use Project\Module\GenericValueObject\Date;
use Project\Module\Runner\RunnerService;

/**
 * Class IndexController
 * @package Project\Controller
 */
class IndexController extends DefaultController
{
    /**
     * index action (standard page)
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Loader
     * @throws \InvalidArgumentException
     * @throws \Twig_Error_Syntax
     */
    public function indexAction(): void
    {
        $this->showStandardPage('home');
    }

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function speakerAction(): void
    {
        $this->showStandardPage('speaker');
    }

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function generateTimeMeasurePageAction(): void
    {
        $this->showStandardPage('timemeasure');
    }

    public function showResultsAction(): void
    {
        $competitionResultsService = new CompetitionResultsService($this->database);
        $clubService = new ClubService($this->database);
        $competitionDataService = new CompetitionDataService($this->database, $clubService);
        $runnerService = new RunnerService($this->database, $this->configuration);
        $competitionService = new CompetitionService($this->database);

        /** @var Date $date */
        $date = Date::fromValue('today');

        $allCompetitionResultsUnsorted = $competitionResultsService->getCompetitionResultsByDate($date, $competitionDataService, $runnerService, $competitionService);

        $competitionResultsViewHelper = new CompetitionResultsViewHelper();

        $competitionResultsSortByAkAndPoints = $competitionResultsViewHelper->sortCompetitionResultsByCompetitionTypeAndPoints($allCompetitionResultsUnsorted);


        $this->viewRenderer->addViewConfig('competitionResultsSortByAkAndPoints', $competitionResultsSortByAkAndPoints);
        $this->viewRenderer->addViewConfig('page', 'competitionResults');
        $this->viewRenderer->renderTemplate();
    }
}
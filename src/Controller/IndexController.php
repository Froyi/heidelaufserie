<?php
declare (strict_types=1);

namespace Project\Controller;

use Project\Module\Competition\Competition;
use Project\Module\Competition\CompetitionService;
use Project\Module\Reader\ReaderService;
use Project\Module\Runner\RunnerService;
use Project\Utilities\Tools;

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

    public function adminAction(): void
    {
        $this->viewRenderer->addViewConfig('competitionData', Competition::POSSIBLE_COMPETITIONS);
        $this->viewRenderer->addViewConfig('page', 'admin');

        $this->viewRenderer->renderTemplate();
    }

    public function importCsvAction(): void
    {
        $runnerService = new RunnerService($this->database, $this->configuration);
        $readerService = new ReaderService();

        $runner = $readerService->readRunnerFile($runnerService);

        foreach ($runner as $singleRunner) {
            $runnerService->saveRunner($singleRunner);
        }
    }

    public function createCompetitionAction(): void
    {
        $competitionService = new CompetitionService($this->database);

        $competition = $competitionService->getCompetitionByParameter($_POST);

        if ($competition === null) {
            $this->notificationService->setError('Der Wettbewerb konnte nicht erstellt werden. Nötige Daten fehlen.');
            header('Location: ' . Tools::getRouteUrl('admin'));
            exit;
        }

        if ($competitionService->saveCompetition($competition) === true) {
            $this->notificationService->setSuccess('Der Wettbewerb wurde erfolgreich erstellt.');
            header('Location: ' . Tools::getRouteUrl('admin'));
            exit;
        }

        $this->notificationService->setError('Der Wettbewerb konnte nicht gespeichert werden.');
        header('Location: ' . Tools::getRouteUrl('admin'));
        exit;
    }
}
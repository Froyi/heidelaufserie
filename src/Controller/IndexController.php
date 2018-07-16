<?php
declare (strict_types=1);

namespace Project\Controller;

use Project\Module\Competition\Competition;
use Project\Module\Competition\CompetitionService;
use Project\Module\Reader\ReaderService;
use Project\Module\Runner\Runner;
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
        $runnerService = new RunnerService($this->database, $this->configuration);

        $allRunner = $runnerService->getAllRunner();

        $this->viewRenderer->addViewConfig('allRunner', $allRunner);
        $this->viewRenderer->addViewConfig('page', 'home');

        $this->viewRenderer->renderTemplate();
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function adminAction(): void
    {
        $competitionService = new CompetitionService($this->database);

        $competitionDays = $competitionService->getAllCompetitionDaysWithCompetitions();

        $this->viewRenderer->addViewConfig('competitionData', Competition::POSSIBLE_COMPETITIONS);
        $this->viewRenderer->addViewConfig('competitionDays', $competitionDays);
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

    public function createCompetitionDayAction(): void
    {
        $competitionService = new CompetitionService($this->database);

        $competitionDay = $competitionService->getCompetitionDayByParameter($_POST);

        if ($competitionDay === null) {
            $this->notificationService->setError('Der Wettbewerb konnte nicht erstellt werden. Nötige Daten fehlen.');
            header('Location: ' . Tools::getRouteUrl('admin'));
            exit;
        }

        if ($competitionService->saveCompetitionDay($competitionDay) === true) {
            if (Tools::getValue('createStandardCompetitions') !== false) {
                $competitionService->createStandardCompetitions($competitionDay->getDate());
            }

            $this->notificationService->setSuccess('Der Wettbewerb wurde erfolgreich erstellt.');
            header('Location: ' . Tools::getRouteUrl('admin'));
            exit;
        }

        $this->notificationService->setError('Der Wettbewerb konnte nicht gespeichert werden.');
        header('Location: ' . Tools::getRouteUrl('admin'));
        exit;
    }

    /**
     * 1. Import runner data and create an array of Runner
     * 2. save all runner in repository
     * 3. save runner startnumber and other data in competition_runner to register them for the run
     */
    public function uploadRunnerFileAction(): void
    {
        $runnerService = new RunnerService($this->database, $this->configuration);
        $readerService = new ReaderService();

        $runner = $readerService->readRunnerFile($runnerService, $_FILES['runnerFile']['tmp_name']);

        if (\count($runner) === 0) {
            $this->notificationService->setError('Die Teilnehmer konnten nicht importiert werden. Entweder ist es die falsche Kodierung, oder die Formatierung stimmt nicht überein, oder die Datei ist leer.');
            header('Location: ' . Tools::getRouteUrl('admin'));
            exit;
        }

        foreach ($runner as $singleRunner) {
            if ($runnerService->saveRunner($singleRunner) === true) {
            }
        }

        $this->notificationService->setSuccess('Die Teilnehmer konnten erfolgreich importiert werden.');
        header('Location: ' . Tools::getRouteUrl('admin'));
        exit;

    }

    public function findDuplicateNamesAction()
    {
        $duplicates = [];
        $runnerService = new RunnerService($this->database, $this->configuration);

        $allRunner = $runnerService->getAllRunner();
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
        $this->viewRenderer->addViewConfig('duplicates', $duplicates);
        $this->viewRenderer->addViewConfig('page', 'duplicates');
        $this->viewRenderer->renderTemplate();
    }

    public function findDuplicatesByLevenshteinAction(): void
    {
        $duplicates = [];
        $runnerService = new RunnerService($this->database, $this->configuration);

        $allRunner = $runnerService->getAllRunner();
        $toCheckRunner = $allRunner;
        $duplicatedKeys = [];

        /** @var Runner $runner */
        foreach ($allRunner as $key => $runner) {
            if (in_array($key, $duplicatedKeys, true)) {
                continue;
            }

            $testedRunner['runner'] = $runner;
            $testedRunner['duplicates'] = [];

            /** @var Runner $otherRunner */
            foreach ($toCheckRunner as $key => $otherRunner) {
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
                if ($firstnameDiff === -1 || $firstnameDiff > 2) {
                    continue;
                }

                if (($surnameDiff + $firstnameDiff) < 3) {
                    $duplicatedKeys[] = $key;
                    $testedRunner['duplicates'][] = $otherRunner;
                }
            }

            if (empty($testedRunner['duplicates']) === false) {
                $duplicates[] = $testedRunner;
            }
        }

        $this->viewRenderer->addViewConfig('duplicates', $duplicates);
        $this->viewRenderer->addViewConfig('page', 'duplicates');
        $this->viewRenderer->renderTemplate();
    }
}
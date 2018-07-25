<?php
declare (strict_types=1);

namespace Project\Controller;

use Project\Module\Competition\CompetitionService;
use Project\Module\CompetitionData\CompetitionDataService;
use Project\Module\GenericValueObject\Date;
use Project\Module\Reader\ReaderService;
use Project\Module\Runner\Runner;
use Project\Module\Runner\RunnerDuplicateService;
use Project\Module\Runner\RunnerService;
use Project\Utilities\Tools;

/**
 * Class AdminController
 * @package Project\Controller
 */
class AdminController extends DefaultController
{
    /**
     * @throws \InvalidArgumentException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function adminAction(): void
    {
        $competitionService = new CompetitionService($this->database);

        $allCompetitionTypes = $competitionService->getAllCompetitionTypes();

        $this->viewRenderer->addViewConfig('allCompetitionTypes', $allCompetitionTypes);
        $this->viewRenderer->addViewConfig('page', 'admin');

        $this->viewRenderer->renderTemplate();
    }

    public function createCompetitionAction(): void
    {
        $competitionService = new CompetitionService($this->database);

        $competitions = $competitionService->getCompetitionsByParameter($_POST);

        if (empty($competitions) === true) {
            $this->notificationService->setError('Die Wettbewerbe konnten nicht erstellt werden. Nötige Daten fehlen.');
            header('Location: ' . Tools::getRouteUrl('admin'));
            exit;
        }

        foreach ($competitions as $competition) {
            if ($competitionService->saveCompetition($competition) === false) {
                $this->notificationService->setError('Die Wettbewerbe konnten nicht komplett gespeichert werden.');
                header('Location: ' . Tools::getRouteUrl('admin'));
                exit;
            }
        }

        $this->notificationService->setSuccess('Die Wettbewerbe wurden erfolgreich erstellt.');
        header('Location: ' . Tools::getRouteUrl('admin'));
        exit;
    }

    public function findDuplicateNamesAction(): void
    {
        $runnerDuplicateService = new RunnerDuplicateService($this->database, $this->configuration);

        $duplicates = $runnerDuplicateService->findNotProvedDuplicates();

        $this->viewRenderer->addViewConfig('duplicates', $duplicates);
        $this->viewRenderer->addViewConfig('page', 'duplicates');

        $this->viewRenderer->renderTemplate();
    }

    /**
     * 1. Import runner data and create an array of Runner
     * 2. save all runner in repository
     * 3. save runner startnumber and other data in competition_runner to register them for the run
     * @throws \InvalidArgumentException
     */
    public function uploadRunnerFileAction(): void
    {
        $errorRunner = [];
        $competitionDataAfterUpload = null;

        $readerService = new ReaderService();
        $runnerService = new RunnerService($this->database, $this->configuration);
        $competitionService = new CompetitionService($this->database);
        $competitionDataService = new CompetitionDataService($this->database);

        $runnerData = $readerService->readRunnerFile($_FILES['runnerFile']['tmp_name']);
        $allRunner = $runnerService->getAllRunnerByParameter($runnerData);

        if (\count($allRunner) === 0) {
            $this->notificationService->setError('Die Teilnehmer konnten nicht importiert werden. Entweder ist es die falsche Kodierung, oder die Formatierung stimmt nicht überein, oder die Datei ist leer.');
            header('Location: ' . Tools::getRouteUrl('admin'));
            exit;
        }

        /** @var Runner $singleRunner */
        foreach ($allRunner as $singleRunner) {
            $runner = $runnerService->runnerExists($singleRunner);

            if ($runner !== null) {
                $runnerData[$singleRunner->getRunnerId()->toString()]['runnerId'] = $runner->getRunnerId()->toString();
                $runnerData[$runner->getRunnerId()->toString()] = $runnerData[$singleRunner->getRunnerId()->toString()];

                unset($runnerData[$singleRunner->getRunnerId()->toString()]);
            } else {
                if ($runnerService->saveRunner($singleRunner) === false) {
                    $errorRunner[] = $singleRunner;
                }
            }
        }

        $date = null;
        if (Tools::getValue('date') !== false) {
            /** @var Date $date */
            $date = Date::fromValue(Tools::getValue('date'));

            $transponderData = $readerService->readTransponderFile();

            $competitions = $competitionService->getCompetitionsByDate($date);
            if (empty($competitions) === false) {
                $competitionDataAfterUpload = $competitionDataService->getCompetitionDataAfterRunnerUpload($runnerData, $competitions, $transponderData);
            }

            if ($competitionDataAfterUpload !== null) {
                foreach ($competitionDataAfterUpload as $competitionData) {
                    $competitionDataService->saveCompetitionData($competitionData);
                }
            }
        }

        $this->notificationService->setSuccess('Die Teilnehmer konnten erfolgreich importiert werden.');
        header('Location: ' . Tools::getRouteUrl('admin'));
        exit;
    }
}
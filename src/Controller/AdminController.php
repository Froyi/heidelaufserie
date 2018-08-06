<?php
declare (strict_types=1);

namespace Project\Controller;

use Project\Configuration;
use Project\Module\Competition\CompetitionService;
use Project\Module\CompetitionData\CompetitionDataService;
use Project\Module\CompetitionData\StartNumber;
use Project\Module\CompetitionResults\CompetitionResultsService;
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
     * AdminController constructor.
     *
     * @param Configuration $configuration
     * @param string $routeName
     */
    public function __construct(Configuration $configuration, string $routeName)
    {
        session_start();

        parent::__construct($configuration, $routeName);
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

        $allCompetitionTypes = $competitionService->getAllCompetitionTypes();

        $this->viewRenderer->addViewConfig('allCompetitionTypes', $allCompetitionTypes);
        $this->viewRenderer->addViewConfig('page', 'admin');

        $this->viewRenderer->renderTemplate();
    }

    /**
     *
     */
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

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function findDuplicateNamesAction(): void
    {
        $competitionDataService = new CompetitionDataService($this->database);
        $runnerDuplicateService = new RunnerDuplicateService($this->database, $this->configuration, $competitionDataService);

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
            } else if ($runnerService->saveRunner($singleRunner) === false) {
                $errorRunner[] = $singleRunner;
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

    /**
     *
     */
    public function uploadCompetitionResultsFileAction(): void
    {
        $readerService = new ReaderService();
        $competitionDataService = new CompetitionDataService($this->database);
        $competitionResultsService = new CompetitionResultsService($this->database);

        $competitionResultsDatas = $readerService->readCompetitionResultsFile($_FILES['resultsFile']['tmp_name']);

        $countData = \count($competitionResultsDatas);
        $savedData = 0;

        foreach ($competitionResultsDatas as $competitionResultsData) {
            /** @var Date $date */
            $date = Date::fromValue($competitionResultsData['date']);
            $startNumber = StartNumber::fromValue($competitionResultsData['startNumber']);

            $competitionData = $competitionDataService->getCompetitionDataByDateAndStartNumber($date, $startNumber);

            if ($competitionData !== null) {
                $competitionResults = $competitionResultsService->getCompetitionResultsByUploadData($competitionResultsData, $competitionData);

                if ($competitionResults !== null && $competitionResultsService->saveCompetitionResults($competitionResults) === true) {
                    $savedData++;
                }
            }
        }

        if ($savedData !== $countData) {
            $this->notificationService->setError('Es wurden nicht alle Daten gespeichert: ' . $savedData . ' / ' . $countData);
            header('Location: ' . Tools::getRouteUrl('admin'));
            exit;
        }

        $this->notificationService->setSuccess('Die Results konnten erfolgreich importiert werden.');
        header('Location: ' . Tools::getRouteUrl('admin'));
        exit;
    }
}
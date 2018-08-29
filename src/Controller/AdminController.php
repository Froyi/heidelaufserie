<?php
declare (strict_types=1);

namespace Project\Controller;

use Project\Configuration;
use Project\Module\Club\ClubService;
use Project\Module\Competition\Competition;
use Project\Module\Competition\CompetitionService;
use Project\Module\Competition\StartTimeGroup;
use Project\Module\CompetitionData\CompetitionDataService;
use Project\Module\CompetitionData\StartNumber;
use Project\Module\CompetitionResults\CompetitionResultsService;
use Project\Module\CompetitionStatistic\CompetitionStatisticService;
use Project\Module\GenericValueObject\Date;
use Project\Module\GenericValueObject\Datetime;
use Project\Module\GenericValueObject\Year;
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

        $this->viewRenderer->addViewConfig('isAdmin', 'true');
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
        $startTimeGroups = $competitionService->getAllStartTimeGroups();

        $actualStartTimes = $competitionService->getStartTimesByDate($this->getToday());

        $this->viewRenderer->addViewConfig('startTimeGroups', $startTimeGroups);
        $this->viewRenderer->addViewConfig('actualStartTimes', $actualStartTimes);
        $this->viewRenderer->addViewConfig('page', 'admin');

        $this->viewRenderer->renderTemplate();
    }
    
    public function setStartTimeAction(): void
    {
        if (Tools::getValue('startTimeGroup') === false) {
            $this->notificationService->setError('Die Startzeit konnte nicht gespeichert werden, da keine Gruppe angegeben wurde.');
            header('Location: ' . Tools::getRouteUrl('admin'));
            exit;
        }

        $startTimeGroup = StartTimeGroup::fromValue(Tools::getValue('startTimeGroup'));
        /** @var Date $date */
        $date = Date::fromValue('today');

        $competitionService = new CompetitionService($this->database);
        $competitions = $competitionService->getCompetitionsByDateAndStartTimeGroup($date, $startTimeGroup);

        /** @var Datetime $startTime */
        $startTime = Datetime::fromValue('now');

        /** @var Competition $competition */
        foreach ($competitions as $competition) {
            $competition->setStartTime($startTime);
        }

        if ($competitionService->updateAllCompetitions($competitions) === true) {
            $this->notificationService->setSuccess('Die Startzeit konnte erfolgreich für alle Wettbewerbe gestartet werden.');
            header('Location: ' . Tools::getRouteUrl('admin'));
            exit;
        }

        $this->notificationService->setError('Die Startzeit konnte nicht gespeichert werden, es gab ein Datenbankfehler.');
        header('Location: ' . Tools::getRouteUrl('admin'));
        exit;
    }

    public function competitionDayAction(): void
    {
        $competitionService = new CompetitionService($this->database);

        $allCompetitionTypes = $competitionService->getCompetitionTypes();

        $this->viewRenderer->addViewConfig('allCompetitionTypes', $allCompetitionTypes);
        $this->viewRenderer->addViewConfig('page', 'competitionDay');

        $this->viewRenderer->renderTemplate();
    }

    public function competitionsAction(): void
    {
        $competitionService = new CompetitionService($this->database);

        $competitions = $competitionService->getAllCompetitions();
        $allCompetitionTypes = $competitionService->getCompetitionTypes();

        $this->viewRenderer->addViewConfig('allCompetitionTypes', $allCompetitionTypes);
        $this->viewRenderer->addViewConfig('competitions', $competitions);
        $this->viewRenderer->addViewConfig('page', 'competitions');

        $this->viewRenderer->renderTemplate();
    }

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function findDuplicateNamesAction(): void
    {
        $clubService = new ClubService($this->database);
        $competitionDataService = new CompetitionDataService($this->database, $clubService);
        $runnerDuplicateService = new RunnerDuplicateService($this->database, $this->configuration, $competitionDataService);

        $duplicates = $runnerDuplicateService->findNotProvedDuplicates();

        $this->viewRenderer->addViewConfig('duplicates', $duplicates);
        $this->viewRenderer->addViewConfig('page', 'duplicates');

        $this->viewRenderer->renderTemplate();
    }

    /**
     * Generate the statistic table [competitionStatistic] by given year.
     * This year is a $_GET parameter from the query.
     * @todo Give them an output (maybe statistics) or an header to an other site.
     * @todo [There could be a function for generating all possible / multiple years.]
     */
    public function generateStatisticsByYearAction(): void
    {
        $runnerService = new RunnerService($this->database, $this->configuration);
        try {
            $year = Year::fromValue(Tools::getValue('year'));
        } catch (\InvalidArgumentException $exception) {
            $this->notificationService->setError('Die Statistik wurde nicht erfolgreich erstellt. Das Jahr passt leider nicht.');
            header('Location: ' . Tools::getRouteUrl('admin'));
            exit;
        }

        $competitionStatisticService = new CompetitionStatisticService($this->database);
        $statistics = $competitionStatisticService->generateStatisticsByYear($year, $runnerService);

        // first remove old entries
        $competitionStatisticService->deleteOldStatisticsByYear($year);

        // then save new ones
        $competitionStatisticService->saveAllCompetitionStatistic($statistics);

        $this->viewRenderer->addViewConfig('statistics', $statistics);
        $this->viewRenderer->addViewConfig('page', 'generateStatistics');
        $this->viewRenderer->renderTemplate();
    }

    /**
     * Old competition results can be uploaded by this action.
     * @todo Optimize the error output. Do not only show how much. Show what exactly.
     */
    public function uploadCompetitionResultsFileAction(): void
    {
        $readerService = new ReaderService();
        $clubService = new ClubService($this->database);
        $competitionDataService = new CompetitionDataService($this->database, $clubService);
        $competitionService = new CompetitionService($this->database);
        $competitionResultsService = new CompetitionResultsService($this->database);

        $competitionResultsDatas = $readerService->readCompetitionResultsFile($_FILES['resultsFile']['tmp_name']);

        $countData = \count($competitionResultsDatas);
        $savedData = 0;
        $falsePoints = '';

        foreach ($competitionResultsDatas as $competitionResultsData) {
            /** @var Date $date */
            $date = Date::fromValue($competitionResultsData['date']);
            $startNumber = StartNumber::fromValue($competitionResultsData['startNumber']);

            $competitionData = $competitionDataService->getCompetitionDataByDateAndStartNumber($date, $startNumber, null, null, $competitionService);

            if ($competitionData !== null) {
                $competitionResults = $competitionResultsService->getCompetitionResultsByUploadData($competitionResultsData, $competitionData);

                if ($competitionResults !== null && $competitionResultsService->saveCompetitionResults($competitionResults) === true) {
                    $competition = $competitionData->getCompetition();
                    if ($competition !== null && $competitionResultsService->provePoints($competitionResults, $competition) === false) {
                        if ($competitionResults->getPoints() !== null) {
                            $falsePoints .= 'PunkteDatei: ' . $competitionResults->getPoints()->getPoints() . ' PunkteGeneriert: ' . $competitionResultsService->getPointsByResult($competitionResults, $competition);
                        } else {
                            $falsePoints .= 'PunkteDatei: null!';
                        }
                    }

                    $savedData++;
                }
            }
        }

        if ($savedData !== $countData) {
            $this->notificationService->setError('Es wurden nicht alle Daten gespeichert: ' . $savedData . ' / ' . $countData);
            header('Location: ' . Tools::getRouteUrl('admin'));
            exit;
        }

        if (empty($falsePoints) === true) {
            $falsePoints = 'keine';
        }

        $this->notificationService->setSuccess('Die Results konnten erfolgreich importiert werden. Es gab folgende Fehler: ' . $falsePoints);
        header('Location: ' . Tools::getRouteUrl('admin'));
        exit;
    }

    /**
     * 1. Import runner data and create an array of Runner
     * 2. save all runner in repository
     * 3. save runner startnumber and other data in competitionData to register them for the run
     * @todo Use errorRunner array to output the runner which could not be saved.
     * @throws \InvalidArgumentException
     */
    public function uploadRunnerFileAction(): void
    {
        $errorRunner = [];
        $competitionDataAfterUpload = null;

        $readerService = new ReaderService();
        $clubService = new ClubService($this->database);
        $runnerService = new RunnerService($this->database, $this->configuration);
        $competitionService = new CompetitionService($this->database);
        $competitionDataService = new CompetitionDataService($this->database, $clubService);

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
            $competitionDataService->saveAllCompetitionData($competitionDataAfterUpload);
        }

        $this->notificationService->setSuccess('Die Teilnehmer konnten erfolgreich importiert werden.');
        header('Location: ' . Tools::getRouteUrl('admin'));
        exit;
    }

    /**
     * The user want to add a new competition.
     * This action creates a new one by fully entered formular data.
     */
    public function createCompetitionAction(): void
    {
        $competitionService = new CompetitionService($this->database);

        $competitions = $competitionService->getCompetitionsByParameter($_POST);

        if (empty($competitions) === true) {
            $this->notificationService->setError('Die Wettbewerbe konnten nicht erstellt werden. Nötige Daten fehlen.');
            header('Location: ' . Tools::getRouteUrl('competitions'));
            exit;
        }

        foreach ($competitions as $competition) {
            if ($competitionService->saveCompetition($competition) === false) {
                $this->notificationService->setError('Die Wettbewerbe konnten nicht komplett gespeichert werden.');
                header('Location: ' . Tools::getRouteUrl('competitions'));
                exit;
            }
        }

        $this->notificationService->setSuccess('Die Wettbewerbe wurden erfolgreich erstellt.');
        header('Location: ' . Tools::getRouteUrl('competitions'));
        exit;
    }
}
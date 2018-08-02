<?php
declare (strict_types=1);

namespace Project\Controller;

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
        $runnerService = new RunnerService($this->database, $this->configuration);
        $runner = $runnerService->getAllCompleteRunner();
        $this->viewRenderer->addViewConfig('page', 'home');
        $this->viewRenderer->addViewConfig('allRunner', $runner);
        $this->viewRenderer->renderTemplate();
    }

    public function speakerAction(): void
    {
        $this->showStandardPage('speaker');
    }

    public function generateTimeMeasurePageAction(): void
    {
        $this->showStandardPage('timemeasure');
    }
}
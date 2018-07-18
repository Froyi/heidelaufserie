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

        $allRunner = $runnerService->getAllRunner();

        $this->viewRenderer->addViewConfig('allRunner', $allRunner);
        $this->viewRenderer->addViewConfig('page', 'home');

        $this->viewRenderer->renderTemplate();
    }
}
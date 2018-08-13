<?php
declare (strict_types=1);

namespace Project\Controller;

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
}
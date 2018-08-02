<?php
declare(strict_types=1);

namespace Project\Module\Tracking\PreviewTime;

use Project\Module\DefaultModel;
use Project\Module\GenericValueObject\Date;
use Project\Module\GenericValueObject\Distance;
use Project\Module\GenericValueObject\Id;
use Project\Module\Tracking\RoundTime;

/**
 * Class PreviewTime
 * @package Project\Module\Tracking\PreviewTime
 */
class PreviewTime extends DefaultModel
{
    /** @var Id $previewTimeId */
    protected $previewTimeId;

    /** @var Id $competitionId */
    protected $competitionId;

    /** @var Date $start */
    protected $start;

    /** @var Date $end */
    protected $end;

    /** @var RoundTime $roundTime */
    protected $roundTime;

    /** @var Distance $distance */
    protected $distance;

    /**
     * @return Id
     */
    public function getPreviewTimeId(): Id
    {
        return $this->previewTimeId;
    }

    /**
     * @return Id
     */
    public function getCompetitionId(): Id
    {
        return $this->competitionId;
    }

    /**
     * @return Date
     */
    public function getStart(): Date
    {
        return $this->start;
    }

    /**
     * @return Date
     */
    public function getEnd(): Date
    {
        return $this->end;
    }

    /**
     * @return RoundTime
     */
    public function getRoundTime(): RoundTime
    {
        return $this->roundTime;
    }

    /**
     * @return Distance
     */
    public function getDistance(): Distance
    {
        return $this->distance;
    }

    /**
     * PreviewTime constructor.
     *
     * @param Id $previewTimeId
     * @param Id $competitionId
     * @param Date $start
     * @param Date $end
     * @param Distance $distance
     */
    public function __construct(Id $previewTimeId, Id $competitionId, Date $start, Date $end, Distance $distance)
    {
        parent::__construct();

        $this->previewTimeId = $previewTimeId;
        $this->competitionId = $competitionId;
        $this->start = $start;
        $this->end = $end;
        $this->distance = $distance;
    }

    /**
     * @param RoundTime $roundTime
     */
    public function setRoundTime(RoundTime $roundTime): void
    {
        $this->roundTime = $roundTime;
    }
}
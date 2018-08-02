<?php
declare(strict_types=1);

namespace Project\TimeMeasure;

use Project\Module\CompetitionData\TransponderNumber;
use Project\Module\DefaultModel;
use Project\Module\GenericValueObject\Datetime;
use Project\Module\GenericValueObject\Id;

/**
 * Class TimeMeasure
 * @package Project\TimeMeasure
 */
class TimeMeasure extends DefaultModel
{
    /** @var Id $timeMeasureId */
    protected $timeMeasureId;

    /** @var TransponderNumber $transponderNumber */
    protected $transponderNumber;

    /** @var Datetime $timestamp */
    protected $timestamp;

    /** @var bool $shown */
    protected $shown;

    /**
     * TimeMeasure constructor.
     *
     * @param Id $timeMeasureId
     * @param TransponderNumber $transponderNumber
     * @param Datetime $timestamp
     * @param bool $shown
     */
    public function __construct(Id $timeMeasureId, TransponderNumber $transponderNumber, Datetime $timestamp, bool $shown)
    {
        parent::__construct();

        $this->timeMeasureId = $timeMeasureId;
        $this->transponderNumber = $transponderNumber;
        $this->timestamp = $timestamp;
        $this->shown = $shown;
    }

    /**
     * @return Id
     */
    public function getTimeMeasureId(): Id
    {
        return $this->timeMeasureId;
    }

    /**
     * @return TransponderNumber
     */
    public function getTransponderNumber(): TransponderNumber
    {
        return $this->transponderNumber;
    }

    /**
     * @return Datetime
     */
    public function getTimestamp(): Datetime
    {
        return $this->timestamp;
    }

    /**
     * @return bool
     */
    public function isShown(): bool
    {
        return $this->shown;
    }

    /**
     * @param bool $shown
     */
    public function setShown(bool $shown): void
    {
        $this->shown = $shown;
    }
}
<?php
declare(strict_types=1);

namespace Project\Module\FinishMeasure;

use Project\Module\CompetitionData\MeasureInterface;
use Project\Module\CompetitionData\TransponderNumber;
use Project\Module\GenericValueObject\Datetime;
use Project\Module\GenericValueObject\Id;

/**
 * Class FinishMeasure
 * @package Project\Module\FinishMeasure
 */
class FinishMeasure implements MeasureInterface
{
    /** @var Id $finishMeasureId */
    protected $finishMeasureId;

    /** @var TransponderNumber $transponderNumber */
    protected $transponderNumber;

    /** @var Datetime $timestamp */
    protected $timestamp;

    /**
     * FinishMeasure constructor.
     *
     * @param Id $finishMeasureId
     * @param TransponderNumber $transponderNumber
     * @param Datetime $timestamp
     */
    public function __construct(Id $finishMeasureId, TransponderNumber $transponderNumber, Datetime $timestamp)
    {
        $this->finishMeasureId = $finishMeasureId;
        $this->transponderNumber = $transponderNumber;
        $this->timestamp = $timestamp;
    }

    /**
     * @return Id
     */
    public function getFinishMeasureId(): Id
    {
        return $this->finishMeasureId;
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
}
<?php declare(strict_types=1);

namespace Project\Module\Competition;

use Project\Module\DefaultModel;
use Project\Module\GenericValueObject\Date;
use Project\Module\GenericValueObject\Datetime;
use Project\Module\GenericValueObject\Id;
use Project\Module\GenericValueObject\Title;

/**
 * Class Competition
 * @package     Project\Module\Competition
 */
class Competition extends DefaultModel
{
    /** @var Id $competitionId */
    protected $competitionId;

    /** @var CompetitionType $competitionType */
    protected $competitionType;

    /** @var Title $title */
    protected $title;

    /** @var Date $date */
    protected $date;

    /** @var Datetime $startTime */
    protected $startTime;

    /**
     * Competition constructor.
     *
     * @param Id $competitionId
     * @param CompetitionType $competitionType
     * @param Title $title
     * @param Date $date
     * @param Datetime $startTime
     */
    public function __construct(Id $competitionId, CompetitionType $competitionType, Title $title, Date $date, Datetime $startTime)
    {
        $this->competitionId = $competitionId;
        $this->competitionType = $competitionType;
        $this->title = $title;
        $this->date = $date;
        $this->startTime = $startTime;
    }

    /**
     * @return Id
     */
    public function getCompetitionId(): Id
    {
        return $this->competitionId;
    }

    /**
     * @return CompetitionType
     */
    public function getCompetitionType(): CompetitionType
    {
        return $this->competitionType;
    }

    /**
     * @return Title
     */
    public function getTitle(): Title
    {
        return $this->title;
    }

    /**
     * @return Date
     */
    public function getDate(): Date
    {
        return $this->date;
    }

    /**
     * @return Datetime
     */
    public function getStartTime(): Datetime
    {
        return $this->startTime;
    }
}
<?php declare(strict_types=1);

namespace Project\Module\Competition;

use Project\Module\DefaultModel;
use Project\Module\GenericValueObject\Date;
use Project\Module\GenericValueObject\Id;
use Project\Module\GenericValueObject\Title;

/**
 * Class CompetitionDay
 * @package Project\Module\Competition
 */
class CompetitionDay extends DefaultModel
{
    /** @var Id $competitionDayId */
    protected $competitionDayId;

    /** @var Title $title */
    protected $title;

    /** @var Date $date */
    protected $date;

    /** @var array $competitionList */
    protected $competitionList;

    /**
     * @param array $competitionList
     */
    public function setCompetitionList(array $competitionList): void
    {
        $this->competitionList = $competitionList;
    }

    /**
     * CompetitionDay constructor.
     *
     * @param Id $competitionDayId
     * @param Title $title
     * @param Date $date
     */
    public function __construct(Id $competitionDayId, Title $title, Date $date)
    {
        parent::__construct();

        $this->competitionDayId = $competitionDayId;
        $this->title = $title;
        $this->date = $date;
    }

    /**
     * @return Id
     */
    public function getCompetitionDayId(): Id
    {
        return $this->competitionDayId;
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
     * @return array
     */
    public function getCompetitionList(): array
    {
        return $this->competitionList;
    }
}
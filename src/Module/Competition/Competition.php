<?php declare(strict_types=1);

namespace Project\Module\Competition;

use Project\Module\DefaultModel;
use Project\Module\GenericValueObject\Date;
use Project\Module\GenericValueObject\Id;

/**
 * Class Competition
 * @package     Project\Module\Competition
 */
class Competition extends DefaultModel
{
    /** @var array POSSIBLE_COMPETITIONS */
    public const POSSIBLE_COMPETITIONS = [
        1 => '5km Laufen',
        2 => '10km Laufen',
        3 => '15km Laufen',
        4 => '5km Nordic Walking',
        5 => '10km Nordic Walking',
    ];

    /** @var Id $competitionId */
    protected $competitionId;

    /** @var Date $date */
    protected $date;

    /** @var int $competitionNumber */
    protected $competitionNumber;

    /** @var string $competition */
    protected $competitionName;

    /**
     * Competition constructor.
     *
     * @param Id $competitionId
     * @param Date $date
     * @param int $competitionNumber
     */
    public function __construct(Id $competitionId, Date $date, int $competitionNumber)
    {
        parent::__construct();

        $this->competitionId = $competitionId;
        $this->date = $date;
        $this->competitionNumber = $competitionNumber;

        $this->competitionName = self::POSSIBLE_COMPETITIONS[$this->competitionNumber];
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
    public function getDate(): Date
    {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getCompetitionNumber(): int
    {
        return $this->competitionNumber;
    }

    /**
     * @return string
     */
    public function getCompetitionName(): string
    {
        return $this->competitionName;
    }

    /**
     * @return array
     */
    public static function getStandardCompetitions(): array
    {
        return self::POSSIBLE_COMPETITIONS;
    }
}
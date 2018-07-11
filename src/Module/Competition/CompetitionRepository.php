<?php declare(strict_types=1);

namespace Project\Module\Competition;

use Project\Module\Database\Database;
use Project\Module\Database\Query;
use Project\Module\GenericValueObject\Date;
use Project\Module\GenericValueObject\Id;

/**
 * Class CompetitionRepository
 * @package Project\Module\Competition
 */
class CompetitionRepository
{
    /** @var string TABLE */
    protected const TABLE = 'competition';

    /** @var string TABLE_COMPETITION_DAY */
    protected const TABLE_COMPETITION_DAY = 'competitionDay';

    /** @var string ORDER_BY_COMPETITION_DAY */
    protected const ORDER_BY_COMPETITION_DAY = 'date';

    /** @var Database $database */
    protected $database;

    /**
     * CompetitionRepository constructor.
     *
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @param Competition $competition
     *
     * @return bool
     */
    public function saveCompetition(Competition $competition): bool
    {
        if ($this->getCompetitionByCompetitionId($competition->getCompetitionId()) === false) {
            $query = $this->database->getNewInsertQuery(self::TABLE);
            $query->insert('competitionId', $competition->getCompetitionId()->toString());
            $query->insert('competitionNumber', $competition->getCompetitionNumber());
            $query->insert('date', $competition->getDate()->toString());

            return $this->database->execute($query);
        }

        return false;
    }

    /**
     * @param CompetitionDay $competitionDay
     *
     * @return bool
     */
    public function saveCompetitionDay(CompetitionDay $competitionDay): bool
    {
        if ($this->getCompetitionDayByCompetitionDayId($competitionDay->getCompetitionDayId()) === false) {
            $query = $this->database->getNewInsertQuery(self::TABLE_COMPETITION_DAY);
            $query->insert('competitionDayId', $competitionDay->getCompetitionDayId()->toString());
            $query->insert('title', $competitionDay->getTitle()->getTitle());
            $query->insert('date', $competitionDay->getDate()->toString());

            return $this->database->execute($query);
        }

        return false;
    }

    /**
     * @param Id $competitionId
     *
     * @return mixed
     */
    public function getCompetitionByCompetitionId(Id $competitionId)
    {
        $query = $this->database->getNewSelectQuery(self::TABLE);
        $query->where('competitionId', '=', $competitionId->toString());

        return $this->database->fetch($query);
    }

    /**
     * @param Date $date
     *
     * @return array
     */
    public function getCompetitionsByDate(Date $date): array
    {
        $query = $this->database->getNewSelectQuery(self::TABLE);
        $query->where('date', '=', $date->toString());

        return $this->database->fetchAll($query);
    }

    /**
     * @param Id $competitionDayId
     *
     * @return mixed
     */
    public function getCompetitionDayByCompetitionDayId(Id $competitionDayId)
    {
        $query = $this->database->getNewSelectQuery(self::TABLE_COMPETITION_DAY);
        $query->where('competitionDayId', '=', $competitionDayId->toString());

        return $this->database->fetch($query);
    }

    /**
     * @param Date $date
     *
     * @return mixed
     */
    public function getCompetitionDayByDate(Date $date)
    {
        $query = $this->database->getNewSelectQuery(self::TABLE_COMPETITION_DAY);
        $query->where('date', '=', $date->toString());

        return $this->database->fetch($query);
    }

    /**
     * @return array
     */
    public function getAllCompetitionDays(): array
    {
        $query = $this->database->getNewSelectQuery(self::TABLE_COMPETITION_DAY);
        $query->orderBy(self::ORDER_BY_COMPETITION_DAY, Query::DESC);

        return $this->database->fetchAll($query);
    }
}
<?php
declare(strict_types=1);

namespace Project\Module\Competition;

use Project\Module\Database\Query;
use Project\Module\DefaultRepository;
use Project\Module\GenericValueObject\Date;
use Project\Module\GenericValueObject\Id;

/**
 * Class CompetitionRepository
 * @package Project\Module\Competition
 */
class CompetitionRepository extends DefaultRepository
{
    /** @var string TABLE */
    protected const TABLE = 'competition';

    /** @var string TABLE_COMPETITION_TYPE */
    protected const TABLE_COMPETITION_TYPE = 'competitionType';

    /**
     * @return array
     */
    public function getAllCompetitionTypes(): array
    {
        $query = $this->database->getNewSelectQuery(self::TABLE_COMPETITION_TYPE);
        $query->orderBy('competitionTypeId', Query::ASC);

        return $this->database->fetchAll($query);
    }

    /**
     * @return array
     */
    public function getAllCompetitions(): array
    {
        $query = $this->database->getNewSelectQuery(self::TABLE);
        $query->orderBy('date', Query::ASC);

        return $this->database->fetchAll($query);
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
            $query->insert('competitionTypeId', $competition->getCompetitionType()->getCompetitionTypeId());
            $query->insert('title', $competition->getTitle()->getTitle());
            $query->insert('date', $competition->getDate()->toString());
            $query->insert('startTime', $competition->getStartTime()->toString());

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
     * @param int $competitionTypeId
     *
     * @return mixed
     */
    public function getCompetitionTypeByCompetitionTypeId(int $competitionTypeId)
    {
        $query = $this->database->getNewSelectQuery(self::TABLE_COMPETITION_TYPE);
        $query->where('competitionTypeId', '=', $competitionTypeId);

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
}
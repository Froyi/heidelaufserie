<?php declare(strict_types=1);

namespace Project\Module\Competition;

use Project\Module\Database\Database;
use Project\Module\GenericValueObject\Id;

/**
 * Class CompetitionRepository
 * @package Project\Module\Competition
 */
class CompetitionRepository
{
    /** @var string TABLE */
    protected const TABLE = 'competition';

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
}
<?php declare(strict_types=1);

namespace Project\Module\Runner;

use Project\Module\Database\Database;
use Project\Module\GenericValueObject\Id;

/**
 * Class RunnerRepository
 * @package Project\Module\Runner
 */
class RunnerRepository
{
    /** @var string string RUNNER */
    protected const TABLE = 'runner';

    /** @var Database $database */
    protected $database;

    /**
     * RunnerRepository constructor.
     *
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @param Id $runnerId
     *
     * @return mixed
     */
    public function getRunnerByRunnerId(Id $runnerId)
    {
        $query = $this->database->getNewSelectQuery(self::TABLE);
        $query->where('runnerId', '=', $runnerId->toString());

        return $this->database->fetch($query);
    }

    /**
     * @param Runner $runner
     *
     * @return bool
     */
    public function runnerExists(Runner $runner): bool
    {
        $query = $this->database->getNewSelectQuery(self::TABLE);
        $query->where('surname', '=', $runner->getSurname()->getName());
        $query->andWhere('firstname', '=', $runner->getFirstname()->getName());
        $query->andWhere('birthYear', '=', $runner->getAgeGroup()->getBirthYear()->getBirthYear());

        return empty($this->database->fetch($query)) === false;
    }

    /**
     * @param Runner $runner
     *
     * @return bool
     */
    public function saveRunner(Runner $runner): bool
    {
        if ($this->runnerExists($runner) === true) {
            return true;
        }

        $query = $this->database->getNewInsertQuery(self::TABLE);
        $query->insert('runnerId', $runner->getRunnerId()->toString());
        $query->insert('surname', $runner->getSurname()->getName());
        $query->insert('firstname', $runner->getFirstname()->getName());
        $query->insert('birthYear', $runner->getAgeGroup()->getBirthYear()->getBirthYear());
        $query->insert('gender', $runner->getAgeGroup()->getGender()->getGender());

        if ($runner->getClub() !== null) {
            $query->insert('club', $runner->getClub()->getClub());
        }

        return $this->database->execute($query);
    }

    /**
     * @return array
     */
    public function getAllRunner(): array
    {
        $query = $this->database->getNewSelectQuery(self::TABLE);

        return $this->database->fetchAll($query);
    }
}
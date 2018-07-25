<?php
declare(strict_types=1);

namespace Project\Module\Runner;

use Project\Module\DefaultRepository;
use Project\Module\GenericValueObject\Id;

/**
 * Class RunnerRepository
 * @package Project\Module\Runner
 */
class RunnerRepository extends DefaultRepository
{
    /** @var string string RUNNER */
    protected const TABLE = 'runner';

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
     * @return mixed
     */
    public function runnerExists(Runner $runner)
    {
        $query = $this->database->getNewSelectQuery(self::TABLE);
        $query->where('surname', '=', $runner->getSurname()->getName());
        $query->andWhere('firstname', '=', $runner->getFirstname()->getName());
        $query->andWhere('birthYear', '=', $runner->getAgeGroup()->getBirthYear()->getBirthYear());

        return $this->database->fetch($query);
    }

    /**
     * @param Runner $runner
     *
     * @return bool
     */
    public function saveRunner(Runner $runner): bool
    {
        if (empty($this->runnerExists($runner)) === false) {
            return true;
        }

        $query = $this->database->getNewInsertQuery(self::TABLE);
        $query->insert('runnerId', $runner->getRunnerId()->toString());
        $query->insert('surname', $runner->getSurname()->getName());
        $query->insert('firstname', $runner->getFirstname()->getName());
        $query->insert('birthYear', $runner->getAgeGroup()->getBirthYear()->getBirthYear());
        $query->insert('gender', $runner->getAgeGroup()->getGender()->getGender());
        $query->insert('proved', $runner->isProved());

        return $this->database->execute($query);
    }

    public function updateRunner(Runner $runner): bool
    {
        $query = $this->database->getNewUpdateQuery(self::TABLE);
        $query->set('runnerId', $runner->getRunnerId()->toString());
        $query->set('surname', $runner->getSurname()->getName());
        $query->set('firstname', $runner->getFirstname()->getName());
        $query->set('birthYear', $runner->getAgeGroup()->getBirthYear()->getBirthYear());
        $query->set('gender', $runner->getAgeGroup()->getGender()->getGender());
        $query->set('proved', $runner->isProved());
        $query->where('runnerId', '=', $runner->getRunnerId()->toString());

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
<?php
declare (strict_types=1);


namespace Project\Module\CompetitionResults;

use Project\Module\DefaultRepository;
use Project\Module\GenericValueObject\Id;

class CompetitionResultsRepository extends DefaultRepository
{
    protected const TABLE = 'competitionResults';

    public function getCompetitionResultsByCompetitionDataId(Id $competitionDataId)
    {
        $query = $this->database->getNewSelectQuery(self::TABLE);
        $query->where('competitionDataId', '=', $competitionDataId->toString());

        return $this->database->fetch($query);
    }
}
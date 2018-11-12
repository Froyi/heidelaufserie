<?php
declare(strict_types=1);

/**
 * ClubRepository.php
 * @author      Maik Schößler <ms2002@onlinehome.de>
 * @since       24.08.2018
 */

namespace Project\Module\Club;

use Project\Module\DefaultRepository;
use Project\Module\GenericValueObject\Id;

/**
 * Class ClubRepository
 * @package Project\Module\Club
 */
class ClubRepository extends DefaultRepository
{
    /** @var string TABLE */
    protected const TABLE = 'club';

    /**
     * @param Id $clubId
     *
     * @return mixed
     */
    public function getClubByClubId(Id $clubId)
    {
        $query = $this->database->getNewSelectQuery(self::TABLE);
        $query->where('clubId', '=', $clubId->toString());

        return $this->database->fetch($query);
    }

    /**
     * @param Club $club
     *
     * @return bool
     */
    public function saveClub(Club $club): bool
    {
            $query = $this->database->getNewInsertQuery(self::TABLE);

            $query->insert('clubId', $club->getClubId()->toString());
            $query->insert('clubName', $club->getClubName()->getClubName());
            $query->insert('prooved', $club->isProoved());

            return $this->database->execute($query);
    }

    /**
     * @param Club $club
     *
     * @return bool
     */
    public function saveOrUpdateClub(Club $club): bool
    {
        if (empty($this->getClubByClubId($club->getClubId())) === false) {
            return $this->updateClub($club);
        }

        return $this->saveClub($club);
    }

    /**
     * @param ClubName $clubName
     *
     * @return mixed
     */
    public function getClubByClubName(ClubName $clubName)
    {
        $query = $this->database->getNewSelectQuery(self::TABLE);
        $query->where('clubName', '=', $clubName->getClubName());

        return $this->database->fetch($query);
    }

    /**
     * @return array
     */
    public function getAllClubs(): array
    {
        $query = $this->database->getNewSelectQuery(self::TABLE);

        return $this->database->fetchAll($query);
    }

    /**
     * @param ClubName $clubName
     *
     * @return array
     */
    public function getClubsByClubName(ClubName $clubName): array
    {
        $query = $this->database->getNewSelectQuery(self::TABLE);
        $query->where('clubName', '=', $clubName->getClubName());

        return $this->database->fetchAll($query);
    }

    /**
     * @param Club $club
     *
     * @return bool
     */
    public function deleteClub(Club $club): bool
    {
        $query = $this->database->getNewDeleteQuery(self::TABLE);

        $query->where('clubId', '=', $club->getClubId()->toString());

        return $this->database->execute($query);
    }

    /**
     * @param Club $club
     *
     * @return bool
     */
    protected function updateClub(Club $club): bool
    {
        $query = $this->database->getNewUpdateQuery(self::TABLE);

        $query->set('clubName', $club->getClubName()->getClubName());
        $query->set('prooved', $club->isProoved());

        $query->where('clubId', '=', $club->getClubId()->toString());

        return $this->database->execute($query);
    }
}
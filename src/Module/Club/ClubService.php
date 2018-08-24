<?php
declare(strict_types=1);

/**
 * ClubService.php
 * @author      Maik Schößler <ms2002@onlinehome.de>
 * @since       24.08.2018
 */

namespace Project\Module\Club;

use Project\Module\Database\Database;
use Project\Module\GenericValueObject\Id;

/**
 * Class ClubService
 * @package Project\Module\Club
 */
class ClubService
{
    /** @var clubFactory $clubFactory */
    protected $clubFactory;

    /** @var ClubRepository $clubRepository */
    protected $clubRepository;

    /**
     * Service constructor.
     *
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->clubFactory = new clubFactory();
        $this->clubRepository = new ClubRepository($database);
    }

    /**
     * @param Club $club
     *
     * @return bool
     */
    public function saveClub(Club $club): bool
    {
        return $this->clubRepository->saveClub($club);
    }

    /**
     * @param ClubName $clubName
     *
     * @return Club
     */
    public function getOrCreateClubByClubName(ClubName $clubName): Club
    {
        $clubData = $this->clubRepository->getClubByClubName($clubName);

        if (empty($clubData) === false) {
            return $this->clubFactory->getClubByObject($clubData);
        }

        return $this->clubFactory->createClubByClubName($clubName);
    }

    /**
     * @param Id $clubId
     *
     * @return null|Club
     */
    public function getClubByClubId(Id $clubId): ?Club
    {
        $clubData = $this->clubRepository->getClubByClubId($clubId);

        if (empty($clubData) === true) {
            return null;
        }

        return $this->clubFactory->getClubByObject($clubData);
    }
}
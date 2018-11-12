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
    /** @var ClubFactory $clubFactory */
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
        $this->clubFactory = new ClubFactory();
        $this->clubRepository = new ClubRepository($database);
    }

    /**
     * @param Club $club
     *
     * @return bool
     */
    public function saveOrUpdateClub(Club $club): bool
    {
        return $this->clubRepository->saveOrUpdateClub($club);
    }

    /**
     * @param ClubName $clubName
     *
     * @return Club
     */
    public function getOrCreateClubByClubName(ClubName $clubName): Club
    {
        $clubData = $this->clubRepository->getClubByClubName($clubName);

        $club = $this->getClubByData($clubData);

        if ($club !== null) {
            return $club;
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

        return $this->getClubByData($clubData);
    }

    /**
     * @return array
     */
    public function getAllClubs(): array
    {
        $clubData = $this->clubRepository->getAllClubs();

        return $this->getClubsByData($clubData);
    }

    /**
     * @param $clubName
     *
     * @return array
     */
    public function getClubsByClubName($clubName): array
    {
        $clubData = $this->clubRepository->getClubsByClubName($clubName);

        return $this->getClubsByData($clubData);
    }

    /**
     * @param Club $club
     *
     * @return bool
     */
    public function deleteClub(Club $club): bool
    {
        return $this->clubRepository->deleteClub($club);
    }

    /**
     * @param array $clubData
     *
     * @return array
     */
    protected function getClubsByData(array $clubData): array
    {
        $clubArray = [];

        foreach ($clubData as $singleClubData) {
            $club = $this->getClubByData($singleClubData);

            if ($club !== null) {
                $clubArray[$club->getClubId()->toString()] = $club;
            }
        }

        return $clubArray;
    }

    /**
     * @param $clubData
     *
     * @return null|Club
     */
    protected function getClubByData($clubData): ?Club
    {
        if (empty($clubData) === true) {
            return null;
        }

        return $this->clubFactory->getClubByObject($clubData);
    }
}
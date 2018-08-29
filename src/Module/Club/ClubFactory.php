<?php
declare(strict_types=1);

/**
 * ClubFactory.php
 * @author      Maik Schößler <ms2002@onlinehome.de>
 * @since       24.08.2018
 */

namespace Project\Module\Club;

use Project\Module\GenericValueObject\Id;

/**
 * Class ClubFactory
 * @package Project\Module\Club
 */
class ClubFactory
{
    /**
     * @param $object
     *
     * @return null|Club
     */
    public function getClubByObject($object): ?Club
    {
        try {
            if (empty($object->clubId) === true) {
                $clubId = Id::generateId();
            } else {
                $clubId = Id::fromString($object->clubId);
            }

            $clubName = ClubName::fromString($object->clubName);

            $club = new Club($clubId, $clubName);

            if (isset($object->prooved) === true) {
                $club->setProoved((bool)$object->prooved);
            }
        } catch (\InvalidArgumentException $exception) {
            return null;
        }

        return $club;
    }

    /**
     * @param ClubName $clubName
     *
     * @return Club
     */
    public function createClubByClubName(ClubName $clubName): Club
    {
        $clubId = Id::generateId();

        return new Club($clubId, $clubName);
    }
}
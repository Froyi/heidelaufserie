<?php declare(strict_types=1);

namespace Project\Module\Runner;

use Project\Module\GenericValueObject\Id;
use Project\Module\GenericValueObject\Name;

/**
 * Class Runner
 * @package     Project\Module\Runner
 */
class Runner
{
    /** @var Id $runnerId */
    protected $runnerId;

    /** @var Name $surname */
    protected $surname;

    /** @var Name $firstname */
    protected $firstname;

    /** @var Club $club */
    protected $club;

    /**
     * Consists of birthyear, gender and ageGroup string
     * @var AgeGroup $ageGroup
     */
    protected $ageGroup;

    /**
     * Runner constructor.
     *
     * @param Id $runnerId
     * @param Name $surname
     * @param Name $firstname
     * @param AgeGroup $ageGroup
     */
    public function __construct(Id $runnerId, Name $surname, Name $firstname, AgeGroup $ageGroup)
    {
        $this->runnerId = $runnerId;
        $this->surname = $surname;
        $this->firstname = $firstname;
        $this->ageGroup = $ageGroup;
    }

    /**
     * @param Club $club
     */
    public function setClub(Club $club): void
    {
        $this->club = $club;
    }

    /**
     * @return Id
     */
    public function getRunnerId(): Id
    {
        return $this->runnerId;
    }

    /**
     * @return Name
     */
    public function getSurname(): Name
    {
        return $this->surname;
    }

    /**
     * @return Name
     */
    public function getFirstname(): Name
    {
        return $this->firstname;
    }

    /**
     * @return Club
     */
    public function getClub(): ?Club
    {
        return $this->club;
    }

    /**
     * @return AgeGroup
     */
    public function getAgeGroup(): AgeGroup
    {
        return $this->ageGroup;
    }
}
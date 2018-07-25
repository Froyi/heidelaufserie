<?php
declare(strict_types=1);

namespace Project\Module\Runner;

use Project\Module\CompetitionData\Club;
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

    /**
     * Consists of birthyear, gender and ageGroup string
     * @var AgeGroup $ageGroup
     */
    protected $ageGroup;

    /** @var bool $proved */
    protected $proved;

    /** @var array $competitionDataList */
    protected $competitionDataList = [];

    /**
     * Runner constructor.
     *
     * @param Id $runnerId
     * @param Name $surname
     * @param Name $firstname
     * @param AgeGroup $ageGroup
     */
    public function __construct(Id $runnerId, Name $surname, Name $firstname, AgeGroup $ageGroup, bool $proved)
    {
        $this->runnerId = $runnerId;
        $this->surname = $surname;
        $this->firstname = $firstname;
        $this->ageGroup = $ageGroup;
        $this->proved = $proved;
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
     * @return AgeGroup
     */
    public function getAgeGroup(): AgeGroup
    {
        return $this->ageGroup;
    }

    /**
     * @return array
     */
    public function getCompetitionDataList(): array
    {
        return $this->competitionDataList;
    }

    /**
     * @param array $competitionDataList
     */
    public function setCompetitionDataList(array $competitionDataList): void
    {
        $this->competitionDataList = $competitionDataList;
    }

    public function getActualClub(): Club
    {
        return $this->competitionDataList['club'];
    }

    /**
     * @return bool
     */
    public function isProved(): bool
    {
        return $this->proved;
    }

    /**
     * @param bool $proved
     */
    public function setProved(bool $proved): void
    {
        $this->proved = $proved;
    }
}
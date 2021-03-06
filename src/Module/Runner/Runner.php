<?php
declare(strict_types=1);

namespace Project\Module\Runner;

use Project\Module\Club\Club;
use Project\Module\CompetitionData\CompetitionData;
use Project\Module\DefaultModel;
use Project\Module\GenericValueObject\Id;
use Project\Module\GenericValueObject\Name;

/**
 * Class Runner
 * @package     Project\Module\Runner
 */
class Runner extends DefaultModel
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

    /** @var ShortCode $shortCode */
    protected $shortCode;

    /** @var array $competitionDataList */
    protected $competitionDataList = [];

    /**
     * Runner constructor.
     *
     * @param Id $runnerId
     * @param Name $surname
     * @param Name $firstname
     * @param AgeGroup $ageGroup
     * @param bool $proved
     */
    public function __construct(Id $runnerId, Name $surname, Name $firstname, AgeGroup $ageGroup, bool $proved)
    {
        parent::__construct();

        $this->runnerId = $runnerId;
        $this->surname = $surname;
        $this->firstname = $firstname;
        $this->ageGroup = $ageGroup;
        $this->proved = $proved;
    }

    /**
     * @return ShortCode
     */
    public function getShortCode(): ShortCode
    {
        return $this->shortCode;
    }

    /**
     * @param ShortCode $shortCode
     */
    public function setShortCode(ShortCode $shortCode): void
    {
        $this->shortCode = $shortCode;
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

    public function getActualClub(): ?Club
    {
        $competitionDataList = $this->competitionDataList;

        if (empty($competitionDataList) === true) {
            return null;
        }

        usort($competitionDataList, [$this, 'sortByDate']);

        /** @var CompetitionData $competitionData */
        $competitionData = reset($competitionDataList);

        return $competitionData->getClub();
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

    /**
     * @param CompetitionData $competitionData1
     * @param CompetitionData $competitionData2
     *
     * @return int
     */
    public function sortByDate(CompetitionData $competitionData1, CompetitionData $competitionData2): int
    {
        if ($competitionData1->getDate()->toString() === $competitionData2->getDate()->toString()) {
            return 0;
        }
        return ($competitionData1->getDate()->toString() < $competitionData2->getDate()->toString()) ? -1 : 1;
    }
}
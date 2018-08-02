<?php
declare (strict_types=1);

namespace Project\Module\Runner;

use Project\Configuration;
use Project\Module\GenericValueObject\BirthYear;
use Project\Module\GenericValueObject\DefaultGenericValueObject;
use Project\Module\GenericValueObject\Gender;

/**
 * Class AgeGroup
 * @package Project\Module\GenericValueObject
 */
class AgeGroup extends DefaultGenericValueObject
{
    /** @var BirthYear $birthYear */
    protected $birthYear;

    /** @var Gender $gender */
    protected $gender;

    /** @var string $ageGroup */
    protected $ageGroup;

    /**
     * AgeGroup constructor.
     *
     * @param BirthYear $birthYear
     * @param Gender $gender
     * @param string $ageGroup
     */
    protected function __construct(BirthYear $birthYear, Gender $gender, string $ageGroup)
    {
        $this->birthYear = $birthYear;
        $this->gender = $gender;
        $this->ageGroup = $ageGroup;
    }

    /**
     * @param BirthYear $birthYear
     * @param Gender $gender
     * @param Configuration $configuration
     *
     * @return AgeGroup
     */
    public static function fromValues(BirthYear $birthYear, Gender $gender, Configuration $configuration): self
    {
        return new self($birthYear, $gender, self::generateAgeGroup($birthYear, $gender, $configuration));
    }

    /**
     * @return BirthYear
     */
    public function getBirthYear(): BirthYear
    {
        return $this->birthYear;
    }

    /**
     * @return Gender
     */
    public function getGender(): Gender
    {
        return $this->gender;
    }

    /**
     * @return string
     */
    public function getAgeGroup(): string
    {
        return $this->ageGroup;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->ageGroup;
    }

    /**
     * @param BirthYear $birthYear
     * @param Gender $gender
     * @param Configuration $configuration
     *
     * @return string
     */
    protected static function generateAgeGroup(BirthYear $birthYear, Gender $gender, Configuration $configuration): string
    {
        $ageGroup = strtoupper($gender->getGender());

        $age = $birthYear->getAge();

        /** @var array $ageTable */
        $ageTable = $configuration->getEntryByName('ageTable');
        foreach ($ageTable as $tableAgeName => $ageEntry) {
            if ($age >= $ageEntry['min'] && $age <= $ageEntry['max']) {
                $ageGroup .= $tableAgeName;
            }
        }
        return $ageGroup;
    }
}


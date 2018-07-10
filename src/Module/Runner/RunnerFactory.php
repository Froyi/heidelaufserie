<?php declare(strict_types=1);

namespace Project\Module\Runner;

use Project\Configuration;
use Project\Module\GenericValueObject\BirthYear;
use Project\Module\GenericValueObject\Gender;
use Project\Module\GenericValueObject\Id;
use Project\Module\GenericValueObject\Name;

/**
 * Class RunnerFactory
 * @package Project\Module\Runner
 */
class RunnerFactory
{
    /**
     * @param $object
     * @param Configuration $configuration
     *
     * @return null|Runner
     */
    public function getRunnerByObject($object, Configuration $configuration): ?Runner
    {
        try {
            if ($this->checkProperties($object) === false) {
                return null;
            }

            $runnerId = Id::fromString($object->runnerId);
            $surname = Name::fromString($object->surname);
            $firstname = Name::fromString($object->firstname);

            $birthYear = BirthYear::fromValue($object->birthYear);
            $gender = Gender::fromString($object->gender);
            $ageGroup = AgeGroup::fromValues($birthYear, $gender, $configuration);

            $runner = new Runner($runnerId, $surname, $firstname, $ageGroup);

            if (empty($object->club) === false) {
                $runner->setClub(Club::fromString($object->club));
            }

            return $runner;
        } catch (\InvalidArgumentException $exception) {
            return null;
        }
    }

    /**
     * @param $properties
     *
     * @return bool
     */
    protected function checkProperties($properties): bool
    {
        return !(empty($properties->runnerId) === true || empty($properties->surname) === true || empty($properties->firstname) === true || empty($properties->birthYear) === true || empty($properties->gender) === true);
    }
}
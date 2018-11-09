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
            $runnerId = Id::fromString($object->runnerId);
            $surname = Name::fromString($object->surname);
            $firstname = Name::fromString($object->firstname);

            $birthYear = BirthYear::fromValue((int)$object->birthYear);
            $gender = Gender::fromString($object->gender);
            $ageGroup = AgeGroup::fromValues($birthYear, $gender, $configuration);

            $proved = false;

            if(isset($object->proved) === true){
                $proved = (bool)$object->proved;
            }

            $runner = new Runner($runnerId, $surname, $firstname, $ageGroup, $proved);

            if (empty($object->shortcode) === false) {
                $runner->setShortCode(ShortCode::fromString($object->shortcode));
            } else {
                $runner->setShortCode(ShortCode::generateShortCode());
            }

            return $runner;
        } catch (\InvalidArgumentException $exception) {
            return null;
        }
    }
}
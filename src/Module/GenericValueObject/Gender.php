<?php
declare (strict_types=1);

namespace Project\Module\GenericValueObject;

/**
 * Class Gender
 * @package Project\Module\GenericValueObject
 */
class Gender extends DefaultGenericValueObject
{
    protected const POSSIBLE_GENDER = ['m', 'w'];

    /** @var string $gender */
    protected $gender;

    /**
     * Gender constructor.
     *
     * @param string $gender
     */
    protected function __construct(string $gender)
    {
        $this->gender = $gender;
    }

    /**
     * @param string $gender
     *
     * @return Gender
     * @throws \InvalidArgumentException
     */
    public static function fromString(string $gender): self
    {
        $gender = strtolower($gender);

        self::ensureGenderIsValid($gender);

        return new self($gender);
    }

    /**
     * @param string $gender
     *
     * @throws \InvalidArgumentException
     */
    protected static function ensureGenderIsValid(string $gender): void
    {
        if (\in_array($gender, self::POSSIBLE_GENDER, true) === false) {
            throw new \InvalidArgumentException('The gender is not valid: ' . $gender);
        }
    }

    /**
     * @return string
     */
    public function getGender(): string
    {
        return $this->gender;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getGender();
    }
}


<?php
declare (strict_types=1);

namespace Project\Module\Club;

use Project\Module\GenericValueObject\DefaultGenericValueObject;

/**
 * Class ClubName
 * @package Project\Module\Club
 */
class ClubName extends DefaultGenericValueObject
{
    protected const CLUB_NAME_MIN_LENGTH = 3;

    /** @var string $clubName */
    protected $clubName;

    /**
     * Club constructor.
     * @param string $clubName
     */
    protected function __construct(string $clubName)
    {
        $this->clubName = $clubName;
    }

    /**
     * @param string $clubName
     *
     * @return ClubName
     */
    public static function fromString(string $clubName): self
    {
        self::ensureClubIsValid($clubName);
        $clubName = self::convertClub($clubName);

        return new self($clubName);
    }

    /**
     * @param string $clubName
     * @throws \InvalidArgumentException
     */
    protected static function ensureClubIsValid(string $clubName): void
    {
        if (\strlen($clubName) < self::CLUB_NAME_MIN_LENGTH) {
            throw new \InvalidArgumentException('The clubName is too short', 1);
        }
    }

    /**
     * @param string $clubName
     * @return string
     */
    protected static function convertClub(string $clubName): string
    {
        return ucfirst(trim($clubName));
    }

    /**
     * @return string
     */
    public function getClubName(): string
    {
        return $this->clubName;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->clubName;
    }
}


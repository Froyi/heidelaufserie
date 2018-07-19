<?php
declare (strict_types=1);

namespace Project\Module\CompetitionData;

use Project\Module\GenericValueObject\DefaultGenericValueObject;

/**
 * Class Club
 * @package Project\Module\GenericValueObject
 */
class Club extends DefaultGenericValueObject
{
    protected const CLUB_MIN_LENGTH = 5;

    /** @var string $club */
    protected $club;

    /**
     * Club constructor.
     * @param string $club
     */
    protected function __construct(string $club)
    {
        $this->club = $club;
    }

    /**
     * @param string $club
     * @return Club
     * @throws \InvalidArgumentException
     */
    public static function fromString(string $club): self
    {
        self::ensureClubIsValid($club);
        $club = self::convertClub($club);

        return new self($club);
    }

    /**
     * @param string $club
     * @throws \InvalidArgumentException
     */
    protected static function ensureClubIsValid(string $club): void
    {
        if (\strlen($club) < self::CLUB_MIN_LENGTH) {
            throw new \InvalidArgumentException('The club is too short', 1);
        }
    }

    /**
     * @param string $club
     * @return string
     */
    protected static function convertClub(string $club): string
    {
        return ucfirst(trim($club));
    }

    /**
     * @return string
     */
    public function getClub(): string
    {
        return $this->club;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->club;
    }
}


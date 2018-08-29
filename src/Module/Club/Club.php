<?php
declare(strict_types=1);

namespace Project\Module\Club;

use Project\Module\GenericValueObject\Id;

/**
 * Class Club
 * @package Project\Module\Club
 */
class Club
{
    /** @var Id $clubId */
    protected $clubId;

    /** @var ClubName $club */
    protected $clubName;

    /** @var bool $prooved */
    protected $prooved;

    /**
     * Club constructor.
     *
     * @param Id $clubId
     * @param ClubName $clubName
     */
    public function __construct(Id $clubId, ClubName $clubName)
    {
        $this->clubId = $clubId;
        $this->clubName = $clubName;
        $this->prooved = false;
    }

    /**
     * @return Id
     */
    public function getClubId(): Id
    {
        return $this->clubId;
    }

    /**
     * @return ClubName
     */
    public function getClubName(): ClubName
    {
        return $this->clubName;
    }

    /**
     * @return bool
     */
    public function isProoved(): bool
    {
        return $this->prooved;
    }

    /**
     * @param bool $prooved
     */
    public function setProoved(bool $prooved): void
    {
        $this->prooved = $prooved;
    }
}
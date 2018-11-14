<?php
declare(strict_types=1);

namespace Project\Module\CompetitionResults;

/**
 * Class Round
 * @package Project\Module\CompetitionResults
 */
class Round
{
    public const PLAUSIBLE_TIME = 900;

    public const SLOW_TIME = 2200;
    public const UNUSUAL_SLOW = 1.5;

    /** @var int $round */
    protected $round;

    /**
     * Round constructor.
     *
     * @param int $round
     */
    protected function __construct(int $round)
    {
        $this->round = $round;
    }

    /**
     * @param $round
     *
     * @return Round
     * @throws \InvalidArgumentException
     */
    public static function fromValue($round): self
    {
        self::ensureRoundIsValid($round);

        return new self(self::convertRound($round));
    }

    /**
     * @param $round
     *
     * @throws \InvalidArgumentException
     */
    protected static function ensureRoundIsValid($round): void
    {
        if ((\is_int($round) === false && \is_string($round) === false) || (int)$round < 0) {
            throw new \InvalidArgumentException('Round is not valid: ' . $round);
        }
    }

    /**
     * @param $round
     *
     * @return int
     */
    protected static function convertRound($round): int
    {
        return (int)$round;
    }

    /**
     * @return int
     */
    public function getRound(): int
    {
        return $this->round;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getRound();
    }
}
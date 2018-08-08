<?php
declare(strict_types=1);

namespace Project\Module\CompetitionStatistic;

/**
 * Class Ranking
 * @package Project\Module\CompetitionStatistic
 */
class Ranking
{
    /** @var int $ranking */
    protected $ranking;

    /**
     * Ranking constructor.
     *
     * @param int $ranking
     */
    protected function __construct(int $ranking)
    {
        $this->ranking = $ranking;
    }

    /**
     * @param $ranking
     *
     * @return Ranking
     * @throws \InvalidArgumentException
     */
    public static function fromValue($ranking): self
    {
        self::ensureRankingIsValid($ranking);

        return new self(self::convertRanking($ranking));
    }

    /**
     * @param $ranking
     *
     * @throws \InvalidArgumentException
     */
    protected static function ensureRankingIsValid($ranking): void
    {
        if ((\is_int($ranking) === false && \is_string($ranking) === false) || (int)$ranking < 1) {
            throw new \InvalidArgumentException('Ranking is not valid: ' . $ranking);
        }
    }

    /**
     * @param $ranking
     *
     * @return int
     */
    protected static function convertRanking($ranking): int
    {
        return (int)$ranking;
    }

    /**
     * @return int
     */
    public function getRanking(): int
    {
        return $this->ranking;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getRanking();
    }
}
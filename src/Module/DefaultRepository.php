<?php
declare(strict_types=1);

namespace Project\Module;

use Project\Module\Database\Database;

/**
 * Class DefaultRepository
 * @package Project\Module
 */
class DefaultRepository
{
    /** @var Database $database */
    protected $database;

    /**
     * RunnerRepository constructor.
     *
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }
}
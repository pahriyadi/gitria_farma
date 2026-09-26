<?php

namespace App\Services;

use App\Services\JournalEngine;

class FinanceService
{
    protected $journalEngine;

    public function __construct()
    {
        $this->journalEngine = new JournalEngine();
    }

    /**
     * Notify finance subsystem about a newly added service (tindakan).
     *
     * @param int   $serviceId ID of the service
     * @param float $price     Service price
     * @return array Result of journal posting
     */
    public function notifyNewService(int $serviceId, float $price): array
    {
        $description = "New service (tindakan) added, ID {$serviceId}";
        return $this->journalEngine->postJournal('NEW_SERVICE', $serviceId, $price, $description);
    }
}

<?php

namespace App\Http\Filters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;


class TenderFilter extends QueryFilter
{
    public function name(string $name): void
    {
        $this->builder->where('name', 'ILIKE', '%' . $name . '%');
    }

    public function dateStart(string $date): void
    {
        if (!$this->isValidDate($date)) {
            throw new \InvalidArgumentException("Invalid date format: {$date}. Expected format: Y-m-d");
        }

        $date = Carbon::parse($date)->format('Y-m-d');
        $this->builder->whereDate('updated_at', '>=', $date);
    }

    public function dateEnd(string $date): void
    {
        if (!$this->isValidDate($date)) {
            throw new \InvalidArgumentException("Invalid date format: {$date}. Expected format: Y-m-d");
        }

        $date = Carbon::parse($date)->format('Y-m-d');
        $this->builder->whereDate('updated_at', '<=', $date);
    }

    protected function isValidDate($date): bool
    {
        try {
            Carbon::parse($date);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

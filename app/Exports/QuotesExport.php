<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class QuotesExport implements FromCollection, WithHeadings
{
    public function __construct(private readonly Collection $quotes)
    {
    }

    public function headings(): array
    {
        return ['Quote Number', 'Number Plate', 'Vehicle', 'Status', 'Total', 'Expires At'];
    }

    public function collection(): Collection
    {
        return $this->quotes->map(fn ($quote) => [
            $quote->quote_number,
            $quote->vehicle->number_plate,
            trim($quote->vehicle->make.' '.$quote->vehicle->model),
            $quote->status,
            number_format($quote->total_cents / 100, 2),
            optional($quote->expires_at)->toDateTimeString(),
        ]);
    }
}

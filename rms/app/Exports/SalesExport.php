<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesExport implements FromCollection, WithHeadings
{
    public function __construct(protected $start, protected $end) {}

    public function collection()
    {
        return Order::whereBetween('paid_at', [$this->start, $this->end])
            ->where('status', 'paid')
            ->select('order_number', 'grand_total', 'tax_total', 'service_charge_total', 'paid_at')
            ->get();
    }

    public function headings(): array
    {
        return ['Order #', 'Total', 'Tax', 'Service', 'Paid At'];
    }
}

<?php

namespace App\Exports;

use App\Models\School;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TenantsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return School::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Code',
            'Subdomain',
            'Domain',
            'Database',
            'Plan',
            'Country',
            'Admin Email',
            'Created At',
        ];
    }

    public function map($school): array
    {
        $meta = $school->meta ?? [];

        return [
            $school->id,
            $school->name,
            $school->code,
            $school->subdomain,
            $school->domain,
            $school->database,
            $meta['plan'] ?? '',
            $meta['country'] ?? '',
            $meta['admin_email'] ?? '',
            $school->created_at,
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RequestsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Requests::all();  // Adjust the query as necessary
    }

    public function headings(): array
    {
        return [
            'Department',
            'Test Category',
            'Date Request',
            'Type of Document',
            'Subject',
            'Modality',
            'Mode',
            'Exam Date',
            'Time Allotment',
            'No. of Pages',
            'No. of Copies',
            'File',
            'Status',
        ];
    }
}

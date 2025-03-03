<?php

namespace App\Exports;

use App\Models\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ApprovedRequestsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Request::where('status', 'final_approved')
            ->with(['user', 'subject'])
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Request ID',
            'Requester',
            'Department',
            'Subject',
            'Document Type',
            'Date Requested',
            'Date Required',
            'Status',
            'Producer',
            'Pages',
            'Copies'
        ];
    }

    public function map($request): array
    {
        return [
            $request->id,
            $request->user ? ($request->user->first_name . ' ' . $request->user->last_name) : 'N/A',
            $request->department,
            $request->subject_id ? $request->subject->name : $request->specific_subject,
            $request->type_of_document ?? $request->test_category,
            date('M d, Y', strtotime($request->date_request)),
            $request->date_exam ? date('M d, Y', strtotime($request->date_exam)) : 
                ($request->date_required ? date('M d, Y', strtotime($request->date_required)) : 'N/A'),
            ucwords(str_replace('_', ' ', $request->status)),
            ucwords(str_replace('_', ' ', $request->producer ?? 'Not Assigned')),
            $request->number_of_pages,
            $request->number_of_copies
        ];
    }
}

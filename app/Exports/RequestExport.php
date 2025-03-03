<?php

namespace App\Exports;

use App\Models\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RequestExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $requests = Request::where('status', 'final_approved')
            ->with('user')
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->id,
                    'requester' => optional($request->user)->first_name . ' ' . optional($request->user)->last_name,
                    'department' => $request->department,
                    'subject' => $request->subject_id ? optional($request->subject)->name : $request->specific_subject,
                    'document_type' => $request->type_of_document ?? $request->test_category,
                    'date_requested' => date('M d, Y', strtotime($request->date_request)),
                    'date_required' => $request->date_exam ? date('M d, Y', strtotime($request->date_exam)) : 
                        ($request->date_required ? date('M d, Y', strtotime($request->date_required)) : 'N/A'),
                    'status' => ucwords(str_replace('_', ' ', $request->status)),
                    'producer' => ucwords(str_replace('_', ' ', $request->producer ?? 'Not Assigned'))
                ];
            });

        return $requests;
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
            'Producer'
        ];
    }
}

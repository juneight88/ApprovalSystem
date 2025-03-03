<?php

namespace App\Services;

use App\Models\Request;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DocumentGeneratorService
{
    const FONT_LARGE = 5;
    const FONT_MEDIUM = 3;
    const FONT_SMALL = 2;
    const TEXT_COLOR = '#000000';
    const SIGNATURE_SIZE = [150, 50]; // Adjusted signature size based on template
    
    // Template dimensions matching your image
    const TEMPLATE_WIDTH = 549;
    const TEMPLATE_HEIGHT = 826;

    public function generateRequestDocument(Request $request)
    {
        try {
            Storage::makeDirectory('public/generated');
            
            $template = Image::make(public_path('images/approval_template.png'));
            
            if ($template->width() !== self::TEMPLATE_WIDTH || $template->height() !== self::TEMPLATE_HEIGHT) {
                $template->resize(self::TEMPLATE_WIDTH, self::TEMPLATE_HEIGHT);
            }

            $this->addFormFields($template, $request);
            $this->addSignatures($template, $request);

            $filename = 'approval_sheet_' . $request->id . '_' . time() . '.png';
            $path = 'generated/' . $filename;
            
            $template->save(storage_path('app/public/' . $path), 90);
            $template->destroy();
            
            $request->generated_document_path = $path;
            $request->save();

            return $path;

        } catch (\Exception $e) {
            \Log::error('Error generating document: ' . $e->getMessage());
            throw $e;
        }
    }

    private function addFormFields($template, $request)
    {
        // Get the full name of the requester
        $requesterName = $request->user->first_name . ' ' . $request->user->last_name;
        
        // Get department name
        $departmentName = strtoupper($request->department ?? 'College of Computing and Information Sciences');

        // Add department header
        $this->addText(
            $template,
            $departmentName,
            275,  // Centered position
            135,  // Position for the header
            self::FONT_LARGE
        );

        // Coordinates adjusted based on the template image
        $fields = [
            'Faculty' => [
                'text' => $requesterName,  // Using requester's full name
                'x' => 180,
                'y' => 240
            ],
            'Department' => [
                'text' => $request->department ?? 'College of Computing and Information Sciences',
                'x' => 180,
                'y' => 270
            ],
            'Subject' => [
                'text' => $request->specific_subject ?? '',  // Using specific_subject field instead of subject relationship
                'x' => 180,
                'y' => 300
            ],
            'Test Category' => [
                'text' => $request->test_category,
                'x' => 180,
                'y' => 330
            ],
            'No. of Copies' => [
                'text' => $request->number_of_copies ?? '50',
                'x' => 420,
                'y' => 330
            ],
            'Date Submitted' => [
                'text' => Carbon::parse($request->date_request)->format('m/d/y'),
                'x' => 180,
                'y' => 360
            ],
            'Date of Exam' => [
                'text' => Carbon::parse($request->date_exam)->format('m/d/y'),
                'x' => 180,
                'y' => 390
            ],
            'Exam Time Allotment' => [
                'text' => $request->exam_time_allotment,
                'x' => 180,
                'y' => 420
            ],
            'Modality of Learning' => [
                'text' => $request->modality_of_learning,
                'x' => 180,
                'y' => 450
            ]
        ];

        foreach ($fields as $field) {
            $this->addText(
                $template, 
                $field['text'], 
                $field['x'], 
                $field['y'], 
                self::FONT_MEDIUM
            );
        }
    }

    private function addSignatures($template, $request)
    {
        // Load relationships explicitly to ensure they're available
        $request->load(['user', 'coordinator', 'headOffice']);

        // Debug log to check all users and their signatures
        \Log::info('Adding signatures for request #' . $request->id);
        \Log::info('Teacher: ' . ($request->user ? $request->user->name : 'None') . ' - Signature: ' . ($request->user ? $request->user->signature : 'None'));
        \Log::info('Coordinator: ' . ($request->coordinator ? $request->coordinator->name : 'None') . ' - Signature: ' . ($request->coordinator ? $request->coordinator->signature : 'None'));
        \Log::info('Head Office: ' . ($request->headOffice ? $request->headOffice->name : 'None') . ' - Signature: ' . ($request->headOffice ? $request->headOffice->signature : 'None'));

        // Signature positions aligned with the pre-printed labels on template
        $signatures = [
            'teacher' => [
                'user' => $request->user,
                'x' => 350,  // Centered after "Prepared by:"
                'y' => 480   // Moved up from 500
            ],
            'coordinator' => [
                'user' => $request->coordinator,
                'x' => 350,  // Centered after "Checked by:"
                'y' => 560   // Moved up from 580
            ],
            'head_office' => [
                'user' => $request->headOffice,
                'x' => 350,  // Centered after "Evaluated/Approved by:"
                'y' => 640   // Moved up from 660
            ]
        ];

        foreach ($signatures as $role => $signature) {
            if ($signature['user'] && $signature['user']->signature) {
                \Log::info("Adding {$role} signature for user: " . $signature['user']->name);
                $this->addSignature(
                    $template, 
                    $signature['user'],
                    $signature['x'], 
                    $signature['y']
                );
            } else {
                $reason = !$signature['user'] ? 'No user assigned' : 
                         (!$signature['user']->signature ? 'No signature uploaded' : 'Unknown error');
                \Log::warning("Missing signature for {$role}: {$reason}");
            }
        }
    }

    private function addSignature($template, $user, $x, $y)
    {
        try {
            if (!$user->signature) {
                \Log::warning("No signature found for user: {$user->name}");
                return;
            }

            $signaturePath = storage_path('app/public/' . $user->signature);
            
            if (!file_exists($signaturePath)) {
                \Log::warning("Signature file not found at: {$signaturePath}");
                return;
            }

            $signature = Image::make($signaturePath);
            
            // Made signatures larger
            $signature->resize(180, 55, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // Adjusted x position more to the left to maintain centering with larger width
            $template->insert($signature, 'top-left', $x - 25, $y);
            $signature->destroy();
        } catch (\Exception $e) {
            \Log::warning('Failed to add signature: ' . $e->getMessage());
        }
    }

    private function addText($template, $text, $x, $y, $fontSize, $color = self::TEXT_COLOR)
    {
        if (empty($text)) return;
        
        $template->text($text, $x, $y, function($font) use ($fontSize, $color) {
            $font->file($fontSize);
            $font->color($color);
            $font->align('left');
            $font->valign('top');
        });
    }
}

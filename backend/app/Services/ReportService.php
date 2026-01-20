<?php

namespace App\Services;

use App\Models\Field;
use App\Models\Job;
use App\Models\Invoice;

class ReportService
{
    /**
     * Generate field measurement report data
     */
    public function generateFieldReport(Field $field): array
    {
        $field->load(['organization', 'user', 'jobs']);

        $boundary = null;
        $coordinates = [];
        
        if ($field->boundary) {
            try {
                // boundary is already cast to array by the model
                $boundary = is_string($field->boundary) ? json_decode($field->boundary, true) : $field->boundary;
                if (is_array($boundary) && isset($boundary['coordinates'][0])) {
                    $coordinates = $boundary['coordinates'][0];
                }
            } catch (\Exception $e) {
                // Log error but continue with empty coordinates
                \Log::warning('Failed to parse field boundary', ['field_id' => $field->id]);
            }
        }

        return [
            'title' => 'Field Measurement Report',
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'field' => [
                'id' => $field->id,
                'name' => $field->name,
                'area_ha' => $field->area ? round($field->area / 10000, 2) : 0,
                'area_sqm' => $field->area ?? 0,
                'perimeter_km' => $field->perimeter ? round($field->perimeter / 1000, 2) : 0,
                'perimeter_m' => $field->perimeter ?? 0,
                'crop_type' => $field->crop_type,
                'measurement_type' => $field->measurement_type,
                'notes' => $field->notes,
                'created_at' => $field->created_at->format('Y-m-d H:i:s'),
            ],
            'organization' => [
                'name' => $field->organization->name,
                'type' => $field->organization->type,
                'email' => $field->organization->email,
            ],
            'measured_by' => [
                'name' => $field->user->name,
                'email' => $field->user->email,
            ],
            'coordinates' => $coordinates,
            'jobs_count' => $field->jobs->count(),
            'jobs' => $field->jobs->map(function ($job) {
                return [
                    'title' => $job->title,
                    'status' => $job->status,
                    'priority' => $job->priority,
                    'due_date' => $job->due_date ? $job->due_date->format('Y-m-d') : null,
                ];
            })->toArray(),
        ];
    }

    /**
     * Generate job report data
     */
    public function generateJobReport(Job $job): array
    {
        $job->load(['organization', 'field', 'creator', 'assignee', 'invoices']);

        return [
            'title' => 'Job Report',
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'job' => [
                'id' => $job->id,
                'title' => $job->title,
                'description' => $job->description,
                'status' => $job->status,
                'priority' => $job->priority,
                'due_date' => $job->due_date ? $job->due_date->format('Y-m-d') : null,
                'started_at' => $job->started_at ? $job->started_at->format('Y-m-d H:i:s') : null,
                'completed_at' => $job->completed_at ? $job->completed_at->format('Y-m-d H:i:s') : null,
                'created_at' => $job->created_at->format('Y-m-d H:i:s'),
            ],
            'organization' => [
                'name' => $job->organization->name,
                'type' => $job->organization->type,
            ],
            'field' => $job->field ? [
                'name' => $job->field->name,
                'area_ha' => $job->field->area ? round($job->field->area / 10000, 2) : 0,
            ] : null,
            'creator' => [
                'name' => $job->creator->name,
                'email' => $job->creator->email,
            ],
            'assignee' => $job->assignee ? [
                'name' => $job->assignee->name,
                'email' => $job->assignee->email,
            ] : null,
            'invoices' => $job->invoices->map(function ($invoice) {
                return [
                    'number' => $invoice->invoice_number,
                    'total' => $invoice->total,
                    'status' => $invoice->status,
                    'due_date' => $invoice->due_date ? $invoice->due_date->format('Y-m-d') : null,
                ];
            })->toArray(),
        ];
    }

    /**
     * Format report data as HTML
     */
    public function formatAsHtml(array $reportData): string
    {
        $html = '<html><head><style>';
        $html .= 'body { font-family: Arial, sans-serif; margin: 20px; }';
        $html .= 'h1 { color: #27ae60; }';
        $html .= 'h2 { color: #2c3e50; margin-top: 20px; }';
        $html .= 'table { width: 100%; border-collapse: collapse; margin: 10px 0; }';
        $html .= 'th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }';
        $html .= 'th { background-color: #27ae60; color: white; }';
        $html .= '.header { margin-bottom: 20px; }';
        $html .= '</style></head><body>';

        $html .= '<div class="header">';
        $html .= '<h1>' . ($reportData['title'] ?? 'Report') . '</h1>';
        $html .= '<p>Generated: ' . ($reportData['generated_at'] ?? date('Y-m-d H:i:s')) . '</p>';
        $html .= '</div>';

        // Field Report
        if (isset($reportData['field'])) {
            $field = $reportData['field'];
            $html .= '<h2>Field Information</h2>';
            $html .= '<table>';
            $html .= '<tr><th>Property</th><th>Value</th></tr>';
            $html .= '<tr><td>Name</td><td>' . htmlspecialchars($field['name']) . '</td></tr>';
            $html .= '<tr><td>Location</td><td>' . htmlspecialchars($field['location'] ?? 'N/A') . '</td></tr>';
            $html .= '<tr><td>Area</td><td>' . $field['area_ha'] . ' ha (' . $field['area_sqm'] . ' m²)</td></tr>';
            $html .= '<tr><td>Perimeter</td><td>' . $field['perimeter_km'] . ' km (' . $field['perimeter_m'] . ' m)</td></tr>';
            $html .= '<tr><td>Crop Type</td><td>' . htmlspecialchars($field['crop_type'] ?? 'N/A') . '</td></tr>';
            $html .= '<tr><td>Measurement Type</td><td>' . htmlspecialchars($field['measurement_type'] ?? 'N/A') . '</td></tr>';
            $html .= '</table>';

            if (isset($reportData['organization'])) {
                $org = $reportData['organization'];
                $html .= '<h2>Organization</h2>';
                $html .= '<table>';
                $html .= '<tr><th>Property</th><th>Value</th></tr>';
                $html .= '<tr><td>Name</td><td>' . htmlspecialchars($org['name']) . '</td></tr>';
                $html .= '<tr><td>Type</td><td>' . ucfirst($org['type']) . '</td></tr>';
                $html .= '</table>';
            }
        }

        // Job Report
        if (isset($reportData['job'])) {
            $job = $reportData['job'];
            $html .= '<h2>Job Details</h2>';
            $html .= '<table>';
            $html .= '<tr><th>Property</th><th>Value</th></tr>';
            $html .= '<tr><td>Title</td><td>' . htmlspecialchars($job['title']) . '</td></tr>';
            $html .= '<tr><td>Description</td><td>' . htmlspecialchars($job['description'] ?? 'N/A') . '</td></tr>';
            $html .= '<tr><td>Status</td><td>' . ucfirst($job['status']) . '</td></tr>';
            $html .= '<tr><td>Priority</td><td>' . ucfirst($job['priority']) . '</td></tr>';
            $html .= '<tr><td>Due Date</td><td>' . ($job['due_date'] ?? 'N/A') . '</td></tr>';
            $html .= '</table>';
        }

        $html .= '</body></html>';

        return $html;
    }

    /**
     * Format report data as JSON
     */
    public function formatAsJson(array $reportData): string
    {
        return json_encode($reportData, JSON_PRETTY_PRINT);
    }
}

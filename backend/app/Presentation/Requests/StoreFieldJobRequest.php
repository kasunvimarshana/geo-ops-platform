<?php

declare(strict_types=1);

namespace App\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFieldJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'land_plot_id' => 'nullable|exists:land_plots,id',
            'driver_id' => 'nullable|exists:users,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            'job_type' => 'required|in:plowing,harvesting,spraying,seeding,other',
            'priority' => 'in:low,medium,high',
            'scheduled_date' => 'required|date',
            'rate_per_unit' => 'nullable|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ];
    }
}

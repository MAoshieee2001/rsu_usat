<?php
// app/Http/Requests/ProgrammingRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProgrammingRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Ajusta según tu lógica de permisos
        return true;
    }

    public function rules(): array
    {
        return [
            'zone_id'      => ['required','exists:zones,id'],
            'schedule_id'  => ['required','exists:schedules,id'],
            'date_start'   => ['required','date','before:date_end'],
            'date_end'     => ['required','date','after:date_start'],
            'employee_ids' => ['required','array','min:1'],
            'employee_ids.*' => ['exists:employees,id'],
        ];
    }
}

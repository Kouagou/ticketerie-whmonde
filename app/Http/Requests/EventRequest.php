<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'max_participants' => 'required|integer|min:1',
            'status' => 'sometimes|in:active,expired',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Le titre est obligatoire',
            'description.required' => 'La description est obligatoire',
            'start_date.required' => 'La date de début est obligatoire',
            'end_date.required' => 'La date de fin est obligatoire',
            'end_date.after' => 'La date de fin doit être postérieure à la date de début',
            'max_participants.required' => 'Le nombre maximum de participants est obligatoire',
            'max_participants.integer' => 'Le nombre maximum de participants doit être un nombre entier',
            'max_participants.min' => 'Le nombre maximum de participants doit être au moins 1',
        ];
    }
}

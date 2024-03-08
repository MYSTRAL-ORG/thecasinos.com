<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CasinoDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return
        [
			'title' => 'required',
			'description' => 'required',
			'sumup' => 'required',
			'games' => 'required',
			'fun_facts' => 'required',
			'resume_1_line' => 'required',
			'resume_2_words' => 'required',
        ];
    }
}

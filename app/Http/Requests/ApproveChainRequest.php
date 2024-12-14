<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApproveChainRequest extends FormRequest
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
        return [
            'project_id' => ['required',Rule::exists('projects','id')],
            'user_id' => ['required',Rule::exists('users','id'),Rule::unique('project_approve_chains')->where('project_id',$this->project_id)],
            'order' => ['required','integer','min:1',Rule::unique('project_approve_chains')->where('project_id',$this->project_id)],
        ];
    }
    public function messages()
    {
        return [
            'user_id.unique' => "This User Already Exist In The Chain",
            'order.unique'   => "There Is Another User With This Order"
        ];
    }
}

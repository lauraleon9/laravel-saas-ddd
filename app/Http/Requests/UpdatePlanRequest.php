<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $planId = $this->route('plan'); 
        
        return [
            'name' => 'sometimes|string|max:255|unique:plans,name,' . $planId,
            'monthly_price' => 'sometimes|numeric|min:0|max:999999.99',
            'user_limit' => 'sometimes|integer|min:1|max:10000',
            'features' => 'nullable|array',
            'is_active' => 'sometimes|boolean'
        ];
    }

    /**
     * Mensajes de error personalizados
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'Ya existe otro plan con este nombre',
            'monthly_price.min' => 'El precio debe ser mayor o igual a 0',
            'user_limit.min' => 'El límite de usuarios debe ser al menos 1',
            'features.array' => 'Las características deben ser un arreglo válido',
            'is_active.boolean' => 'El estado activo debe ser verdadero o falso'
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Por ahora permitimos todo, luego implementaremos políticas
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:plans,name',
            'monthly_price' => 'required|numeric|min:0|max:999999.99',
            'user_limit' => 'required|integer|min:1|max:10000',
            'features' => 'nullable|array'
        ];
    }

    /**
     * Mensajes de error personalizados
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del plan es obligatorio',
            'name.unique' => 'Ya existe un plan con este nombre',
            'monthly_price.required' => 'El precio mensual es obligatorio',
            'monthly_price.min' => 'El precio debe ser mayor o igual a 0',
            'user_limit.required' => 'El límite de usuarios es obligatorio',
            'user_limit.min' => 'El límite de usuarios debe ser al menos 1',
            'features.array' => 'Las características deben ser un arreglo válido'
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validacion de la ficha del estudiante.
     */
    public function rules(): array
    {
        return [
            'colegio_nombre' => 'required|string|min:2|max:150',
            'nombre'         => 'required|string|min:2|max:100',
            'apellido'       => 'required|string|min:2|max:100',
            'sexo'           => 'required|in:Masculino,Femenino,Otro',
            'edad'           => 'required|integer|min:12|max:35',
            'celular'        => 'required|string|min:7|max:20',
            'email'          => 'nullable|email|max:150',
            'nombre_madre'   => 'nullable|string|max:100',
            'celular_madre'  => 'nullable|string|max:20',
            'nombre_padre'   => 'nullable|string|max:100',
            'celular_padre'  => 'nullable|string|max:20',
        ];
    }

    /**
     * Mensajes en espanol para el estudiante.
     */
    public function messages(): array
    {
        return [
            'colegio_nombre.required' => 'Por favor ingresa el nombre de tu colegio.',
            'colegio_nombre.min'      => 'El nombre del colegio debe tener al menos 2 caracteres.',
            'nombre.required'         => 'Por favor ingresa tu nombre.',
            'nombre.min'              => 'El nombre debe tener al menos 2 caracteres.',
            'apellido.required'       => 'Por favor ingresa tu apellido.',
            'sexo.required'           => 'Selecciona una opción.',
            'sexo.in'                 => 'Selecciona una opción válida.',
            'edad.required'           => 'Indica tu edad.',
            'edad.min'                => 'La edad mínima es 12 años.',
            'edad.max'                => 'La edad máxima es 35 años.',
            'celular.required'        => 'Por favor ingresa tu número de celular.',
            'celular.min'             => 'Ingresa un número de celular válido.',
            'email.email'             => 'Ingresa un correo electrónico válido.',
        ];
    }
}

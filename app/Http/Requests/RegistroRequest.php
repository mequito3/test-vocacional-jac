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
        $colegioRequerido = config('app.show_colegio_field', true)
            && ! $this->session()->has('colegio_id');

        return [
            'colegio_nombre' => $colegioRequerido ? 'required|string|min:2|max:150' : 'nullable|string|max:150',
            'nombre'         => 'required|string|min:2|max:40',
            'apellido'       => 'required|string|min:2|max:50',
            'sexo'           => 'required|in:Masculino,Femenino,Otro',
            'edad'           => 'required|integer|min:12|max:35',
            'celular'        => 'required|string|min:7|max:15',
            'email'          => 'nullable|email|max:100',
            'nombre_madre'   => 'nullable|string|max:60',
            'celular_madre'  => 'nullable|string|max:15',
            'nombre_padre'   => 'nullable|string|max:60',
            'celular_padre'  => 'nullable|string|max:15',
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
            'colegio_nombre.max'      => 'El nombre del colegio es demasiado largo.',
            'nombre.required'         => 'Por favor ingresa tu nombre.',
            'nombre.min'              => 'El nombre debe tener al menos 2 caracteres.',
            'nombre.max'              => 'El nombre no puede superar los 40 caracteres.',
            'apellido.required'       => 'Por favor ingresa tu apellido.',
            'apellido.min'            => 'El apellido debe tener al menos 2 caracteres.',
            'apellido.max'            => 'El apellido no puede superar los 50 caracteres.',
            'sexo.required'           => 'Selecciona una opción.',
            'sexo.in'                 => 'Selecciona una opción válida.',
            'edad.required'           => 'Indica tu edad.',
            'edad.min'                => 'La edad mínima es 12 años.',
            'edad.max'                => 'La edad máxima es 35 años.',
            'celular.required'        => 'Por favor ingresa tu número de celular.',
            'celular.min'             => 'Ingresa un número de celular válido.',
            'celular.max'             => 'El número de celular es demasiado largo.',
            'email.email'             => 'Ingresa un correo electrónico válido.',
            'email.max'               => 'El correo electrónico es demasiado largo.',
            'nombre_madre.max'        => 'El nombre de la madre es demasiado largo.',
            'nombre_padre.max'        => 'El nombre del padre es demasiado largo.',
            'celular_madre.max'       => 'El celular de la madre es demasiado largo.',
            'celular_padre.max'       => 'El celular del padre es demasiado largo.',
        ];
    }
}

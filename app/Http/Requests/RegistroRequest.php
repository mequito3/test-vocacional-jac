<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class RegistroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'colegio_nombre' => $this->limpiarTexto($this->input('colegio_nombre')),
            'nombre' => $this->limpiarTexto($this->input('nombre')),
            'apellido' => $this->limpiarTexto($this->input('apellido')),
            'celular' => $this->limpiarTelefono($this->input('celular')),
            'email' => $this->limpiarTexto($this->input('email')),
            'nombre_madre' => $this->limpiarTexto($this->input('nombre_madre')),
            'celular_madre' => $this->limpiarTelefono($this->input('celular_madre')),
            'nombre_padre' => $this->limpiarTexto($this->input('nombre_padre')),
            'celular_padre' => $this->limpiarTelefono($this->input('celular_padre')),
        ]);
    }

    /**
     * Reglas de validacion de la ficha del estudiante.
     */
    public function rules(): array
    {
        $colegioRequerido = config('app.show_colegio_field', true)
            && ! $this->session()->has('colegio_id');

        return [
            'colegio_nombre' => $colegioRequerido ? 'required|string|min:2|max:120' : 'nullable|string|max:120',
            'nombre'         => ['required', 'string', 'min:2', 'max:35', "regex:/^[\\pL\\pM .'-]+$/u"],
            'apellido'       => ['required', 'string', 'min:2', 'max:40', "regex:/^[\\pL\\pM .'-]+$/u"],
            'sexo'           => 'required|in:Masculino,Femenino,Otro',
            'edad'           => 'required|integer|min:12|max:35',
            'celular'        => 'required|string|min:7|max:12',
            'email'          => 'nullable|email|max:100',
            'nombre_madre'   => ['nullable', 'string', 'max:60', "regex:/^[\\pL\\pM .'-]+$/u"],
            'celular_madre'  => 'nullable|string|max:12',
            'nombre_padre'   => ['nullable', 'string', 'max:60', "regex:/^[\\pL\\pM .'-]+$/u"],
            'celular_padre'  => 'nullable|string|max:12',
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
            'colegio_nombre.max'      => 'El nombre del colegio no puede superar los 120 caracteres.',
            'nombre.required'         => 'Por favor ingresa tu nombre.',
            'nombre.min'              => 'El nombre debe tener al menos 2 caracteres.',
            'nombre.max'              => 'El nombre no puede superar los 35 caracteres.',
            'nombre.regex'            => 'El nombre solo puede contener letras, espacios, punto, apostrofe o guion.',
            'apellido.required'       => 'Por favor ingresa tu apellido.',
            'apellido.min'            => 'El apellido debe tener al menos 2 caracteres.',
            'apellido.max'            => 'El apellido no puede superar los 40 caracteres.',
            'apellido.regex'          => 'El apellido solo puede contener letras, espacios, punto, apostrofe o guion.',
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
            'nombre_madre.regex'      => 'El nombre de la madre solo puede contener letras y espacios.',
            'nombre_padre.max'        => 'El nombre del padre es demasiado largo.',
            'nombre_padre.regex'      => 'El nombre del padre solo puede contener letras y espacios.',
            'celular_madre.max'       => 'El celular de la madre es demasiado largo.',
            'celular_padre.max'       => 'El celular del padre es demasiado largo.',
        ];
    }


    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $this->validarNombreHumano($validator, 'nombre', 4);
            $this->validarNombreHumano($validator, 'apellido', 4);
            $this->validarNombreHumano($validator, 'nombre_madre', 6, false);
            $this->validarNombreHumano($validator, 'nombre_padre', 6, false);
        });
    }

    private function limpiarTexto(mixed $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        $texto = preg_replace('/\s+/u', ' ', trim((string) $valor));

        return $texto === '' ? null : $texto;
    }

    private function limpiarTelefono(mixed $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        $telefono = preg_replace('/[^0-9+]/', '', trim((string) $valor));

        return $telefono === '' ? null : $telefono;
    }

    private function validarNombreHumano(Validator $validator, string $campo, int $maxPalabras, bool $requerido = true): void
    {
        $valor = (string) $this->input($campo, '');
        if ($valor === '') {
            return;
        }

        $palabras = preg_split('/\s+/u', $valor, -1, PREG_SPLIT_NO_EMPTY);
        if (count($palabras) > $maxPalabras) {
            $validator->errors()->add($campo, "Este campo no puede tener mas de {$maxPalabras} palabras.");
        }

        foreach ($palabras as $palabra) {
            if (mb_strlen($palabra) > 18) {
                $validator->errors()->add($campo, 'Hay una palabra demasiado larga. Revisa que el dato este escrito correctamente.');
                break;
            }

            if (preg_match('/([\pL\pM])\1{3,}/iu', $palabra)) {
                $validator->errors()->add($campo, 'No se permiten letras repetidas muchas veces seguidas.');
                break;
            }
        }
    }
}

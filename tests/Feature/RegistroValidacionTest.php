<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verifica todas las reglas de validación del formulario de registro del estudiante.
 */
class RegistroValidacionTest extends TestCase
{
    use RefreshDatabase;

    // ── Campos requeridos ─────────────────────────────────────────

    public function test_nombre_requerido(): void
    {
        $this->post(route('registro.guardar'), $this->datos(['nombre' => '']))
            ->assertSessionHasErrors('nombre');
    }

    public function test_apellido_requerido(): void
    {
        $this->post(route('registro.guardar'), $this->datos(['apellido' => '']))
            ->assertSessionHasErrors('apellido');
    }

    public function test_colegio_requerido(): void
    {
        $this->post(route('registro.guardar'), $this->datos(['colegio_nombre' => '']))
            ->assertSessionHasErrors('colegio_nombre');
    }

    public function test_sexo_requerido(): void
    {
        $this->post(route('registro.guardar'), $this->datos(['sexo' => '']))
            ->assertSessionHasErrors('sexo');
    }

    public function test_edad_requerida(): void
    {
        $this->post(route('registro.guardar'), $this->datos(['edad' => '']))
            ->assertSessionHasErrors('edad');
    }

    public function test_celular_requerido(): void
    {
        $this->post(route('registro.guardar'), $this->datos(['celular' => '']))
            ->assertSessionHasErrors('celular');
    }

    // ── Validaciones de sexo ──────────────────────────────────────

    public function test_sexo_invalido_rechazado(): void
    {
        $this->post(route('registro.guardar'), $this->datos(['sexo' => 'Robot']))
            ->assertSessionHasErrors('sexo');
    }

    public function test_sexo_masculino_valido(): void
    {
        $this->post(route('registro.guardar'), $this->datos(['sexo' => 'Masculino']))
            ->assertSessionDoesntHaveErrors('sexo');
    }

    public function test_sexo_femenino_valido(): void
    {
        $this->post(route('registro.guardar'), $this->datos(['sexo' => 'Femenino']))
            ->assertSessionDoesntHaveErrors('sexo');
    }

    public function test_sexo_otro_valido(): void
    {
        $this->post(route('registro.guardar'), $this->datos(['sexo' => 'Otro']))
            ->assertSessionDoesntHaveErrors('sexo');
    }

    // ── Validaciones de edad ──────────────────────────────────────

    public function test_edad_menor_a_12_rechazada(): void
    {
        $this->post(route('registro.guardar'), $this->datos(['edad' => 11]))
            ->assertSessionHasErrors('edad');
    }

    public function test_edad_12_aceptada(): void
    {
        $this->post(route('registro.guardar'), $this->datos(['edad' => 12]))
            ->assertSessionDoesntHaveErrors('edad');
    }

    public function test_edad_mayor_a_35_rechazada(): void
    {
        $this->post(route('registro.guardar'), $this->datos(['edad' => 36]))
            ->assertSessionHasErrors('edad');
    }

    public function test_edad_35_aceptada(): void
    {
        $this->post(route('registro.guardar'), $this->datos(['edad' => 35]))
            ->assertSessionDoesntHaveErrors('edad');
    }

    public function test_edad_no_numerica_rechazada(): void
    {
        $this->post(route('registro.guardar'), $this->datos(['edad' => 'diecisiete']))
            ->assertSessionHasErrors('edad');
    }

    // ── Validaciones de email (campo opcional) ────────────────────

    public function test_email_invalido_rechazado(): void
    {
        $this->post(route('registro.guardar'), $this->datos(['email' => 'no-es-email']))
            ->assertSessionHasErrors('email');
    }

    public function test_email_vacio_permitido(): void
    {
        $this->post(route('registro.guardar'), $this->datos(['email' => '']))
            ->assertSessionDoesntHaveErrors('email');
    }

    public function test_email_valido_aceptado(): void
    {
        $this->post(route('registro.guardar'), $this->datos(['email' => 'alumno@colegio.edu.bo']))
            ->assertSessionDoesntHaveErrors('email');
    }

    // ── Validaciones de longitud ──────────────────────────────────

    public function test_nombre_de_un_solo_caracter_rechazado(): void
    {
        $this->post(route('registro.guardar'), $this->datos(['nombre' => 'A']))
            ->assertSessionHasErrors('nombre');
    }

    public function test_celular_menor_a_7_digitos_rechazado(): void
    {
        $this->post(route('registro.guardar'), $this->datos(['celular' => '123']))
            ->assertSessionHasErrors('celular');
    }

    public function test_colegio_de_un_solo_caracter_rechazado(): void
    {
        $this->post(route('registro.guardar'), $this->datos(['colegio_nombre' => 'X']))
            ->assertSessionHasErrors('colegio_nombre');
    }

    // ── Campos opcionales de padres ───────────────────────────────

    public function test_datos_de_padres_son_completamente_opcionales(): void
    {
        $this->post(route('registro.guardar'), $this->datos([
            'nombre_madre'  => '',
            'celular_madre' => '',
            'nombre_padre'  => '',
            'celular_padre' => '',
        ]))->assertSessionDoesntHaveErrors([
            'nombre_madre', 'celular_madre', 'nombre_padre', 'celular_padre',
        ]);
    }

    // ── Helper ────────────────────────────────────────────────────

    private function datos(array $overrides = []): array
    {
        return array_merge([
            'colegio_nombre' => 'Colegio San Miguel',
            'nombre'         => 'Ana',
            'apellido'       => 'López',
            'sexo'           => 'Femenino',
            'edad'           => 17,
            'celular'        => '71234567',
            'email'          => '',
        ], $overrides);
    }
}

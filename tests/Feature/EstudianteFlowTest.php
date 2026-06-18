<?php

namespace Tests\Feature;

use App\Models\Colegio;
use App\Models\Estudiante;
use App\Models\ResultadoChaside;
use App\Support\Chaside;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cubre el flujo completo del estudiante:
 * Bienvenida → Registro → Test → Resultado guardado en BD → Welcome.
 * El estudiante nunca ve ni descarga el PDF.
 */
class EstudianteFlowTest extends TestCase
{
    use RefreshDatabase;

    // ── Páginas públicas ──────────────────────────────────────────

    public function test_pagina_bienvenida_carga(): void
    {
        $this->get(route('welcome'))->assertOk();
    }

    public function test_pagina_registro_carga(): void
    {
        $this->get(route('registro'))->assertOk();
    }

    // ── Registro exitoso ──────────────────────────────────────────

    public function test_registro_valido_crea_estudiante_y_redirige_al_test(): void
    {
        $this->post(route('registro.guardar'), $this->datosValidos())
            ->assertRedirect(route('test'));

        $this->assertDatabaseCount('estudiantes', 1);
    }

    public function test_registro_crea_colegio_nuevo_si_no_existe(): void
    {
        $this->post(route('registro.guardar'), $this->datosValidos([
            'colegio_nombre' => 'Colegio Prueba Nuevo',
        ]));

        $this->assertDatabaseHas('colegios', ['nombre' => 'Colegio Prueba Nuevo']);
    }

    public function test_registro_no_duplica_colegio_existente(): void
    {
        Colegio::factory()->create(['nombre' => 'Colegio Ya Existente']);

        $this->post(route('registro.guardar'), $this->datosValidos(['colegio_nombre' => 'Colegio Ya Existente']));
        $this->post(route('registro.guardar'), $this->datosValidos(['colegio_nombre' => 'Colegio Ya Existente']));

        $this->assertDatabaseCount('colegios', 1);
    }

    public function test_registro_guarda_estudiante_id_en_sesion(): void
    {
        $response = $this->post(route('registro.guardar'), $this->datosValidos());

        $response->assertSessionHas('estudiante_id', Estudiante::first()->id);
    }

    public function test_registro_guarda_todos_los_campos_del_estudiante(): void
    {
        $this->post(route('registro.guardar'), $this->datosValidos([
            'nombre'        => 'Ana',
            'apellido'      => 'Pérez',
            'sexo'          => 'Femenino',
            'edad'          => 17,
            'celular'       => '71234567',
            'email'         => 'ana@example.com',
            'nombre_madre'  => 'María Quispe',
            'celular_madre' => '70000001',
        ]));

        $this->assertDatabaseHas('estudiantes', [
            'nombre'       => 'Ana',
            'apellido'     => 'Pérez',
            'sexo'         => 'Femenino',
            'edad'         => 17,
            'celular'      => '71234567',
            'email'        => 'ana@example.com',
            'nombre_madre' => 'María Quispe',
        ]);
    }

    // ── Token de grupo ────────────────────────────────────────────

    public function test_token_de_grupo_valido_guarda_colegio_en_sesion_y_redirige(): void
    {
        $colegio = Colegio::factory()->create(['token' => 'abc123abc123']);

        $this->get(route('grupo', 'abc123abc123'))
            ->assertRedirect(route('welcome'))
            ->assertSessionHas('colegio_id', $colegio->id);
    }

    public function test_token_de_grupo_invalido_retorna_404(): void
    {
        $this->get(route('grupo', 'tokeninexistente'))->assertNotFound();
    }

    public function test_registro_con_token_de_grupo_asigna_colegio_del_grupo(): void
    {
        $colegio = Colegio::factory()->create();

        $this->withSession(['colegio_id' => $colegio->id])
            ->post(route('registro.guardar'), $this->datosValidos())
            ->assertRedirect(route('test'));

        $this->assertDatabaseHas('estudiantes', ['colegio_id' => $colegio->id]);
    }

    // ── Acceso al test ────────────────────────────────────────────

    public function test_test_sin_sesion_redirige_a_registro(): void
    {
        $this->get(route('test'))->assertRedirect(route('registro'));
    }

    public function test_test_con_sesion_valida_carga_las_98_preguntas(): void
    {
        $estudiante = Estudiante::factory()->create();

        $this->withSession(['estudiante_id' => $estudiante->id])
            ->get(route('test'))
            ->assertOk()
            ->assertSee('SÍ')
            ->assertSee('NO')
            ->assertSee('98');
    }

    // ── Cálculo y guardado del resultado ─────────────────────────

    public function test_calcular_sin_sesion_redirige_a_registro(): void
    {
        $this->post(route('resultado.calcular'), ['respuestas' => $this->respuestas()])
            ->assertRedirect(route('registro'));
    }

    public function test_calcular_guarda_resultado_en_base_de_datos(): void
    {
        $estudiante = Estudiante::factory()->create();

        $this->withSession(['estudiante_id' => $estudiante->id])
            ->post(route('resultado.calcular'), ['respuestas' => $this->respuestas()]);

        $this->assertDatabaseCount('resultados_chaside', 1);
        $this->assertDatabaseHas('resultados_chaside', ['estudiante_id' => $estudiante->id]);
    }

    public function test_calcular_con_todo_si_suma_14_por_area(): void
    {
        $estudiante = Estudiante::factory()->create();

        $this->withSession(['estudiante_id' => $estudiante->id])
            ->post(route('resultado.calcular'), ['respuestas' => $this->respuestas(todoSi: true)]);

        $r = ResultadoChaside::first();
        $this->assertSame(14, $r->puntaje_c);
        $this->assertSame(14, $r->puntaje_h);
        $this->assertSame(14, $r->puntaje_a);
        $this->assertSame(14, $r->puntaje_s);
        $this->assertSame(14, $r->puntaje_i);
        $this->assertSame(14, $r->puntaje_d);
        $this->assertSame(14, $r->puntaje_e);
    }

    public function test_calcular_determina_area_principal_correctamente(): void
    {
        $estudiante = Estudiante::factory()->create();

        // Solo respuestas SÍ en preguntas del área S (Salud)
        $respuestas = array_fill_keys(range(1, 98), 0);
        foreach (Chaside::TABLA['S'] as $n) {
            $respuestas[$n] = 1;
        }

        $this->withSession(['estudiante_id' => $estudiante->id])
            ->post(route('resultado.calcular'), ['respuestas' => $respuestas]);

        $this->assertDatabaseHas('resultados_chaside', ['area_principal' => 'S']);
    }

    public function test_calcular_genera_share_token_de_32_chars(): void
    {
        $estudiante = Estudiante::factory()->create();

        $this->withSession(['estudiante_id' => $estudiante->id])
            ->post(route('resultado.calcular'), ['respuestas' => $this->respuestas()]);

        $resultado = ResultadoChaside::first();
        $this->assertNotNull($resultado->share_token);
        $this->assertSame(32, strlen($resultado->share_token));
    }

    public function test_calcular_guarda_las_98_respuestas_en_json(): void
    {
        $estudiante = Estudiante::factory()->create();

        $this->withSession(['estudiante_id' => $estudiante->id])
            ->post(route('resultado.calcular'), ['respuestas' => $this->respuestas()]);

        $resultado = ResultadoChaside::first();
        $this->assertIsArray($resultado->respuestas_json);
        $this->assertCount(98, $resultado->respuestas_json);
    }

    public function test_calcular_redirige_a_bienvenida(): void
    {
        $estudiante = Estudiante::factory()->create();

        $this->withSession(['estudiante_id' => $estudiante->id])
            ->post(route('resultado.calcular'), ['respuestas' => $this->respuestas()])
            ->assertRedirect(route('welcome'));
    }

    public function test_calcular_elimina_sesion_del_estudiante(): void
    {
        $estudiante = Estudiante::factory()->create();

        $this->withSession(['estudiante_id' => $estudiante->id])
            ->post(route('resultado.calcular'), ['respuestas' => $this->respuestas()])
            ->assertSessionMissing('estudiante_id');
    }

    // ── El estudiante NO puede ver el resultado después del test ──

    public function test_resultado_mostrar_sin_sesion_redirige_a_welcome(): void
    {
        $resultado = ResultadoChaside::factory()->create();

        $this->get(route('resultado.mostrar', $resultado->id))
            ->assertRedirect(route('welcome'));
    }

    public function test_resultado_mostrar_con_sesion_de_otro_estudiante_redirige(): void
    {
        $otroEstudiante = Estudiante::factory()->create();
        $resultado      = ResultadoChaside::factory()->create();

        $this->withSession(['estudiante_id' => $otroEstudiante->id])
            ->get(route('resultado.mostrar', $resultado->id))
            ->assertRedirect(route('welcome'));
    }

    // ── Reiniciar ─────────────────────────────────────────────────

    public function test_reiniciar_borra_sesion_y_redirige_a_bienvenida(): void
    {
        $estudiante = Estudiante::factory()->create();

        $this->withSession(['estudiante_id' => $estudiante->id])
            ->get(route('reiniciar'))
            ->assertRedirect(route('welcome'))
            ->assertSessionMissing('estudiante_id');
    }

    // ── Resultado público compartido (share) ──────────────────────

    public function test_share_publico_accesible_con_token_valido(): void
    {
        $token     = str_repeat('a', 32);
        ResultadoChaside::factory()->create(['share_token' => $token]);

        $this->get(route('resultado.share', $token))->assertOk();
    }

    public function test_share_publico_retorna_404_con_token_invalido(): void
    {
        $this->get(route('resultado.share', 'este_token_no_existe_xyz'))->assertNotFound();
    }

    // ── Helpers ───────────────────────────────────────────────────

    private function datosValidos(array $overrides = []): array
    {
        return array_merge([
            'colegio_nombre' => 'Colegio San Miguel',
            'nombre'         => 'María',
            'apellido'       => 'Quispe',
            'sexo'           => 'Femenino',
            'edad'           => 17,
            'celular'        => '71234567',
            'email'          => '',
        ], $overrides);
    }

    /** Respuestas para las 98 preguntas. $todoSi=true → todas en 1. */
    private function respuestas(bool $todoSi = false): array
    {
        $r = [];
        foreach (range(1, 98) as $n) {
            $r[$n] = $todoSi ? 1 : ($n % 2 === 0 ? 1 : 0);
        }
        return $r;
    }
}

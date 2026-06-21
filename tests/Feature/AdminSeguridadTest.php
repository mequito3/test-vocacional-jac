<?php

namespace Tests\Feature;

use App\Models\Colegio;
use App\Models\Estudiante;
use App\Models\ResultadoChaside;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verifica que los estudiantes no puedan acceder al panel de administración
 * ni descargar PDFs sin la sesión correcta.
 */
class AdminSeguridadTest extends TestCase
{
    use RefreshDatabase;

    // ── Admin protegido sin sesión ────────────────────────────────

    public function test_dashboard_admin_sin_auth_redirige_a_login(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_ver_colegio_sin_auth_redirige_a_login(): void
    {
        $colegio = Colegio::factory()->create();

        $this->get(route('admin.colegios.ver', $colegio->id))
            ->assertRedirect(route('admin.login'));
    }

    public function test_sin_colegio_sin_auth_redirige_a_login(): void
    {
        $this->get(route('admin.sin_colegio'))
            ->assertRedirect(route('admin.login'));
    }

    // ── Sesión de estudiante no sirve para admin ──────────────────

    public function test_sesion_de_estudiante_no_da_acceso_al_dashboard(): void
    {
        $estudiante = Estudiante::factory()->create();

        $this->withSession(['estudiante_id' => $estudiante->id])
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_sesion_de_estudiante_no_da_acceso_a_colegios(): void
    {
        $estudiante = Estudiante::factory()->create();
        $colegio    = Colegio::factory()->create();

        $this->withSession(['estudiante_id' => $estudiante->id])
            ->get(route('admin.colegios.ver', $colegio->id))
            ->assertRedirect(route('admin.login'));
    }

    // ── PDF protegido ─────────────────────────────────────────────

    public function test_pdf_sin_sesion_redirige_a_welcome(): void
    {
        $resultado = ResultadoChaside::factory()->create();

        $this->get(route('resultado.pdf', $resultado->id))
            ->assertRedirect(route('welcome'));
    }

    public function test_pdf_con_sesion_de_otro_estudiante_redirige_a_welcome(): void
    {
        $otroEstudiante = Estudiante::factory()->create();
        $resultado      = ResultadoChaside::factory()->create();

        $this->withSession(['estudiante_id' => $otroEstudiante->id])
            ->get(route('resultado.pdf', $resultado->id))
            ->assertRedirect(route('welcome'));
    }

    public function test_resultado_inexistente_retorna_404(): void
    {
        $this->get(route('resultado.mostrar', 99999))->assertNotFound();
    }

    // ── Login admin es público ────────────────────────────────────

    public function test_pagina_login_admin_es_accesible(): void
    {
        $this->get(route('admin.login'))->assertOk();
    }

    public function test_login_rechaza_acceso_si_no_hay_password_configurado(): void
    {
        config()->set('app.admin_password', null);

        $this->post(route('admin.login.post'), ['password' => 'admin123'])
            ->assertSessionHasErrors('password');

        $this->assertFalse((bool) session('admin_authed'));
    }

    public function test_login_acepta_password_configurado(): void
    {
        config()->set('app.admin_password', 'una-clave-segura-de-prueba');

        $this->post(route('admin.login.post'), ['password' => 'una-clave-segura-de-prueba'])
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('admin_authed', true);
    }

    public function test_login_admin_limita_intentos(): void
    {
        config()->set('app.admin_password', 'una-clave-segura-de-prueba');

        for ($intento = 0; $intento < 5; $intento++) {
            $this->post(route('admin.login.post'), ['password' => 'incorrecta']);
        }

        $this->post(route('admin.login.post'), ['password' => 'incorrecta'])
            ->assertTooManyRequests();
    }
}

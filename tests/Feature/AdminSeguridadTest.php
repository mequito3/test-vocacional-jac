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

        for ($intento = 0; $intento < 20; $intento++) {
            $this->post(route('admin.login.post'), ['password' => 'incorrecta']);
        }

        $this->post(route('admin.login.post'), ['password' => 'incorrecta'])
            ->assertTooManyRequests();
    }

    public function test_dashboard_admin_oculta_colegios_sin_estudiantes(): void
    {
        Colegio::factory()->create(['nombre' => 'Colegio Nuevo Sin Alumnos']);

        $this->withSession(['admin_authed' => true])
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Aún no hay colegios registrados.')
            ->assertDontSee('0 estudiantes');
    }

    public function test_dashboard_admin_muestra_colegios_con_estudiantes(): void
    {
        $colegio = Colegio::factory()->create(['nombre' => 'Colegio Con Alumnos']);
        Estudiante::factory()->create(['colegio_id' => $colegio->id]);

        $this->withSession(['admin_authed' => true])
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Colegio Con Alumnos')
            ->assertSee('estudiante');
    }

    public function test_crear_colegio_redirige_a_su_pagina_de_enlace(): void
    {
        $response = $this->withSession(['admin_authed' => true])
            ->post(route('admin.colegios.crear'), ['nombre' => 'Unidad Educativa Nueva']);

        $colegio = Colegio::where('nombre', 'Unidad Educativa Nueva')->firstOrFail();

        $response
            ->assertRedirect(route('admin.colegios.ver', $colegio->id))
            ->assertSessionHas('success');
    }

    public function test_crear_colegio_existente_no_duplica_registro(): void
    {
        $colegio = Colegio::factory()->create(['nombre' => 'Colegio Existente']);

        $this->withSession(['admin_authed' => true])
            ->post(route('admin.colegios.crear'), ['nombre' => 'Colegio Existente'])
            ->assertRedirect(route('admin.colegios.ver', $colegio->id));

        $this->assertDatabaseCount('colegios', 1);
    }

    public function test_dashboard_admin_muestra_acceso_a_estudiantes_sin_colegio(): void
    {
        Estudiante::factory()->create(['colegio_id' => null]);

        $this->withSession(['admin_authed' => true])
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Sin colegio asignado')
            ->assertSee(route('admin.sin_colegio'), false);
    }
}

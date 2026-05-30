<?php

namespace Tests\Unit;

use App\Support\Chaside;
use PHPUnit\Framework\TestCase;

/**
 * Protege la integridad de la clave de correccion CHASIDE.
 * Si alguien vuelve a duplicar u olvidar una pregunta, estas pruebas fallan.
 */
class ChasideTest extends TestCase
{
    public function test_cada_pregunta_pertenece_a_exactamente_una_area(): void
    {
        $ubicacion = [];
        foreach (Chaside::TABLA as $area => $preguntas) {
            foreach ($preguntas as $numero) {
                $ubicacion[$numero][] = $area;
            }
        }

        $repetidas = array_filter($ubicacion, fn ($areas) => count($areas) > 1);
        $this->assertSame([], $repetidas, 'Hay preguntas asignadas a mas de un area.');
    }

    public function test_las_98_preguntas_estan_cubiertas_sin_huecos(): void
    {
        $numeros = array_merge(...array_values(Chaside::TABLA));
        sort($numeros);

        $this->assertSame(range(1, 98), $numeros, 'La clave no cubre exactamente las preguntas 1..98.');
    }

    public function test_la_tabla_coincide_con_las_preguntas_definidas(): void
    {
        $this->assertSame(98, count(Chaside::PREGUNTAS), 'Deben existir 98 preguntas.');

        foreach (Chaside::TABLA as $area => $preguntas) {
            foreach ($preguntas as $numero) {
                $this->assertArrayHasKey($numero, Chaside::PREGUNTAS, "La pregunta {$numero} ({$area}) no existe.");
            }
        }
    }

    public function test_responder_si_a_todo_suma_98_puntos(): void
    {
        $respuestas = array_fill_keys(range(1, 98), true);
        $total = array_sum(Chaside::calcular($respuestas)['puntajes']);

        $this->assertSame(98, $total, 'La suma total con todo en SI debe ser 98.');
    }

    public function test_principal_y_secundaria_son_areas_distintas_y_validas(): void
    {
        // Estudiante que solo responde SI a las preguntas del area de Ingenieria.
        $respuestas = array_fill_keys(range(1, 98), false);
        foreach (Chaside::TABLA['I'] as $numero) {
            $respuestas[$numero] = true;
        }

        $calculo = Chaside::calcular($respuestas);

        $this->assertSame('I', $calculo['principal'], 'El area principal deberia ser I.');
        $this->assertNotSame($calculo['principal'], $calculo['secundaria'], 'Principal y secundaria no pueden ser iguales.');
        $this->assertArrayHasKey($calculo['secundaria'], Chaside::AREAS);
    }
}

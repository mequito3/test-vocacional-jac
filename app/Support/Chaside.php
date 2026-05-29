<?php

namespace App\Support;

/**
 * Datos y logica de correccion del Test de Orientacion Vocacional CHASIDE
 * para el Instituto Tecnico JAC Bolivia 2000.
 *
 * Centraliza:
 *  - Las 98 preguntas del cuestionario.
 *  - La tabla de correccion (que preguntas suman a cada area si se responden SI).
 *  - Los datos de cada area (nombre, color, intereses, aptitudes, carreras).
 *  - El algoritmo que calcula puntajes y determina area principal y secundaria.
 */
class Chaside
{
    /**
     * Tabla CHASIDE: por cada area, las preguntas que suman 1 punto
     * cuando el estudiante responde SI.
     */
    public const TABLA = [
        'C' => [1, 12, 15, 20, 22, 26, 46, 51, 53, 71, 78, 85, 91, 98],
        'H' => [9, 25, 30, 34, 41, 45, 56, 63, 67, 72, 74, 80, 86, 89, 95],
        'A' => [2, 11, 21, 28, 50, 57, 59, 76, 81, 82, 96],
        'S' => [5, 8, 23, 29, 33, 40, 44, 58, 62, 69, 70, 87, 92, 95],
        'I' => [3, 6, 19, 22, 26, 27, 38, 47, 54, 60, 65, 83, 90, 97],
        'D' => [4, 10, 13, 18, 24, 31, 36, 37, 39, 43, 48, 58, 66, 73, 79, 84, 90, 94],
        'E' => [7, 17, 32, 35, 38, 42, 49, 52, 55, 61, 68, 70, 75, 77, 88, 93],
    ];

    /**
     * Datos descriptivos de cada area para el informe de resultados.
     */
    public const AREAS = [
        'C' => [
            'nombre'    => 'Administrativas y Contables',
            'color'     => '#2557a7',
            'intereses' => ['Organizativo', 'Supervisión', 'Orden', 'Análisis y síntesis', 'Colaboración', 'Cálculo'],
            'aptitudes' => ['Persuasivo', 'Objetivo', 'Práctico', 'Tolerante', 'Responsable', 'Ambicioso'],
            'carreras'  => ['Administración de Empresas', 'Contaduría General', 'Secretariado Ejecutivo', 'Comercio Internacional', 'Auditoría', 'Marketing'],
        ],
        'H' => [
            'nombre'    => 'Humanísticas y Sociales',
            'color'     => '#7c3aed',
            'intereses' => ['Precisión Verbal', 'Organización', 'Relación de Hechos', 'Lingüística', 'Orden', 'Justicia'],
            'aptitudes' => ['Responsable', 'Justo', 'Conciliador', 'Persuasivo', 'Sagaz', 'Imaginativo', 'Comprensivo', 'Estabilidad Emocional'],
            'carreras'  => ['Lic. en Psicología', 'Lic. en Cs. de la Educación', 'Lic. en Lingüística', 'Lic. en Trabajo Social', 'Lic. en Comunicación Social', 'Lic. en Ciencias Políticas', 'Historia', 'Derecho'],
        ],
        'A' => [
            'nombre'    => 'Artísticas',
            'color'     => '#db2777',
            'intereses' => ['Estético', 'Armónico', 'Manual', 'Visual', 'Auditivo', 'Sensible'],
            'aptitudes' => ['Imaginativo', 'Creativo', 'Detallista', 'Innovador', 'Intuitivo'],
            'carreras'  => ['Diseño Gráfico', 'Arquitectura', 'Artes Plásticas', 'Música', 'Diseño de Modas', 'Publicidad', 'Fotografía'],
        ],
        'S' => [
            'nombre'    => 'Medicina y Cs. de la Salud',
            'color'     => '#059669',
            'intereses' => ['Asistir', 'Investigativo', 'Precisión', 'Senso-Perceptivo', 'Analítico', 'Ayudar'],
            'aptitudes' => ['Altruista', 'Solidario', 'Paciente', 'Comprensivo', 'Respetuoso', 'Persuasivo'],
            'carreras'  => ['Medicina', 'Enfermería', 'Odontología', 'Farmacia', 'Bioquímica', 'Fisioterapia', 'Nutrición'],
        ],
        'I' => [
            'nombre'    => 'Ingeniería y Computación',
            'color'     => '#d97706',
            'intereses' => ['Cálculo', 'Científico', 'Manual', 'Exacto', 'Planificar', 'Preciso'],
            'aptitudes' => ['Práctico', 'Crítico', 'Analítico', 'Metódico'],
            'carreras'  => ['Ingeniería Informática', 'Ingeniería Civil', 'Ingeniería Industrial', 'Ingeniería Electrónica', 'Sistemas', 'Telecomunicaciones'],
        ],
        'D' => [
            'nombre'    => 'Defensa y Seguridad',
            'color'     => '#dc2626',
            'intereses' => ['Justicia', 'Equidad', 'Colaboración', 'Espíritu de Equipo', 'Liderazgo', 'Arriesgado'],
            'aptitudes' => ['Solidario', 'Valiente', 'Agresivo', 'Persuasivo'],
            'carreras'  => ['Policía Nacional', 'Fuerzas Armadas', 'Bomberos', 'Criminología', 'Seguridad Privada', 'Derecho Penal'],
        ],
        'E' => [
            'nombre'    => 'Ciencias Exactas y Agrarias',
            'color'     => '#16a34a',
            'intereses' => ['Investigación', 'Orden', 'Organización', 'Análisis y Síntesis', 'Numérico', 'Clasificar'],
            'aptitudes' => ['Metódico', 'Analítico', 'Observador', 'Introvertido', 'Paciente', 'Seguro'],
            'carreras'  => ['Biología', 'Química', 'Física', 'Matemáticas', 'Agronomía', 'Veterinaria', 'Geología'],
        ],
    ];

    /**
     * Las 98 preguntas del cuestionario, indexadas de 1 a 98.
     */
    public const PREGUNTAS = [
        1  => '¿Aceptarías trabajar escribiendo artículos en la sección del periódico "Los Tiempos"?',
        2  => '¿Organizarías la despedida de soltero de un amigo?',
        3  => '¿Te gustaría ser ingeniero para dirigir un proyecto de urbanización en una provincia?',
        4  => '¿Ante un fracaso mantienes un pensamiento positivo?',
        5  => '¿Ayudarías voluntariamente a personas accidentadas o atacadas por asaltantes?',
        6  => '¿Cuándo eras niño(a), te interesaba saber cómo estaban construidos tus juguetes?',
        7  => '¿Tienes mayor curiosidad por los misterios de la naturaleza que por los avances de la tecnología?',
        8  => '¿Escuchas atentamente las complicaciones que te plantean tus amigos?',
        9  => '¿Te brindarías para explicar a tus compañeros un tema que ellos no entendieron?',
        10 => '¿Cuándo trabajas en equipo te consideras estricto y crítico?',
        11 => '¿Te atrae armar rompecabezas?',
        12 => '¿Sabes qué significa macroeconomía y microeconomía?',
        13 => '¿Usar traje formal como profesional te hace sentir diferente e importante?',
        14 => '¿Participarías de una investigación sobre la Violencia Contra la Mujer?',
        15 => '¿Administras tu dinero de manera que te alcance para todos tus gastos mensuales?',
        16 => '¿Tienes la facilidad de convencer sobre la validez de tus argumentos a otras personas?',
        17 => '¿Sabes sobre los descubrimientos que se están realizando sobre la Teoría del Big Bang?',
        18 => '¿Actuarías con rapidez ante una probable situación de emergencia?',
        19 => '¿Al resolver un problema matemático, insistes hasta hallar la respuesta?',
        20 => '¿Si tu equipo favorito te convocara para dirigir un campo deportivo, aceptarías?',
        21 => '¿Eres el que pone un toque de alegría en las fiestas de cumpleaños?',
        22 => '¿Crees que los detalles son muy importantes en tu vida?',
        23 => '¿Te sentirías a gusto trabajando en el hospital "Viedma"?',
        24 => '¿Te gustaría mantener la calma en situaciones de emergencia o sismos?',
        25 => '¿Dedicarías varias horas para leer un libro de tu preferencia?',
        26 => '¿Te organizas detalladamente antes de empezar tus trabajos de investigación?',
        27 => '¿Controla tu vida personal tu celular?',
        28 => '¿Disfrutas trabajando con arcilla o plastilina?',
        29 => '¿Ayudas frecuentemente a los no videntes, ancianos(as) a cruzar la calle?',
        30 => '¿Crees que los estudiantes de primaria deberían ser críticos y reflexivos?',
        31 => '¿Considerando la igualdad de géneros, aceptarías que las mujeres formen parte de las fuerzas armadas con las mismas reglas que los hombres?',
        32 => '¿Te gustaría innovar nuevas técnicas a través del microscopio para descubrir nuevas enfermedades?',
        33 => '¿Participarías en una campaña sobre la prevención del VIH?',
        34 => '¿Te importa conocer temas del pasado y la evolución del ser humano?',
        35 => '¿Te gustaría participar en un proyecto de investigación de los movimientos sísmicos y sus consecuencias?',
        36 => '¿Fuera de los horarios escolares realizas algún deporte?',
        37 => '¿Te interesan las actividades de mucha acción como las carreras de autos y de reacción rápida en situaciones imprevistas como un accidente?',
        38 => '¿Te ofrecerías como ayudante para colaborar en los gabinetes espaciales de la NASA?',
        39 => '¿Te interesa más el trabajo manual que el trabajo intelectual?',
        40 => '¿Estarías dispuesto a renunciar a un momento familiar de diversión para ofrecer tus servicios profesionales?',
        41 => '¿Participarías en una investigación sobre violencia de género?',
        42 => '¿Te gustaría trabajar en un laboratorio bioquímico mientras estudias?',
        43 => '¿Expondrías tu vida para salvar otra vida, así no la conozcas?',
        44 => '¿Te gustaría conocer sobre los cursos de primeros auxilios para salvar vidas?',
        45 => '¿Intentarías trabajar un proyecto todas las veces necesarias hasta cumplir tu objetivo?',
        46 => '¿Organizas correctamente tu tiempo para alcanzar lo planificado?',
        47 => '¿Te gustaría aprender a fabricar y reparar máquinas?',
        48 => '¿Elegirías una profesión de tu agrado en la que estuvieras alejado de tu familia?',
        49 => '¿Te gustaría vivir en una zona agrícola-ganadera para desarrollarte como profesional?',
        50 => '¿Cuándo trabajas en equipo te gusta producir ideas originales para que te tomen en cuenta?',
        51 => '¿Te sientes a gusto coordinando un grupo de trabajo?',
        52 => '¿Crees que es interesante el estudio de las ciencias biológicas?',
        53 => '¿Te sentirías a gusto trabajando en "YPFB" como director comercial?',
        54 => '¿Te gustaría participar en un proyecto de extracción de Litio en el Salar de Uyuni?',
        55 => '¿Tienes interés por saber cuáles son las causas que determinan los fenómenos naturales, aunque no altere tu vida?',
        56 => '¿Descubriste un escritor o filósofo que tenga tus mismas ideas con anticipación?',
        57 => '¿Te gustaría algún instrumento musical para tu cumpleaños?',
        58 => '¿Aceptarías colaborar con el cumplimiento de las normas de higiene en lugares públicos?',
        59 => '¿Cuándo tus ideas son importantes, las pones en práctica?',
        60 => '¿Cuándo se rompe un electrodoméstico en tu casa, intentas repararlo?',
        61 => '¿Formarías parte de un equipo de preservación de la flora y la fauna en extinción?',
        62 => '¿Te gusta investigar en el celular artículos científicos relacionados con la salud?',
        63 => '¿Te parece importante preservar las raíces culturales en Bolivia?',
        64 => '¿Te gustaría trabajar por una distribución más justa de nuestra riqueza aurífera de los ríos del departamento de Beni?',
        65 => '¿Te gustaría realizar tareas de mantenimiento mecánico en un automóvil?',
        66 => '¿Estás a favor de la compra de armamento en tu país?',
        67 => '¿Para ti es fundamental la libertad y la justicia como valor principal?',
        68 => '¿Aceptarías hacer una práctica en la empresa "PIL", en el sector de control de calidad?',
        69 => '¿Crees que la salud pública debe ser gratuita, prioritaria y eficiente para todos?',
        70 => '¿Te interesaría investigar profundamente sobre la vacuna del COVID-19?',
        71 => '¿En un equipo de trabajo de la empresa "COMTECO", prefieres el rol de coordinador?',
        72 => '¿Cuándo discuten entre dos amigos, te ofreces como intermediario?',
        73 => '¿Estás de acuerdo con la formación de soldados con distintas carreras profesionales?',
        74 => '¿Combatirías por una idea correcta hasta las últimas consecuencias?',
        75 => '¿Te gustaría investigar científicamente sobre los cultivos de variedades de papas?',
        76 => '¿Harías un nuevo diseño de una prenda pasada de moda usando tu creatividad, ante una reunión imprevista?',
        77 => '¿Visitarías un observatorio astronómico para saber cómo funcionan los telescopios?',
        78 => '¿Dirigirías el área de importación y exportación de Industrias "VENADO"?',
        79 => '¿Te da miedo entrar a un lugar nuevo con personas que no conoces?',
        80 => '¿Te complacería trabajar con niños?',
        81 => '¿Harías el diseño de un afiche para una campaña contra el mal de Chagas?',
        82 => '¿Administrarías un grupo de teatro propio?',
        83 => '¿Enviarías tu hoja de vida a una empresa automotriz que solicita gerente para su área de producción?',
        84 => '¿Te alistarías en un grupo de defensa internacional dentro de la policía boliviana?',
        85 => '¿Costearías tus estudios trabajando en una empresa auditora?',
        86 => '¿Eres de los que defiende problemas ajenos perdidos?',
        87 => '¿Ante una emergencia epidémica como el COVID-19 participarías en una campaña brindando tu ayuda?',
        88 => '¿Sabes qué significa ADN y ARN?',
        89 => '¿Elegirías un trabajo cuya principal herramienta fuera hablar un idioma extranjero?',
        90 => '¿Prefieres trabajar con objetos que trabajar con personas?',
        91 => '¿Te resultaría gratificante ser asesor contable en la empresa "COBOCE"?',
        92 => '¿Te ofrecerías para cuidar a un enfermo?',
        93 => '¿Te atrae investigar sobre los misterios del universo?',
        94 => '¿Crees que es mejor trabajar individualmente que en equipo?',
        95 => '¿Dedicarías parte de tu tiempo ayudando a personas de zonas alejadas de pobreza extrema con carencias económicas?',
        96 => '¿Cuándo eliges tu ropa o decoras tu casa, tienes en cuenta la combinación de colores y la calidad de los muebles?',
        97 => '¿Te gustaría dirigir como ingeniero la empresa hidroeléctrica "ENDE"?',
        98 => '¿Sabes qué es el PIB?',
    ];

    /**
     * Orden fijo de las areas (sirve como criterio de desempate estable).
     */
    public const ORDEN_AREAS = ['C', 'H', 'A', 'S', 'I', 'D', 'E'];

    /**
     * Calcula los puntajes CHASIDE a partir de las respuestas del estudiante.
     *
     * @param  array  $respuestas  Mapa [numeroPregunta => bool] con las 98 respuestas.
     * @return array{puntajes: array<string,int>, principal: string, secundaria: string}
     */
    public static function calcular(array $respuestas): array
    {
        $puntajes = [];

        foreach (self::TABLA as $area => $preguntas) {
            $suma = 0;
            foreach ($preguntas as $numero) {
                // Suma 1 punto solo si la pregunta fue respondida con SI.
                if (! empty($respuestas[$numero])) {
                    $suma++;
                }
            }
            $puntajes[$area] = $suma;
        }

        // Ordenamos por puntaje (desc) manteniendo el orden de areas como desempate.
        $ordenadas = self::ORDEN_AREAS;
        usort($ordenadas, function ($a, $b) use ($puntajes) {
            if ($puntajes[$b] === $puntajes[$a]) {
                // Desempate: respeta el orden CHASIDE original.
                return array_search($a, self::ORDEN_AREAS) <=> array_search($b, self::ORDEN_AREAS);
            }
            return $puntajes[$b] <=> $puntajes[$a];
        });

        return [
            'puntajes'   => $puntajes,
            'principal'  => $ordenadas[0],
            'secundaria' => $ordenadas[1],
        ];
    }
}

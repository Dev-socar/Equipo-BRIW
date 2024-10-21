<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

require '../config/conexion.php';
$db = conectarDB();

// Verificar la conexión
if ($db->connect_error) {
    die(json_encode(['error' => "Error de conexión: " . $db->connect_error]));
}

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $textQuery = isset($_POST["query"]) ? $_POST["query"] : "";

    if ($textQuery != "") {
        $TempTextQuery = $textQuery;

        // Definir la tabla y la columna de búsqueda
        $tabla = "Posting";
        $columna = "fragmento";
        $query = [];

        // Función CADENA
        if (preg_match('/cadena\((.*?)\)/i', $textQuery, $matches)) {
            $token = $db->real_escape_string($matches[1]); // Evitar inyección SQL
            $query[] = "($columna LIKE '%$token%')";
            $textQuery = str_replace($matches[0], '', $textQuery);
        }

        // Función PATRÓN
        if (preg_match('/patron\((.*?)\)/i', $textQuery, $matches)) {
            $pattern = $db->real_escape_string($matches[1]);
            $query[] = "($columna LIKE '%$pattern%')";
            $textQuery = str_replace($matches[0], '', $textQuery);
        }

        // Transformar operadores a mayúsculas y escapar la consulta
        $textQuery = strtoupper(trim($textQuery));
        $textQuery = preg_replace('/\s+/', ' ', $textQuery); // Normalizar espacios
        $terms = explode(' ', $textQuery);

        foreach ($terms as $term) {
            if (in_array($term, ['AND', 'OR', 'NOT'])) {
                $query[] = $term; // Guardar operador
            } else if ($term != "") {
                $term = $db->real_escape_string($term); // Escapar cada término
                $query[] = "($columna LIKE '%$term%')";
            }
        }

        // Crear la consulta final
        $finalQuery = implode(' ', $query);

        // Consulta de documentos y tf-idf
        $sqlTotalDocs = "SELECT COUNT(DISTINCT documento) as totalDocs FROM $tabla";
        $resultadoTotalDocs = $db->query($sqlTotalDocs);
        $totalDocs = $resultadoTotalDocs->fetch_assoc()['totalDocs'];

        // Realizar la consulta principal
        $sql = "SELECT P.documento, MAX(P.frecuencia) as frecuencia, 
                       GROUP_CONCAT(P.fragmento SEPARATOR '; ') AS fragmentos, 
                       D.nombre_hash AS ruta_archivo
                FROM $tabla P
                JOIN Documentos D ON P.documento = D.id
                WHERE $finalQuery
                GROUP BY P.documento";

        // Imprimir la consulta para depuración
        // echo $sql; // Descomentar para ver la consulta generada

        $resultado = $db->query($sql);
        $resultadosConSimilitud = [];

        if ($resultado && $resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                $documento = $row['documento'];
                $frecuencia = $row['frecuencia'];
                $fragmentos = $row['fragmentos'];
                $rutaArchivo = $row['ruta_archivo'];

                // Cálculo de tf-idf
                $tfIdf = 0;

                // Recuperar todos los términos únicos del documento desde Posting usando idTermino
                $sqlTerminos = "SELECT D.termino, P.frecuencia 
                                FROM Diccionario D 
                                JOIN Posting P ON D.id = P.idTermino 
                                WHERE P.documento = ?";
                $stmtTerminos = $db->prepare($sqlTerminos);
                $stmtTerminos->bind_param("s", $documento);
                $stmtTerminos->execute();
                $resultTerminos = $stmtTerminos->get_result();

                $terminosDocumento = [];
                while ($row = $resultTerminos->fetch_assoc()) {
                    $terminosDocumento[] = [
                        'termino' => $row['termino'],
                        'frecuencia' => $row['frecuencia']
                    ];
                }

                foreach ($terminosDocumento as $item) {
                    $termino = $item['termino'];
                    $frecuenciaActual = $item['frecuencia'];

                    // Obtener el DF desde la tabla Diccionario
                    $sqlDf = "SELECT COUNT(DISTINCT P.documento) as df 
                              FROM Posting P 
                              JOIN Diccionario D ON D.id = P.idTermino 
                              WHERE D.termino = ?";
                    $stmtDf = $db->prepare($sqlDf);
                    $stmtDf->bind_param("s", $termino);
                    $stmtDf->execute();
                    $resultDf = $stmtDf->get_result();
                    $df = $resultDf->fetch_assoc()['df'];

                    if ($df > 0) {
                        $idf = log($totalDocs / $df);
                        $tfIdf += $frecuenciaActual * $idf; // Utiliza la frecuencia actual
                    }
                }

                $similitudCoseno = calcularSimilitudCoseno($TempTextQuery, $documento, $db);

                $resultadosConSimilitud[] = [
                    'documento' => $documento,
                    'frecuencia' => $frecuencia,
                    'fragmentos' => $fragmentos,
                    'rutaArchivo' => $rutaArchivo,
                    'similitud' => $similitudCoseno,
                    'tfIdf' => $tfIdf
                ];
            }

            // Ordenar los resultados por tf-idf
            usort($resultadosConSimilitud, function ($a, $b) {
                return $b['tfIdf'] <=> $a['tfIdf'];
            });

            $response['results'] = $resultadosConSimilitud;
        } else {
            $response['message'] = "No se encontraron resultados.";
        }

        echo json_encode($response);
        exit;
    } else {
        $response['error'] = "No se ha proporcionado ninguna consulta.";
        echo json_encode($response);
        exit;
    }
} else {
    $response['error'] = "Método no permitido. Utiliza POST.";
    echo json_encode($response);
    exit;
}

function calcularSimilitudCoseno($consulta, $documento, $db)
{
    // Tokenizar la consulta y convertirla en un vector de frecuencias
    $terminosConsulta = explode(' ', $consulta);
    $vectorConsulta = [];

    foreach ($terminosConsulta as $termino) {
        if (isset($vectorConsulta[$termino])) {
            $vectorConsulta[$termino]++;
        } else {
            $vectorConsulta[$termino] = 1;
        }
    }

    // Tokenizar el documento y convertirlo en un vector de frecuencias
    $sqlDocumento = "SELECT D.termino, P.frecuencia FROM Posting P 
                     JOIN Diccionario D ON D.id = P.idTermino 
                     WHERE P.documento = ?";
    $stmtDocumento = $db->prepare($sqlDocumento);
    $stmtDocumento->bind_param("s", $documento);
    $stmtDocumento->execute();
    $resultadoDocumento = $stmtDocumento->get_result();

    $vectorDocumento = [];
    while ($row = $resultadoDocumento->fetch_assoc()) {
        $termino = $row['termino'];
        $frecuencia = $row['frecuencia'];
        $vectorDocumento[$termino] = $frecuencia;
    }

    // Calcular el producto punto entre los vectores de la consulta y del documento
    $productoPunto = 0;
    foreach ($vectorConsulta as $termino => $frecuenciaConsulta) {
        if (isset($vectorDocumento[$termino])) {
            $productoPunto += $frecuenciaConsulta * $vectorDocumento[$termino];
        }
    }

    // Calcular la magnitud del vector de la consulta
    $magnitudConsulta = sqrt(array_sum(array_map(function ($x) {
        return pow($x, 2);
    }, $vectorConsulta)));

    // Calcular la magnitud del vector del documento
    $magnitudDocumento = sqrt(array_sum(array_map(function ($x) {
        return pow($x, 2);
    }, $vectorDocumento)));

    // Evitar división por cero
    if ($magnitudConsulta == 0 || $magnitudDocumento == 0) {
        return 0;
    }

    // Calcular la similitud del coseno
    return $productoPunto / ($magnitudConsulta * $magnitudDocumento);
}

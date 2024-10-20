<?php
header('Access-Control-Allow-Origin: *'); // Permitir cualquier origen
header('Access-Control-Allow-Methods: POST, GET, OPTIONS'); // Métodos permitidos
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Encabezados permitidos
header('Content-Type: application/json'); // Configurar el tipo de contenido a JSON

require '../config/conexion.php';
$db = conectarDB();

$response = []; // Inicializa la respuesta

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $textQuery = isset($_POST["query"]) ? $_POST["query"] : "";

    if ($textQuery != "") {
        $TempTextQuery = $textQuery;

        // Definir la tabla y la columna de búsqueda
        $tabla = "Posting";
        $columna = "fragmento"; // La columna donde buscas
        $query = "";

        // Función CADENA
        if (preg_match('/cadena\((.*?)\)/i', $textQuery, $matches)) {
            $token = $matches[1];
            $query .= " ($columna LIKE '%$token%') ";
            $textQuery = str_replace($matches[0], '', $textQuery);
        }

        // Función Patron 
        if (preg_match('/patron\((.*?)\)/i', $textQuery, $matches)) {
            $pattern = $matches[1];
            $query .= " ($columna LIKE '%$pattern%') ";
            $textQuery = str_replace($matches[0], '', $textQuery);
        }

        // Transformar operadores a mayúsculas
        $textQuery = str_replace('and not', 'AND NOT', $textQuery);
        $textQuery = str_replace('and', 'AND', $textQuery);
        $textQuery = str_replace('or', 'OR', $textQuery);
        $textQuery = str_replace('not', 'NOT', $textQuery);

        // Separar términos y construir la consulta
        $terms = explode(' ', $textQuery);
        foreach ($terms as $term) {
            if ($term === 'AND' || $term === 'OR' || $term === 'NOT') {
                $query .= " $term ";
            } else if ($term != "") {
                // Aquí se busca en la columna fragmento
                $query .= " ($columna LIKE '%$term%') ";
            }
        }

        // Realizar la consulta
        $sql = "SELECT P.documento, COUNT(P.frecuencia) AS frecuencia, GROUP_CONCAT(P.fragmento SEPARATOR '; ') AS fragmentos 
                FROM $tabla P 
                WHERE $query
                GROUP BY P.documento
                ORDER BY frecuencia DESC"; // Ordenar por frecuencia o relevancia

        $resultado = $db->query($sql);

        // Arreglo para almacenar resultados
        $resultadosConSimilitud = [];

        while ($row = $resultado->fetch_assoc()) {
            $documento = $row['documento'];
            $frecuencia = $row['frecuencia'];
            $fragmentos = $row['fragmentos'];

            // Aquí puedes calcular similitud de coseno si es necesario
            $similitudCoseno = calcularSimilitudCoseno($documento);

            $resultadosConSimilitud[] = [
                'documento' => $documento,
                'frecuencia' => $frecuencia,
                'fragmentos' => $fragmentos,
                'similitud' => $similitudCoseno,
            ];
        }

        // Si no se encontraron resultados
        if (count($resultadosConSimilitud) === 0) {
            $response['message'] = "No se encontraron resultados.";
        } else {
            $response['results'] = $resultadosConSimilitud;
        }

        // Enviar la respuesta en formato JSON
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

// Función para calcular la similitud del coseno
function calcularSimilitudCoseno($documento)
{
    // Aquí implementa tu lógica para calcular la similitud del coseno
    return rand(0, 1); // Ejemplo: retorna un valor aleatorio entre 0 y 1
}

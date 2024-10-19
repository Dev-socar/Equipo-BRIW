<?php
header('Content-Type: application/json');

require '../config/conexion.php';
$db = conectarDB();

// Función para generar un hash único basado en el nombre del archivo y su contenido
function generarHash($filePath)
{
    return hash_file('sha256', $filePath);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
    $nombreReal = $_FILES['archivo']['name'];
    $rutaTemporal = $_FILES['archivo']['tmp_name'];

    // Generar hash del archivo
    $nombreHash = generarHash($rutaTemporal);

    // Mover el archivo al servidor con el nombre hash y un timestamp
    $rutaDestino = '../uploads/' . $nombreHash . '_' . time() . '.txt';
    if (move_uploaded_file($rutaTemporal, $rutaDestino)) {
        // Insertar en la tabla Documentos
        $stmt = $db->prepare("INSERT INTO Documentos (nombre_real, nombre_hash) VALUES (?, ?)");
        $stmt->bind_param('ss', $nombreReal, $nombreHash);
        if ($stmt->execute()) {
            $idDocumento = $stmt->insert_id;
            $stmt->close();

            // Lógica para indexar el archivo
            $resultadoIndexacion = indexarArchivo($rutaDestino, $nombreHash, $idDocumento, $db);

            if ($resultadoIndexacion) {
                echo json_encode(["success" => true, "message" => "Archivo subido e indexado correctamente."]);
            } else {
                echo json_encode(["success" => false, "message" => "Error en la indexación del archivo."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Error al insertar en la tabla Documentos."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Error al mover el archivo."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido o archivo no recibido."]);
}

function indexarArchivo($rutaArchivo, $nombreHash, $idDocumento, $db)
{
    // Leer el contenido del archivo
    $contenido = file_get_contents($rutaArchivo);

    // Dividir el contenido en palabras
    $palabras = preg_split('/\s+/', strtolower($contenido));

    // Arreglo para rastrear los términos que ya hemos procesado
    $terminosProcesados = [];

    // Recorrer cada palabra y actualizar el índice invertido
    foreach ($palabras as $palabra) {
        if (strlen($palabra) > 0 && !in_array($palabra, $terminosProcesados)) {
            // Marcar el término como procesado
            $terminosProcesados[] = $palabra;

            // Insertar o actualizar el diccionario
            $stmtDic = $db->prepare("SELECT id, numero_de_docs FROM Diccionario WHERE termino = ?");
            $stmtDic->bind_param('s', $palabra);
            $stmtDic->execute();
            $result = $stmtDic->get_result();

            if ($result->num_rows > 0) {
                // Si el término ya existe, actualizamos el número de documentos
                $row = $result->fetch_assoc();
                $idTermino = $row['id'];
                $numeroDeDocs = $row['numero_de_docs'];

                // Solo incrementamos si el documento no estaba previamente asociado
                $numeroDeDocs++;
                $updateStmt = $db->prepare("UPDATE Diccionario SET numero_de_docs = ? WHERE id = ?");
                $updateStmt->bind_param('ii', $numeroDeDocs, $idTermino);
                $updateStmt->execute();
                $updateStmt->close();
            } else {
                // Si el término no existe, lo insertamos
                $insertStmt = $db->prepare("INSERT INTO Diccionario (termino, numero_de_docs) VALUES (?, 1)");
                $insertStmt->bind_param('s', $palabra);
                $insertStmt->execute();
                $idTermino = $insertStmt->insert_id;
                $insertStmt->close();
            }
            $stmtDic->close();

            // Calcular la frecuencia del término en el documento
            $frecuencia = substr_count(strtolower($contenido), $palabra);

            // Extraer un fragmento de 50 caracteres alrededor del término (si es posible)
            $pos = strpos(strtolower($contenido), $palabra);
            $fragmento = substr($contenido, max(0, $pos - 25), 50);

            // Insertar en la tabla Posting
            $stmtPost = $db->prepare("INSERT INTO Posting (idTermino, documento, frecuencia, fragmento) VALUES (?, ?, ?, ?)");
            $stmtPost->bind_param('isis', $idTermino, $nombreHash, $frecuencia, $fragmento);
            $stmtPost->execute();
            $stmtPost->close();
        }
    }
    return true; // Retornar true si se completó la indexación
}

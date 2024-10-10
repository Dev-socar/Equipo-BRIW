<?php
require_once("admin/config.php");
$TempTextQuery = "";
$tabla = "";
$query = "";

if ( isset($_GET["query"] ) && $_GET["query"] != "" ) {
    
    $textQuery = $_GET["query"];
    $TempTextQuery = $textQuery;

$queryDivided = explode(" ", $textQuery);
$patron = "/\(([^)]+)\)/";

$SELECT = " SELECT  ";
$FROM   = " FROM products WHERE";
$query = "";

$tabla = "products";
$columnas = ["product_name", "quantity_per_unit", "category"];



// Obtener la tabla y el campo. Función CAMPO
if (preg_match('/campos\((.*?)\)/i', $textQuery, $matches)) {
    // la estructura debe ser tabla.columna. 
    $campo = explode('.', $matches[1]);
    $tabla = $campo[0];
    $columnas = [$campo[1]];
    $textQuery = str_replace($matches[0], '', $textQuery);
}

//  Función CADENA
if (preg_match('/cadena\((.*?)\)/i', $textQuery, $matches)) {
    $token = $matches[1];
    $query .= " (" . implode(" LIKE '%$token%' OR ", $columnas) . " LIKE '%$token%') ";
    $textQuery = str_replace($matches[0], '', $textQuery);
}


// Función Patron 
if (preg_match('/patron\((.*?)\)/i', $textQuery, $matches)) {
    $pattern = $matches[1];
    $query .= " (" . implode(" LIKE '%$pattern%' OR ", $columnas) . " LIKE '%$pattern%') ";
    $textQuery = str_replace($matches[0], '', $textQuery);
}

// Transforma los operadores a mayúscula solo para mejor visivilidad. 
$textQuery = str_replace('and not', 'AND NOT', $textQuery);
$textQuery = str_replace('and', 'AND', $textQuery);
$textQuery = str_replace('or', 'OR', $textQuery);
$textQuery = str_replace('not', 'NOT', $textQuery);

//echo "<br>".$query."<br>";
$terms = explode(' ', $textQuery);
foreach ($terms as $term) {
    if ($term === 'AND' || $term === 'OR' || $term === 'NOT') {
        $query .= " $term ";
    } else if($term != "") {
        $query .= " (" . implode(" LIKE '%$term%' OR ", $columnas) . " LIKE '%$term%') ";
    }

    if (!preg_match('/\s+(AND|OR|NOT)\s+/i', $query)) {
        $query = preg_replace('/\)\s*\(/', ') OR (', $query);
    }
}

$query = preg_replace('/\)\s*\(/', ') OR (', $query);

$conn = new mysqli($host, $usuario, $password, $base_datos);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$sql  = "SELECT * FROM ".$tabla." WHERE $query";
$resultado = $conn->query($sql);

}else{
    $resultado = null;
}

if(0){
    header('Content-type: application/json');
    echo json_encode($resultado->fetch_all(), true);
    die();
}

require("views/index.view.php");

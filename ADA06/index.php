<?php
require_once("admin/config.php");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ada_06";

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$textQuery = "";
if ( isset($_GET['query']) ) {
    $textQuery = $_GET['query'];    
}


$query = "SELECT *, MATCH(nombre_real, contenido) AGAINST (:query IN NATURAL LANGUAGE MODE) AS score
          FROM documentos
          WHERE MATCH(nombre_real, contenido) AGAINST (:query IN NATURAL LANGUAGE MODE)
          ORDER BY score DESC";
$stmt = $conn->prepare($query);
$stmt->bindValue(':query', $textQuery, PDO::PARAM_STR);
$stmt->execute();
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);


if(0){
    header('Content-type: application/json');
    echo json_encode($resultados, true);
    die();
}

require("views/index.view.php");

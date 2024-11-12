CREATE DATABASE ada_06;

CREATE TABLE documentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ruta_del_documento VARCHAR(255),
    nombre_real VARCHAR(255),
    nombre_hash VARCHAR(255),
    contenido TEXT,
    FULLTEXT (nombre_real,contenido)
);
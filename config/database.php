<?php
// Connessione al database

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'biblioteca_db');

// Creazione connessione
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verifica connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Set charset UTF-8
$conn->set_charset("utf8mb4");

?>




<?php
// Configurazioni generali

// Avvio sessione
session_start();

// Costanti
define('SITE_NAME', 'Biblioteca Digitale');
define('SITE_URL', 'http://localhost/ProgettoPersonaleInformatica-2026/');
define('GIORNI_PRESTITO', 14); // Durata prestito in giorni
define('MAX_PRESTITI_UTENTE', 3); // Max prestiti contemporanei

// Timezone
date_default_timezone_set('Europe/Rome');

// Error reporting (disattivare in produzione)
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>




<?php
// File da includere nelle pagine protette
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (!is_logged_in()) {
    redirect(SITE_URL . 'auth/login.php', 'Devi effettuare il login', 'warning');
}
?>

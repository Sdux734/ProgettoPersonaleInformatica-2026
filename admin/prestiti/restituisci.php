<?php
require_once '../../config/database.php';
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../auth/check_login.php';

if (!is_admin()) {
    redirect('../../user/dashboard.php', 'Accesso negato', 'error');
}

$id_prestito = $_GET['id'] ?? 0;

// Carica prestito
$stmt = $conn->prepare("SELECT * FROM prestiti WHERE id = ? AND stato IN ('attivo', 'scaduto')");
$stmt->bind_param("i", $id_prestito);
$stmt->execute();
$prestito = $stmt->get_result()->fetch_assoc();

if (!$prestito) {
    redirect('index.php', 'Prestito non trovato o già restituito', 'error');
}

// Inizia transazione
$conn->begin_transaction();

try {
    // Aggiorna prestito
    $stmt = $conn->prepare("UPDATE prestiti SET stato = 'restituito', data_restituzione = NOW() WHERE id = ?");
    $stmt->bind_param("i", $id_prestito);
    $stmt->execute();
    
    // Incrementa copie disponibili
    $stmt = $conn->prepare("UPDATE libri SET copie_disponibili = copie_disponibili + 1 WHERE id = ?");
    $stmt->bind_param("i", $prestito['id_libro']);
    $stmt->execute();
    
    $conn->commit();
    
    redirect('index.php', 'Prestito segnato come restituito', 'success');
    
} catch (Exception $e) {
    $conn->rollback();
    redirect('index.php', 'Errore durante la restituzione', 'error');
}
?>

<?php
require_once '../config/database.php';
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../auth/check_login.php';

$user_id = $_SESSION['user_id'];
$id_prestito = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM prestiti WHERE id = ? AND id_utente = ? AND stato IN ('attivo', 'scaduto')");
$stmt->bind_param("ii", $id_prestito, $user_id);
$stmt->execute();
$prestito = $stmt->get_result()->fetch_assoc();

if (!$prestito) {
    redirect('miei_prestiti.php', 'Prestito non trovato o non restituibile', 'error');
}

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("UPDATE prestiti SET stato = 'restituito', data_restituzione = NOW() WHERE id = ?");
    $stmt->bind_param("i", $id_prestito);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE libri SET copie_disponibili = copie_disponibili + 1 WHERE id = ?");
    $stmt->bind_param("i", $prestito['id_libro']);
    $stmt->execute();

    $conn->commit();
    redirect('miei_prestiti.php', 'Libro restituito con successo', 'success');
} catch (Exception $e) {
    $conn->rollback();
    redirect('miei_prestiti.php', 'Errore durante la restituzione', 'error');
}
?>
<?php
require_once '../config/database.php';
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../auth/check_login.php';

$id_libro = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Verifica che il libro esista e sia disponibile
$stmt = $conn->prepare("SELECT * FROM libri WHERE id = ? AND copie_disponibili > 0");
$stmt->bind_param("i", $id_libro);
$stmt->execute();
$libro = $stmt->get_result()->fetch_assoc();

if (!$libro) {
    redirect('catalogo.php', 'Libro non disponibile', 'error');
}

// Verifica che l'utente non abbia già questo libro
$stmt = $conn->prepare("SELECT * FROM prestiti WHERE id_utente = ? AND id_libro = ? AND stato = 'attivo'");
$stmt->bind_param("ii", $user_id, $id_libro);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    redirect('catalogo.php', 'Hai già questo libro in prestito', 'warning');
}

// Verifica limite prestiti
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM prestiti WHERE id_utente = ? AND stato = 'attivo'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$count = $stmt->get_result()->fetch_assoc()['count'];

if ($count >= MAX_PRESTITI_UTENTE) {
    redirect('catalogo.php', 'Hai raggiunto il limite di ' . MAX_PRESTITI_UTENTE . ' prestiti contemporanei', 'warning');
}

// Calcola data scadenza
$data_scadenza = calcola_data_scadenza();

// Inizia transazione
$conn->begin_transaction();

try {
    // Inserisci prestito
    $stmt = $conn->prepare("INSERT INTO prestiti (id_utente, id_libro, data_scadenza) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $id_libro, $data_scadenza);
    $stmt->execute();
    
    // Decrementa copie disponibili
    $stmt = $conn->prepare("UPDATE libri SET copie_disponibili = copie_disponibili - 1 WHERE id = ?");
    $stmt->bind_param("i", $id_libro);
    $stmt->execute();
    
    // Commit
    $conn->commit();
    
    redirect('miei_prestiti.php', 'Prestito richiesto con successo! Data restituzione: ' . date('d/m/Y', strtotime($data_scadenza)), 'success');
    
} catch (Exception $e) {
    $conn->rollback();
    redirect('catalogo.php', 'Errore durante la richiesta del prestito', 'error');
}
?>

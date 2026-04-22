<?php
require_once '../../config/database.php';
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../auth/check_login.php';

if (!is_admin()) {
    redirect('../../user/dashboard.php', 'Accesso negato', 'error');
}

$id = $_GET['id'] ?? 0;

// Verifica se ci sono prestiti attivi
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM prestiti WHERE id_libro = ? AND stato = 'attivo'");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if ($result['count'] > 0) {
    redirect('index.php', 'Impossibile eliminare: ci sono prestiti attivi per questo libro', 'error');
}

// Recupera info libro per eliminare copertina
$stmt = $conn->prepare("SELECT copertina FROM libri WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$libro = $stmt->get_result()->fetch_assoc();

if (!$libro) {
    redirect('index.php', 'Libro non trovato', 'error');
}

// Elimina libro
$stmt = $conn->prepare("DELETE FROM libri WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Elimina copertina se esiste
    if ($libro['copertina']) {
        @unlink("../../uploads/copertine/" . $libro['copertina']);
    }
    redirect('index.php', 'Libro eliminato con successo', 'success');
} else {
    redirect('index.php', 'Errore durante l\'eliminazione', 'error');
}
?>

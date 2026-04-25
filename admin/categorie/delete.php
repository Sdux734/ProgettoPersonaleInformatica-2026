<?php
require_once '../../config/database.php';
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../auth/check_login.php';

if (!is_admin()) {
    redirect('../../user/dashboard.php', 'Accesso negato', 'error');
}

$id = $_GET['id'] ?? 0;

// Verifica se ci sono libri in questa categoria
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM libri WHERE id_categoria = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if ($result['count'] > 0) {
    redirect('index.php', 'Impossibile eliminare: ci sono libri in questa categoria', 'error');
}

// Elimina categoria
$stmt = $conn->prepare("DELETE FROM categorie WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    redirect('index.php', 'Categoria eliminata con successo', 'success');
} else {
    redirect('index.php', 'Errore durante l\'eliminazione', 'error');
}
?>




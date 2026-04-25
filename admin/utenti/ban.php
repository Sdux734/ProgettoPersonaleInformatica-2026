<?php
require_once '../../config/database.php';
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../auth/check_login.php';

if (!is_admin()) {
    redirect('../../user/dashboard.php', 'Accesso negato', 'error');
}

$id = $_GET['id'] ?? 0;
$action = $_GET['action'] ?? '';

if ($id == $_SESSION['user_id']) {
    redirect('index.php', 'Non puoi modificare lo stato del tuo account', 'error');
}

if (empty($action) || !in_array($action, ['ban', 'unban'])) {
    redirect('index.php', 'Azione non valida', 'error');
}

$attivo = $action === 'ban' ? 0 : 1;
$message = $action === 'ban' ? 'Utente bannato con successo' : 'Utente ripristinato con successo';

$stmt = $conn->prepare("UPDATE utenti SET attivo = ? WHERE id = ?");
$stmt->bind_param("ii", $attivo, $id);

if ($stmt->execute()) {
    redirect('index.php', $message, 'success');
} else {
    redirect('index.php', 'Errore durante l\'operazione', 'error');
}
?>



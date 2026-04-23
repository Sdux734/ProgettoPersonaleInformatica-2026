<?php
// Funzioni riutilizzabili

// Sanificazione input
function pulisci_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Verifica se utente è loggato
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Verifica se utente è admin
function is_admin() {
    return isset($_SESSION['ruolo']) && $_SESSION['ruolo'] === 'admin';
}

// Redirect con messaggio
function redirect($page, $message = '', $type = 'info') {
    if ($message) {
        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $type; // success, error, warning, info
    }
    header("Location: " . $page);
    exit();
}

// Mostra messaggio flash
function mostra_messaggio() {
    if (isset($_SESSION['message'])) {
        $type = $_SESSION['message_type'] ?? 'info';
        $message = $_SESSION['message'];
        echo "<div class='alert alert-{$type}'>{$message}</div>";
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
}

// Hash password
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Verifica password
function verifica_password($password, $hash) {
    return password_verify($password, $hash);
}

// Calcola data scadenza prestito
function calcola_data_scadenza($giorni = GIORNI_PRESTITO) {
    return date('Y-m-d', strtotime("+{$giorni} days"));
}

// Controlla se prestito è scaduto
function is_prestito_scaduto($data_scadenza) {
    return strtotime($data_scadenza) < strtotime('today');
}

// Upload immagine copertina
function upload_copertina($file) {
    $target_dir = __DIR__ . '/../uploads/copertine/';
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Verifica tipo file
    $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
    if (!in_array($file_extension, $allowed_types)) {
        return ['success' => false, 'message' => 'Tipo file non permesso'];
    }
    
    // Verifica dimensione (max 5MB)
    if ($file["size"] > 5000000) {
        return ['success' => false, 'message' => 'File troppo grande'];
    }
    
    // Crea la cartella se non esiste
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    // Upload
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ['success' => true, 'filename' => $new_filename];
    } else {
        return ['success' => false, 'message' => 'Errore upload'];
    }
}

?>

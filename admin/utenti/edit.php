<?php
require_once '../../config/database.php';
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../auth/check_login.php';

if (!is_admin()) {
    redirect('../../user/dashboard.php', 'Accesso negato', 'error');
}

$id = $_GET['id'] ?? 0;
$errors = [];

$stmt = $conn->prepare("SELECT * FROM utenti WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$utente = $stmt->get_result()->fetch_assoc();

if (!$utente) {
    redirect('index.php', 'Utente non trovato', 'error');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = pulisci_input($_POST['nome']);
    $cognome = pulisci_input($_POST['cognome']);
    $ruolo = $_POST['ruolo'];
    $attivo = isset($_POST['attivo']) ? 1 : 0;
    
    if (empty($nome) || empty($cognome)) {
        $errors[] = "Nome e cognome sono obbligatori";
    }
    
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE utenti SET nome=?, cognome=?, ruolo=?, attivo=? WHERE id=?");
        $stmt->bind_param("sssii", $nome, $cognome, $ruolo, $attivo, $id);
        
        if ($stmt->execute()) {
            redirect('index.php', 'Utente modificato con successo', 'success');
        } else {
            $errors[] = "Errore durante l'aggiornamento";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica Utente</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../includes/navbar.php'; ?>
    
    <div class="container">
        <h1>Modifica Utente</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" style="max-width: 600px;">
            <div class="form-group">
                <label>Nome: *</label>
                <input type="text" name="nome" required value="<?php echo $utente['nome']; ?>">
            </div>
            
            <div class="form-group">
                <label>Cognome: *</label>
                <input type="text" name="cognome" required value="<?php echo $utente['cognome']; ?>">
            </div>
            
            <div class="form-group">
                <label>Email:</label>
                <input type="email" value="<?php echo $utente['email']; ?>" disabled>
                <small>Non può essere modificata</small>
            </div>
            
            <div class="form-group">
                <label>Ruolo:</label>
                <select name="ruolo">
                    <option value="utente" <?php echo $utente['ruolo'] == 'utente' ? 'selected' : ''; ?>>Utente</option>
                    <option value="admin" <?php echo $utente['ruolo'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="attivo" <?php echo $utente['attivo'] ? 'checked' : ''; ?>>
                    Utente Attivo
                </label>
            </div>
            
            <button type="submit" class="btn btn-success">Salva Modifiche</button>
            <a href="index.php" class="btn btn-secondary">Annulla</a>
        </form>
    </div>
</body>
</html>

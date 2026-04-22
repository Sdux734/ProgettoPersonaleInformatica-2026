<?php
require_once '../../config/database.php';
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../auth/check_login.php';

if (!is_admin()) {
    redirect('../../user/dashboard.php', 'Accesso negato', 'error');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = pulisci_input($_POST['nome']);
    $cognome = pulisci_input($_POST['cognome']);
    $nazionalita = pulisci_input($_POST['nazionalita']);
    $data_nascita = $_POST['data_nascita'] ?: null;
    $biografia = pulisci_input($_POST['biografia']);
    
    if (empty($nome) || empty($cognome)) {
        $errors[] = "Nome e cognome sono obbligatori";
    }
    
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO autori (nome, cognome, nazionalita, data_nascita, biografia) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nome, $cognome, $nazionalita, $data_nascita, $biografia);
        
        if ($stmt->execute()) {
            redirect('index.php', 'Autore aggiunto con successo', 'success');
        } else {
            $errors[] = "Errore durante l'inserimento";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Aggiungi Autore</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../includes/navbar.php'; ?>
    
    <div class="container">
        <h1>Aggiungi Nuovo Autore</h1>
        
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
                <input type="text" name="nome" required value="<?php echo $_POST['nome'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label>Cognome: *</label>
                <input type="text" name="cognome" required value="<?php echo $_POST['cognome'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label>Nazionalità:</label>
                <input type="text" name="nazionalita" value="<?php echo $_POST['nazionalita'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label>Data di Nascita:</label>
                <input type="date" name="data_nascita" value="<?php echo $_POST['data_nascita'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label>Biografia:</label>
                <textarea name="biografia" rows="5"><?php echo $_POST['biografia'] ?? ''; ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-success">Salva Autore</button>
            <a href="index.php" class="btn btn-secondary">Annulla</a>
        </form>
    </div>
</body>
</html>

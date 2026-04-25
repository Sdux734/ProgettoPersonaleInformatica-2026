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
    $descrizione = pulisci_input($_POST['descrizione']);
    
    if (empty($nome)) {
        $errors[] = "Il nome è obbligatorio";
    }
    
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO categorie (nome, descrizione) VALUES (?, ?)");
        $stmt->bind_param("ss", $nome, $descrizione);
        
        if ($stmt->execute()) {
            redirect('index.php', 'Categoria aggiunta con successo', 'success');
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
    <title>Aggiungi Categoria</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../includes/navbar.php'; ?>
    
    <div class="container">
        <h1>Aggiungi Nuova Categoria</h1>
        
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
                <label>Descrizione:</label>
                <textarea name="descrizione" rows="5"><?php echo $_POST['descrizione'] ?? ''; ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-success">Salva Categoria</button>
            <a href="index.php" class="btn btn-secondary">Annulla</a>
        </form>
    </div>
</body>
</html>




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

$stmt = $conn->prepare("SELECT * FROM categorie WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$categoria = $stmt->get_result()->fetch_assoc();

if (!$categoria) {
    redirect('index.php', 'Categoria non trovata', 'error');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = pulisci_input($_POST['nome']);
    $descrizione = pulisci_input($_POST['descrizione']);
    
    if (empty($nome)) {
        $errors[] = "Il nome è obbligatorio";
    }
    
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE categorie SET nome=?, descrizione=? WHERE id=?");
        $stmt->bind_param("ssi", $nome, $descrizione, $id);
        
        if ($stmt->execute()) {
            redirect('index.php', 'Categoria modificata con successo', 'success');
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
    <title>Modifica Categoria</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../includes/navbar.php'; ?>
    
    <div class="container">
        <h1>Modifica Categoria</h1>
        
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
                <input type="text" name="nome" required value="<?php echo $categoria['nome']; ?>">
            </div>
            
            <div class="form-group">
                <label>Descrizione:</label>
                <textarea name="descrizione" rows="5"><?php echo $categoria['descrizione']; ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-success">Salva Modifiche</button>
            <a href="index.php" class="btn btn-secondary">Annulla</a>
        </form>
    </div>
</body>
</html>

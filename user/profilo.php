<?php
require_once '../config/database.php';
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../auth/check_login.php';

$user_id = $_SESSION['user_id'];

// Carica dati utente
$stmt = $conn->prepare("SELECT * FROM utenti WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$utente = $stmt->get_result()->fetch_assoc();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = pulisci_input($_POST['nome']);
    $cognome = pulisci_input($_POST['cognome']);
    
    if (empty($nome) || empty($cognome)) {
        $error = "Nome e cognome sono obbligatori";
    } else {
        $stmt = $conn->prepare("UPDATE utenti SET nome = ?, cognome = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nome, $cognome, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['nome'] = $nome;
            $_SESSION['cognome'] = $cognome;
            $message = "Profilo aggiornato con successo";
            $utente['nome'] = $nome;
            $utente['cognome'] = $cognome;
        } else {
            $error = "Errore durante l'aggiornamento";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Profilo Utente</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <h1>Il Mio Profilo</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" style="max-width: 500px;">
            <div class="form-group">
                <label>Nome:</label>
                <input type="text" name="nome" value="<?php echo $utente['nome']; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Cognome:</label>
                <input type="text" name="cognome" value="<?php echo $utente['cognome']; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Email:</label>
                <input type="email" value="<?php echo $utente['email']; ?>" disabled>
                <small>Non può essere modificata</small>
            </div>
            
            <div class="form-group">
                <label>Data Registrazione:</label>
                <input type="text" value="<?php echo date('d/m/Y H:i', strtotime($utente['data_registrazione'])); ?>" disabled>
            </div>
            
            <button type="submit" class="btn btn-success">Salva Modifiche</button>
            <a href="dashboard.php" class="btn btn-secondary">Indietro</a>
        </form>
    </div>
</body>
</html>

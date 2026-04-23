<?php
require_once '../config/database.php';
require_once '../config/config.php';
require_once '../includes/functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recupera e pulisci dati
    $nome = pulisci_input($_POST['nome']);
    $cognome = pulisci_input($_POST['cognome']);
    $email = pulisci_input($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    
    // Validazione
    if (empty($nome)) $errors[] = "Il nome è obbligatorio";
    if (empty($cognome)) $errors[] = "Il cognome è obbligatorio";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email non valida";
    }
    if (strlen($password) < 6) {
        $errors[] = "La password deve essere di almeno 6 caratteri";
    }
    if ($password !== $password_confirm) {
        $errors[] = "Le password non coincidono";
    }
    
    // Verifica se email già esiste
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM utenti WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = "Email già registrata";
        }
        $stmt->close();
    }
    
    // Se non ci sono errori, registra l'utente
    if (empty($errors)) {
        $password_hash = hash_password($password);
        
        $stmt = $conn->prepare("INSERT INTO utenti (nome, cognome, email, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $cognome, $email, $password_hash);
        
        if ($stmt->execute()) {
            redirect('login.php', 'Registrazione completata! Effettua il login', 'success');
        } else {
            $errors[] = "Errore durante la registrazione";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Registrazione - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <h1>Registrazione</h1>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Nome:</label>
                    <input type="text" name="nome" required value="<?php echo $_POST['nome'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Cognome:</label>
                    <input type="text" name="cognome" required value="<?php echo $_POST['cognome'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" required value="<?php echo $_POST['email'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label>Conferma Password:</label>
                    <input type="password" name="password_confirm" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Registrati</button>
            </form>
            
            <p class="auth-link">Hai già un account? <a href="login.php">Accedi qui</a></p>
        </div>
    </div>
</body>
</html>

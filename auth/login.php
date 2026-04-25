<?php
require_once '../config/database.php';
require_once '../config/config.php';
require_once '../includes/functions.php';

// Se già loggato, redirect
if (is_logged_in()) {
    redirect(is_admin() ? '../admin/dashboard.php' : '../user/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = pulisci_input($_POST['email']);
    $password = $_POST['password'];
    
    // Query per trovare utente
    $stmt = $conn->prepare("SELECT id, nome, cognome, password, ruolo FROM utenti WHERE email = ? AND attivo = 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verifica password
        if (verifica_password($password, $user['password'])) {
            // Login riuscito
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nome'] = $user['nome'];
            $_SESSION['cognome'] = $user['cognome'];
            $_SESSION['ruolo'] = $user['ruolo'];
            
            // Redirect in base al ruolo
            if ($user['ruolo'] === 'admin') {
                redirect('../admin/dashboard.php', 'Benvenuto ' . $user['nome'], 'success');
            } else {
                redirect('../user/dashboard.php', 'Benvenuto ' . $user['nome'], 'success');
            }
        } else {
            $error = "Credenziali non valide";
        }
    } else {
        $error = "Credenziali non valide";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <h1>Login</h1>
            
            <?php mostra_messaggio(); ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Accedi</button>
            </form>
            
            <p class="auth-link">Non hai un account? <a href="register.php">Registrati qui</a></p>
        </div>
    </div>
</body>
</html>




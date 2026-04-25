<?php
require_once '../../config/database.php';
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../auth/check_login.php';

if (!is_admin()) {
    redirect('../../user/dashboard.php', 'Accesso negato', 'error');
}

$errors = [];

// Carica autori e categorie per i dropdown
$autori = $conn->query("SELECT * FROM autori ORDER BY cognome, nome");
$categorie = $conn->query("SELECT * FROM categorie ORDER BY nome");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recupera dati
    $titolo = pulisci_input($_POST['titolo']);
    $isbn = pulisci_input($_POST['isbn']);
    $id_autore = $_POST['id_autore'] ?: null;
    $id_categoria = $_POST['id_categoria'] ?: null;
    $anno_pubblicazione = $_POST['anno_pubblicazione'] ?: null;
    $editore = pulisci_input($_POST['editore']);
    $numero_pagine = $_POST['numero_pagine'] ?: null;
    $descrizione = pulisci_input($_POST['descrizione']);
    $copie_totali = intval($_POST['copie_totali']);
    $copie_disponibili = $copie_totali;
    
    // Validazione
    if (empty($titolo)) $errors[] = "Il titolo è obbligatorio";
    if (empty($isbn)) $errors[] = "L'ISBN è obbligatorio";
    if ($copie_totali < 1) $errors[] = "Deve avere almeno 1 copia";
    
    // Upload copertina
    $copertina_filename = null;
    if (isset($_FILES['copertina']) && $_FILES['copertina']['error'] == 0) {
        $upload_result = upload_copertina($_FILES['copertina']);
        if ($upload_result['success']) {
            $copertina_filename = $upload_result['filename'];
        } else {
            $errors[] = $upload_result['message'];
        }
    }
    
    // Se non ci sono errori, inserisci
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO libri (titolo, isbn, id_autore, id_categoria, anno_pubblicazione, editore, numero_pagine, descrizione, copertina, copie_totali, copie_disponibili) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("ssiisissssii", 
            $titolo, 
            $isbn, 
            $id_autore, 
            $id_categoria, 
            $anno_pubblicazione, 
            $editore, 
            $numero_pagine, 
            $descrizione, 
            $copertina_filename, 
            $copie_totali, 
            $copie_disponibili
        );
        
        if ($stmt->execute()) {
            redirect('index.php', 'Libro aggiunto con successo', 'success');
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
    <title>Aggiungi Libro</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../includes/navbar.php'; ?>
    
    <div class="container">
        <h1>Aggiungi Nuovo Libro</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Titolo: *</label>
                <input type="text" name="titolo" required value="<?php echo $_POST['titolo'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label>ISBN: *</label>
                <input type="text" name="isbn" required value="<?php echo $_POST['isbn'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label>Autore:</label>
                <select name="id_autore">
                    <option value="">Seleziona autore</option>
                    <?php 
                    $autori->data_seek(0);
                    while($autore = $autori->fetch_assoc()): 
                    ?>
                        <option value="<?php echo $autore['id']; ?>">
                            <?php echo $autore['cognome'] . ' ' . $autore['nome']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Categoria:</label>
                <select name="id_categoria">
                    <option value="">Seleziona categoria</option>
                    <?php 
                    $categorie->data_seek(0);
                    while($cat = $categorie->fetch_assoc()): 
                    ?>
                        <option value="<?php echo $cat['id']; ?>">
                            <?php echo $cat['nome']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Anno Pubblicazione:</label>
                <input type="number" name="anno_pubblicazione" min="1000" max="<?php echo date('Y'); ?>" 
                       value="<?php echo $_POST['anno_pubblicazione'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label>Editore:</label>
                <input type="text" name="editore" value="<?php echo $_POST['editore'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label>Numero Pagine:</label>
                <input type="number" name="numero_pagine" value="<?php echo $_POST['numero_pagine'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label>Descrizione:</label>
                <textarea name="descrizione" rows="5"><?php echo $_POST['descrizione'] ?? ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Copertina:</label>
                <input type="file" name="copertina" accept="image/*">
            </div>
            
            <div class="form-group">
                <label>Numero Copie Totali: *</label>
                <input type="number" name="copie_totali" min="1" value="1" required>
            </div>
            
            <button type="submit" class="btn btn-success">Salva Libro</button>
            <a href="index.php" class="btn btn-secondary">Annulla</a>
        </form>
    </div>
</body>
</html>




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

// Carica libro esistente
$stmt = $conn->prepare("SELECT * FROM libri WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$libro = $stmt->get_result()->fetch_assoc();

if (!$libro) {
    redirect('index.php', 'Libro non trovato', 'error');
}

// Carica autori e categorie
$autori = $conn->query("SELECT * FROM autori ORDER BY cognome, nome");
$categorie = $conn->query("SELECT * FROM categorie ORDER BY nome");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titolo = pulisci_input($_POST['titolo']);
    $isbn = pulisci_input($_POST['isbn']);
    $id_autore = $_POST['id_autore'] ?: null;
    $id_categoria = $_POST['id_categoria'] ?: null;
    $anno_pubblicazione = $_POST['anno_pubblicazione'] ?: null;
    $editore = pulisci_input($_POST['editore']);
    $numero_pagine = $_POST['numero_pagine'] ?: null;
    $descrizione = pulisci_input($_POST['descrizione']);
    $copie_totali = intval($_POST['copie_totali']);
    $copie_disponibili = intval($_POST['copie_disponibili']);
    
    if (empty($titolo)) $errors[] = "Il titolo è obbligatorio";
    
    // Gestione upload nuova copertina
    $copertina_filename = $libro['copertina'];
    if (isset($_FILES['copertina']) && $_FILES['copertina']['error'] == 0) {
        $upload_result = upload_copertina($_FILES['copertina']);
        if ($upload_result['success']) {
            if ($libro['copertina']) {
                @unlink("../../uploads/copertine/" . $libro['copertina']);
            }
            $copertina_filename = $upload_result['filename'];
        }
    }
    
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE libri SET titolo=?, isbn=?, id_autore=?, id_categoria=?, anno_pubblicazione=?, editore=?, numero_pagine=?, descrizione=?, copertina=?, copie_totali=?, copie_disponibili=? WHERE id=?");
        
        $stmt->bind_param("ssiisissssiii", 
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
            $copie_disponibili,
            $id
        );
        
        if ($stmt->execute()) {
            redirect('index.php', 'Libro modificato con successo', 'success');
        } else {
            $errors[] = "Errore durante l'aggiornamento";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica Libro</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../includes/navbar.php'; ?>
    
    <div class="container">
        <h1>Modifica Libro</h1>
        
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
                <input type="text" name="titolo" required value="<?php echo $libro['titolo']; ?>">
            </div>
            
            <div class="form-group">
                <label>ISBN: *</label>
                <input type="text" name="isbn" required value="<?php echo $libro['isbn']; ?>">
            </div>
            
            <div class="form-group">
                <label>Autore:</label>
                <select name="id_autore">
                    <option value="">Seleziona autore</option>
                    <?php while($autore = $autori->fetch_assoc()): ?>
                        <option value="<?php echo $autore['id']; ?>" <?php echo $libro['id_autore'] == $autore['id'] ? 'selected' : ''; ?>>
                            <?php echo $autore['cognome'] . ' ' . $autore['nome']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Categoria:</label>
                <select name="id_categoria">
                    <option value="">Seleziona categoria</option>
                    <?php while($cat = $categorie->fetch_assoc()): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $libro['id_categoria'] == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo $cat['nome']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Anno Pubblicazione:</label>
                <input type="number" name="anno_pubblicazione" min="1000" max="<?php echo date('Y'); ?>" 
                       value="<?php echo $libro['anno_pubblicazione']; ?>">
            </div>
            
            <div class="form-group">
                <label>Editore:</label>
                <input type="text" name="editore" value="<?php echo $libro['editore']; ?>">
            </div>
            
            <div class="form-group">
                <label>Numero Pagine:</label>
                <input type="number" name="numero_pagine" value="<?php echo $libro['numero_pagine']; ?>">
            </div>
            
            <div class="form-group">
                <label>Descrizione:</label>
                <textarea name="descrizione" rows="5"><?php echo $libro['descrizione']; ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Copertina:</label>
                <?php if ($libro['copertina']): ?>
                    <p><img src="../../uploads/copertine/<?php echo $libro['copertina']; ?>" width="100" alt="Copertina"></p>
                <?php endif; ?>
                <input type="file" name="copertina" accept="image/*">
            </div>
            
            <div class="form-group">
                <label>Numero Copie Totali:</label>
                <input type="number" name="copie_totali" min="1" value="<?php echo $libro['copie_totali']; ?>">
            </div>
            
            <div class="form-group">
                <label>Copie Disponibili:</label>
                <input type="number" name="copie_disponibili" min="0" value="<?php echo $libro['copie_disponibili']; ?>">
            </div>
            
            <button type="submit" class="btn btn-success">Salva Modifiche</button>
            <a href="index.php" class="btn btn-secondary">Annulla</a>
        </form>
    </div>
</body>
</html>

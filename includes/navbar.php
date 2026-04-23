<nav class="navbar">
    <div class="navbar-brand">
        <a href="<?php echo SITE_URL; ?>index.php" style="font-weight: bold; font-size: 18px;">📚 <?php echo SITE_NAME; ?></a>
    </div>
    
    <div class="navbar-menu">
        <?php if (is_logged_in()): ?>
            <?php if (is_admin()): ?>
                <a href="<?php echo SITE_URL; ?>admin/dashboard.php">Dashboard Admin</a>
                <a href="<?php echo SITE_URL; ?>admin/libri/index.php">Libri</a>
                <a href="<?php echo SITE_URL; ?>admin/autori/index.php">Autori</a>
                <a href="<?php echo SITE_URL; ?>admin/categorie/index.php">Categorie</a>
                <a href="<?php echo SITE_URL; ?>admin/prestiti/index.php">Prestiti</a>
                <a href="<?php echo SITE_URL; ?>admin/utenti/index.php">Utenti</a>
            <?php else: ?>
                <a href="<?php echo SITE_URL; ?>user/dashboard.php">Dashboard</a>
                <a href="<?php echo SITE_URL; ?>user/catalogo.php">Catalogo</a>
                <a href="<?php echo SITE_URL; ?>user/miei_prestiti.php">I Miei Prestiti</a>
            <?php endif; ?>
            
            <span class="user-info">
                Benvenuto, <?php echo $_SESSION['nome']; ?>!
                <button id="book-style-toggle" onclick="toggleBookStyle()">📖 <span>Stile Libro</span></button>

                <a href="<?php echo SITE_URL; ?>auth/logout.php">Logout</a>
            </span>
        <?php else: ?>
            <a href="<?php echo SITE_URL; ?>auth/login.php">Login</a>
            <a href="<?php echo SITE_URL; ?>auth/register.php">Registrati</a>
        <?php endif; ?>
    </div>
</nav>

<!DOCTYPE html>
<html lang="fr">

    <head>
        <meta charset="utf-8">
        <title>Jeu de mémo</title>
        <meta name="author" content="Prénom NOM">
        <meta name="description" content="Jeu de mémo en ligne">
        <link rel="stylesheet" href="css/style.css">
    </head>

    <body>
        <h1>Jeu de mémoire en ligne</h1>
        <h2> Taille de la matrice de jeu </h2>
        <form action="memo.php" method="post">
            <label for="taille">Taille :</label>
            <input type="number" id="taille" name="taille" value="2" mini="2" max="8" step="2" size="3" required>
            <input type="submit" value="Générer">
        </form>
        
        <?php
            // Avant de commencer on efface les cookies 
            // éventuels de la partie précédente
            // Astuce : date d'expiration passée
            setcookie("tab_cartes", "", time() - 60);
            setcookie("tab_choix", "", time() - 60);
            setcookie("tab_recto", "", time() - 60); 
        ?>
    </body>
</html>
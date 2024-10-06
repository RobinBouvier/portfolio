<!DOCTYPE html>
<html lang="fr">

    <head>
        <meta charset="utf-8">
        <title>Jeu de mémo</title>
        <meta name="author" content="Prénom NOM">
        <meta name="description" content="Jeu de mémoire en ligne">
        <link rel="stylesheet" href="css/style.css">
    </head>

    <body>
        <h1>Jeu de mémoire en ligne</h1>
        <?php
            // 1- Inclure les fonctions php dans la page
            require("php/code.php");

            // 2- Tester si la page est obtenue après avoir validé le formulaire de index.php (la requête POST contient bien un paramètre portant le nom 'taille')
            // Si c'est le cas, appeler la fonction jouer(), sinon afficher un message 'Aucun paramètre reçu cliquer sur le bouton' dans la page

            if (($_POST['taille'])){
                jouer();
            }else{
                echo "<h2> Veuillez entrer une taille valide <h2>";
            }
        ?>

        <!--
            Rajouter ici une redirection vers index.php pour recommencer le jeu
        -->
        <form action="index.php" method="post">
            <input type="submit" name="recommencer" value="Recommencer">
        </form>
    </body>

</html>
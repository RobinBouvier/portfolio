<?php
    // fonction de débogage pour vérifier visuellement le contenu d'un tableau
    function voir_tableau($un_tab){
        echo "<pre>";
        print_r($un_tab);
        echo "</pre>";
    }

    // Fonction qui récupère le bon nombre d'émoticônes dans la BDD
    // et retourne un tableau représentant le plateau de jeu
    function charge_emo($taille){
        // Connexion à la base de données
        try{ 
            $connexion = new PDO('mysql:dbname=yourdbname; host:localhost;', "yourname", "yourpassword");
        }
        catch(Exception $erreur){
            exit("Erreur : " . $erreur->getMessage());
        }

        // Requête paramétrée pour récupérer les émoticônes
        $sql = "select emo from emoticon where emo_id <= ?";

        // Exécution de la requête avec le bon paramètre 
        $requete = $connexion->prepare($sql);
        $requete->execute(array($taille * $taille / 2)) or exit($connexion->errorInfo()[2]); 

        // Récupération du résultat dans un tableau
        $tab_res = $requete->fetchall(PDO::FETCH_COLUMN);
        
        // Fin de la connexion à la base de données
        $connexion = null;

        // Créer le tableau final pour remplir le plateau
        $tab_cartes = array_merge($tab_res, $tab_res);

        // Mélanger le tableau
        shuffle($tab_cartes);

        // On enregistre le tableau dans un cookie afin 
        // d'éviter de le charger plusieurs fois pendant le jeu
        // Durée de vie = durée de la session (disparaît si on ferme le navigateur)
        // Serialize permet de transformer un tableau en cookie
        setcookie("tab_cartes", serialize($tab_cartes)); 

        // retourner le tableau de jeu
        return $tab_cartes;
    }

    // Fonction qui affiche le plateau ($taille x $taille) de cartes
    function affiche_plateau($tab_cartes, $tab_recto, $tab_choix){
        // Récupère la taille de la grille dans une variable locale
        $taille = $_POST['taille'];

        // On crée un formulaire pour y placer le plateau 
        echo "<form action='memo.php' method='post'>";
        echo "<input type='hidden' name='taille' value='$taille'>";
        echo "<table>";
        $cpt_tab = 0;
        for ($cpt_ligne = 0; $cpt_ligne < $taille; $cpt_ligne++) {
            echo "<tr>";
            for ($cpt_colonne = 0; $cpt_colonne < $taille; $cpt_colonne++){
                if (in_array($cpt_tab, $tab_recto) or in_array($cpt_tab, $tab_choix)){
                    $contenu = $tab_cartes[$cpt_tab];
                }else{
                    $contenu = "";
                }
                echo "<td><input type='submit' name='$cpt_tab' value = '$contenu'> </td>";
                $cpt_tab++;
            }
            echo "</tr>";
        }
        echo "</table>";
        // Fin du formulaire 
        echo "</form>";
    }
    
    // Fonction qui crée ou récupère la liste des cartes émoticônes
    function recup_grille_cartes(){
        // S'il n'existe pas déjà dans un cookie,
        // on crée le tableau des émoticônes 
        if (!isset($_COOKIE['tab_cartes'])) {
            $tab_cartes = charge_emo($_POST["taille"]); // On le crée
        } else { // Sinon on récupère le tableau depuis le cookie
            $tab_cartes = unserialize($_COOKIE['tab_cartes']); // unserialize retransforme en tableau
        }
        // Dans tous les cas on retourne un tableau plein
        return $tab_cartes;
    }

    // Fonction qui crée ou récupère le tableau des cartes découvertes (par 2)
    function recup_grille_recto(){
        // S'il n'existe pas déjà dans un cookie,
        if (!isset($_COOKIE['tab_recto'])) {
        // On crée le tableau des cartes découvertes
            $tab_recto = array();
        } else { // Sinon on récupère le tableau depuis le cookie
            $tab_recto = unserialize($_COOKIE['tab_recto']); // unserialize retransforme en tableau
        }
        // Dans tous les cas on retourne un tableau 
        return $tab_recto;
    }


    // Fonction qui récupère le dernier ou les deux derniers choix éventuels
    function recup_choix(){
        // Si le tableau n'existe pas déjà dans un cookie
        if (!isset($_COOKIE['tab_choix'])) {
            $tab_choix = array(-1, -1); // on crée un tableau vide (2 éléments), -
        // Sinon on récupère le tableau depuis le cookie
        } else {
            $tab_choix = unserialize($_COOKIE['tab_choix']); // unserialize retransforme en tableau
        }
        // Dans tous les cas on retourne un tableau
        return $tab_choix;
    }

    // Fonction de débogage
    function affiche_debug($tab_cartes, $tab_recto, $tab_choix){
        echo "_POST ";
        voir_tableau($_POST);
        echo "tab_cartes :";
        voir_tableau($tab_cartes);
        echo "tab_recto :";
        voir_tableau($tab_recto);
        echo "tab_choix :";
        voir_tableau($tab_choix);
        if (count($_POST) == 2) echo "Case choisie : ". array_keys($_POST)[1];
    }

    // Fonction qui lance le jeu
    function jouer(){
        // Tableau à 1 dimension des cartes du jeu dans l'ordre
        $tab_cartes = recup_grille_cartes();

        // Tableau à 1 dimension des cartes découvertes (indices des cartes)
        $tab_recto = recup_grille_recto();

        // Tableau à 1 dimension (2 élts) des cartes sélectionnées à étudier 
        $tab_choix = recup_choix();

        
        // Traite le choix éventuel (clic sur une carte)
        if (count($_POST)==2){
            $choix = array_key_last($_POST);
            if ($tab_choix[0] == -1){
                $tab_choix[0] = $choix; //première carte retournée
            }elseif ($tab_choix[1] == -1) {
                $tab_choix[1] = $choix; //deuxième carte retournée
                if ($tab_cartes[$tab_choix[0]] == $tab_cartes[$tab_choix[1]]){ //si cartes identiques
                    $tab_recto[] = $tab_choix[0];
                    $tab_recto[] = $tab_choix[1];
    
                    setcookie("tab_recto", serialize($tab_recto));
    
                    $tab_choix[0] = -1;
                    $tab_choix[1] = -1;
                    if (count($tab_recto) == count($tab_cartes)){
                        echo "<h2> Gagné </h2>";
                    }
                }//pas de else car pas de transfert du tableau ni d'effacement
            }else{ //clic sur une autre carte après deux pas pareils 
                $tab_choix[0] = $choix;
                $tab_choix[1] = -1;
            }

            //on enregistre le le tableau de choix dans un cookie
            setcookie("tab_choix", serialize($tab_choix));
        }//pas de else 
        

        // Dans tous les cas (début de jeu ou partie en cours)
        // On affiche le plateau de jeu
        //voir_tableau($tab_cartes);
        affiche_plateau($tab_cartes, $tab_recto, $tab_choix);
        // Appel de la fonction affiche_debug()
        //affiche_debug($tab_cartes, $tab_recto, $tab_choix);
    }
?>

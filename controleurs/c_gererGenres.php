<?php
/**
 * Contrôleur secondaire chargé de la gestion des genres
 * @author  dk
 * @package default (mission 4)
 */

// récupération de l'action à effectuer
if (isset($_GET["action"])) {
    $action = $_GET["action"];
}
else {
    $action = 'listerGenres';
}

// variables pour la gestion des messages
$titrePage = 'Gestion des genres';

// variables pour la gestion des erreurs
$tabErreurs = array(); 
$hasErrors = false;

// ouvrir une connexion
$cnx = connectDB();

// charger la vue en fonction du choix de l'utilisateur
switch ($action) {
    case 'consulterGenre' : {
        if (isset($_GET["id"])) {
            $strCode = strtoupper(htmlentities($_GET["id"]));
            // récupération du libellé dans la base
            $strSQL = "SELECT lib_genre  "
                ."FROM genre "
                ."WHERE code_genre = ?";
            try {
                $leGenre = getRows($cnx, $strSQL, array($strCode));
                if ($leGenre) {
                    $strLibelle = $leGenre[0][0];
                }
                else {
                    $tabErreurs["Erreur"] = "Ce genre n'existe pas !";
                    $tabErreurs["Code"] = $strCode;
                    $hasErrors = true;
                }                
            }
            catch (PDOException $e) {
                $tabErreurs["Erreur"] = $e->getMessage();
                $hasErrors = true;
            }
        }
        else {
            // pas d'id dans l'url ni clic sur Valider : c'est anormal
            $tabErreurs["Erreur"] = 
		"Aucun genre n'a été transmis pour consultation !";
            $hasErrors = true;
        }
        if ($hasErrors) {
            $msg = "Une erreur s'est produite :";
            include 'vues/v_afficherErreurs.php';
        }
        else {
            include 'vues/v_consulterGenre.php';
        }
    } break;
    case 'ajouterGenre' : {
        // initialisation des variables
        $strCode = '';
        $strLibelle = '';
        // traitement de l'option : saisie ou validation ?
        if (isset($_GET["option"])) {
            $option = htmlentities($_GET["option"]);
        }
        else {
            $option = 'saisirGenre';
        }
        switch($option) {            
            case 'saisirGenre' : {
                include 'vues/v_ajouterGenre.php';
            } break;
            case 'validerGenre' : {
                // tests de gestion du formulaire
                if (isset($_POST["cmdValider"])) {
                    // récupération du libellé
                    if (!empty($_POST["txtLibelle"])) {
                        $strLibelle = ucfirst(htmlentities(
                                $_POST["txtLibelle"])
                        );
                    }
                    if (!empty($_POST["txtCode"])) {
                        $strCode = strtoupper(htmlentities(
                                $_POST["txtCode"])
                        );
                    }
                    // test zones obligatoires
                    if (!empty($strCode) and !empty($strLibelle)) {
                        // les zones obligatoires sont présentes
                        // tests de cohérence 
                        // contrôle d'existence d'un genre avec le même code
                        $strSQL = "SELECT COUNT(*) FROM genre 
                            WHERE code_genre = ?";
                        $doublon = getValue($cnx, $strSQL, array($strCode));
                        if ($doublon) {
                            // signaler l'erreur
                            $tabErreurs["Erreur"] = 
                                    "Il existe déjà un genre avec ce code !";
                            $tabErreurs["Genre"] = $strCode;
                            $hasErrors = true;
                        }
                    }
                    else {
                        // une ou plusieurs valeurs n'ont pas été saisies
                        if (empty($strCode)) {                                
                            $tabErreurs["Code"] = 
                                    "Le code doit être renseigné !";
                        }
                        if (empty($strLibelle)) {
                            $tabErreurs["Libellé"] = 
                                    "Le libellé doit être renseigné !";
                        }
                        $hasErrors = true;
                    }
                    if (!$hasErrors) {
                        // ajout dans la base de données
                        $strSQL = "INSERT INTO genre VALUES (?,?)";
                        try {
                            $res = execSQL(
                                $cnx, $strSQL, array($strCode,$strLibelle)
                            );
                            if ($res) {                                    
                                $msg = '<span class="info">Le genre '
                                    .$strCode.'-'
                                    .$strLibelle.' a été ajouté</span>';
                                include 'vues/v_consulterGenre.php';
                            }
                            else {
                                $tabErreurs["Erreur"] = 
                                        "Une erreur s'est produite dans 
                                            l'opération d'ajout !";
                                $tabErreurs["Code"] = $strCode;
                                $tabErreurs["Libellé"] = $strLibelle;
                                $hasErrors = true;
                            }
                        }
                        catch (PDOException $e) {
                            $tabErreurs["Erreur"] = 
                                    "Une exception PDO a été levée !";
                            $hasErrors = true;
                        }
                    }
                    else {
                        $msg = "L'opération d'ajout n'a pas pu être menée 
                            à terme en raison des erreurs suivantes :";
                        $lien = '<a href="index.php?uc=gererGenres&action=ajouterGenre">Retour à la saisie</a>';
                        include 'vues/v_afficherErreurs.php';
                    }
                }
            } break;
        }        
    } break;    
    case 'modifierGenre' : {
        // initialisation des variables
        $strLibelle = '';        
        // traitement de l'option : saisie ou validation ?
        if (isset($_GET["option"])) {
            $option = htmlentities($_GET["option"]);
        }
        else {
            $option = 'saisirGenre';
        }
        switch($option) {            
            case 'saisirGenre' : {
                // récupération du code
                if (isset($_GET["id"])) {
                    $strCode = strtoupper(htmlentities($_GET["id"]));
                    // récupération du libellé dans la base
                    $strSQL = "SELECT lib_genre  "
                        ."FROM genre "
                        ."WHERE code_genre = ?";
                    $leGenre = getRows($cnx, $strSQL, array($strCode));
                    if (count($leGenre) == 1) {
                        $strLibelle = $leGenre[0][0];
                    }
                    else {
                        $tabErreurs["Erreur"] = "Ce genre n'existe pas !";
                        $tabErreurs["Code"] = $strCode;
                        $hasErrors = true;
                    }
                }
                include 'vues/v_modifierGenre.php';
            } break;
            case 'validerGenre' : {
                // si on a cliqué sur Valider
                if (isset($_POST["cmdValider"])) {
                    // mémoriser les valeurs pour les réafficher 
                    $strCode = $_POST["txtCode"];
                    $strLibelle = ucfirst(htmlentities($_POST["txtLibelle"]));
                    // test zones obligatoires
                    if (!empty($strLibelle)) {
                        // les zones obligatoires sont présentes
                        // tests de cohérence
                    }
                    else {
                        if (empty($strLibelle)) {
                            $tabErreurs["Erreur"] = 
                                    "Le libellé doit être renseigné !";
                            $tabErreurs["Code"] = $strCode;
                        }
                        $hasErrors = true;
                    }
                    if (!$hasErrors) {
                        // mise à jour dans la base de données
                        $strSQL = "UPDATE genre SET lib_genre = ? "
                                 ."WHERE code_genre = ?";
                        try {
                            $res = execSQL(
                                $cnx, $strSQL, array($strLibelle,$strCode)
                            );
                            if ($res) {                                    
                                $msg = '<span class="info">Le genre '
                                    .$strCode.' a été modifié</span>';
                                include 'vues/v_consulterGenre.php';
                            }
                            else {
                                $tabErreurs["Erreur"] = 
                                        "Une erreur s'est produite lors de 
                                            l'opération de mise à jour !";
                                $tabErreurs["Code"] = $strCode;
                                $tabErreurs["Libellé"] = $strLibelle;
                                // en phase de test, on peut ajouter le SQL :
                                $tabErreurs["SQL"] = $strSQL;
                                $hasErrors = true;
                            }
                        }
                        catch (PDOException $e) {
                            $tabErreurs["Code"] = 
                                    "Une exception a été levée !";
                            $hasErrors = true;
                        }
                    }
                }
                else {
                    // pas d'id dans l'url ni clic sur Valider : c'est anormal
                    $tabErreurs["Erreur"] = 
                            "Aucun genre n'a été transmis pour modification !";
                    $hasErrors = true;
                }
            }
        }
        // affichage des erreurs
        if ($hasErrors) {
            $msg = "Une erreur s'est produite :";
            $lien = '<a href="index.php?uc=gererGenres&action=modifierGenre&id='
                .$strCode.'">Retour à la modification</a>';
            include 'vues/v_afficherErreurs.php';            
        }
    } break;
    case 'supprimerGenre' : {
        // récupération de l'identifiant du genre passé dans l'URL
        if (isset($_GET["id"])) {
            $strCode = strtoupper(htmlentities($_GET["id"]));
            // récupération du libellé dans la base
            $strSQL = "SELECT lib_genre  "
                ."FROM genre "
                ."WHERE code_genre = ?";
            $leGenre = getRows($cnx, $strSQL, array($strCode));            
            if (count($leGenre) == 1) {
                $strLibelle = $leGenre[0][0];
            }
            else {
                $tabErreurs["Erreur"] = "Ce genre n'existe pas !";
                $tabErreurs["Code"] = $strCode;
                $hasErrors = true;
            }
            if (!$hasErrors) {
                // rechercher des ouvrages de ce genre
                $strSQL = "SELECT COUNT(*)  "
                    ."FROM ouvrage "
                    ."WHERE code_genre = ?";
                try {
                    $ouvragesDuGenre = getValue($cnx, $strSQL, array($strCode));
                    if ($ouvragesDuGenre == 0) {
                        // c'est bon, on peut le supprimer
                        $strSQL = "DELETE FROM genre WHERE code_genre = ?";
                        try {
                            $res = execSQL($cnx, $strSQL, array($strCode));
                            if ($res) {
                                $msg = '<span class="info">Le genre '
                                    .$strCode.'-'
                                    .$strLibelle.' a été supprimé</span>';
                                include 'vues/v_afficherMessage.php';
                            }
                            else {
                                $tabErreurs["Erreur"] = "Une erreur s'est 
                                    produite lors de l'opération 
                                    de suppression !";
                                $tabErreurs["Code"] = $strCode;
                                // en phase de test, on peut ajouter le SQL :
                                $tabErreurs["SQL"] = $strSQL;
                                $hasErrors = true;
                            }
                        }
                        catch (PDOException $e) {
                            $tabErreurs["Erreur"] = 
                                    "Une exception PDO a été levée !";
                            $tabErreurs["Message"] = $e->getMessage();
                            $hasErrors = true;
                        }
                    }
                    else {
                        $tabErreurs["Erreur"] = 
                                "Ce genre est référencé par des ouvrages, 
                                 suppression impossible !";
                        $tabErreurs["Code"] = $strCode;
                        $tabErreurs["Ouvrages"] = $ouvragesDuGenre;
                        $hasErrors = true;                        
                    }
                }
                catch (PDOException $e) {
                     $tabErreurs["Erreur"] = $e->getMessage();
                }
            }
        }
        // affichage des erreurs
        if ($hasErrors) {
            $msg = "Une erreur s'est produite :";
            $lien = '<a href="index.php?uc=gererGenres&action=consulterGenre&id=' 
                        .$strCode.'">Retour à la consultation</a>';
            include 'vues/v_afficherErreurs.php';            
        }
    } break;   
    case 'listerGenres' : {
        // récupérer les genres
        $strSQL = "SELECT code_genre as Code, "
            . "lib_genre as 'Libellé' "
            . "FROM genre ";
        $lesGenres = getRows($cnx, $strSQL, array());
        // afficher le nombre de genres
        $nbGenres = count($lesGenres);
        include 'vues/v_listeGenres.php';
    } break;
    // déconnexion
    disconnectDB($cnx);
}


<?php
/** 
 * Page de gestion des genres
 * @author 
 * @package default
*/
?>
<div id="content">
    <h2>Gestion des genres</h2>    
    <span class="info">
    <?php 
    if (strlen($msg) > 0) {
        echo '<span class="info">.$msg.</span>';
    }
    ?>
    <div id="object-list">
        <div class="corps-form">
            <fieldset>
                <legend>Consulter un genre</legend>                        
                <div id="breadcrumb">
                    <a href="index.php?uc=gererGenres&action=ajouterGenre">Ajouter</a>&nbsp;
                    <a href="index.php?uc=gererGenres&action=modifierGenre&option=saisirGenre&id=<?php echo $strCode ?>">Modifier</a>&nbsp;
                    <a href="index.php?uc=gererGenres&action=supprimerGenre&id=<?php echo $strCode ?>">Supprimer</a>
                </div>
                <table>
                    <tr>
                        <td class="h-entete">
                            Code :
                        </td>
                        <td class="h-valeur">
                            <?php echo $strCode ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="h-entete">
                            Libellé :
                        </td>
                        <td class="h-valeur">
                            <?php echo $strLibelle ?>
                        </td>
                    </tr>
                </table>
            </fieldset>                    
        </div>
    </div>
</div>

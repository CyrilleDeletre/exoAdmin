<?php
session_start();

// Simulation d'une base de données d'utilisateurs (à remplacer par une vraie base de données en production)
$utilisateurs = [
    'utilisateur1' => ['nom' => 'Doe', 'prenom' => 'Jane', 'password' => '1234', 'email' => 'john@example.com'],
    'utilisateur2' => ['nom' => 'Doe', 'prenom' => 'John', 'password' => '0000', 'email' => 'jane@example.com']
];

// Fonction pour afficher les informations de l'utilisateur actuel
function afficherInformationsUtilisateur()
{
    if (isset($_SESSION['utilisateur'])) {
        $utilisateur = $_SESSION['utilisateur'];
        echo "<p>Nom: {$utilisateur['nom']}</p>";
        echo "<p>Prénom: {$utilisateur['prenom']}</p>";
    } else {
        echo "<p>Aucun utilisateur connecté.</p>";
    }
}

// Vérifier si le formulaire de mise à jour des paramètres a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    // Mettre à jour les informations de la session avec les données du formulaire
    $_SESSION['utilisateur']['nom'] = isset($_POST['nom']) ? $_POST['nom'] : $_SESSION['utilisateur']['nom'];
    $_SESSION['utilisateur']['prenom'] = isset($_POST['prenom']) ? $_POST['prenom'] : $_SESSION['utilisateur']['prenom'];

    // Mettre à jour les informations dans le tableau $utilisateurs
    $utilisateur_cle = $_SESSION['utilisateur_cle'];

    // Assurez-vous que l'index existe dans le tableau $utilisateurs
    if (isset($utilisateurs[$utilisateur_cle])) {
        // Mettre à jour les informations dans le tableau $utilisateurs
        $utilisateurs[$utilisateur_cle]['nom'] = isset($_POST['nom']) ? $_POST['nom'] : $utilisateurs[$utilisateur_cle]['nom'];
        $utilisateurs[$utilisateur_cle]['prenom'] = isset($_POST['prenom']) ? $_POST['prenom'] : $utilisateurs[$utilisateur_cle]['prenom'];

        // Assurez-vous que la clé 'password' existe dans le tableau $utilisateurs avant de l'ajouter à la session
        $_SESSION['utilisateur']['password'] = isset($utilisateurs[$utilisateur_cle]['password']) ? $utilisateurs[$utilisateur_cle]['password'] : null;
    } else {
        // Gérer l'erreur si l'index n'existe pas dans le tableau $utilisateurs
        echo "<p>Erreur lors de la mise à jour des paramètres de l'utilisateur (index manquant).</p>";
    }
}



// Vérifier si le formulaire de connexion ou de création de compte a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $password = $_POST['password'];

    // Initialisez la variable $utilisateur_trouve
    $utilisateur_trouve = false;

    if (isset($_POST['login'])) {
        // Logique de connexion existante
        // Vérifier si l'utilisateur existe dans la base de données de simulation
        foreach ($utilisateurs as $cle => $utilisateur) {
            if ($utilisateur['nom'] === $nom && $utilisateur['prenom'] === $prenom && $utilisateur['password'] === $password) {
                $_SESSION['utilisateur_cle'] = $cle; // Stocker la clé de l'utilisateur dans la session
                $utilisateur_trouve = true;
                $_SESSION['utilisateur'] = $utilisateur; // Stocker les informations de l'utilisateur dans la session
                break;
            }
        }

        if (!$utilisateur_trouve) {
            echo "<p>Utilisateur non trouvé.</p>";
        }
    } elseif (isset($_POST['create_account'])) {
        // Logique pour créer un nouveau compte
        // Vous pouvez ajouter une logique de validation et de stockage des nouveaux utilisateurs ici.
        // Dans cet exemple, nous ajoutons simplement un nouvel utilisateur à la base de données de simulation.
        $nouvel_utilisateur = ['nom' => $nom, 'prenom' => $prenom, 'password' => $password];
        $utilisateurs[] = $nouvel_utilisateur;

        // Connectez l'utilisateur nouvellement créé en enregistrant ses informations dans la session
        $_SESSION['utilisateur_cle'] = count($utilisateurs) - 1; // Stocker la clé du nouvel utilisateur dans la session
        $_SESSION['utilisateur'] = $nouvel_utilisateur; // Stocker les informations de l'utilisateur dans la session
        $utilisateur_trouve = true; // Mettez à jour la variable ici si nécessaire
    }
}

// Ajouter une logique pour gérer la déconnexion
if (isset($_GET['onglet']) && $_GET['onglet'] === 'deconnexion') {
    // Détruire la session et rediriger vers la page d'accueil
    session_destroy();
    header('Location: index.php?onglet=accueil');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Panneaux d'administration</title>
    <link rel="stylesheet" href="style/style.css">
</head>

<body>
    <header>

        <nav>
            <ul>
                <li class="admin">Panneaux d'administration</li>
                <?php
                // Afficher la lien vers la page d'Accueil
                echo '<li><a href="?onglet=accueil">Accueil</a></li>';

                // Afficher les informations personnelles de l'utilisateur si il y'a un utilisateur
                if (isset($_SESSION['utilisateur'])) {
                    // Si un utilisateur est connecté, afficher le lien des informations personnelles
                    echo '<li><a href="?onglet=utilisateurs">Informations personnelles</a></li>';
                } else {
                    // Si aucun utilisateur ne rien afficher
                    echo '';
                }

                // Afficher les paramètres de l'utilisateur si il y'a un utilisateur
                if (isset($_SESSION['utilisateur'])) {
                    // Si un utilisateur est connecté, afficher le lien de modification des paramètres
                    echo '<li><a href="?onglet=parametres">Paramètres</a></li>';
                } else {
                    // Si aucun utilisateur ne rien afficher
                    echo '';
                }

                // Afficher le lien de connexion/déconnexion en fonction de l'état de la session
                if (isset($_SESSION['utilisateur'])) {
                    // Si un utilisateur est connecté, afficher le lien de déconnexion
                    echo '<li><a href="?onglet=deconnexion">Déconnexion</a></li>';
                } else {
                    // Si aucun utilisateur n'est connecté, afficher le lien de connexion
                    echo '<li><a href="?onglet=connexion">Connexion</a></li>';
                }
                ?>
            </ul>
        </nav>
    </header>

    <div id="contenu">
        <?php
        // Afficher le contenu de l'onglet correspondant
        if (isset($_GET['onglet'])) {
            $onglet = $_GET['onglet'];
            switch ($onglet) {
                case 'accueil':
                    // Afficher le contenu de l'onglet Accueil
                    echo "<h2>Accueil</h2>";
                    break;
                case 'utilisateurs':
                    // Afficher le contenu de l'onglet Utilisateurs
                    echo "<h2>Utilisateurs</h2>";
                    afficherInformationsUtilisateur();
                    break;
                case 'parametres':
                    // Afficher le contenu de l'onglet Paramètres
                    if (isset($_SESSION['utilisateur'])) {
                        echo "<h2>Paramètres</h2>";
        ?>
                        <form method="post" action="">
                            <label for="nom">Nouveau Nom:</label>
                            <input type="text" name="nom" required value="<?php echo $_SESSION['utilisateur']['nom']; ?>">

                            <label for="prenom">Nouveau Prénom:</label>
                            <input type="text" name="prenom" required value="<?php echo $_SESSION['utilisateur']['prenom']; ?>">

                            <input type="submit" name="update" value="Mettre à jour">
                        </form>
        <?php
                    } else {
                        echo "<p>Veuillez d'abord vous connecter.</p>";
                    }
                    break;
                case 'connexion':
                    // Afficher le contenu de l'onglet Connexion
                    if (!isset($_SESSION['utilisateur'])) {
                        echo '
                        <form method="post" action="">
                            <label for="nom">Nom:</label>
                            <input type="text" name="nom" required>
                            
                            <label for="prenom">Prénom:</label>
                            <input type="text" name="prenom" required>
                        
                            <label for="password">Mot de passe:</label>
                            <input type="password" name="password" required>
                        
                            <input type="submit" name="login" value="Se connecter">
                            <input type="submit" name="create_account" value="Créer un compte">
                        </form>';
                    } else {
                        echo "<p>Vous êtes déjà connecté.</p>";
                    }
                    break;
                default:
                    echo "<p>Onglet inconnu.</p>";
                    break;
            }
        } else {
            echo "<p>Veuillez sélectionner un onglet.</p>";
        }
        ?>
    </div>
</body>

</html>
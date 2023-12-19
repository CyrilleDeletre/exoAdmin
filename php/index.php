<?php
// Démarre une session
session_start();

// Tableau permettant de simuler une base de donnée d'utilisateurs
$users = [
    'user1' => ['firstName' => 'John', 'lastName' => 'Doe', 'password' => '1234',],
    'user2' => ['firstName' => 'Jane', 'lastName' => 'Doe', 'password' => '0000',]
];

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
                // On crée une variable pour vérifier s'il y a un utilisateur connecté dans la session
                $userConnected = isset($_SESSION['user']);

                // On affiche le bouton d'accueil
                echo '<li><a href="?page=home">Accueil</a></li>';

                // S'il y a un utilisateur connecté
                if ($userConnected) {
                    // On affiche un onglet information personnel, un onglet paramètres et un onglet de déconnexion
                    echo '<li><a href="?page=userInformations">Informations personnelles</a></li>';
                    echo '<li><a href="?page=settings">Paramètres</a></li>';
                    echo '<li><a href="?page=disconnection">Déconnexion</a></li>';
                }

                // Sinon on affiche un onglet de connexion
                else {
                    echo '<li><a href="?page=connection">Connexion</a></li>';
                }
                ?>
            </ul>
        </nav>
    </header>

    <div id="contenu">
        <?php
        // Si on clique sur un lien qui pointe vers l'url page, on récupère cette url
        if (isset($_GET['page'])) {

            // On crée une variable $page de l'url 'page' récupéré
            $page = $_GET['page'];

            // On switch les pages en fonction de leur valeur dans l'url
            switch ($page) {

                    // Le cas où page = home
                case 'home':

                    // S'il y a un utilisateur connecté dans la session,
                    if (isset($_SESSION['user'])) {
                        // On crée une variable user en fonction de cet utilisateur
                        $user = $_SESSION['user'];
                        // On affiche dans la page d'accueil un message personnalisé pour cet utilisateur
                        echo "<h2>Bienvenue dans ton espace personnel, {$user['firstName']} {$user['lastName']}</h2>";
                    }
                    // Sinon, on affiche juste un titre d'accueil
                    else
                        echo "<h2>Accueil</h2>";
                    break;

                    // Le cas où page = userInformations
                case 'userInformations':

                    // S'il y a un utilisateur connecté dans la session
                    if (isset($_SESSION['user'])) {

                        // On crée une variable user en fonction de cet utilisateur
                        $user = $_SESSION['user'];

                        // On affiche les information de l'utilisateur connecté
                        echo "<h2>Vos informations personnelles :</h2>";
                        echo "<p>Prénom : {$user['firstName']}</p>";
                        echo "<p>Nom : {$user['lastName']}</p>";
                    }

                    // Sinon, on affiche qu'il n'y a aucun utilisateur connecté
                    else {
                        echo "<p>Aucun utilisateur connecté.</p>";
                    }
                    break;

                    // Le cas où page = settings
                    // Le cas où page = settings
                case 'settings':

                    // S'il y a un utilisateur connecté dans la session
                    if (isset($_SESSION['user'])) {

                        // On crée une variable user en fonction de cet utilisateur
                        $user = $_SESSION['user'];

                        // Si le formulaire est soumis
                        if (isset($_POST['update'])) {

                            // Récupérez les nouvelles valeurs des champs
                            $newFirstName = $_POST['firstName'];
                            $newLastName = $_POST['lastName'];
                            $newPassword = $_POST['password'];

                            // Mettez à jour les informations de l'utilisateur dans la session
                            $_SESSION['user']['firstName'] = $newFirstName;
                            $_SESSION['user']['lastName'] = $newLastName;
                            $_SESSION['user']['password'] = $newPassword;
                        }

                        // Affichez les paramètres actuels de l'utilisateur
                        echo "<h2>Vos paramètres</h2>";
        ?>
                        <!-- Début de l'html -->
                        <form method="post" action="">
                            <label for="firstName">Modifiez votre prénom</label>
                            <input type="text" name="firstName" value="<?php echo $_SESSION['user']['firstName']; ?>" required>

                            <label for="lastName">Modifiez votre nom</label>
                            <input type="text" name="lastName" value="<?php echo $_SESSION['user']['lastName']; ?>" required>

                            <label for="password">Modifiez votre nom</label>
                            <input type="password" name="password" value="<?php echo $_SESSION['user']['password']; ?>" required>

                            <input type="submit" name="update" value="Mettre à jour">
                        </form>
                        <!-- Fin de l'html -->
        <?php
                    } else {
                        // Sinon, on affiche qu'il faut se connecter
                        echo "<p>Veuillez vous connecter.</p>";
                    }
                    break;

                    // Le cas où page = connection
                case 'connection':

                    // S'il n'y a pas d'utilisateur connecté dans la session,
                    if (!isset($_SESSION['user'])) {
                        // Vérifier si l'utilisateur existe dans la database
                        if (isset($_POST['login'])) {
                            $firstName = $_POST['firstName'];
                            $lastName = $_POST['lastName'];
                            $password = $_POST['password'];

                            $userSignedIn = false;

                            // Vérifier les informations d'identification avec le tableau $users
                            foreach ($users as $user => $userInfo) {
                                if ($userInfo['firstName'] === $firstName && $userInfo['lastName'] === $lastName) {
                                    if ($userInfo['password'] === $password) {
                                        // Authentification réussie, définir la variable de session
                                        $_SESSION['user'] = $userInfo;

                                        // Rédiriger vers la page d'accueil
                                        header("Location: ?page=home");
                                        exit();
                                    } else {
                                        // Mot de passe incorrect
                                        $userSignedIn = true;
                                        echo "Le mot de passe est incorrect";
                                    }
                                }
                            }

                            // Afficher le message d'erreur si l'authentification a échoué
                            if (!$userSignedIn) {
                                echo "L'utilisateur n'existe pas";
                            }
                        }

                        // Afficher des champs de saisie pour se connecter
                        echo '
                            <form method="post" action="">
                                <label for="firstName">Prénom:</label>
                                <input type="text" name="firstName" required>

                                <label for="lastName">Nom:</label>
                                <input type="text" name="lastName" required>
                            
                                <label for="password">Mot de passe:</label>
                                <input type="password" name="password" required>
                            
                                <input type="submit" name="login" value="Se connecter">
                            </form>';
                    } else {
                        echo "<p>Vous êtes connecté.</p>";
                    }
                    break;

                    // Le cas où page = disconnection
                case 'disconnection':
                    if (isset($_SESSION['user'])) {
                        // Récupérer les informations de l'utilisateur connecté
                        $currentUser = $_SESSION['user'];

                        // Trouver la clé de l'utilisateur dans le tableau des utilisateurs
                        $currentUserKey = array_search($currentUser, $users);

                        // Si l'utilisateur est trouvé dans le tableau, le supprimer
                        if ($currentUserKey !== false) {
                            unset($users[$currentUserKey]);
                        }

                        // Détruire la session
                        session_destroy();

                        // Rédiriger vers la page d'accueil
                        header("Location: ?page=home");
                        exit();
                    }

                    // Le cas où l'on est pas dans page
                default:
                    echo "<p>Onglet inconnu.</p>";
                    break;
            }
        }
        ?>
    </div>
</body>

</html>
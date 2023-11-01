<?php

try{
    header('Content-Type: application/json');
    $Output = array(
       "error" => false,
       "message" => "N/A",
       "output" => "N/A"
    );

    if(isset($_POST['module']) & !empty($_POST['module']) & isset($_POST['csrf']) & !empty($_POST['csrf'])){

        $Module = $_POST['module'];
        $Csrf = $_POST['csrf'];
        
        // if ($Csrf != $_SESSION["csrf_token"]) throw new Exception("CSRF invalide");

        $pdo = new PDO('mysql:host=localhost;dbname=privatemessage;charset=UTF8', 'privatemessage', 'a#hK4DO4Yb0e6Znz1!');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        switch($Module){
            case 'Signup':
                if(!isset($_POST['password']) | !isset($_POST['login'])) throw new Exception("Paramètres manquant");

                $Login = $_POST['login'];
                $Password = $_POST['password'];

                // le user existe déjà ?
                $sql = "SELECT login FROM pmapiusers WHERE login = :login";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['login' => $Login]);
                $user = $stmt->fetch();
                
                // user existant ?
                if($user){
                    
                    throw new Exception('Utilisateur existant');

                }else{ // non, création de l'utilisateur
    
                    $Apikey = bin2hex(random_bytes(32));
                    $Password = password_hash($Password, PASSWORD_DEFAULT);
                    $sql = "INSERT INTO pmapiusers (login, password, apikey) VALUES (:login, :password, :apikey)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(['login' => $Login, 'password' => $Password, 'apikey' => $Apikey]);

                    $_SESSION["Mail"] = $Login;
                    $_SESSION["Apikey"] = $Apikey;

                    $Output["output"] = 'Utilisateur créé.'; 
                    http_response_code(200);  
                }

            break;

            case 'Signin':
                if(!isset($_POST['password']) | !isset($_POST['login'])) throw new Exception("Paramètres manquant");

                $Login = $_POST['login'];
                $Password = $_POST['password'];
                $Password = password_hash($Password, PASSWORD_DEFAULT);

                // le user existe déjà ?
                $sql = "SELECT apikey FROM pmapiusers WHERE login = :login and password = :password";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['login' => $Login, 'password' => $Password]);
                $user = $stmt->fetch();

                // user existant ?
                if($user){
                    
                    $_SESSION["Mail"] = $Login;
                    $_SESSION["Apikey"] = $user["apikey"];

                    $Output["message"] = 'Utilisateur trouvé.'; 
                    http_response_code(200);  

                }else{ // non, création de l'utilisateur
                    throw new Exception('Login non trouvé ou mot de passe incorrect');
                }

            break;
            
            default:
            throw new Exception('Module non trouvé');
              break; 
        }
    }
    else {
        throw new Exception('Paramètres manquants');
    }
} catch (\Throwable $e) {
    $Output["error"] = true;
    $Output["message"] = $e->getMessage();
    http_response_code(400);
} finally {
    echo json_encode($Output, JSON_FORCE_OBJECT);
    die();
}
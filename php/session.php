<?php
if(!isset($_SESSION)){

    session_start();
}

require_once 'php/global.php';

if(!isset($_SESSION['auth'])){
    
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        
        if(isset($_POST['username']) && isset($_POST['password'])){

            $username = isset($_POST["username"])?strtolower($_POST["username"]):''; //remove case sensitivity on the username
            $password = isset($_POST["password"])?$_POST["password"]:'';

            unset($_SESSION['auth']);

            if($username != NULL && $password != NULL){

                $mySQL = new db_mysql();

                $sql = 'select * from raffle.user where username=\''.$username.'\' and password=\''.$password.'\'';

                if($result = mysqli_query($mySQL->connection, $sql)) {

                    while ($row = mysqli_fetch_assoc($result)) {

                        $_SESSION['auth']["id"]        = $row['id'];
                        $_SESSION['auth']["username"]  = $row['username'];
                        $_SESSION['auth']["password"]  = $row['password'];
                        $_SESSION['auth']["firstname"] = $row['firstname'];
                        $_SESSION['auth']["lastname"]  = $row['lastname'];
                        $_SESSION['auth']["email"]     = $row['email'];
                    }

                    mysqli_free_result($result);
                }
            }

            /*$_SESSION['auth']["id"] = 1;
            $_SESSION['auth']["username"]  = 'username_jason';
            $_SESSION['auth']["password"]  = 'password_jason';
            $_SESSION['auth']["firstname"] = 'firstname_jason';
            $_SESSION['auth']["lastname"]  = 'lastname_jason';
            $_SESSION['auth']["email"]     = 'email_jason';*/

            header('Location: /');
        }
    }
}

if(isset($_GET['logout'])){

    unset($_SESSION['auth']);

    session_destroy();

    echo "<script type='text/javascript'>top.location.reload()</script>";
    exit;
    //header('location: /');
}
?>

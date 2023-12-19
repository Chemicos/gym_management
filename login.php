<?php
session_start();
if(isset($_SESSION["users"])){
    header("Location:membri.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Form</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <link rel="stylesheet" href="register.css">
</head>
<body>
  <div class= "container">
    <?php
    if(isset($_POST["login"])){
      $username = $_POST["username"];
      $password = $_POST["password"];
      require_once "database.php";
      $sql = "SELECT * FROM users WHERE username = '$username'";
      $result = mysqli_query($conn, $sql);
      $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
      if($user){
        if(password_verify($password, $user["password"])){
          session_start();
          $_SESSION["user"] = "yes";
          header("Location:membri.php");
        }else{
          echo"<div class='alert alert-danger'>Password is incorrect</div>";
        }
      }else{
        echo"<div class='alert alert-danger'>Username does not exist</div>";
      }
      if($result->num_rows > 0){
        $data = $result->fetch_assoc();
        foreach($data as $k => $v){
            $_SESSION['login_'.$k] = $v;
        }
        /**
         * Saving the input data to Cookie
         */
        if(isset($_POST['rememberMe'])){
            /**
             * Store Login Credential
             */
            setcookie('username', $_POST['username'], ( time() + ((365 * 24 * 60 * 60) *3) ));
            setcookie('password', $_POST['password'], ( time() + ((365 * 24 * 60 * 60) *3) ));
        }else{
            /**
             * Delete Login Credential
             */
            setcookie('username', $_POST['username'], ( time() - (24 * 60 * 60) ));
            setcookie('password', $_POST['password'], ( time() - (24 * 60 * 60) ));
        }
    }
  }
    
    ?>
    <form class = "card" action="login.php" method="POST">
      <div class="card-header">
        <h3 class="card-title text-center fw-bold mb-4">Login</h3>
      </div>
      <div class="card-body">
      <div class="form-floating mb-3">
        <input type ="text" placeholder="Username" id="floatingInput" value="<?= isset($_COOKIE['username']) ? $_COOKIE['username'] : '' ?>" name="username" class="form-control">
        <label for="floatingInput">Username</label>
      </div>
      <div class="form-floating mb-3">
        <input type ="password" placeholder="Password" id="floatingPassword" value="<?= isset($_COOKIE['password']) ? $_COOKIE['password'] : '' ?>" name="password" class="form-control">
        <label for="floatingPassword">Password</label>
      </div>
      <div class="d-grid gap-2">
        <input type ="submit" value="Login" name="login" class="btn btn-primary">
      </div>
 
      <div class="">
        <input class="form-check-input my-2" type="checkbox" name="rememberMe" id="flexCheckDefault"<?= (isset($_COOKIE['username']) && isset($_COOKIE['password'])) ? "checked" : '' ?>>
        <label class="form-check-label my-1" for="flexCheckDefault">Remember Me</label>
        </div>
        </div>
    </form>
    <div class = "mt-5 card-footer text-body-secondary"><p>Don't have an account yet? <a href="register.php" class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Register here!</a></p></div>
    </div>
</body>
</html>

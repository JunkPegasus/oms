<?php
include_once 'Common/UserInfo.php';
if (userLoggedIn()) {
    header('Location: oms.php');
}
?>
    <!DOCTYPE html>
    
    <html>

    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>OMS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400|Roboto:700,900" rel="stylesheet">
        <link rel="stylesheet" type="text/css" media="screen" href="css/style_login.css" />
         <script src="js/jquery.js"></script>
        <script src="js/ajax.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.min.js" integrity="sha384-FzT3vTVGXqf7wRfy8k4BiyzvbNfeYjK+frTVqZeNDFl8woCbF0CYG6g2fMEFFo/i" crossorigin="anonymous"></script>
        <script src="js/script.js"></script>

    </head>
    <body>
    <h1>Login</h1>
        <?php if(isset($_GET['login'])) {?>
            <div class="messageIndicator">
                <?php
                    if($_GET['login'] == "error") echo "Falsches Passwort oder Benutzername!";
                    if($_GET['login'] == "credentials") echo "Kein Passwort oder Benutzername eingegeben!";
                    if($_GET['login'] == "server") echo "Interner Serverfehler. Bitte melde den Fehler.";
                ?>
            </div>
        <?php } ?>
        <form action="Services/LoginService.php" method="post">
            <input type="text" name="username" placeholder="Benutzername" required>
            <input type="password" name="password" placeholder="Passwort" required>
            <input type="submit" value="Login">
        </form>
    </body>
</html>
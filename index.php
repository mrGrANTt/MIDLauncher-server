<?php
    global $mainUrl;
    $mainUrl = "http://localhost/GameServer/";
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Game Server</title>
    
    <link href="colors.css" rel="stylesheet" />
    <link href="index.css" rel="stylesheet" />

</head>
<body>
    <?php
        include_once('page/base.html');
        session_start();
        include_once('page/includes/database.php');
        connect();
    ?>

    <header class="head"> 
        <div class="head-inner">
            <a href="/GameServer" class="logo">
                <img src="img/logo/title_logo.png" alt="Logo">
            </a>

            <div class="hello">
                <?php
                    if(isset($_SESSION['name'])) {
                        echo '<b>Hello, '.$_SESSION['name'].'!</b>';
                    }
                ?>
            </div>

            <nav class="nav-links">
                <?php
                    if(isset($_SESSION['name'])) {
                        echo '<a href="?page=account">Account</a>';
                        echo '<a href="?page=suggest">Suggest</a>';
                        $adm = checkRole('admin');
                        $mod = checkRole('moderator');
                        if($adm) {
                            echo '<a href="?page=admin">Admin</a>';
                        }
                        if($adm || $mod) {
                            echo '<a href="?page=games">Games</a>';
                        }
                    } else {
                            echo '<a href="?page=login">Login</a>';
                    }
                ?>
            </nav>
        </div>
    </header>

    <?php

        if(isset($_GET['page'])) {
            switch ($_GET['page']) {
                case "admin": include_once("page/admin.php"); break;
                case "games": include_once("page/games.php"); break;
                case "login": include_once("page/login.php"); break;
                case "account": include_once("page/account.php"); break;
                case "suggest": include_once("page/suggest.php"); break;
                case "download": include_once("page/download.php"); break;
                default: include_once("page/404.html"); break;
            }
        } else {
            include_once('page/main.php');
        }
    ?>

    <footer class="footer">
        <div class="footer-content">
            <div class="contacts">
                <p>Email: <a href="mailto:example@example.com">example@example.com</a></p>
                <p>Telegram: <a href="https://t.me/yourtelegram" target="_blank">@yourtelegram</a></p>
                <p>GitHub: <a href="https://github.com/yourusername" target="_blank">github.com/yourusername</a></p>
            </div>
            <div class="credits">
                <p>Particles.js by <a href="https://github.com/VincentGarreau/particles.js/?tab=readme-ov-file" target="_blank">VincentGarreau</a></p>
            </div>
        </div>
    </footer>

</body>

</html>
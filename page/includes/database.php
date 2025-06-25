<?php

use function PHPSTORM_META\type;

function connect(
    $host='localhost',
    $user='MIDLauncher',
    $pass='XbD%VO3NM#1a',
    $dbname='MDLDatabase'
) {
    global $link;
    $link = mysqli_connect($host, $user, $pass);

    if (!$link) {
        die('Conecction error: ' . mysqli_connect_error());
    }
    if (!mysqli_select_db($link, $dbname)) {
        die('Exeption on open Database: ' . mysqli_error($link));
    }
    generate_tablet();
}

function generate_tablet() {
    global $link;
    $crs = [
        'CREATE TABLE IF NOT EXISTS `users` (
            `id` INTEGER NOT NULL UNIQUE AUTO_INCREMENT PRIMARY KEY,
            `name` varchar(30) NOT NULL UNIQUE,
            `email` varchar(30) NOT NULL UNIQUE,
            `pass` varchar(32) NOT NULL,
            `role` varchar(10) 
        );',
        'CREATE TABLE IF NOT EXISTS `suggest` (
            `id` INTEGER NOT NULL UNIQUE AUTO_INCREMENT PRIMARY KEY,
            `sender_id` INTEGER NOT NULL,
            `name` varchar(30) NOT NULL,
            `url` varchar(128) NOT NULL,
            `description` varchar(255),
            `unsver` varchar(255),
            `date` varchar(10),
            FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`)
        );',
        'CREATE TABLE IF NOT EXISTS `games` (
            `id` INTEGER NOT NULL UNIQUE AUTO_INCREMENT PRIMARY KEY,
            `sender_id` INTEGER NOT NULL,
            `name` varchar(30) NOT NULL,
            `date` varchar(10),
            `description` varchar(255) NOT NULL,
            `url` varchar(255),
            FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`)
        );' // файл будет сохранятся по id
    ];

    foreach($crs as $cr) {
        //echo $cr.'<br />';
        mysqli_query($link, $cr);
        $ex = mysqli_error($link);
        if ($ex) {
            die('Tablet generate exception: '.$ex.'<br />');
        }
    }
}

function checkRole($role) {
    if (isset($_SESSION['name'])) {
        if(is_array($role)) {
            foreach($role as $r) {
                if(checkRole($r)) {
                    return true;
                }
            }
        } else {
            global $link;

            $sel = $link->prepare("SELECT `id` FROM `users` WHERE `name`= ? AND `role` = ? LIMIT 1;"); // 1';DELETE from `users` WHERE name = '123
            $sel->bind_param('ss', $_SESSION['name'], $role);

            try {
                $sel->execute();
            } catch(mysqli_sql_exception $ex) {
                echo '<p style="color: red;">Some exception... Code: '.$ex->getCode().'. Pleace tell Administrator!</p>';
                return false;
            }

            $res = $sel->get_result();
            $err = $sel->error;
            if($err != "") {
                echo $err."<br/>";
                ?>
                    <p style="color: red;">Some think wrong... Call Administrator!</p>
                <?php
            }

            $arr = mysqli_fetch_array($res);
            
            if($arr) {
                return true;
            }
        }
    }
    return false;
}
?>
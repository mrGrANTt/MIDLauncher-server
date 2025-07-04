<?php

use BcMath\Number;

function register($name, $email, $pas) {
    $name = trim(htmlspecialchars($name));
    $email = trim(htmlspecialchars($email));
    $pas = trim(htmlspecialchars($pas));

    $passl = strlen($pas);
    $pas = md5($pas);

    $namel = strlen($name);
    $emaill = strlen($email);

    if(2 >= $namel || $namel >= 31) {
        echo '<p style="color: var(--bad);">Name must be from 3 to 30 simbol!</p>';
        return false;
    } elseif(!preg_match('/^[-_ A-Za-z0-9]{3,}$/', $name)) {
        echo '<p style="color: var(--bad);">Name must contain only a-z, A-Z, 0-9, "-" or "_"!</p>';
        return false;
    } 

    if(1 > $emaill || $emaill >= 31) {
        echo '<p style="color: var(--bad);">Email must be less than 30 simbol and not empty!</p>';
        return false;
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '<p style="color: var(--bad);">Uncorrectly email!</p>';
        return false;
    }

    if(1 > $passl) {
        echo '<p style="color: var(--bad);">Password can\'t be empty!</p>';
        return false;
    }

    global $link;

    $ins = $link->prepare("INSERT into `users`(`name`, `email`, `pass`, `role`) values (?, ?, ?, 'user');");
    $sel = $link->prepare("SELECT `id` FROM `users` WHERE `name`= ?;"); // 1';DELETE from `users` WHERE name = '123
    $ins->bind_param('sss', $name, $email, $pas);
    $sel->bind_param('s', $name);

    try {
        $ins->execute();
    } catch(mysqli_sql_exception $ex) {
        if($ex->getCode() == 1062) {
            ?>
                <p style="color: var(--bad);">Name or email alredy used!</p>
            <?php
            return false;
        } else {
            echo '<p style="color: var(--bad);">Some exception... Code: '.$ex->getCode().'. Pleace tell Administrator!</p>';
            return false;
        }
    }

    try {
        $sel->execute();
    } catch(mysqli_sql_exception $ex) {
        echo '<p style="color: var(--bad);">Some exception... Code: '.$ex->getCode().'. Pleace tell Administrator!</p>';
        return false;
    }

    $res = $sel->get_result();
    $err = $sel->error;
    if($err != "") {
        echo $err."<br/>";
        ?>
            <p style="color: var(--bad);">Some think wrong... Call Administrator!</p>
        <?php
        return false;
    }

    $arr = mysqli_fetch_array($res);
    
    if($arr) {
        $_SESSION['name'] = $name;
        $_SESSION['id'] = $arr['id'];
        return true;
    }
    return false;
}

function login($name, $pas) {
    $name = trim(htmlspecialchars($name));
    $pas = trim(htmlspecialchars($pas));

    $passl = strlen($pas);
    $pas = md5($pas);

    $namel = strlen($name);

    if(2 >= $namel || $namel >= 31) {
        echo '<p style="color: var(--bad);">Name must be from 3 to 30 simbol!</p>';
        return false;
    }
    if(1 > $passl) {
        echo '<p style="color: var(--bad);">Password can\'t be empty!</p>';
        return false;
    }

    global $link;

    $sel = $link->prepare('SELECT `name`, `id` FROM `users` WHERE `pass` = ? AND (`name` = ? OR email = ?);');
    $sel->bind_param('sss', $pas, $name, $name);
    $err = "";

    try {
        $sel->execute();
        $res = $sel->get_result(); 
    } catch(mysqli_sql_exception $ex) {
        $err = $ex->getMessage();
    }
    if(!($err == "" && $res)) {
        echo '<p style="color: var(--bad);">Bad login or password...</p>';
        return false;
    }
    $arr = mysqli_fetch_array($res);
    
    if($arr) {
        $_SESSION['name'] = $arr['name'];
        $_SESSION['id'] = $arr['id'];
        return true;
    } else {
        ?> 
            <p style="color: var(--bad);">Bad login or password...</p>
        <?php
        return false;
    }
}

function chengePassword($lastPass, $newPass, $newPass2) {
    $lastPass = trim(htmlspecialchars($lastPass)); 
    $newPass = trim(htmlspecialchars($newPass));
    $newPass2 = trim(htmlspecialchars($newPass2));

    if($newPass != $newPass2) {
        return '<p style="color: var(--bad);">New passwords must compare...</p>';
    }

    $passl = strlen($newPass);
    if(1 > $passl) {
        return '<p style="color: var(--bad);">Password can\'t be not empty!</p>';
    }

    global $link;

    $sel = $link->prepare('SELECT `pass` FROM `users` WHERE `id` = ?;');
    $sel->bind_param('i', $_SESSION['id']);
    $err = "";

    try {
        $sel->execute();
        $res = $sel->get_result(); 
    } catch(mysqli_sql_exception $ex) {
        $err = $ex->getMessage();
    }
    if(!($err == "" && $res)) {
        return '<p style="color: var(--bad);">Something was wrongly. Call administrator...</p>';
    }
    $arr = mysqli_fetch_array($res);
    
    if($arr) {
        $pass = $arr['pass'];
        $lastPass = md5($lastPass);
        $newPass = md5($newPass);
        
        if($pass != $lastPass) {
            return '<p style="color: var(--bad);">Bad last password...</p>';
        }
        if($pass == $newPass) {
            return '<p style="color: var(--bad);">Last and new password must be different</p>';
        }

        $upd = $link->prepare('UPDATE `users` SET pass = ? WHERE id = ?;');
        $upd->bind_param('si', $newPass, $_SESSION['id']);
        $err = "";

        try {
            $upd->execute(); 
        } catch(mysqli_sql_exception $ex) {
            $err = $ex->getMessage();
        }
        if($err != "") {
            return '<p style="color: var(--bad);">Something was wrongly. Password don\'t updated...</p>';
        }

        return '<p style="color: var(--good);">Password sucsses chenged!</p>';
    } else { 
        return '<p style="color: var(--bad);">Something was wrongly. Call administrator...</p>';
    }
}

function chengeEmail($email, $pass) {
    $email = trim(htmlspecialchars($email)); 
    $pass = trim(htmlspecialchars($pass));

    $emaill = strlen($email);

    if(1 > $emaill || $emaill >= 31) {
        return '<p style="color: var(--bad);">Email must be less than 30 simbol and not empty!</p>';
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return '<p style="color: var(--bad);">Uncorrectly email!</p>';
    }

    global $link; 

    $sel = $link->prepare('SELECT `pass` FROM `users` WHERE `id` = ?;');
    $sel->bind_param('i', $_SESSION['id']);
    $err = "";

    try {
        $sel->execute();
        $res = $sel->get_result(); 
    } catch(mysqli_sql_exception $ex) {
        $err = $ex->getMessage();
    }
    if(!($err == "" && $res)) {
        return '<p style="color: var(--bad);">Something was wrongly. Call administrator...</p>';
    }
    $arr = mysqli_fetch_array($res);

    if($arr) {
        $tpass = $arr['pass'];
        $pass = md5($pass);
        if($pass == $tpass) 
        {
            $upd = $link->prepare('UPDATE `users` SET email = ? WHERE id = ?;');
            $upd->bind_param('si', $email, $_SESSION['id']);
            $err = "";

            try {
                $upd->execute(); 
            } catch(mysqli_sql_exception $ex) {
                if($ex->getCode() == 1062) {
                    return '<p style="color: var(--bad);">Email alredy used!</p>';
                } else {
                    $err = $ex->getMessage(); 
                }
            }
            if($err != "") {
                return '<p style="color: var(--bad);">Something was wrongly. Email don\'t updated...</p>';
            }

            return '<p style="color: var(--good);">Email sucsses chenged!</p>';
        } else {
            return '<p style="color: var(--bad);">Bad password... Try again</p>';
        }
    } else { 
        return '<p style="color: var(--bad);">Something was wrongly. Call administrator...</p>';
    }
}

function logout() {
    session_destroy();
}
?>
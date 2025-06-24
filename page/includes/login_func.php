<?php
function register($name, $email, $pas) {
    $name = trim(htmlspecialchars($name));
    $email = trim(htmlspecialchars($email));
    $pas = md5(trim(htmlspecialchars($pas)));

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
                <p style="color: red;">Name or email alredy used!</p>
            <?php
            return false;
        } else {
            echo '<p style="color: red;">Some exception... Code: '.$ex->getCode().'. Pleace tell Administrator!</p>';
            return false;
        }
    }

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
    $pas = md5(trim(htmlspecialchars($pas)));

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
        echo '<p style="color: red;">Bad login or password...</p>';
        return false;
    }
    $arr = mysqli_fetch_array($res);
    
    if($arr) {
        $_SESSION['name'] = $arr['name'];
        $_SESSION['id'] = $arr['id'];
        return true;
    } else {
        ?> 
            <p style="color: red;">Bad login or password...</p>
        <?php
        return false;
    }
}

function logout() {
    session_destroy();
}
?>
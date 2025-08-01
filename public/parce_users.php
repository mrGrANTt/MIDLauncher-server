<?php
    // подключение базы данных
    session_start(); 
    include_once('../page/includes/database.php');
    connect();
    //проверка роли
    if(checkRole('admin')) {
        if(isset($_GET['value'])) {
            //Формирование запроса
            $value = '%'.$_GET['value'].'%';
            global $link;
            $sel = $link->prepare('SELECT `name` FROM `users` WHERE `name` LIKE ? LIMIT 12;');
            $sel->bind_param('s', $value);
            $err = "";
            try {
                $sel->execute();
                $res = $sel->get_result(); 
            } catch(mysqli_sql_exception $ex) {
                $err = $ex->getMessage();
            }
            // генерация таблицы
            if($err == "" && $res) {
                $arr = mysqli_fetch_all($res);
                if($arr) { 
                    foreach($arr as $v) {
                        $str = str_replace($_GET['value'], '<span class="serch_res">'.$_GET['value'].'</span>', $v[0]);
                        echo '<a href="?page=admin&user='.$v[0].'" class="user_link">'.$str.'<br />';
                    }
                    exit;
                }
            }
        }
        echo "No result";
    } else {
        global $mainUrl;
        header("Location: ".$mainUrl);
        exit;
    }
?>
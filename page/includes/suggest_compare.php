<?php 
    function unsver($uns, $id, $chose) {
        $uns = trim($uns);
        $id = trim($id);
        $chose = $chose == 1 ? 1 : 0;

        if(!is_numeric($id)) {
            return '<p class="err">Something was wrongly. Call administrator...</p>';
        }
        if(strlen($uns) > 255) {
            return '<p class="err">Unsver must be less then 255 simbol!</p>';
        }
        if(!preg_match('/^[-_ A-Za-z0-9.,!?]{1,}$/', $uns)) {
            return '<p class="err">Description must contain only a-z, A-Z, 0-9 or "!?.,_-"!</p>';
        }

        global $link;
        $upd = $link->prepare('UPDATE `suggest` SET `unsver` = ?, `accept` = ? WHERE `id` = ? AND `accept` IS NULL;');
        $upd->bind_param('sii', $uns, $chose, $id);
        $err = "";

        try {
            $upd->execute();
        } catch(mysqli_sql_exception $ex) {
            $err = $ex->getMessage();
        }
        if($err != "") {
            return '<p class="err">Something was wrongly. Call administrator...</p>';
        }
        
        if($upd->affected_rows > 0) {
            return true;
        } else { 
            return '<p class="err">Nothing changed. Try reload this page...</p>';
        }
    }


    function compare($name, $desc, $url) {
        $name = trim($name);
        $desc = trim($desc); 
        $url = trim($url);
        $date = date('Y-m-d');        
        
        $inputErr = [];

        if(strlen($name) > 30) $inputErr[] .= '<p class="err">Name must be less then 30 simbol</p>';
        elseif(!preg_match('/^[-_ A-Za-z0-9]{3,}$/', $name)) $inputErr[] .= '<p class="err">Name must contain only a-z, A-Z, 0-9, "-" or "_"!</p>';
        else $inputErr[] .= '-';

        if(strlen($desc) > 255) $inputErr[] .= '<p class="err">Description must be less then 255 simbol!</p>';
        elseif(!preg_match('/^[-_ A-Za-z0-9.,!?]{0,}$/', $desc)) $inputErr[] .= '<p class="err">Description must contain only a-z, A-Z, 0-9 or "!?.,_-"!</p>';
        else $inputErr[] .= '-';

        if(strlen($url) > 255) $inputErr[] .= '<p class="err">Original url must be less then 255 simbol!</p>';
        elseif(!filter_var($url, FILTER_VALIDATE_URL)) $inputErr[] .= '<p class="err">It\'s not url addres!</p>';
        else $inputErr[] .= '-';

        foreach($inputErr as $v) {
            if($v != '-') {
                return $inputErr;
            }
        }
        
        global $link;
        $ins = $link->prepare('INSERT INTO `suggest` (`sender_id`, `name`, `url`, `description`, `date`, `unsver`, `accept`) VALUES (?, ?, ?, ?, ?, NULL, NULL);');
        $sel = $link->prepare('SELECT `id` FROM `suggest` WHERE `sender_id` = ? AND `name` = ? AND `url` = ?');
        $ins->bind_param('issss', $_SESSION['id'], $name, $url, $desc, $date);
        $sel->bind_param('iss', $_SESSION['id'], $name, $url);
        $err = "";

        try {
            $ins->execute();
            $sel->execute();
            $res = $sel->get_result(); 
        } catch(mysqli_sql_exception $ex) {
            $err = $ex->getMessage();
        }
        if(!($err == "" && $res)) {
            echo '<p class="err">Somthing wrongly in sql...</p>';
            return false;
        }
        $arr = mysqli_fetch_array($res);
        
        if(!$arr) {
            echo '<p class="err">Somthing wrongly in sql result...</p>';
            return false;
        }
        $id = $arr['id'];
        return $id;
    }
?>
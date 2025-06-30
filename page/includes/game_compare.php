<?php 
    function compare($name, $autor, $entry, $desc, $url) {
        $date = date('d.m.Y');

        $game = $_FILES['game']['tmp_name'];

        $inputErr = [];

        if(strlen($name) > 30) $inputErr[] .= '<p class="err">Name must be less then 30 simbol</p>';
        elseif(!preg_match('/^[-_ A-Za-z0-9]{3,}$/', $name)) $inputErr[] .= '<p class="err">Name must contain only a-z, A-Z, 0-9, "-" or "_"!</p>';
        else $inputErr[] .= '-';

        if(!preg_match('/^[-_ A-Za-z0-9]{3,}$/', $autor)) $inputErr[] .= '<p class="err">Author name must contain only a-z, A-Z, 0-9, "-" or "_"!</p>';
        else $inputErr[] .= '-';

        if(!preg_match('/^\.\/.*\.(exe|bat|bin)$/', $entry)) $inputErr[] .= '<p class="err">Entry must start from "./" and be runnable file!</p>';
        else $inputErr[] .= '-';

        if(strlen($desc) > 255) $inputErr[] .= '<p class="err">Description must be less then 255 simbol!</p>';
        elseif(!preg_match('/^[-_ A-Za-z0-9.,!?]{0,}$/', $desc)) $inputErr[] .= '<p class="err">Description must contain only a-z, A-Z, 0-9 or "!?.,_-"!</p>';
        else $inputErr[] .= '-';

        if(strlen($url) > 255) $inputErr[] .= '<p class="err">Original url must be less then 255 simbol!</p>';
        elseif(!filter_var($url, FILTER_VALIDATE_URL)) $inputErr[] .= '<p class="err">It\'s not url addres!</p>';
        else $inputErr[] .= '-';

        $inputErr[] .= '-';//game
        
        if(!isset($_FILES['icon']['tmp_name']) || $_FILES['icon']['type'] != 'image/png') $inputErr[] .= '<p class="err">Icon must be .png type!</p>';
        else $inputErr[] .= '-';

        foreach($inputErr as $v) {
            if($v != '-') {
                return $inputErr;
            }
        }
        
        global $link;
        $ins = $link->prepare('INSERT INTO `games`(`sender_id`, `name`, `date`, `description`, `url`) VALUE (?, ?, ?, ?, ?)');
        $sel = $link->prepare('SELECT `id` FROM `games` WHERE `sender_id` = ? AND `name` = ? AND `url` = ?');
        $ins->bind_param('issss', $_SESSION['id'], $name, $date, $desc, $url);
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
            echo '<p style="color: var(--bad);">Somthing wrongly in sql...</p>';
            return false;
        }
        $arr = mysqli_fetch_array($res);
        
        if(!$arr) {
            echo '<p style="color: var(--bad);">Somthing wrongly in sql result...</p>';
            return false;
        }
        $id = $arr['id'];
        
        $json = '
{
    "name": "'.$name.'",
    "description": "'.$desc.'",
    "path": "'.$entry.'",
    "autor": "'.$autor.'",
    "url": "'.$url.'"
}
        ';

        if(!is_dir('download/'.$id)) mkdir('download/'.$id, 0777, true);
        $jsonFile = fopen('download/'.$id.'/data.json', 'w');
        if(!$jsonFile) {
            echo '<p style="color: var(--bad);">Can\'t open file...</p>';
            return false;
        }
        fwrite($jsonFile, $json);
        fclose($jsonFile);

        move_uploaded_file($_FILES['icon']['tmp_name'], "download/".$id."/icon.png");

        //add zip

        return $id;
    }
?>

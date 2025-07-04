<?php 
    function compare($name, $autor, $entry, $desc, $url) {
        $name = trim(htmlspecialchars($name));
        $autor = trim(htmlspecialchars($autor)); 
        $entry = trim(htmlspecialchars($entry)); 
        $desc = trim(htmlspecialchars($desc)); 
        $url = trim(htmlspecialchars($url));
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

        if(!preg_match('/^application\/(x-|)zip(-compressed|)$/', $_FILES['game']['type'])) $inputErr[] .= '<p class="err">You must select .zip file with game</p>';
        else $inputErr[] .= '-';

        if($_FILES['icon']['type'] != 'image/png') $inputErr[] .= '<p class="err">Icon must be .png type!</p>';
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
        
        $json = '{'."\n".
        '   "name": "'.$name.'",'."\n".
        '   "description": "'.$desc.'",'."\n".
        '   "path": "./'.$name.'/game'.substr($entry, 1).'",'."\n".
        '   "autor": "'.$autor.'",'."\n".
        '   "url": "'.$url.'"'."\n".
        '}'."\n".'';

        $zip = new ZipArchive();

        if ($zip->open('download/'.$id.'.zip', ZipArchive::CREATE)!==TRUE) {
            exit('<p style="color: var(--bad);">Can\'t open zip file...</p>');
        }

        $zip->addFromString($name.'/data.json', $json);
        $zip->addFile($_FILES['icon']['tmp_name'], $name.'/icon.png');
        
        $gameZip = new ZipArchive();

        if ($gameZip->open($_FILES['game']['tmp_name'], ZipArchive::CREATE)!==TRUE) {
            exit('<p style="color: var(--bad);">Can\'t open zip file...</p>');
        }

        for ($i=0; $i<$gameZip->numFiles; $i++) {
            $info = $gameZip->statIndex($i);
            $fname = $info['name'];

            $content = $gameZip->getFromIndex($i);

            $zip->addFromString($name.'/game/'.$fname, $content);
        }

        $gameZip->close();
        $zip->close();
         
        return $id;
    }
?>

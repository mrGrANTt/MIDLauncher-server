<?php
    $onePageCount = 7;
    $curentPage = 0;
    $maxPageCount = 0;

    
    $type = isset($_GET['page']) ? 
        ($_GET['page'] == 'suggest' ? 'suggest' : 'games')
         : 'games'; // (?page=games | ?page=suggest | ?page=suggest&closed) + count=<num> + value=<serch>


    function genWHERE($type) {
        $value = $type.(isset($_GET['value']) ? '1' : '0');

        $res = '';
        switch ($value) {
            case 'suggest1': $res = 'WHERE `unsver` IS '.(isset($_GET['closed']) ? 'NOT' : '').' NULL AND `name` LIKE ?'; break;
            case 'suggest0': $res = 'WHERE `unsver` IS '.(isset($_GET['closed']) ? 'NOT' : '').' NULL'; break;
            case 'games1': $res = 'WHERE `name` LIKE ?'; break;
        }
        return $res;
    }

    session_start(); 
    include_once('../page/includes/database.php');
    connect();

    if(checkRole(['admin', 'moderator'])) {
        if(isset($_GET['value'])) $value = '%'.$_GET['value'].'%';

        global $link;

        $count = 'SELECT COUNT(*) FROM `'.$type.'` '.genWHERE($type).';';
        echo "'$count'</br>";
        $count = $link->prepare($count);

        if(isset($_GET['value'])) $count->bind_param('s', $value);
        $err = "";

        if(isset($_GET['count']) && is_numeric($_GET['count'])) {
            $curentPage = $_GET['count'];
        }

        try {
            $count->execute();
            $res = $count->get_result(); 
        } catch(mysqli_sql_exception $ex) {
            $err = $ex->getMessage();
        }
        if(!($err == "" && $res)) {
            echo $err.'<br />';
            exit;
        }

        $arr = mysqli_fetch_array($res);
        if($arr) {
            $maxPageCount = ceil($arr[0] / $onePageCount);
        }

        $curentPage = max(min($curentPage, $maxPageCount - 1), 0);
        $num = $curentPage * $onePageCount;

        $sel = 'SELECT `date`, `id`, `name` FROM `'.$type.'` '.genWHERE($type).' ORDER BY `date` DESC, `name` LIMIT ?, ?;';
        echo "'$sel'</br>";
        $sel = $link->prepare($sel);

        if(isset($_GET['value'])) $sel->bind_param('sii', $value, $num, $onePageCount);
        else $sel->bind_param('ii', $num, $onePageCount);

        $err = "";
        
        try {
            $sel->execute();
            $res = $sel->get_result();
        } catch(mysqli_sql_exception $ex) {
            $err = $ex->getMessage();
        }
        if(!($err == "" && $res)) {
            echo $err.'<br />';
            exit;
        }

        $arr = mysqli_fetch_all($res);

        if($arr) {
            ?>
                <table class="info table">
                    <tr class="mainTR"> <td class="tabletTitle">Date</td> <td class="tabletTitle">Game</td> </tr>
                    
                    <?php 
                    foreach($arr as $v) {
                        echo '
                        <tr class="tabletTR"> 
                            <td class="tabletValue">
                                '.$v[0].'
                            </td> 
                            <td class="tabletValue">
                                <a href="?page='.$type.'&id='.$v[1].'">
                                    '.$v[2].'
                                </a>
                            </td> 
                        </tr>';
                    }
                    ?>
                </table>
            <?php 
        } else { echo '<p class="noresult">No result...'; }

        $variants = [$curentPage - 2, $curentPage - 1, $curentPage, $curentPage + 1, $curentPage + 2];
        switch ($curentPage) {
        }

        foreach($variants as $var) {
            if($var < $maxPageCount && $var > -1) {
                echo '<a class="page_switch '.($var == $curentPage ? 'select' : '').'" '.($var == $curentPage ? '' : 'onclick="loadFn('.$var.')"').'>'.($var + 1).'</a>';
            }
        }

    } else {
        global $mainUrl;
        header("Location: ".$mainUrl);
        exit;
    }
?>
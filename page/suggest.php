<main class="suggest">
<?php
    function resultCheck($res, $index) {
        return ($res != '' && isset($res[$index]) && $res[$index] != '-') ? $res[$index] : '';
    }
    function tryToValue($param) {
        return (isset($_POST[$param])) ? ' value="'.$_POST[$param].'" ' : '';
    }


    if(checkRole(['admin', 'moderator'])) {
        ?>
                <?php
                    if(isset($_GET['id']) && is_numeric($_GET['id'])) {

                        $unsverRes = false;
                        if(isset($_POST['accept']) || isset($_POST['reject'])) {
                            include_once('page/includes/suggest_compare.php');
                            $unsverRes = unsver($_POST['unsver'], $_POST['id'], isset($_POST['accept']) ? 1 : 0);
                        }

                        global $link;
                        $sel = $link->prepare('SELECT `suggest`.`id`, `suggest`.`name`, `suggest`.`url`, `suggest`.`description`, `suggest`.`accept`, `suggest`.`unsver`, `users`.`name` as `sender_name` FROM `suggest` INNER JOIN `users` ON `suggest`.`sender_id` = `users`.`id` WHERE `suggest`.`id` = ?;');
                        $sel->bind_param('i', $_GET['id']);
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

                        $arr = mysqli_fetch_array($res);
                        
                        if($arr) {
                            $id = $arr['id'];
                            $name = htmlspecialchars(trim($arr['name']));
                            $url = htmlspecialchars(trim($arr['url']));
                            $description = htmlspecialchars(trim($arr['description']));
                            $accept = $arr['accept'];
                            $unsver = htmlspecialchars(trim($arr['unsver']));
                            $senderName = htmlspecialchars(trim($arr['sender_name']));

                            if ($unsverRes === true && isset($_POST['accept'])) {
                                ?>
                                    <form id="form" action="?page=games&newgame" method="post">
                                        <?php
                                            echo '
                                                <input type="hidden" name="name" value="'.$name.'">
                                                <input type="hidden" name="description" value="'.$description.'">
                                                <input type="hidden" name="original_url" value="'.$url.'">
                                            ';
                                        ?>
                                    </form>
                                    <script type="text/javascript">
                                        document.getElementById('form').submit();
                                    </script>
                                <?php
                                exit;
                            }
                        
                            echo '
                                <h4 class="tablet-title">'.$name.'</h4>
                                <div class="content">
                                    <form method="POST" class="unsver-form">
                                        <p class="description" >'.$description.'</p>
                                        <p class="url" >Url: <a href="'.$url.'">'.$url.'</a></p>
                                        <p class="senderbox" > Sender: <a class="sender" href="?page=admin&user='.$senderName.'">'.$senderName.'</a></p>
                                        <input name="id" hidden value="'.$id.'" />
                                        <textarea placeholder="Write a response to the suggest" class="unsver '.(is_null($accept) ? '" name="unsver">' : ($accept == 1 ? 'accepted' : 'rejected').'" name="unsver" readonly>'.$unsver).'</textarea>
                                        '.(is_bool($unsverRes) ? '' : $unsverRes).'
                                        '.(is_null($accept) ? '
                                            <button type="sybmit" class="accept" name="accept">Accept</button>
                                            <button type="sybmit" class="reject" name="reject">Reject</button>
                                        ' : '').'
                                        <a class="back" href="?page=suggest'.(isset($_GET['closed'])? '&closed' : '').'">← Back to list</a>
                                    </form>
                                </div>';
                                
                                echo '<div class="space"></div>
                            ';
                        } else {
                            echo '
                                <h4 class="tablet-title">Suggest undefined...</h4>
                                <a class="back" href="?page=suggest'.(isset($_GET['closed'])? '&closed' : '').'">← Back to list</a>
                            ';
                        }
                    } elseif (isset($_GET['closed'])) {
                        ?>
                            <div class="suggest-type">
                                <a class="page_type unselect">Closed</a>
                                <a href="?page=suggest" class="page_type">Suggest</a>
                            </div>
                            <h4 class="tablet-title" id="title">Closed Suggests</h4>
                            <div id="result" id="result"></div>

                            <script>
                                const xhttp = new XMLHttpRequest();

                                function loadFn(num) {
                                    xhttp.onload = function() {
                                        document.getElementById("result").innerHTML = this.responseText;
                                    }
                                    xhttp.open("GET", "public\\page_list_gen.php?page=suggest&closed&count=" + num, true);
                                    xhttp.send();
                                }
                                loadFn(0);
                    
                                document.getElementById('result').addEventListener('wheel', (ev) => {
                                    let el = document.getElementById('page_input'); 
                                    let deleta = (ev.deltaY > 0 ? 1 : -1);
                                    let newValue = Number(el.value) + deleta;
                                    newValue = newValue > 0 ? newValue : 1;

                                    if (el.value != newValue) {
                                        el.value = newValue;
                                        loadFn(el.value -1);
                                    }
                                });
                            </script>
                            <input id="page_input" type="number" min="1" value="1" oninput="loadFn(document.getElementById('page_input').value - 1)" />
                        <?php
                    } else {
                        ?>
                            <div class="suggest-type">
                                <a href="?page=suggest&amp;closed" class="page_type">Closed</a>
                                <a class="page_type unselect">Suggest</a>
                            </div>
                            <h4 class="tablet-title" id="title">Suggests</h4>
                            <div id="result"></div>

                            <script>
                                const x_http = new XMLHttpRequest();

                                function loadFn(num) {
                                    x_http.onload = function() {
                                        document.getElementById("result").innerHTML = this.responseText;
                                    }
                                    x_http.open("GET", "public\\page_list_gen.php?page=suggest&count=" + num, true);
                                    x_http.send();
                                }
                                loadFn(0);
                    
                                document.getElementById('result').addEventListener('wheel', (ev) => {
                                    let el = document.getElementById('page_input'); 
                                    let deleta = (ev.deltaY > 0 ? 1 : -1);
                                    let newValue = Number(el.value) + deleta;
                                    newValue = newValue > 0 ? newValue : 1;

                                    if (el.value != newValue) {
                                        el.value = newValue;
                                        loadFn(el.value -1);
                                    }
                                });
                            </script>
                            <input id="page_input" type="number" min="1" value="1" oninput="loadFn(document.getElementById('page_input').value - 1)" />
                        <?php 
                    }
                ?>
        <?php
    } elseif(isset($_SESSION['name'])) { 
        if(isset($_GET['id']) && is_numeric($_GET['id'])) {

            global $link;
            $sel = $link->prepare('SELECT `suggest`.`id`, `suggest`.`name`, `suggest`.`url`, `suggest`.`description`, `suggest`.`accept`, `suggest`.`unsver`, `users`.`name` as `sender_name` FROM `suggest` INNER JOIN `users` ON `suggest`.`sender_id` = `users`.`id` WHERE `suggest`.`id` = ?;');
            $sel->bind_param('i', $_GET['id']);
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

            $arr = mysqli_fetch_array($res);

            if($arr && $arr['sender_name'] == $_SESSION['name']) {


                $id = $arr['id'];
                $name = htmlspecialchars(trim($arr['name']));
                $url = htmlspecialchars(trim($arr['url']));
                $description = htmlspecialchars(trim($arr['description']));
                $accept = $arr['accept'];
                $unsver = htmlspecialchars(trim($arr['unsver']));
                $senderName = htmlspecialchars(trim($arr['sender_name']));

                echo '
                    <h4 class="tablet-title">'.$name.'</h4>
                    <div class="content">
                        <p class="description" >'.$description.'</p>
                        <p class="url" >Url: <a href="'.$url.'">'.$url.'</a></p>
                        <p class="senderbox" > Sender: <span class="sender" >'.$senderName.'</span></p>
                        <input name="id" hidden value="'.$id.'" />
                        <p class="unsverall" >Unsver: <textarea class="unsver '.(is_null($accept) ? 
                            '" placeholder="Pleace wait for a response" name="unsver" readonly>' :
                            ($accept == 1 ? 'accepted' : 'rejected').'" name="unsver" readonly>'.$unsver).'</textarea>
                        </p>
                        <a class="back" href="?page=account">← Back to account</a>
                    </div>';
                    
                    echo '<div class="space"></div>
                ';
            } else {
                echo '
                    <h4 class="tablet-title">Suggest undefined...</h4>
                    <a class="back" href="?page=account">← Back to account</a>
                ';
            }
        } else {
            ?> 
                <h4 class="tablet-title">Create Suggest</h4>
                    <?php
                        $result = '';
                        if(isset($_POST['newsug'])) {
                            include_once('page/includes/suggest_compare.php');
                            $result = compare($_POST['name'], $_POST['description'], $_POST['original_url']);
                            if($result !== false && is_numeric($result)) {
                                ?>
                                    <script>
                                        window.location="<?php global $mainUrl; echo $mainUrl.'?page=suggest&id='.$result; ?>";
                                    </script>
                                <?php
                            }
                        }

                        echo '
                            <form method="POST" class="content-user-form">
                                <input type="text" name="name" class="sug_input sug_name" placeholder="Game name" '.tryToValue('name').'/>
                                '.resultCheck($result, 0).'
                                <textarea maxlength="255" name="description" class="sug_input sug_description" placeholder="Some description of game">'.(isset($_POST['description']) ? $_POST['description'] : '').'</textarea>
                                '.resultCheck($result, 1).'
                                <input type="url" name="original_url" class="sug_input sug_original_url" placeholder="Url to original game" '.tryToValue('original_url').' />
                                '.resultCheck($result, 2).'
                                <button type="submit" name="newsug" id="sug_submit">Send</button>
                            </form>';
                    ?>
                    
                    <div class="space"></div>
            <?php
        }
    } else { 
        global $mainUrl;
        header("Location: ".$mainUrl."?page=login");
        exit;
    }
?>
</main>
<style>
    main.suggest {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 120px 20px 40px;
        gap: 10px;
        color: rgb(var(--text));
        position: relative;
    }

    #result {
        width: 100%;
        max-width: 400px;
        padding: 20px;
        border-radius: 10px;
    }

    .tablet-title {
        margin: 20px 0 10px;
        text-align: center;
        color: rgb(var(--text));
        font-size: 24px;
    }

    a {
        color: rgb(var(--accent));
        text-decoration: none;
        position: relative;
        margin-right: 2px;
    }

    a:hover {
        cursor: pointer;
        text-decoration: underline;
    }

    a.back {
        color: rgb(var(--mini-text));
        text-decoration: none;
        font-size: 14px;
        margin-top: 10px;
    }

    a.back:hover {
        text-decoration: underline;
    }
    
    .content, .content-user-form {
        background-color: rgb(var(--panel-bg));
        padding: 25px 30px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
        display: flex;
        flex-direction: column;
        gap: 16px;
        width: 100%;
        max-width: 600px;
    }

    .tablet-title {
        margin: 20px 0 10px;
        text-align: center;
        color: rgb(var(--text));
        font-size: 24px;
    }
    
    .sug_input, .unsver {
        background-color: rgb(var(--main-bg));
        color: rgb(var(--text));
        border: none;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 14px;
        outline: none;
        transition: box-shadow 0.2s;
        width: 100%;
        box-sizing: border-box;
    }

    .sug_input:focus, .unsver:focus {
        box-shadow: 0 0 0 2px rgb(var(--accent));
    }

    .sug_description {
        resize: vertical;
    }

    .table {
        width: 100%;
        max-width: 600px;
        border-collapse: collapse;
        background-color: rgb(var(--panel-bg));
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0,0,0,0.3);
    }

    #sug_submit, .accept, .reject {
        background-color: rgb(var(--accent));
        color: rgb(var(--text));
        border: none;
        border-radius: 8px;
        padding: 10px 16px;
        font-weight: 500;
        transition: background-color 0.2s, transform 0.2s;
        cursor: pointer;
        max-width: 200px;
    }

    .accept {
        background-color: rgb(var(--good));
    } 

    .reject {
        background-color: rgb(var(--bad));
    }

    #sug_submit:hover, .accept:hover, .reject:hover {
        transform: scale(1.03);
        background-color: rgb(var(--accent-hover));
    }

    .accept:hover {
        background-color: rgb(var(--good-hover));
    } 

    .reject:hover {
        background-color: rgb(var(--bad-hover));
    }

    .unsver.accepted {
        border-left: 4px solid rgb(var(--good));
        background-color: rgba(var(--good), 0.1);
    }

    .unsver.rejected {
        border-left: 4px solid rgb(var(--bad));
        background-color: rgba(var(--bad), 0.1);
    }

    .mainTR {
        background-color: rgb(var(--main-bg));
    }

    .tabletTitle, .tabletValue {
        padding: 12px 16px;
        text-align: left;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .tabletTR:last-child td {
        border-bottom: none;
    }

    #page_input {
        position: relative;
        background-color: rgb(var(--main-bg));
        color: white;
        padding: 8px 14px;
        border: none;
        border-radius: 8px;
        outline: none;
        box-shadow: 0 0 0 2px transparent;
        transition: box-shadow 0.2s;
        max-width: 80px;
    }

    #page_input {
        position: relative;
        box-shadow: 0 0 0 2px rgb(var(--accent));
    }                
    
    .select {
        color: rgb(var(--text));
    }

    .select:hover {
        text-decoration: none;
        cursor:default;
    }

    .suggest-type {
        display: flex;
        flex-direction:row;
        gap: 10px;
    }

    .page_type {
        padding: 10px;
        border-radius: 12px;
        background-color: rgb(var(--accent));
        color: rgb(var(--text));
        transition: background-color 0.2s, transform 0.2s;
    }

    .page_type:hover {
        background-color: rgb(var(--accent-hover));
        transform: scale(1.03);
        text-decoration: none;
    }

    .url, .description, .senderbox {
        color: rgb(var(--text));
        font-size: 14px;
        word-break: break-all;
        margin: 0;
    }

    .description {
        border-bottom: 4px solid rgb(var(--hover-bg));
        padding-bottom: 15px;
    }

    .unsver-form {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .unsver[readonly] {
        resize: none;
    }

    textarea.unsver {
        border-left: 4px solid rgb(var(--main-bg));
        background-color: rgba(0, 0, 0, 0.1);
        min-height: 80px;
        resize: vertical;
    }

    .sender {
        color: rgb(var(--accent));
        text-decoration: none;
    }

    .unselect {
        background-color: rgb(var(--panel-bg));
        opacity: 0.5;
    }

    .unselect:hover {
        background-color: rgb(var(--panel-bg));
        transform: scale(1);
        cursor: default;
    }

    .err {
        color: rgb(var(--bad));
        font-size: 13px;
    }
                
    .space {
        padding-bottom: 120px;
    }

</style>
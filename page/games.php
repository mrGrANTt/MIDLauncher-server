<?php 
    function resultCheck($res, $index) {
        return ($res != '' && isset($res[$index]) && $res[$index] != '-') ? $res[$index] : '';
    }
    function tryToValue($param) {
        return (isset($_POST[$param])) ? ' value="'.$_POST[$param].'" ' : '';
    }


    if(checkRole(['admin', 'moderator'])) {
        echo '<main class="games">';
        if(isset($_GET['newgame'])) {
            $result = '';
            if(isset($_POST['newgame'])) {
                $name = trim(htmlspecialchars($_POST['name']));
                $autor = trim(htmlspecialchars($_POST['author']));
                $entry = trim(htmlspecialchars($_POST['entry']));
                $desc = trim(htmlspecialchars($_POST['description']));
                $url = trim(htmlspecialchars($_POST['original_url']));
                
                include_once('page/includes/game_compare.php');
                $result = compare($name, $autor, $entry, $desc, $url);
                if($result !== false && is_numeric($result)) {
                    ?>
                        <script>
                            window.location="<?php global $mainUrl; echo $mainUrl.'?page=games&id='.$result; ?>";
                        </script>
                    <?php
                }
            }

            echo '
                <form method="POST" enctype="multipart/form-data">
                    <div class="content" id="game_conteiner">
                        <div class="colorTitle">
                            <input type="text" name="name" class="game_input game_name" placeholder="Game name" '.tryToValue('name').'/>
                            <input type="color" name="color" id="color" class="game_input game_color" placeholder="" '.tryToValue('color').'/>
                        </div>
                        '.resultCheck($result, 0).'
                        <input type="text" name="author" class="game_input game_author" placeholder="Author" '.tryToValue('author').' />
                        '.resultCheck($result, 1).'
                        <input type="text" name="entry" class="game_input game_entry" placeholder="Relative path to entry file" '.tryToValue('entry').' />
                        '.resultCheck($result, 2).'
                        <textarea maxlength="255" name="description" class="game_input game_description" placeholder="Some description of game">'.(isset($_POST['description']) ? $_POST['description'] : '').'</textarea>
                        '.resultCheck($result, 3).'
                        <input type="url" name="original_url" class="game_input game_original_url" placeholder="Url to original game" '.tryToValue('original_url').' />
                        '.resultCheck($result, 4).'
                        <span>Game\'s files(<a href="?page=guide&guide=uploadgame">More info</a>):</span>
                        <input type="file" name="game" class="game_input game_icon" />
                        '.resultCheck($result, 5).'
                        <span>Game\'s icon(4x3 image)</span>
                        <input type="file" name="icon" class="game_input game_icon" />
                        '.resultCheck($result, 6).'
                    </div>
                    <button type="submit" name="newgame" id="game_submit">Save</button>
                </form>

                <script>
                    let border = document.getElementById(\'game_conteiner\');
                    let color = document.getElementById(\'color\');

                    color.onchange = (ev) => {border.style.border = \'2px solid \' + color.value};
                    border.style.border = \'2px solid \' + color.value;
                </script>
            ';
            echo '<div class="space" />';

        } elseif(isset($_GET['id']) && is_numeric($_GET['id'])) {
            if (isset($_GET['remove'])) {
                global $link;

                $del = $link->prepare('DELETE FROM `games` WHERE `id` = ?;');
                $del->bind_param('i', $_GET['id']);
                $err = "";

                try {
                    $del->execute();
                } catch(mysqli_sql_exception $ex) {
                    $err = $ex->getMessage();
                }
                if(!$err == "") {
                    echo $err.'<br />';
                    exit;
                } else {
                    $filename = 'download/'.$_GET['id'].'.zip'; //форматы сжатия?
                    if(file_exists($filename)) {
                        unlink($filename);
                    }

                    ?>
                        <script>
                            window.location="<?php global $mainUrl; echo $mainUrl.'?page=games'; ?>";
                        </script>
                        </main>
                    <?php
                }
            } else {

                global $link;
                $sel = $link->prepare('SELECT `games`.`id`, `games`.`name`, `games`.`description`, `games`.`url`, `users`.`name` as `sender_name` FROM `games` INNER JOIN `users` ON `games`.`sender_id` = `users`.`id` WHERE `games`.`id` = ?;');
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

                    echo '
                        <h4 class="tablet-title">'.$arr['name'].'</h4>
                        <div class="content">
                            <table class="contentTable">
                                <tr>
                                    <td>
                                        <p class="description" >'.$arr['description'].'</p>
                                        <p class="senderbox" > Sender: <a class="sender" href="?page=admin&user='.$arr['sender_name'].'">'.$arr['sender_name'].'</a></p>
                                    </td>
                                    <td class="buttonsMenu">
                                        <a class="btn original" href="'.$arr['url'].'" >Original game page</a>
                                        <a class="btn download" href="?page=download&id='.$arr['id'].'">Download game</a>
                                        <a class="btn remove" id="openacc">Remove</a>
                                    </td>
                                </tr>
                            </table>
                            <a class="back" href="?page=games">← Back to list</a>
                        </div>';
                        
                        echo '<div class="space" />';
                    ?>
                        <div id="remove_menu">
                            <div class="menu_elements">
                                <p class="tablet-title">You realy want remove game?</p>
                                <a id="yes" class="btn remove" href="<?php echo '?page=games&id='.$arr['id'].'&remove';?>">Remove</a>
                                <a id="no" class="btn">Cancel</a>
                            </div>
                        </div>
                        <script>
                            let open = document.getElementById('openacc');
                            let close = document.getElementById('no');
                            let menu = document.getElementById('remove_menu')

                            open.onclick = () => {openMenu(true)};
                            close.onclick = () => {openMenu(false)};

                            function openMenu(value) {
                                console.log(menu.style.display + '  ' + value);
                                menu.style.display = value == true ? '' : 'none';
                            }
                            openMenu(false);
                        </script>
                    <?php
                } else {
                    echo '
                        <h4 class="tablet-title">Game undefined...</h4>
                        <a class="back" href="?page=games">← Back to list</a>
                    ';
                }
            }
        } else {
            ?>
                <a href="?page=games&newgame" class="create_new">Create new game</a>
                <h4 class="tablet-title">Games</h4>
                <div id="result"></div>

                <script>
                    const xhttp = new XMLHttpRequest();

                    function loadFn(num) {
                        xhttp.onload = function() {
                            document.getElementById("result").innerHTML = this.responseText;
                        }
                        xhttp.open("GET", "public\\page_list_gen.php?page=game&count=" + num, true);
                        xhttp.send();
                    }
                    loadFn(0);
                    
                    document.getElementById('result').addEventListener('wheel', (ev) => {
                        let el = document.getElementById('page_input'); 
                        let deleta = (ev.deltaY > 0 ? 1 : -1);
                        let newValue = Number(el.value) + deleta;
                        el.value = newValue > 0 ? newValue : 1;
                        loadFn(el.value -1);
                    });
                </script>
                <input id="page_input" type="number" min="1" value="1" oninput="loadFn(document.getElementById('page_input').value - 1)" />
            <?php
        }
        ?>
            </main>
            <style>
                main.games {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 120px 20px 40px;
                    gap: 10px;
                    color: var(--text);
                }

                #result {
                    width: 100%;
                    max-width: 400px;
                    padding: 20px;
                    border-radius: 10px;
                    z-index: 3;
                }

                .tablet-title {
                    margin: 20px 0 10px;
                    text-align: center;
                    color: var(--text);
                    font-size: 24px;
                }

                .content, .menu_elements {
                    background-color: var(--panel-bg);
                    padding: 25px 30px;
                    border-radius: 12px;
                    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
                    display: flex;
                    flex-direction: column;
                    gap: 16px;
                    width: 100%;
                    max-width: 600px;
                    z-index: 3;
                }

                a {
                    color: var(--accent);
                    text-decoration: none;
                    position: relative;
                    z-index: 3;
                    margin-right: 2px;
                }

                a:hover {
                    cursor: pointer;
                    text-decoration: underline;
                }

                .senderbox {
                    margin: 0;
                    color: var(--mini-text);
                    font-size: 14px;
                    display: inline-block;
                    width: fit-content;
                }

                .content .sender {
                    color: var(--accent);
                    text-decoration: none;
                }

                .content .sender:hover {
                    text-decoration: underline;
                }

                .btn, .back {
                    display: inline-block;
                    text-decoration: none;
                    padding: 10px 16px;
                    border-radius: 8px;
                    font-weight: 500;
                    transition: background-color 0.2s, transform 0.2s;
                    margin-right: 10px;
                    margin-top: 10px;
                    z-index: 3;
                }

                .btn {
                    background-color: var(--hover-bg);
                    color: var(--text);
                    text-align: center;
                }

                .buttonsMenu {
                    text-align: right;
                    max-width: 100px;
                    min-width: 40px;
                }

                .back {
                    background-color: transparent;
                    color: var(--mini-text);
                    padding: 10px 0;
                    width: 100px;
                }

                .btn:hover {
                    transform: scale(1.03);
                    background-color: var(--accent-hover);
                    text-decoration: none;
                }

                .back:hover {
                    text-decoration: underline;
                }

                .table {
                    width: 100%;
                    max-width: 600px;
                    border-collapse: collapse;
                    background-color: var(--panel-bg);
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 4px 16px rgba(0,0,0,0.3);
                    z-index: 3;
                }

                .mainTR {
                    background-color: #1e1e2f;
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
                    z-index: 3;
                    background-color: var(--main-bg);
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
                    z-index: 3;
                    box-shadow: 0 0 0 2px var(--accent);
                }

                .description {
                    color: var(--text);
                    line-height: 1.6;
                    margin: 0;
                }

                .select {
                    color: var(--text);
                }

                .select:hover {
                    text-decoration: none;
                    cursor:default;
                }

                .create_new {
                    padding: 10px;
                    border-radius: 12px;
                    background-color: var(--accent);
                    color: var(--text);
                    transition: background-color 0.2s, transform 0.2s;
                }

                .create_new:hover {
                    background-color: var(--accent-hover);
                    transform: scale(1.03);
                    text-decoration: none;
                }

                .remove {
                    background-color: var(--bad);
                }

                .remove:hover {
                    background-color: var(--bad-hover);
                }

                #remove_menu {
                    position: absolute;
                    left: 0;
                    right: 0;
                    top: 0;
                    bottom: 0;

                    background-color: rgba(0,0,0,0.3);
                    z-index: 4;

                    display:flex;
                    justify-content: center;
                    flex-direction: column;
                    align-items: center;

                }

                .menu_elements {
                    max-width: 400px;
                }
                
                #game_conteiner {
                    position: relative;
                    z-index: 3;
                    border-radius: 12px;
                    overflow: hidden;
                    transition: border 0.3s;
                }

                .colorTitle {
                    display: flex;
                    gap: 12px;
                    align-items: center;
                }

                .game_input {
                    box-sizing: border-box;
                    background-color: var(--main-bg);
                    color: var(--text);
                    border: none;
                    border-radius: 8px;
                    padding: 10px 14px;
                    outline: none;
                    font-size: 14px;
                    box-shadow: 0 0 0 2px transparent;
                    transition: box-shadow 0.2s, transform 0.2s;
                    width: 100%;
                }

                .game_input:focus {
                    box-shadow: 0 0 0 2px var(--accent);
                    transform: scale(1.02);
                }

                .game_color {
                    width: 50px;
                    padding: 0;
                    height: 40px;
                    border-radius: 8px;
                    cursor: pointer;
                }

                .game_description {
                    resize: vertical;
                    min-height: 100px;
                }

                #game_submit {
                    position: relative;
                    z-index: 3;

                    background-color: var(--accent);
                    color: var(--text);
                    border: none;
                    border-radius: 8px;
                    padding: 12px 20px;
                    font-weight: 600;
                    font-size: 16px;
                    cursor: pointer;
                    margin-top: 20px;
                    transition: background-color 0.2s, transform 0.2s;
                    align-self: flex-end;
                }

                #game_submit:hover {
                    background-color: var(--accent-hover);
                    transform: scale(1.05);
                }

                .err {
                    color: var(--bad);
                    margin: -10px;
                    margin-bottom: 10px;
                    text-align: center;
                }       
                
                .space {
                    padding-bottom: 120px;
                }

            </style>
        <?php
    } else { 
        include_once("page/404.html");
    }
?>
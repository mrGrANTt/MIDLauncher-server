<?php 
    if(checkRole(['admin', 'moderator'])) {
        echo '<main class="games">';
        if(isset($_GET['newgame'])) {

        } elseif(isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id = $_GET['id'];

            global $link;
            $sel = $link->prepare('SELECT `games`.`id`, `games`.`name`, `games`.`description`, `games`.`url`, `users`.`name` as `sender_name` FROM `games` INNER JOIN `users` ON `games`.`sender_id` = `users`.`id` WHERE `games`.`id` = ?;');
            $sel->bind_param('i', $id);
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
                                    <a class="original" href="'.$arr['url'].'" >Original game page</a>
                                    <a class="download" href="?page=download&id='.$arr['id'].'">Download game</a>
                                </td>
                            </tr>
                        </table>
                            <a class="back" href="?page=games">← Back to list</a>
                    </div>
                ';
            }
        } else {
            ?>
                <a href="?page=games&newgame">Create new game</a>
                <h4 class="tablet-title">Games</h4>
                <div id="result"></div>

                <script>
                    const xhttp = new XMLHttpRequest();

                    function loadFn(num) {
                        xhttp.onload = function() {
                            document.getElementById("result").innerHTML = this.responseText;
                        }
                        xhttp.open("GET", "page\\includes\\page_list_gen.php?page=game&count=" + num, true);
                        xhttp.send();
                    }
                    loadFn(0);
                    
                    document.getElementById('result').addEventListener('wheel', (ev) => {
                        let el = document.getElementById('page_input'); 
                        let deleta = (ev.deltaY > 0 ? 1 : -1);
                        newValue = Number(el.value) + deleta;
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

                .content {
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

                .original, .download, .back {
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

                .original, .download{
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

                .original:hover, .download:hover {
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
                    background-color: #1e1e2f;
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

            </style>
        <?php
    } else { 
        include_once("page/404.html");
    }
?>
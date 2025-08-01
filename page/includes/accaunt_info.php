<script>
    function hide(self, el, value, beforText) { // скрыть список
        self.innerText = (value == true ? '▲' : '▼') + beforText;
        el.style.display = value == true ? 'none' : '';
        self.onclick = (ev) => {
            hide(self, el, !value, beforText);
        }
    }
</script>
<?php
    function getInfo($id, $hasGame) { // генерация информации
        global $link;
        echo '<h4 id="title-suggest" class="tablet-title sends unselectable">Sended Suggest</h4>';
        $sel = $link->prepare('SELECT `date`, `id`, `name` FROM `suggest` WHERE `sender_id` = ?;');
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
        $arr = mysqli_fetch_all($res);
        if($arr) {
            ?>
                <table class="suggest" id="tablet-suggest">
                    <tr class="mainTR"> <td class="tabletTitle">Date</td> <td class="tabletTitle">Suggest</td> </tr>                    
                    <?php 
                        foreach($arr as $v) {
                            echo '
                            <tr class="tabletTR"> 
                                <td class="tabletValue">
                                    '.htmlspecialchars(trim($v[0])).'
                                </td> 
                                <td class="tabletValue">
                                    <a href="?page=suggest&id='.htmlspecialchars(trim($v[1])).'">
                                        '.htmlspecialchars(trim($v[2])).'
                                    </a>
                                </td> 
                            </tr>';
                        }
                    ?>
                </table>
                <script>
                    hide(document.getElementById('title-suggest'), document.getElementById('tablet-suggest'), false, ' Sended Suggest');
                </script>
            <?php 
        }
        else { 
            echo '<p class="noresult">No result...';
        }        
        if($hasGame === true) {
            echo '<h4 id="title-games" class="tablet-title accepted unselectable">Added game</h4>';
            $sel = $link->prepare('SELECT `date`, `id`, `name` FROM `games` WHERE `sender_id` = ?;');
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
            $arr = mysqli_fetch_all($res);
            if($arr) {
                ?>
                    <table class="accepted_suggest"  id="tablet-games">
                        <tr class="mainTR"> <td class="tabletTitle">Date</td> <td class="tabletTitle">Game</td> </tr>
                        <?php 
                        foreach($arr as $v) {
                            echo '
                            <tr class="tabletTR"> 
                                <td class="tabletValue">
                                    '.htmlspecialchars(trim($v[0])).'
                                </td> 
                                <td class="tabletValue">
                                    <a href="?page=games&id='.htmlspecialchars(trim($v[1])).'">
                                        '.htmlspecialchars(trim($v[2])).'
                                    </a>
                                </td> 
                            </tr>';
                        }
                        ?>
                    </table>
                    <script>
                        hide(document.getElementById('title-games'), document.getElementById('tablet-games'), false, ' Added game');
                    </script>
                <?php 
            } else {
                echo '<p class="noresult">No result...'; 
            }
        }
        ?>
            <style>
                .tablet-title {
                    position: relative;
                    margin: 20px 0 10px;
                    text-align: center;
                    color: rgb(var(--accent));
                    font-size: 18px;
                    transition: transform 0.2s;
                }
                .tablet-title:hover {
                    transform: scale(1.03);
                    text-decoration: underline;
                }
                table {
                    width: 100%;
                    max-width: 600px;
                    border-collapse: collapse;
                    background-color: rgb(var(--panel-bg));
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 4px 16px rgba(0,0,0,0.3);
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
                .noresult {
                    font-style: italic;
                    color: rgb(var(--mini-text));
                    text-align: center;
                }
                p {
                    display: inline-block;
                }
                .unselectable {
                    -webkit-touch-callout: none;
                    -webkit-user-select: none;
                    -khtml-user-select: none;
                    -moz-user-select: none;
                    -ms-user-select: none;
                    user-select: none;
                    cursor: default;
                }   
            </style>
        <?php
    }
?>
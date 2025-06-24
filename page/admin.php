<?php 
    if(isset($_SESSION['role']) && $_SESSION['role'] == "admin") {
        echo '<main class="accaunts">';
        if(!isset($_GET['user'])) {
            ?>
                <input type="search" id="search" placeholder="Print needed username" oninput="loadDoc()"/>
                <div id="result">
                    <span class="default_text">Start search to see variants</span>
                </div>
                <script>
                    const xhttp = new XMLHttpRequest();

                    function loadDoc() {
                        xhttp.onload = function() {
                            console.log(this.responseText)
                            document.getElementById("result").innerHTML = this.responseText;
                        }
                        el = document.getElementById("search")
                        xhttp.open("GET", "page/includes/parce_users.php?value=" + el.value, true);
                        xhttp.send();
                    }
                </script>
            <?php
        } else {
            global $link;
            $sel = $link->prepare('SELECT * FROM `users` WHERE `name` = ?;');
            $sel->bind_param('s', $_GET['user']);
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
            if(!$arr) {
                exit;
            }
            
            $id = $arr['id'];
            $name = $arr['name'];
            $email = $arr['email'];
            $role = $arr['role'];

            if(isset($_POST['save']) && $name != $_SESSION['name']) {
                $role = $_POST['role'];
                if ($role != 'admin' && $role != 'moderator') {
                    $role = 'user';
                }

                $ins = $link->prepare('UPDATE `users` SET role = ? WHERE id = ?;');
                $ins->bind_param('si', $_POST['role'], $id);
                $err = "";

                try {
                    $ins->execute();
                } catch(mysqli_sql_exception $ex) {
                    $err = $ex->getMessage();
                }
                if($err != "") {
                    echo $err.'<br />';
                    exit;
                }   
            } 
            ?>
                <form method="POST" class="edit_form">
                    <div class="info">
                        <?php
                            echo '
                                <h3 class="infotext username">Name: '.$name.'</h3>
                                <p class="infotext email">Email: <a href="mailto:'.$email.'">'.$email.'</a></p>
                                <p class="infotext role">Role: <select name="role" class="role_selection">
                                    <option value="user" '.($role == 'user' ? 'selected' : '').'>user</option>
                                    <option value="moderator" '.($role == 'moderator' ? 'selected' : '').'>moderator</option>
                                    <option value="admin" '.($role == 'admin' ? 'selected' : '').'>admin</option>
                                </select></p>
                            ';
                        ?>
                    </div>
                    <div class="buttons">
                        <a href="?page=admin" class="btn back">Back</a>
                        <button type="submit" class="btn save" name="save">Save</button>
                    </div>
                </form>
            <?php
            
            if(isset($_POST['save'])) {
                if ($name == $_SESSION['name']) {
                    echo '<p style="color: red;">You can\'t edit yourself!</p>';
                }
                elseif($err == "") {
                    echo '<p style="color: green;">Success update!</p>';
                }
            }

            ?>
                <h4 class="tablet-title sends">Sended Suggest</h4>
            <?php 

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
                    <table class="suggest">
                        <tr class="mainTR"> <td class="tabletTitle">Date</td> <td class="tabletTitle">Suggest</td> </tr>
                        
                        <?php 
                        foreach($arr as $v) {
                            echo '
                            <tr class="tabletTR"> 
                                <td class="tabletValue">
                                    '.$v[0].'
                                </td> 
                                <td class="tabletValue">
                                    <a href="?page=suggest&id='.$v[1].'">
                                        '.$v[2].'
                                    </a>
                                </td> 
                            </tr>';
                        }
                        ?>
                    </table>
                <?php 
            } else { echo '<p class="noresult">No result...'; }

            if($role == 'moderator' || $role == 'admin') {
                ?>
                    <h4 class="tablet-title accepted">Accepted Suggest</h4>
                <?php
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
                        <table class="accepted_suggest">
                            <tr class="mainTR"> <td class="tabletTitle">Date</td> <td class="tabletTitle">Suggest</td> </tr>
                            
                        <?php 
                        foreach($arr as $v) {
                            echo '
                            <tr class="tabletTR"> 
                                <td class="tabletValue">
                                    '.$v[0].'
                                </td> 
                                <td class="tabletValue">
                                    <a href="?page=games&id='.$v[1].'">
                                        '.$v[2].'
                                    </a>
                                </td> 
                            </tr>';
                        }
                        ?>
                        </table>
                    <?php 
                } else { echo '<p class="noresult">No result...'; }
            }
            echo '<div class="space" />';
        }
    ?>
    </main>

    <style>
        main.accaunts {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 60px 20px 40px;
            gap: 30px;
            color: var(--text);
            padding-top: 120px;
        }

        #search {
            padding: 10px 15px;
            font-size: 16px;
            width: 100%;
            max-width: 400px;
            border: none;
            border-radius: 8px;
            background-color: var(--panel-bg);
            color: white;
            outline: none;
            box-shadow: 0 0 0 2px transparent;
            transition: box-shadow 0.2s;
            z-index: 3;
        }

        #search:focus {
            box-shadow: 0 0 0 2px var(--accent);
        }

        #search:hover {
            box-shadow: 0 0 0 2px var(--accent);
        }

        #result {
            width: 100%;
            max-width: 400px;
            background-color: var(--panel-bg);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
            z-index: 3;
        }

        .default_text {
            opacity: 0.6;
            font-style: italic;
        }

        .edit_form {
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 100%;
            max-width: 300px;
            background-color: var(--panel-bg);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.4);
            z-index: 3;
        }

        .edit_form .info {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .infotext {
            margin: 0;
        }

        .username {

            text-align: center;
        }

        .role_selection {
            padding: 8px 12px;
            font-size: 14px;
            background-color: var(--main-bg);
            color: white;
            border-radius: 6px;
            border: none;
            outline: none;
            z-index: 3;
        }

        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .btn {
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            color: white;
            transition: background-color 0.2s, transform 0.2s;
        }

        .btn.back {
            background-color: var(--main-bg);
        }

        .btn.save {
            background-color: var(--accent);
        }

        .tablet-title {
            margin: 20px 0 10px;
            text-align: center;
            color: var(--accent);
            font-size: 18px;
        }

        table {
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

        a {
            color: var(--accent);
            text-decoration: none;
            z-index: 3;
        }

        a:hover {
            text-decoration: underline;
        }

        .serch_res {
            color: var(--text);
        }

        .btn:hover {
            transform: scale(1.03);
            background-color: var(--accent-hover);
            text-decoration: none;
        }

        .noresult {
            font-style: italic;
            color: var(--mini-text);
            text-align: center;
        }

        p {
            display: inline-block;
        }

        .footer {
            z-index: 5;
        }

        .space {
            padding-bottom: 100px;
        }

    </style>

    <?php
    } else { 
        include_once("page/404.html");
    }
?>
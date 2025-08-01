<?php 
    if(checkRole('admin')) {
        echo '<main class="accaunts">';
        if(isset($_GET['user'])) { // Проверка выбранного пользователя
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
            // обработка данных из базы данных
            $id = htmlspecialchars(trim($arr['id']));
            $name = htmlspecialchars(trim($arr['name']));
            $email = htmlspecialchars(trim($arr['email']));
            $role = htmlspecialchars(trim($arr['role']));
            if(isset($_POST['save']) && $name != $_SESSION['name']) { // обработка изменений
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
                        <a href="?page=admin" class="btn back">To List</a>
                        <button type="submit" class="btn save" name="save">Save</button>
                    </div>
                </form>
            <?php         
            if(isset($_POST['save'])) { // вывод ошибок
                if ($name == $_SESSION['name']) {
                    echo '<p style="color: rgb(var(--bad));">You can\'t edit yourself!</p>';
                }
                elseif($err == "") {
                    echo '<p style="color: rgb(var(--good));">Success update!</p>';
                }
            }
            // вывод деятельности аккаунта
            include_once('page/includes/accaunt_info.php');
            getInfo($id, ($role == 'admin' || $role == 'moderator'));
            echo '<div class="space"></div>';
        } else { // вывод списка аккаунтов
            ?>
                <input type="search" id="search" placeholder="Print needed username" oninput="loadDoc()"/>
                <div id="result">
                    <span class="default_text">Start search to see variants</span>
                </div>
                <script>
                    const xhttp = new XMLHttpRequest();

                    function loadDoc() {
                        xhttp.onload = function() {
                            document.getElementById("result").innerHTML = this.responseText;
                        }
                        el = document.getElementById("search")
                        xhttp.open("GET", "public/parce_users.php?value=" + el.value, true);
                        xhttp.send();
                    }
                    loadDoc();
                </script>
            <?php
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
            color: rgb(var(--text));
            padding-top: 120px;
        }
        #search {
            padding: 10px 15px;
            font-size: 16px;
            width: 100%;
            max-width: 400px;
            border: none;
            border-radius: 8px;
            background-color: rgb(var(--panel-bg));
            color: white;
            outline: none;
            box-shadow: 0 0 0 2px transparent;
            transition: box-shadow 0.2s;
        }
        #search:focus {
            box-shadow: 0 0 0 2px rgb(var(--accent));
        }
        #search:hover {
            box-shadow: 0 0 0 2px rgb(var(--accent));
        }
        #result {
            width: 100%;
            max-width: 400px;
            background-color: rgb(var(--panel-bg));
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
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
            background-color: rgb(var(--panel-bg));
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.4);
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
            background-color: rgb(var(--main-bg));
            color: white;
            border-radius: 6px;
            border: none;
            outline: none;
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
            background-color: rgb(var(--main-bg));
        }
        .btn.save {
            background-color: rgb(var(--accent));
        }
        .btn:hover {
            transform: scale(1.03);
            background-color: rgb(var(--accent-hover));
            text-decoration: none;
        }
        a {
            color: rgb(var(--accent));
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .serch_res {
            color: rgb(var(--text));
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
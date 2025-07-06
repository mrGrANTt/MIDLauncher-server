<?php
    include_once('page/includes/login_func.php');

    function checkAction() {
        if (isset($_POST['act'])) {
            switch ($_POST['act']) {
                case 'logout': 
                    logout(); 
                    global $mainUrl;
                    header("Location: ".$mainUrl."?page=login");
                    exit;
                case 'repass': 
                    return [1, chengePassword($_POST['lastPass'], $_POST['newPass'], $_POST['newPass2'])];
                case 'reemail': 
                    return [2, chengeEmail($_POST['email'], $_POST['pass'])];
            }
        }
        return [0,''];
    }

    

    if(isset($_SESSION['name'])) {
        $res = checkAction();
        ?>
            <main class="accaunt">  
                <h2 class="title">Accaunt page</h2>
                <div class="info box"> 
                    <?php
                        global $link;
                        $sel = $link->prepare('SELECT `name`, `email`, `role` FROM `users` WHERE `id` = ?;');
                        $sel->bind_param('i', $_SESSION['id']);
                        $err = "";

                        try {
                            $sel->execute();
                            $result = $sel->get_result(); 
                        } catch(mysqli_sql_exception $ex) {
                            $err = $ex->getMessage();
                        }
                        if(!($err == "" && $result)) {
                            echo $err.'<br />';
                            exit;
                        }
                        $arr = mysqli_fetch_array($result);
                        if(!$arr) {
                            exit;
                        }
                        
                        $name = $arr['name'];
                        $email = $arr['email'];
                        $role = $arr['role'];


                        echo '
                            <h3 class="infotext username"><span class="sec-text">Name:</span> '.$name.'</h3>
                            <p class="infotext email"><span class="sec-text">Email:</span> <a href="mailto:'.$email.'">'.$email.'</a></p>
                            <p class="infotext role"><span class="sec-text">Role:</span> '.$role.'</p>
                        ';
                    ?>
                </div>
                <div class="pass box">
                    <h3 class="title">Chenge password</h3>
                    <form class="pass-form" method="POST">
                        <input type="password" name="lastPass" placeholder="last password" class="inputer"/>
                        <input type="password" name="newPass" placeholder="new password" class="inputer"/>
                        <input type="password" name="newPass2" placeholder="confirm new password" class="inputer"/>

                        <?php if($res[0] == 1) echo $res[1]; ?>

                        <button class="save btn" type="submit" name="act" value="repass">Save</button>
                    </form>
                </div>
                <div class="email box">
                    <h3 class="title">Chenge email</h3>
                    <form class="email-form" method="POST">
                        <input type="email" name="email" placeholder="new email" class="inputer" />
                        <input type="password" name="pass" placeholder="password" class="inputer"/>

                        <?php if($res[0] == 2) echo $res[1]; ?>

                        <button class="save btn" type="submit" name="act" value="reemail">Save</button>
                    </form>
                </div>
                <div class="logout">
                    <form class="logout-form" method="POST">
                        <button class="bad btn" type="submit" name="act" value="logout">Logout</button>
                    </form>
                </div>
        <?php
            include_once('page/includes/accaunt_info.php');
            getInfo($_SESSION['id'], checkRole(['admin', 'moderator']));
            echo '<div class="space" />';
        ?>
            </main>
            <style>
                main.accaunt {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 60px 20px 40px;
                    gap: 15px;
                    color: rgb(var(--text));
                    padding-top: 120px;
                }

                .box {       
                    width: 100%;
                    max-width: 350px;
                    background-color: rgb(var(--panel-bg));
                    padding: 20px;
                    padding-top: 0px;
                    border-radius: 10px;
                    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
                    z-index: 3;
                }

                .pass-form,
                .email-form,
                .logout-form {
                    display: flex;
                    flex-direction: column;    
                    gap: 15px;
                }

                .info {
                    display: flex;
                    flex-direction: column;
                    gap: 10px;
                }

                .infotext {
                    margin: 0;
                }

                .username {
                    margin-top: 20px;
                    text-align: center;
                }

                .sec-text {
                    color: rgb(var(--mini-text));
                }

                .inputer {
                    padding: 10px 14px;
                    font-size: 16px;
                    border: none;
                    border-radius: 8px;
                    background-color: rgb(var(--main-bg));
                    color: rgb(var(--text));
                    outline: none;
                    transition: background-color 0.2s, box-shadow 0.2s;
                    z-index: 3;
                }

                .inputer:focus {
                    background-color: rgb(var(--input-focus-bg));
                    box-shadow: 0 0 0 2px rgb(var(--accent));
                }

                .btn {
                    max-width: 100px;
                    border: none;
                    padding: 10px 18px;
                    border-radius: 8px;
                    font-weight: bold;
                    cursor: pointer;
                    transition: background-color 0.2s, transform 0.2s;
                    z-index: 3;
                    color: rgb(var(--text));
                }

                .bad {
                    background-color: rgb(var(--bad));
                }

                .bad:hover {
                    transform: scale(1.03);
                    background-color: rgb(var(--bad-hover));
                }

                .save {
                    background-color: rgb(var(--accent));
                }
                
                .save:hover {
                    transform: scale(1.03);
                    background-color: rgb(var(--accent-hover));
                }

                .title {
                    text-align: center;
                    color: rgb(var(--text));
                }

                a {
                    color: rgb(var(--accent));
                    text-decoration: none;
                    z-index: 3;
                }

                a:hover {
                    text-decoration: underline;
                }

                .space {
                    padding-bottom: 100px;
                }
            </style>
        <?php      
    } else {
        global $mainUrl;
        header("Location: ".$mainUrl."?page=login");
        exit;
    }
?>
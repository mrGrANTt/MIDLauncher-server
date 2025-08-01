<?php
    include_once('page/includes/login_func.php');
?>    
<main class="login">
    <?php
        if(isset($_GET['reg'])) { // меню регистрации
            ?>
                <div class="upper">
                    <a href="?page=login" class="a-log">Login</a>
                    <a href="?page=login&amp;reg" class="a-reg unselect">Registration</a>
                </div>
                <div class="reg-form">
                    <form method="POST">
                        <input type="text" name="name" placeholder="name" value= <?php echo isset($_POST['name']) ? '"'.$_POST['name'].'"' : '""'?> class="inputer" />
                        <input type="email" name="email" placeholder="email" value= <?php echo isset($_POST['email'])? '"'.$_POST['email'].'"' : '""'?> class="inputer" />
                        <input type="password" name="pas1" placeholder="password" class="inputer"/>
                        <input type="password" name="pas2" placeholder="confirm password" class="inputer"/>

                        <div class="sub-conteiner"><button type="submit" name="reg" class="submit-btn">Done</button></div>
                    </form>
                </div>
            <?php
            if(isset($_POST['reg'])) { // обработка регистрации
                if($_POST['pas1'] == $_POST['pas2']) {
                    if (register($_POST['name'],$_POST['email'],$_POST['pas1'])) {
                        ?>
                            <script>
                                window.location="<?php global $mainUrl; echo $mainUrl; ?>";
                            </script>
                        <?php
                    }
                } else {
                    ?>
                        <p style="color: red;">Password must match!</p>
                    <?php
                }
            }
        } else { // меню входа в аккаунт
            ?>
                <div class="upper">
                    <a href="?page=login" class="a-log unselect">Login</a>
                    <a href="?page=login&amp;reg" class="a-reg">Registration</a>
                </div>
                <div class="log-form">
                    <form method="POST">
                        <input type="text" name="name" placeholder="name or email" value=<?php echo isset($_POST['name']) ? '"'.$_POST['name'].'"' : '""'?> class="inputer" />
                        <input type="password" name="pass" placeholder="password" class="inputer"/>

                        <div class="sub-conteiner"><button type="submit" name="log" class="submit-btn">Done</button></div>
                    </form>
                </div>
            <?php
            if(isset($_POST['log'])) { // обработка входа в аккаунт
                if (login($_POST['name'], $_POST['pass'])) {
                    ?>
                        <script>
                            window.location="<?php global $mainUrl; echo $mainUrl; ?>";
                        </script>
                    <?php
                }
            }
        }
    ?>
</main>
<style>
.login {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 100px);
    padding: 40px 20px;
}
form {
    display: flex;
    flex-direction: column;
    gap: 15px;
    max-width: 400px;
    background-color: rgb(var(--panel-bg));
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
}
.log-form,
.reg-form {
    width: 100%;
    max-width: 400px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 15px;
    padding: 10px;
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
}
.inputer:focus {
    background-color: rgb(var(--input-focus-bg));
    box-shadow: 0 0 0 2px rgb(var(--accent));
}
.sub-conteiner {
    display: flex;
    justify-content: flex-end;
}
.submit-btn {
    background-color: rgb(var(--accent));
    border: none;
    padding: 10px 18px;
    border-radius: 8px;
    color: rgb(var(--text));
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.2s, transform 0.2s;
}
.submit-btn:hover {
    background-color: rgb(var(--accent-hover));
    transform: scale(1.03);
}
.upper {
    display: flex;
    justify-content: center;
    gap: 20px;
}
.a-log,
.a-reg {
    text-decoration: none;
    padding: 8px 16px;
    border-radius: 6px;
    background-color: rgb(var(--panel-bg));
    color: rgb(var(--text));
    transition: background-color 0.2s;
}
.a-log:hover,
.a-reg:hover {
    background-color: rgb(var(--hover-bg));
}
.a-log.unselect,
.a-reg.unselect {
    opacity: 0.5;
    pointer-events: none;
}
</style>
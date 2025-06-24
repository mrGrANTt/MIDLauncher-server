<?php 
    if(isset($_SESSION['role']) && ($_SESSION['role'] == "admin" || $_SESSION['role'] == "moderator")) {
?>
<main class="accaunts">

</main>
<style>

</style>
<?php
    } else { 
        include_once("page/404.html");
    }
?>
<?php 
    if(checkRole(['admin', 'moderator'], $_SESSION['name'])) {
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
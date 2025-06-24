<?php 
    if(isset($_SESSION['name'])) {
?>
<main class="accaunts">

</main>
<style>

</style>
<?php
    } else { 
        global $mainUrl;
        header("Location: ".$mainUrl."?page=login");
        exit;
    }
?>
<?php 
    if(checkRole(['admin', 'moderator'])) {

?>
<main class="accaunts">
    <h4 class="tablet-title">Games</h4>
    
</main>
<style>

</style>
<?php
    } else { 
        include_once("page/404.html");
    }
?>
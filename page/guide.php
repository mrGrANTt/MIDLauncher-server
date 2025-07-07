<?php 
    $guide = isset($_GET['guide']) ? $_GET['guide'] : '';

    if (file_exists('guide/'.$guide.'.php')) {
        include_once('guide/'.$guide.'.php');
    } else {
        include_once('guide/unfinded.php');
    }
?>
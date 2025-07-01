<main class="main">
    <div class="text_conteiner">
        <h2 class="text title">Oooops...</h2>
        
        <?php
        if(isset($_GET['id']) && is_numeric($_GET['id']) && checkRole(['admin','moderator'])) {
            $file = 'download/'.$_GET['id'].'.zip';
        } elseif(isset($_GET['dayly'])) {
            $file = 'download/dayly.zip';
        } else {
            $file = 'download/launcher-lastest.zip';
        }
        
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));

            readfile($file);
            echo '<p class="text">If installing don\'t started, try to reload page.</p>';
            exit;
        } else {
            echo '<p class="text">Can\'t find file, contact the administration.</p>';
        }
        ?>
    </div>
</main>

<style>
.main {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 100px);
}

.text_conteiner {
    text-align: center;
    background-color: var(--panel-bg);
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
}

.text {
    position: relative;
    z-index: 2;
}

.text>a {
    color: var(--accent);
    text-decoration: none;
}

.text>a:hover {
    text-decoration: underline;
}

.title {
    scale: 2;
    padding-bottom: 20px;
}
</style>
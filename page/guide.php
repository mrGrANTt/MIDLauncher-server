<main class="guide">
    <?php 
        $guide = isset($_GET['guide']) ? $_GET['guide'] : '';

        if (file_exists('guides/'.$guide.'.html')) {
            include_once('guides/'.$guide.'.html');
        } else {
            include_once('guides/unfinded.html');
        }
    ?>
    <div class="space"></div>
</main>

<style>
    .space {
        padding-bottom: 180px;
    }

    .guide {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 120px 20px 40px;
        gap: 10px;
        color: rgb(var(--text));
    }

    .guide div:not(.space) {
        width: fit-content;
        max-width: 80%;
        min-width: 400px;
        background-color: rgb(var(--panel-bg));
        padding: 25px 30px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
        display: flex;
        flex-direction: column;
        gap: 16px;
    }


    .guide h1 {
        font-size: 32px;
        margin: 20px 0 10px;
        color: rgb(var(--accent));
    }

    .guide h2 {
        font-size: 28px;
        margin: 18px 0 10px;
        color: rgb(var(--accent));
    }

    .guide h3 {
        font-size: 24px;
        margin: 16px 0 8px;
        color: rgb(var(--text));
    }

    .guide h4 {
        font-size: 20px;
        margin: 14px 0 8px;
        color: rgb(var(--text));
    }

    .guide h5 {
        font-size: 18px;
        margin: 12px 0 6px;
        color: rgb(var(--text));
    }

    .guide h6 {
        font-size: 16px;
        margin: 10px 0 6px;
        color: rgb(var(--text));
    }

    .guide p {
        font-size: 16px;
        line-height: 1.6;
        margin: 10px 0;
        color: rgb(var(--text));
    }

    .guide a {
        color: rgb(var(--accent));
        text-decoration: none;
        transition: text-decoration 0.2s;
    }

    .guide a:hover {
        text-decoration: underline;
    }

    .guide ul,
    .guide ol {
        margin: 10px 0 10px 20px;
        padding-left: 20px;
    }

    .guide li {
        margin-bottom: 6px;
        line-height: 1.5;
    }

    .guide table {
        width: fit-content;
        max-width: 80%;
        margin: 20px auto;
        border-collapse: collapse;
        background-color: rgb(var(--panel-bg));
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
    }

    .guide table td {
        padding: 12px 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        color: rgb(var(--text));
    }

    .guide table tr:last-child td {
        border-bottom: none;
    }

    .guide table > tbody > tr:first-of-type {
        background-color: rgb(var(--main-bg));
        font-weight: bold;
        color: rgb(var(--accent));
    }

</style>

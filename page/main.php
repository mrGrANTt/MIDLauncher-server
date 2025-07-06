<main class="main-content">
    <a href="?page=download" class="download_btn">Download</a>
</main>

<style>
    .main-content {
        padding-top: 100px;
        padding-bottom: 140px;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 240px);
    }

    .download_btn {
        padding: 14px 28px;
        background-color: rgb(var(--accent));
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: bold;
        font-size: 18px;
        text-decoration: none;
        transition: transform 0.2s, box-shadow 0.3s;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        z-index: 2;
    }

    .download_btn:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.4);
    }
</style>
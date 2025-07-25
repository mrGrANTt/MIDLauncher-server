<main class="main-content">
    <div class="launcher-gallery" id="gallery">
        <img src="img/launcher/1.jpg" class="gallery-image active">
        <img src="img/launcher/2.jpg" class="gallery-image">
    </div>

    <div class="launcher-info">
        <h1 class="launcher-title">MIDLauncher</h1>
        <p class="launcher-description">
            MIDLauncher is free application, created for giving diversified playing experience. Every day is new surprise game day. Save your favorite games and play randomly selected games from Itch.io library!
        </p>
        <a href="?page=download" class="download_btn">Download</a>
    </div>
</main>

<script>
    const images = document.querySelectorAll('.gallery-image');
    const gallery = document.getElementById('gallery');
    let currentIndex = 0;

    function showImage(index) {
        images.forEach((img, i) => {
            img.classList.toggle('active', i === index);
        });
    }

    function chengeImage() {
        currentIndex = (currentIndex + 1) % images.length;
        showImage(currentIndex);
    }

    gallery.addEventListener('click', chengeImage);
    setInterval(chengeImage, 5000);
</script>

<style>
    .main-content {
        padding-top: 100px;
        padding-bottom: 140px;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 60px;
        min-height: calc(100vh - 240px);
        color: rgb(var(--text));
    }

    .launcher-gallery {
        position: relative;
        width: 320px;
        aspect-ratio: 16 / 9;
        overflow: hidden;
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        cursor: pointer;
    }

    .gallery-image {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 16px;
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
    }

    .gallery-image.active {
        opacity: 1;
    }

    .launcher-info {
        max-width: 500px;
        background-color: rgb(var(--panel-bg));
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .launcher-title {
        font-size: 36px;
        font-weight: bold;
        margin: 0;
    }

    .launcher-description {
        font-size: 16px;
        color: rgb(var(--mini-text));
        line-height: 1.5;
    }

    .download_btn {
        align-self: flex-start;
        padding: 14px 28px;
        background-color: rgb(var(--accent));
        color: rgb(var(--text));
        border: none;
        border-radius: 12px;
        font-weight: bold;
        font-size: 18px;
        text-decoration: none;
        transition: transform 0.2s, box-shadow 0.3s;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    }

    .download_btn:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.4);
    }
</style>
<style>
    .hero-banner {
        position: relative;
        height: 500px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        text-align: center;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-image: url('<?php echo $heroImage; ?>');
    }

    .hero-banner::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        max-width: 900px;
        padding: 0 1.5rem;
    }

    .hero-content h1 {
        font-size: 2.8rem;
        margin-bottom: 0.75rem;
        font-weight: 700;
    }

    .hero-content p {
        font-size: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .search-box {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 0.75rem;
    }

    .search-box input,
    .search-box button {
        padding: 0.85rem 1rem;
        border: none;
        border-radius: 0.35rem;
        font-size: 1rem;
    }

    .search-box input {
        width: 100%;
    }

    .search-box button {
        background-color: #111;
        color: #fff;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .search-box button:hover {
        background-color: #222;
    }
</style>

<section class="hero-banner">
    <div class="hero-content">
        <h1><?php echo $heroTitle ?? ''; ?></h1>
        <p><?php echo $heroSubTitle ?? ''; ?></p>
        <div class="search-box">
            <input type="text" placeholder="Ciudad o zona">
            <input type="number" placeholder="Precio máx.">
            <input type="number" placeholder="Habitaciones">
            <button type="button">Buscar</button>
        </div>
    </div>
</section>

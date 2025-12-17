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
    .search-box select,
    .search-box button {
        padding: 0.85rem 1rem;
        border: none;
        border-radius: 0.35rem;
        font-size: 1rem;
    }

    .search-box input,
    .search-box select {
        width: 100%;
        background-color: #fff;
    }

    .search-box select {
        cursor: pointer;
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
        <h1>Encuentra tu hogar en Valencia</h1>
        <p>Propiedades seleccionadas en Valencia y alrededores.</p>
        <form method="GET" action="/propiedades" class="search-box">
            <select name="operacion" id="operacion" aria-label="Operación">
                <option value="">Todas</option>
                <option value="venta">Venta</option>
                <option value="alquiler">Alquiler</option>
                <option value="vacacional">Vacacional</option>
            </select>
            
            <select name="tipo" id="tipo" aria-label="Tipo de propiedad">
                <option value="">Todos</option>
                <option value="piso">Piso</option>
                <option value="casa">Casa</option>
                <option value="chalet">Chalet</option>
                <option value="adosado">Adosado</option>
                <option value="duplex">Duplex</option>
                <option value="local">Local</option>
                <option value="oficina">Oficina</option>
                <option value="terreno">Terreno</option>
                <option value="otros">Otros</option>
            </select>
            
            <input type="number" name="m2_min" id="m2_min" placeholder="Mín. m²" min="0" step="1" aria-label="Mínimos metros cuadrados">
            
            <input type="number" name="precio_max" id="precio_max" placeholder="Precio máx. (€)" min="0" step="1" aria-label="Precio máximo">
            
            <button type="submit">Buscar</button>
        </form>
    </div>
</section>

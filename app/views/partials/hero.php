<section class="hero-banner" style="background-image: url('<?= htmlspecialchars($heroImage, ENT_QUOTES, 'UTF-8') ?>');">
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

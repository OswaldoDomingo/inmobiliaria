    <!-- 3. ESTRUCTURA HTML (WIDGET) -->
    <div id="valuation-widget"
        class="w-full max-w-5xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 my-8">

        <!-- Header -->
        <div class="bg-gradient-to-r from-[#191A2E] to-[#242642] p-6 sm:p-8 text-white">
            <div class="flex items-center gap-3 mb-2">
                <i data-lucide="calculator" class="w-8 h-8 opacity-90"></i>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight">Tasación Online</h2>
            </div>
            <p class="text-indigo-100 opacity-90">Valoración profesional basada en datos de mercado actuales.</p>
        </div>

        <div class="p-6 sm:p-8 grid lg:grid-cols-12 gap-8 relative">

            <!-- COLUMNA IZQUIERDA: FORMULARIO TÉCNICO -->
            <div class="lg:col-span-7 space-y-6">

                <!-- Fila 1: CP y Barrio -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="flex flex-col gap-1 w-full">
                        <label class="text-sm font-medium text-gray-700">Código Postal</label>
                        <div class="relative">
                            <input type="text" id="input-cp" maxlength="5" placeholder="Ej: 28001"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-colors">
                        </div>
                    </div>

                    <div class="flex flex-col gap-1 w-full">
                        <label class="text-sm font-medium text-gray-700">Barrio</label>
                        <select id="select-barrio" disabled
                            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none disabled:bg-gray-100 disabled:text-gray-400">
                            <option value="">Seleccione CP primero...</option>
                        </select>
                    </div>
                </div>

                <!-- Fila 2: Zona y Superficie -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="flex flex-col gap-1 w-full">
                        <label class="text-sm font-medium text-gray-700">Zona</label>
                        <select id="select-zona" disabled
                            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none disabled:bg-gray-100 disabled:text-gray-400">
                            <option value="">Seleccione Barrio...</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-1 w-full">
                        <label class="text-sm font-medium text-gray-700">Superficie (m²)</label>
                        <div class="relative">
                            <input type="number" id="input-surface" min="1" placeholder="Ej: 90"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                            <span
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm pointer-events-none">m²</span>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100 my-2">

                <!-- Fila 3: Orientación y Estado -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="flex flex-col gap-1 w-full">
                        <label class="text-sm font-medium text-gray-700">Orientación</label>
                        <select id="select-orientation"
                            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <option value="exterior">Exterior</option>
                            <option value="interior">Interior</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-1 w-full">
                        <label class="text-sm font-medium text-gray-700">Estado</label>
                        <select id="select-state"
                            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <option value="entrar">Entrar a vivir</option>
                            <option value="reformar">A reformar</option>
                            <option value="reformado">Reformado recientemente</option>
                        </select>
                    </div>
                </div>

                <!-- Fila 4: Ascensor y Planta (Condicional) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="flex flex-col gap-1 w-full">
                        <label class="text-sm font-medium text-gray-700">¿Tiene Ascensor?</label>
                        <select id="select-elevator"
                            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <option value="yes">Con Ascensor</option>
                            <option value="no">Sin Ascensor</option>
                        </select>
                    </div>

                    <!-- Este select se muestra solo si NO hay ascensor -->
                    <div id="container-floor" class="hidden-animated flex flex-col gap-1 w-full">
                        <label class="text-sm font-medium text-gray-700">Planta (Altura)</label>
                        <select id="select-floor"
                            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <option value="0">Bajo</option>
                            <option value="1">Planta 1</option>
                            <option value="2">Planta 2</option>
                            <option value="3">Planta 3</option>
                            <option value="4">Planta 4</option>
                            <option value="5">Planta 5</option>
                            <option value="6">Planta 6</option>
                            <option value="7">Planta 7 o superior</option>
                        </select>
                    </div>
                </div>

                <!-- Extras (Checkboxes) -->
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 mt-2">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <i data-lucide="home" class="w-4 h-4"></i> Características Adicionales
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Balcón -->
                        <label class="flex items-center space-x-3 cursor-pointer group select-none">
                            <input type="checkbox" id="check-balcony" class="custom-checkbox hidden">
                            <div
                                class="w-5 h-5 rounded border border-gray-300 bg-white flex items-center justify-center transition-colors group-hover:border-indigo-400">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-white hidden"></i>
                            </div>
                            <span class="text-sm text-gray-600">Balcón / Terraza</span>
                        </label>

                        <!-- Ático (Condicional: Oculto si no hay ascensor) -->
                        <label id="container-penthouse"
                            class="visible-animated flex items-center space-x-3 cursor-pointer group select-none">
                            <input type="checkbox" id="check-penthouse" class="custom-checkbox hidden">
                            <div
                                class="w-5 h-5 rounded border border-gray-300 bg-white flex items-center justify-center transition-colors group-hover:border-indigo-400">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-white hidden"></i>
                            </div>
                            <span class="text-sm text-gray-600">Es un Ático</span>
                        </label>

                        <!-- Bajo (Condicional: Visible SOLO si hay ascensor) -->
                        <label id="container-ground"
                            class="visible-animated flex items-center space-x-3 cursor-pointer group select-none">
                            <input type="checkbox" id="check-ground" class="custom-checkbox hidden">
                            <div
                                class="w-5 h-5 rounded border border-gray-300 bg-white flex items-center justify-center transition-colors group-hover:border-indigo-400">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-white hidden"></i>
                            </div>
                            <span class="text-sm text-gray-600">Es un Bajo</span>
                        </label>
                    </div>
                </div>

                <!-- Botón Acción (Fase 1) -->
                <button id="btn-calculate" disabled
                    class="w-full bg-gray-900 hover:bg-black text-white py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed flex justify-center items-center gap-2 mt-4">
                    <span>TASAR INMUEBLE</span>
                </button>
            </div>

            <!-- COLUMNA DERECHA: CONTACTO Y RESULTADO -->
            <div class="lg:col-span-5 flex flex-col h-full min-h-[400px] relative">

                <!-- 1. Estado Inicial: Placeholder -->
                <div id="result-placeholder"
                    class="flex flex-col items-center justify-center h-full text-center p-8 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                    <i data-lucide="building" class="w-16 h-16 text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900">Listo para tasar</h3>
                    <p class="text-gray-500 max-w-xs mt-2">Complete el formulario para obtener su valoración inmediata.
                    </p>
                </div>

                <!-- 2. Estado Intermedio: Formulario de Contacto (Captación) -->
                <div id="contact-form-container" style="display:none;"
                    class="hidden bg-white border border-gray-200 rounded-2xl p-6 shadow-lg flex-1 flex-col animate-fade-in w-full">

                    <!-- Gancho visual: Precio Estimado -->
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center mb-6">
                        <p class="text-xs text-green-700 font-bold uppercase tracking-wide mb-1">Valoración Estimada</p>
                        <p class="text-2xl font-bold text-green-800" id="preview-price-range">Calculando...</p>
                    </div>

                    <h3 class="text-lg font-bold text-gray-800 mb-4">Recibe tu informe detallado</h3>
                    <p class="text-sm text-gray-500 mb-4">Para enviarte el informe completo de tasación y que un experto
                        valide el precio, necesitamos tus datos de contacto.</p>

                    <div class="space-y-4 flex-1">
                        <div>
                            <label class="text-sm font-medium text-gray-700 block mb-1">Correo Electrónico <span
                                    class="text-red-500">*</span></label>
                            <input type="email" id="input-email" placeholder="tu@email.com"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 block mb-1">Teléfono Móvil <span
                                    class="text-red-500">*</span></label>
                            <input type="tel" id="input-phone" placeholder="600 000 000"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>

                        <!-- Checks Legales -->
                        <div class="space-y-3 pt-2">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" id="check-privacy" class="gdpr-checkbox mt-1">
                                <span class="text-xs text-gray-600 leading-tight">
                                    He leído y acepto la <a href="#" class="text-indigo-600 underline">política de
                                        privacidad</a> y el tratamiento de mis datos.
                                </span>
                            </label>
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" id="check-commercial" class="gdpr-checkbox mt-1">
                                <span class="text-xs text-gray-600 leading-tight">
                                    Acepto recibir información comercial, ofertas y novedades por email, teléfono o
                                    mensajería instantánea.
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Botón Enviar Lead -->
                    <button id="btn-send-lead" disabled
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-semibold shadow-md transition-all mt-6 disabled:opacity-50 disabled:bg-gray-400 flex justify-center items-center gap-2">
                        <span>VER INFORME COMPLETO</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                    <p id="send-error-msg" class="text-xs text-red-500 mt-2 text-center hidden">Por favor revisa los
                        campos.</p>
                </div>

                <!-- 3. Estado Final: Resultado Detallado -->
                <div id="result-content" style="display:none;"
                    class="hidden bg-indigo-50 border border-indigo-100 rounded-2xl p-6 sm:p-8 flex-1 flex-col animate-fade-in relative z-0">
                    <div class="flex items-center gap-2 text-indigo-700 font-semibold mb-4">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                        <span>Tasación Completada</span>
                    </div>

                    <div class="mb-8">
                        <p class="text-sm text-gray-500 mb-1">Su inmueble está valorado entre</p>
                        <div class="flex flex-col sm:flex-row sm:items-baseline gap-2 sm:gap-3 flex-wrap">
                            <span id="price-min" class="text-3xl sm:text-4xl font-bold text-gray-900">0 €</span>
                            <span class="text-gray-400 font-medium hidden sm:inline">y</span>
                            <span id="price-max" class="text-3xl sm:text-4xl font-bold text-gray-900">0 €</span>
                        </div>
                    </div>

                    <div
                        class="bg-white rounded-xl p-4 shadow-sm border border-indigo-100 mb-6 overflow-y-auto max-h-[300px] flex-1">
                        <!-- Lista limpia de características -->
                        <div id="modifiers-list" class="space-y-1">
                            <!-- Los items se inyectarán aquí vía JS -->
                        </div>
                    </div>

                    <div class="mt-auto pt-4 border-t border-indigo-100 text-center">
                        <p class="text-xs text-indigo-400">Esta valoración es una estimación de mercado.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- 4. LÓGICA JAVASCRIPT -->
    <script>
        // Inicializar Iconos
        const csrfToken = '<?= htmlspecialchars($csrfToken ?? '') ?>';
        lucide.createIcons();

        /* =========================================================================
           📍 CONFIGURACIÓN DE DATOS (GOOGLE SHEETS)
           ========================================================================= */
        const URL_DATOS_CSV = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vQVyX2rRqNeaqc1WUcVnyIc_C4RfRtBAv19Btc9BahBNQ5vd6wXdFBnqhkPugUvQTZ8yXVLDUVnxf9g/pub?gid=0&single=true&output=csv';
        const URL_CONFIG_CSV = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vQVyX2rRqNeaqc1WUcVnyIc_C4RfRtBAv19Btc9BahBNQ5vd6wXdFBnqhkPugUvQTZ8yXVLDUVnxf9g/pub?gid=833011405&single=true&output=csv';

        // --- DATOS DE PRUEBA (MOCK DATA) ---
        const MOCK_DATOS_PROPIEDADES = [
            { cp: '28001', barrio: 'Salamanca', zona: 'Recoletos', precio_m2: 8500 }
        ];

        const MOCK_CONFIGURACION = [
            { clave: 'balcon_terraza', valor: 5, tipo_operacion: 'porcentaje' },
            { clave: 'exterior', valor: 5, tipo_operacion: 'porcentaje' },
            { clave: 'interior', valor: -10, tipo_operacion: 'porcentaje' },
            { clave: 'bajo', valor: -10, tipo_operacion: 'porcentaje' },
            { clave: 'atico', valor: 15, tipo_operacion: 'porcentaje' },
            { clave: 'estado_reformar', valor: -20, tipo_operacion: 'porcentaje' },
            { clave: 'estado_reformado', valor: 10, tipo_operacion: 'porcentaje' },
            { clave: 'estado_entrar', valor: 0, tipo_operacion: 'porcentaje' },
            { clave: 'sin_ascensor_piso_1', valor: -5, tipo_operacion: 'porcentaje' },
            { clave: 'sin_ascensor_piso_2', valor: -10, tipo_operacion: 'porcentaje' },
            { clave: 'sin_ascensor_piso_7+', valor: -35, tipo_operacion: 'porcentaje' },
        ];

        // Estado Global
        let marketData = [];
        let configRules = [];
        let currentCalculation = null; // Almacena el resultado temporalmente

        // Referencias al DOM
        const els = {
            cp: document.getElementById('input-cp'),
            barrio: document.getElementById('select-barrio'),
            zona: document.getElementById('select-zona'),
            surface: document.getElementById('input-surface'),
            orientation: document.getElementById('select-orientation'),
            state: document.getElementById('select-state'),
            elevator: document.getElementById('select-elevator'),
            floor: document.getElementById('select-floor'),
            floorContainer: document.getElementById('container-floor'),
            checkBalcony: document.getElementById('check-balcony'),
            checkPenthouse: document.getElementById('check-penthouse'),
            penthouseContainer: document.getElementById('container-penthouse'),
            checkGround: document.getElementById('check-ground'),
            groundContainer: document.getElementById('container-ground'),

            // Botones y Paneles
            btnCalculate: document.getElementById('btn-calculate'),
            resultPlaceholder: document.getElementById('result-placeholder'),
            resultContent: document.getElementById('result-content'),

            // Contacto
            contactContainer: document.getElementById('contact-form-container'),
            inputEmail: document.getElementById('input-email'),
            inputPhone: document.getElementById('input-phone'),
            checkPrivacy: document.getElementById('check-privacy'),
            checkCommercial: document.getElementById('check-commercial'),
            btnSendLead: document.getElementById('btn-send-lead'),
            previewPriceRange: document.getElementById('preview-price-range'),
            sendErrorMsg: document.getElementById('send-error-msg'),

            // Resultados
            priceMin: document.getElementById('price-min'),
            priceMax: document.getElementById('price-max'),
            modifiersList: document.getElementById('modifiers-list')
        };

        // --- INICIALIZACIÓN ---
        async function initData() {
            try {
                const [dataRaw, configRaw] = await Promise.all([
                    fetchCSV(URL_DATOS_CSV, MOCK_DATOS_PROPIEDADES, 'Datos'),
                    fetchCSV(URL_CONFIG_CSV, MOCK_CONFIGURACION, 'Config')
                ]);
                marketData = dataRaw;
                configRules = configRaw;
            } catch (e) {
                console.error("Error cargando datos", e);
                marketData = MOCK_DATOS_PROPIEDADES;
                configRules = MOCK_CONFIGURACION;
            }
        }

        function fetchCSV(url, mockFallback, type) {
            return new Promise((resolve) => {
                if (!url || url.includes('PON_AQUI') || url.length < 10) {
                    resolve(mockFallback);
                    return;
                }
                let finalUrl = url;
                if (url.includes('docs.google.com') && url.includes('/pubhtml')) {
                    finalUrl = url.replace('/pubhtml', '/pub');
                    finalUrl += finalUrl.includes('?') ? '&output=csv' : '?output=csv';
                }

                Papa.parse(finalUrl, {
                    download: true, header: true, dynamicTyping: true, skipEmptyLines: true,
                    complete: (results) => {
                        if (results.data && results.data.length > 0) {
                            const cleanData = results.data
                                .filter(row => row && Object.keys(row).length > 0)
                                .map(row => {
                                    const newRow = {};
                                    for (let key in row) {
                                        if (!key) continue;
                                        const cleanKey = key.trim().toLowerCase();
                                        let val = row[key];
                                        if (typeof val === 'string') val = val.trim();
                                        newRow[cleanKey] = val;
                                    }
                                    return newRow;
                                });
                            resolve(cleanData);
                        } else {
                            resolve(mockFallback);
                        }
                    },
                    error: (err) => resolve(mockFallback)
                });
            });
        }

        // --- LÓGICA DE EVENTOS ---
        function setupEventListeners() {
            // Filtros CP
            els.cp.addEventListener('input', (e) => {
                const val = e.target.value.trim();
                els.barrio.innerHTML = '<option value="">Seleccione...</option>';
                els.barrio.disabled = true;
                els.zona.innerHTML = '<option value="">Seleccione Barrio...</option>';
                els.zona.disabled = true;
                els.btnCalculate.disabled = true;

                if (val.length >= 4) {
                    const barrios = [...new Set(marketData
                        .filter(d => d && d.cp != null && String(d.cp).includes(val))
                        .map(d => d.barrio))];

                    if (barrios.length > 0) {
                        barrios.forEach(b => {
                            if (!b) return;
                            const opt = document.createElement('option');
                            opt.value = b;
                            opt.textContent = b;
                            els.barrio.appendChild(opt);
                        });
                        els.barrio.disabled = false;
                    }
                }
            });

            els.barrio.addEventListener('change', (e) => {
                const barrio = e.target.value;
                const cp = els.cp.value.trim();
                els.zona.innerHTML = '<option value="">Seleccione...</option>';
                els.zona.disabled = true;

                if (barrio) {
                    const zonas = [...new Set(marketData
                        .filter(d => d && d.cp != null && String(d.cp).includes(cp) && d.barrio === barrio)
                        .map(d => d.zona))];
                    if (zonas.length > 0) {
                        zonas.forEach(z => {
                            if (!z) return;
                            const opt = document.createElement('option');
                            opt.value = z;
                            opt.textContent = z;
                            els.zona.appendChild(opt);
                        });
                        els.zona.disabled = false;
                    }
                }
                validateCalculatorForm();
            });

            els.zona.addEventListener('change', validateCalculatorForm);
            els.surface.addEventListener('input', validateCalculatorForm);

            els.elevator.addEventListener('change', (e) => {
                const hasElevator = e.target.value === 'yes';
                if (hasElevator) {
                    els.floorContainer.className = 'hidden-animated';
                    els.penthouseContainer.className = 'visible-animated flex items-center space-x-3 cursor-pointer group select-none';
                    els.groundContainer.className = 'visible-animated flex items-center space-x-3 cursor-pointer group select-none';
                } else {
                    els.floorContainer.className = 'visible-animated flex flex-col gap-1 w-full';
                    els.penthouseContainer.className = 'hidden-animated';
                    els.checkPenthouse.checked = false;
                    els.groundContainer.className = 'hidden-animated';
                    els.checkGround.checked = false;
                }
            });

            els.checkGround.addEventListener('change', (e) => { if (e.target.checked) els.checkPenthouse.checked = false; });
            els.checkPenthouse.addEventListener('change', (e) => { if (e.target.checked) els.checkGround.checked = false; });

            // Validación formulario contacto
            const validateContact = () => {
                const emailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(els.inputEmail.value);
                const phoneValid = els.inputPhone.value.length > 6;
                const privacy = els.checkPrivacy.checked;
                const commercial = els.checkCommercial.checked;

                if (emailValid && phoneValid && privacy && commercial) {
                    els.btnSendLead.disabled = false;
                } else {
                    els.btnSendLead.disabled = true;
                }
            };

            els.inputEmail.addEventListener('input', validateContact);
            els.inputPhone.addEventListener('input', validateContact);
            els.checkPrivacy.addEventListener('change', validateContact);
            els.checkCommercial.addEventListener('change', validateContact);

            // ACCIONES PRINCIPALES
            els.btnCalculate.addEventListener('click', prepareCalculation);
            els.btnSendLead.addEventListener('click', sendEmailsAndShowResult);
        }

        function validateCalculatorForm() {
            const isValid = els.cp.value && els.barrio.value && els.zona.value && els.surface.value > 0;
            els.btnCalculate.disabled = !isValid;
        }

        // PASO 1: Calcular pero detenerse en el formulario de contacto
        function prepareCalculation() {
            const cp = els.cp.value;
            const barrio = els.barrio.value;
            const zona = els.zona.value;
            const surface = parseFloat(els.surface.value);

            const record = marketData.find(d =>
                d && d.cp != null && String(d.cp).includes(cp) && d.barrio === barrio && d.zona === zona
            );

            if (!record) { alert("Error: No se encontró precio para esta zona."); return; }

            let basePrice = record.precio_m2 * surface;
            let currentPrice = basePrice;
            let modifiersList = [];
            let modifiersText = [];

            const apply = (key, condition, labelOverride = null) => {
                if (!condition) return;
                const cleanKey = key.trim().toLowerCase();
                const rule = configRules.find(c => c && c.clave && String(c.clave).trim().toLowerCase() === cleanKey);
                if (rule) {
                    if (rule.tipo_operacion === 'porcentaje') currentPrice += basePrice * (rule.valor / 100);
                    else currentPrice += rule.valor;

                    const label = labelOverride || key.replace(/_/g, ' ');
                    modifiersList.push({ label: label });
                    modifiersText.push(label);
                }
            };

            // Aplicar reglas
            apply('balcon_terraza', els.checkBalcony.checked, 'Con Balcón/Terraza');
            apply('exterior', els.orientation.value === 'exterior', 'Exterior');
            apply('interior', els.orientation.value === 'interior', 'Interior');
            const isGround = (els.elevator.value === 'yes' && els.checkGround.checked) || (els.elevator.value === 'no' && parseInt(els.floor.value) === 0);
            apply('bajo', isGround, 'Es un Bajo');

            if (els.elevator.value === 'yes') {
                apply('atico', els.checkPenthouse.checked, 'Es un Ático');
            } else {
                const floor = parseInt(els.floor.value);
                if (floor >= 1 && floor <= 6) apply(`sin_ascensor_piso_${floor}`, true, `Sin ascensor (${floor}º)`);
                else if (floor >= 7) apply('sin_ascensor_piso_7+', true, 'Sin ascensor (7º+)');
            }

            apply('estado_reformar', els.state.value === 'reformar', 'A Reformar');
            apply('estado_reformado', els.state.value === 'reformado', 'Reformado');
            apply('estado_entrar', els.state.value === 'entrar', 'Entrar a vivir');

            const priceMin = currentPrice * 0.90;
            const priceMax = currentPrice * 1.10;
            const fmt = new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 });

            // Guardar cálculo en memoria
            currentCalculation = {
                min: fmt.format(priceMin),
                max: fmt.format(priceMax),
                rawMin: priceMin,
                rawMax: priceMax,
                modifiers: modifiersList,
                modifiersString: modifiersText.join(', ') || 'Estándar',
                cp, barrio, zona, surface
            };

            // Mostrar el formulario de contacto con el precio preview
            els.previewPriceRange.textContent = `${fmt.format(priceMin)} - ${fmt.format(priceMax)}`;
            
            // Ocultar placeholder y resultado previo (si existe)
            els.resultPlaceholder.classList.add('hidden');
            els.resultPlaceholder.style.display = 'none';
            els.resultContent.classList.add('hidden');
            els.resultContent.style.display = 'none';
            
            // Mostrar formulario de contacto
            els.contactContainer.classList.remove('hidden');
            els.contactContainer.style.display = 'flex';
        }

        // PASO 2: Enviar Emails y Mostrar Resultado Final
        async function sendEmailsAndShowResult() {
            const btn = els.btnSendLead;
            const originalText = btn.innerHTML;

            // UI Loading
            btn.disabled = true;
            btn.innerHTML = '<div class="loader"></div><span class="ml-2">Procesando...</span>';
            els.sendErrorMsg.classList.add('hidden');

            const params = {
                to_email: els.inputEmail.value,
                user_phone: els.inputPhone.value,
                cp: currentCalculation.cp,
                barrio: currentCalculation.barrio,
                zona: currentCalculation.zona,
                superficie: currentCalculation.surface,
                precio_min: currentCalculation.min,
                precio_max: currentCalculation.max,
                caracteristicas: currentCalculation.modifiersString,
                date: new Date().toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }),
                reply_to: els.inputEmail.value
            };

            try {
                // NUEVO: Envío mediante PHP al Controlador
                // Usamos ruta relativa "tasacion/enviar" que el Router capturará
                const response = await fetch('tasacion/enviar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(params)
                });

                const result = await response.json();

                if (result.status === 'success') {
                    console.log("Emails enviados correctamente vía PHP");
                } else {
                    console.warn("Error enviando emails vía PHP:", result.message);
                }

                // Transición al resultado final
                els.contactContainer.classList.add('hidden');
                els.contactContainer.style.display = 'none';
                renderFinalResult();

            } catch (error) {
                console.error("Error intentando enviar email (continuando flujo):", error);
                // Si falla el email, mostramos el resultado igual para no perder al usuario
                els.contactContainer.classList.add('hidden');
                els.contactContainer.style.display = 'none';
                renderFinalResult();
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }

        function renderFinalResult() {
            els.priceMin.textContent = currentCalculation.min;
            els.priceMax.textContent = currentCalculation.max;

            els.modifiersList.innerHTML = '';
            if (currentCalculation.modifiers.length > 0) {
                const header = document.createElement('p');
                header.className = "text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 mt-2";
                header.textContent = "Características del inmueble";
                els.modifiersList.appendChild(header);

                currentCalculation.modifiers.forEach(mod => {
                    const row = document.createElement('div');
                    row.className = "flex items-center text-sm text-gray-600 mb-2";
                    row.innerHTML = `<i data-lucide="check-circle-2" class="w-4 h-4 text-indigo-500 mr-2"></i><span class="capitalize">${mod.label}</span>`;
                    els.modifiersList.appendChild(row);
                });
                lucide.createIcons();
            }

            els.resultContent.classList.remove('hidden');
            els.resultContent.style.display = 'flex';
        }

        document.addEventListener('DOMContentLoaded', () => {
            initData();
            setupEventListeners();
        });
    </script>

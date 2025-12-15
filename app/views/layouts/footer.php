    </main>

    <!-- 4. Footer -->
    <footer class="py-4 border-top bg-white mt-auto">
        <div class="container">
            <div class="row gy-3 align-items-center">
                <div class="col-12 col-md-4">
                    <ul class="list-unstyled small mb-0">
                        <li><a class="text-decoration-none text-secondary" href="/">Inicio</a></li>
                        <li><a class="text-decoration-none text-secondary" href="/contacto">Contacto</a></li>
                        <li><a class="text-decoration-none text-secondary" href="/quienes-somos">Quienes somos</a></li>
                    </ul>
                </div>
                <div class="col-12 col-md-4 text-md-center">
                    <p class="small mb-1">
                        C/ Ejemplo 123, Valencia<br>
                        96 000 00 00 - contacto@inmobiliaria.es
                    </p>
                    <p class="small mb-0">&copy; <?= date('Y') ?> Inmobiliaria</p>
                </div>
                <div class="col-12 col-md-4">
                    <nav class="d-flex justify-content-md-end justify-content-start mb-2">
                        <div class="d-flex flex-wrap gap-3 small">
                            <a class="text-decoration-none text-secondary" href="/legal/aviso-legal">Aviso Legal</a>
                            <span class="text-secondary">|</span>
                            <a class="text-decoration-none text-secondary" href="/legal/privacidad">Privacidad</a>
                            <span class="text-secondary">|</span>
                            <a class="text-decoration-none text-secondary" href="/legal/cookies">Cookies</a>
                        </div>
                    </nav>
                    <ul class="list-inline mb-0 d-flex justify-content-md-end justify-content-start gap-3 small">
                        <li class="list-inline-item mb-0"><a class="text-decoration-none text-secondary" href="https://www.linkedin.com" target="_blank" rel="noopener noreferrer">LinkedIn</a></li>
                        <li class="list-inline-item mb-0"><a class="text-decoration-none text-secondary" href="https://www.instagram.com" target="_blank" rel="noopener noreferrer">Instagram</a></li>
                        <li class="list-inline-item mb-0"><a class="text-decoration-none text-secondary" href="https://www.youtube.com" target="_blank" rel="noopener noreferrer">YouTube</a></li>
                        <li class="list-inline-item mb-0"><a class="text-decoration-none text-secondary" href="https://www.tiktok.com" target="_blank" rel="noopener noreferrer">TikTok</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <div id="cookieBanner" class="bg-dark text-white py-3 d-none" style="position: fixed; bottom: 0; left: 0; width: 100%; z-index: 1080;">
        <div class="container d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <p class="mb-0">Utilizamos cookies propias y de terceros para mejorar la experiencia. &#191;Aceptas?</p>
            <div class="d-flex gap-2">
                <button id="cookieAccept" type="button" class="btn text-white" style="background-color: #004B87; border-color: #004B87;">Aceptar</button>
                <button id="cookieReject" type="button" class="btn btn-secondary text-white">Rechazar</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            const banner = document.getElementById('cookieBanner');
            if (!banner) return;

            const acceptBtn = document.getElementById('cookieAccept');
            const rejectBtn = document.getElementById('cookieReject');
            const hideBanner = () => banner.classList.add('d-none');

            let consent = null;
            try {
                consent = window.localStorage.getItem('cookie_consent');
            } catch (error) {
                consent = null;
            }

            if (!consent) {
                banner.classList.remove('d-none');
            }

            const setConsent = (value) => {
                try {
                    window.localStorage.setItem('cookie_consent', value);
                } catch (error) {
                    // Si localStorage no esta disponible (modo privado), no bloqueamos la navegacion
                }
                hideBanner();
            };

            acceptBtn?.addEventListener('click', () => setConsent('accepted'));
            rejectBtn?.addEventListener('click', () => setConsent('rejected'));
        })();
    </script>
</body>
</html>

# Checklist de workflow (desarrollo + documentación)

Este documento define el flujo estándar para implementar cambios en el proyecto y dejarlo correctamente documentado y versionado.

---

## 0) Principio de oro
Cada cambio debe dejar:
- Código estable (sin errores fatales ni fugas de info)
- Documentación mínima actualizada
- Commits claros y rastreables

---

## 1) Antes de empezar (siempre)
- [ ] `git status` limpio o, como mínimo, entender qué cambios arrastras.
- [ ] Identificar el tipo de tarea:
  - Feature (nueva vista / nueva funcionalidad)
  - Bugfix / Hotfix
  - Refactor (sin cambio funcional)
  - Base de datos (migración / seed / ajuste)
  - UI/Estilos/Assets
  - Email / plantillas
- [ ] Definir el criterio de “he terminado” (2-5 checks de prueba manual).

---

## 2) Durante el cambio (calidad mínima)
- [ ] Validar y sanitizar inputs si hay formularios.
- [ ] Manejar errores con UX decente:
  - No mostrar stack traces ni paths internos
  - Mensajes legibles para usuario
- [ ] Tener en cuenta Linux (case-sensitive en nombres de carpetas/archivos).
- [ ] Evitar duplicidad y mantener consistencia:
  - Reutilizar layouts/partials si aplica
  - Seguir el patrón existente del proyecto (MVC)

---

## 3) Checklist por tipo de tarea

### A) Nueva página pública (ej. “Quiénes somos” desde footer)
Código:
- [ ] Ruta pública definida
- [ ] Controller: acción para renderizar la página
- [ ] Vista creada en `app/views/...`
- [ ] Enlace añadido en `app/views/layouts/footer.php` (o partial equivalente)
- [ ] (Opcional) Estilos en `public/assets/css/...` si hace falta

Documentación:
- [ ] `docs/avances.md`: qué se hizo + archivos tocados + prueba manual
- [ ] `docs/memoria_proyecto.md`: solo si añade sección relevante (portal público / UX / estructura)

Pruebas mínimas:
- [ ] Navegación desde footer OK
- [ ] Responsive (desktop/móvil)
- [ ] No rompe layout global

---

### B) Bugfix / Hotfix
Código:
- [ ] Reproducible: pasos claros del bug
- [ ] Fix aplicado en el punto correcto (controller/service/model)
- [ ] Manejo correcto de excepciones / errores (sin fatal)
- [ ] Prueba de regresión (caso bug + caso válido)

Documentación:
- [ ] `docs/avances.md`: Problema → Causa raíz → Solución → Verificación
- [ ] `docs/memoria_proyecto.md`: si afecta a seguridad, autenticación, uploads, validación, etc.

---

### C) Cambios en Base de Datos
Código/SQL:
- [ ] Script incremental en `database/migrations/` (no machacar dumps a lo loco)
- [ ] Modelos/Servicios actualizados
- [ ] Ajustar validaciones si cambia esquema

Documentación:
- [ ] `docs/avances.md`: script aplicado + motivo + verificación
- [ ] `docs/base_datos.md` (si cambia el modelo de datos)
- [ ] `docs/memoria_proyecto.md` si cambia el diseño de datos de forma relevante

Pruebas mínimas:
- [ ] Migración aplicada en local sin errores
- [ ] Funcionalidad afectada funcionando

---

### D) Formularios + Emails (contacto/tasación/etc.)
Código:
- [ ] Vista del formulario
- [ ] Validación server-side + CSRF (si aplica)
- [ ] Templates en `app/views/emails/`
- [ ] Servicio de envío (reutilizar si existe)

Documentación:
- [ ] `docs/avances.md`
- [ ] `docs/memoria_proyecto.md` si añade funcionalidad importante
- [ ] `docs/CONFIGURAR_EMAIL.txt` si cambian credenciales/infra/servidor

Pruebas mínimas:
- [ ] Envío a oficina OK
- [ ] Autorespuesta al cliente OK
- [ ] Manejo de errores de envío OK

---

## 4) Documentación: qué actualizar y cuándo
- `docs/avances.md` (SIEMPRE):
  - Qué se hizo
  - Archivos tocados
  - Pruebas manuales
- `docs/memoria_proyecto.md` (cuando aporte valor al tribunal):
  - Decisiones técnicas
  - Arquitectura / seguridad / despliegue
  - Cambios relevantes del sistema
- `docs/base_datos.md` (cuando cambie el esquema o relaciones)
- `README.md` (si cambia instalación/arranque/configuración)

---

## 5) Commits (convención práctica)
Recomendación: separar por intención
- `feat(...)` → nueva funcionalidad
- `fix(...)` → corrección de bug
- `docs(...)` → documentación
- `sql(...)` → cambios de BBDD (opcional)
- `chore(...)` → mantenimiento (gitignore, scripts, etc.)

Checklist:
- [ ] Commit de código
- [ ] Commit de docs (si se han tocado)
- [ ] `git push`

---

## 6) Plantilla de verificación manual (pegar en avances)
- Pasos:
  1) ...
  2) ...
  3) ...
- Resultado esperado:
  - ...

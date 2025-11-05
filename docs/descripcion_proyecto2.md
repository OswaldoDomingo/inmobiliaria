
# INMOBILIARIA — Descripción del Proyecto
Autor: Oswaldo Domingo  
Fecha: 3 de noviembre de 2025

---

## 1. Introducción y Contexto
En el sector inmobiliario actual, muchas agencias pequeñas se están desvinculando de grandes firmas por limitaciones territoriales. Estas agencias independientes necesitan expandirse a nuevos lugares para captar propiedades, ya que sus mercados locales pueden estar saturados. A menudo carecen de herramientas digitales propias y asequibles que les den visibilidad y control sobre su cartera.

**Oportunidad:** construir un portal propio, sencillo y económico, que centralice el catálogo de inmuebles y permita su gestión interna sin depender de plataformas de terceros.

---

## 2. Objetivos y Solución Propuesta

### 2.1 Objetivo General
Desarrollar el portal **“Inmobiliaria”**, una plataforma web MVC que permita la **consulta pública** de propiedades y la **gestión privada** (CRUD) por parte del personal autorizado.

### 2.2 Objetivos Específicos
- Proveer una **interfaz pública** intuitiva para buscar, filtrar y consultar propiedades.
- Implementar un **panel de control privado** para crear, editar, despublicar y eliminar inmuebles.
- Incorporar **autenticación** y **autorización por roles** (admin/gestor).
- Registrar y responder **consultas** enviadas desde formularios públicos.
- (Opcional) Gestión básica de usuarios por un rol administrador.

### 2.3 Indicadores de Éxito (KPIs)
- Tiempo de carga de la página de listado ≤ **2 s** con 50+ propiedades.
- Búsqueda con filtros respondiendo en ≤ **500 ms** (en servidor local).
- Flujo de creación/edición de propiedad completado en **≤ 2 minutos**.
- **0** vulnerabilidades críticas en revisión de seguridad básica (inyección SQL, XSS, CSRF).

---

## 3. Alcance y Requisitos Principales

### 3.1 Interfaz Pública (Frontend)
- **RF 1.1 Navegación:** Inicio, Quiénes Somos, Contacto.
- **RF 1.2 Listado:** galería/lista de propiedades con paginación.
- **RF 1.3 Detalle:** ficha con imágenes, descripción, características, precio, ubicación.
- **RF 1.4 Búsqueda básica:** por ciudad o tipo.
- **RF 1.5 Filtros avanzados:** operación (venta/alquiler), tipo (piso/casa/local), rango de precios, habitaciones, (opcional: m², ascensor, terraza, etc.).
- **RF 1.6 Contacto general.**
- **RF 1.7 Contacto por propiedad.**

### 3.2 Interfaz Privada (Backend)
- **RF 2.1 Autenticación segura:** iniciar/cerrar sesión.
- **RF 2.2 Dashboard:** últimas propiedades, mensajes recibidos, estado general.
- **RF 2.3 CRUD de propiedades:** crear/leer/actualizar/eliminar, subir imágenes, publicar/despublicar.
- **RF 2.4 Estado de propiedad:** disponible, reservada, vendida/alquilada.
- **RF 2.5 Gestión de mensajes:** ver/responder consultas.
- **RF 2.6 Gestión de usuarios (solo Admin, opcional):** alta/baja/edición y asignación de roles.

### 3.3 Fuera de Alcance (versión inicial)
- Pasarela de pago.
- Integraciones con portales externos (Idealista, Fotocasa…).
- Multidioma.
- SEO avanzado.

---

## 4. Planificación del Proyecto (Fases)
- **Fase I – Diseño y definición:** investigación, UX/UI (Figma), arquitectura.
- **Fase II – Datos y MVC:** modelo de datos, script SQL, estructura de carpetas, conexión PDO, router.
- **Fase III – Funcionalidades:** autenticación, roles, CRUD propiedades, parte pública.
- **Fase IV – Calidad:** validaciones, seguridad (CSRF, XSS, SQLi), pruebas, documentación.
- **Fase V – Defensa:** presentación y demo.

---

## 5. Entregables
- **Memoria** (documentación funcional y técnica).
- **Diagrama Entidad–Relación (ER).**
- **Script SQL** de creación de la BD.
- **Código fuente MVC** (PHP + MySQL + HTML/CSS/JS).
- **Presentación** (para defensa).

---

## 6. Requisitos No Funcionales (RNF)
- **RNF-01 Rendimiento:** Tiempo de respuesta del backend < 500 ms en operaciones CRUD típicas.
- **RNF-02 Seguridad:** Autenticación mediante sesiones seguras; uso de sentencias preparadas; tokens CSRF.
- **RNF-03 Mantenibilidad:** Código organizado en MVC, PSR-12 (estilo PHP) cuando sea posible.
- **RNF-04 Usabilidad:** Formularios accesibles (labels, feedback de error), diseño responsive (mobile-first).
- **RNF-05 Portabilidad:** Funciona en entorno LAMP básico (Apache/Nginx, PHP 8.x, MySQL/MariaDB).

---

## 7. Arquitectura y Tecnologías
- **Arquitectura:** MVC casero (sin frameworks).
- **Backend:** PHP 8.x (PDO).
- **Base de datos:** MySQL/MariaDB.
- **Frontend:** HTML5, CSS3 (puede usarse Bootstrap), JavaScript.
- **Servidor web:** Apache (mod_rewrite) o Nginx.
- **Control de versiones:** Git + GitHub.

**Estructura inicial de carpetas (propuesta):**
```
/app
  /controllers
  /models
  /views
/config
  database.php
/public
  index.php
  /assets (css, js, img)
/storage (subidas de imágenes)
/vendor (si se usa composer en el futuro)
README.md
```

---

## 8. Modelo de Datos (borrador)
**Tablas principales (mínimas):**
- `users` (id, nombre, email, password_hash, rol, created_at)
- `properties` (id, titulo, descripcion, precio, tipo_operacion, tipo_propiedad, habitaciones, baños, m2, direccion, ciudad, cp, lat, lng, estado, publicada, created_at, updated_at)
- `images` (id, property_id, ruta, orden)
- `messages` (id, nombre, email, telefono, property_id?, mensaje, created_at)

**Relaciones clave:**
- `properties (1) — (N) images`
- `properties (0..1) — (N) messages` (mensajes pueden referirse a una propiedad concreta o ser generales)
- `users` (admin/gestor) para autenticación y permisos

---

## 9. Casos de Uso Principales (Historias + Criterios de Aceptación)

### CU-01: Búsqueda y filtrado de propiedades (público)
**Como** visitante, **quiero** buscar propiedades por ciudad, tipo y precio **para** encontrar las que encajan conmigo.  
**Criterios de aceptación:**
- Dado que estoy en el listado, cuando introduzco filtros y aplico, **veo** solo resultados coincidentes.
- Si no hay resultados, **se muestra** un mensaje “No se han encontrado propiedades”.
- Los filtros persisten al cambiar de página de resultados (paginación).

### CU-02: Ver ficha de propiedad (público)
**Como** visitante, **quiero** ver una ficha detallada **para** decidir si contactar.  
**Criterios de aceptación:**
- La ficha muestra título, precio, descripción, características y galería de imágenes.
- Existe un botón “Contactar por esta propiedad” que abre el formulario con el ID preseleccionado.

### CU-03: Crear propiedad (admin/gestor)
**Como** gestor, **quiero** dar de alta una propiedad **para** publicarla en el portal.  
**Criterios de aceptación:**
- Formulario con validación (campos obligatorios: título, precio, ciudad, tipo, operación).
- Subida de imágenes (extensiones permitidas, límite de tamaño).
- Al guardar, la propiedad aparece en el listado interno con estado “publicada” (si se marca).

### CU-04: Autenticación y roles
**Como** usuario registrado, **quiero** iniciar sesión **para** acceder al panel.  
**Criterios de aceptación:**
- Login requiere email y contraseña válidos.
- Gestor no puede acceder a páginas exclusivas de admin (gestión de usuarios).
- Sesión se invalida al cerrar sesión o expirar.

---

## 10. Seguridad (mínimos razonables)
- **SQL Injection:** consultas preparadas (PDO).
- **XSS:** escape de salida, validación y sanitización.
- **CSRF:** token oculto en formularios sensibles.
- **Sesiones:** `session_regenerate_id()` en login y cookies con `httponly`.
- **Subidas:** validar tipo/tamaño, renombrar archivos, directorio fuera del docroot si es posible.

---

## 11. Plan de Pruebas (resumen)
- **Unitarias (si procede):** funciones auxiliares (validaciones).
- **Funcionales:** login, CRUD, filtros, formularios.
- **Integración:** interacción entre modelos y controladores.
- **UI manual:** pruebas de usabilidad y responsive.
- **Checklist de aceptación:** cumplir los CU-01…CU-04.

---

## 12. Riesgos y Mitigaciones
- **Falta de tiempo:** plan por hitos semanales, cortes verticales (entregables funcionales).
- **Complejidad del CRUD + imágenes:** empezar con 1 imagen obligatoria, ampliar a galería después.
- **Seguridad básica mal resuelta:** plantilla de control (CSRF, SQLi, XSS) y revisión antes de entrega.
- **Datos reales sensibles:** usar imágenes y datos de ejemplo durante desarrollo.

---

## 13. Integraciones Futuras (no incluidas en esta versión)
- **Google Calendar API:** crear eventos/tareas automáticamente desde el panel (p. ej., visitas programadas).
- **Google Maps API:** geocodificación y mapas (lat/lng a partir de dirección; mapa en ficha de propiedad).
- **Integración CRM externo:** importar/exportar inventario (CSV/API).

> Estas integraciones se documentarán en un apartado “Hoja de Ruta (Futuras mejoras)” de la memoria, indicando objetivos, endpoints, costes y permisos necesarios.

---

## 14. Glosario breve
- **CRUD:** Create, Read, Update, Delete (altas, consultas, ediciones, bajas).
- **MVC:** Modelo–Vista–Controlador.
- **ER:** Entidad–Relación.
- **CSRF/XSS/SQLi:** vectores de ataque web comunes.

---

## 15. Próximos pasos inmediatos
1. Terminar mockups en Figma (monitor/tablet/móvil) y definir tokens de diseño (colores, tipografías, espaciado).
2. Definir ER definitivo (añadiendo tablas auxiliares si fueran necesarias, p. ej., `property_features`).
3. Generar `CREATE TABLE` inicial y preparar conexión PDO.
4. Montar router mínimo y “Hola Mundo” con controlador y vista.

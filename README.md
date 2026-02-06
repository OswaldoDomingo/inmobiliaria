# 🏠 Proyecto Inmobiliaria

## 📋 Datos del proyecto

* **Nombre del alumno:** Oswaldo Domingo Pérez
* **Ciclo formativo:** Desarrollo de Aplicaciones Web (DAW)
* **Centro:** IES Abastos — Valencia
* **Tutor del proyecto:** *(Por asignar)*
* **Fecha de inicio:** 30 de octubre de 2025
* **Entrega prevista:** 12 de enero de 2026

---

## 📚 Descripción general

El **Proyecto Inmobiliaria** consiste en el desarrollo de un portal web inmobiliario desde cero, sin el uso de frameworks externos.  
El objetivo es construir una aplicación web funcional y profesional que permita mostrar, gestionar y publicar propiedades en venta y alquiler mediante una arquitectura clara y mantenible.

Este proyecto incorpora:

- **Frontend** (HTML, CSS, JavaScript)  
- **Backend en PHP orientado a objetos (POO)**  
- **Arquitectura MVC propia**  
- **Base de datos MySQL/MariaDB**  
- **Documentación completa conforme a las fases del módulo de Proyecto del IES Abastos**

---

## 🌟 Objetivos

* Desarrollar un **portal inmobiliario completo y funcional**.  
* Aplicar los principios de la **arquitectura MVC (Modelo–Vista–Controlador)**.  
* Integrar y gestionar datos mediante **MySQL/MariaDB**.  
* Crear una interfaz clara, moderna y **responsive**, apta para móvil, tablet y escritorio.  
* Documentar todas las fases del proyecto según las directrices del IES Abastos.

---

## 🧩 Alcance del proyecto

### 🔹 Frontend
* Home (landing page)  
* Sección de inmuebles en venta  
* Sección de alquiler  
* Ficha individual de propiedad  
* Página “Vende tu piso”  
* Formulario de contacto  
* Diseño responsive completo (móvil/tablet/desktop)

### 🔹 Backend
* Panel administrativo para gestión de propiedades  
* Gestión de usuarios  
* Validación de formularios  
* Enrutamiento interno mediante controladores  
* Generación dinámica de vistas

### 🔹 Base de datos
* Sistema relacional en MySQL/MariaDB  
* Tablas para:
  - Usuarios
  - Propiedades
  - Clientes
  - Operaciones (venta/alquiler)  
* Relaciones y claves foráneas para garantizar integridad

---

## 🔧 Tecnologías utilizadas

| Tipo                 | Tecnología                         |
| -------------------- | ---------------------------------- |
| Lenguaje backend     | PHP 8+                             |
| Base de datos        | MySQL / MariaDB                    |
| Frontend             | HTML5, CSS3, JavaScript            |
| Arquitectura         | MVC (Modelo - Vista - Controlador) |
| Control de versiones | Git + GitHub                       |

---

## 🚀 Instalación y Despliegue en Servidor

Para desplegar la aplicación en un entorno de producción o servidor de pruebas, sigue estos pasos.

### 1) Requisitos del sistema
- **PHP:** 8.0 o superior
- **Base de datos:** MySQL o MariaDB
- **Servidor web:** Apache (con `mod_rewrite`) o Nginx
- **Extensiones PHP recomendadas:** `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `json`

---

### 2) Configuración de la Base de Datos
1. Crea una base de datos (ej.: `inmobiliaria_db`).
2. Importa la base de datos:
   - Estructura: `database/inmobiliaria_db_estructura.sql`
   - Datos: `database/inmobiliaria_db_datos.sql`

---

### 3) Configuración del entorno (.env)
Por seguridad, las credenciales sensibles no se incluyen en el repositorio.

1. Usa la plantilla **de la raíz**: `.env.example`
2. Copia la plantilla a `config/.env`
3. Edita `config/.env` con las credenciales reales:

```ini
DB_HOST=localhost
DB_NAME=nombre_tu_base_datos
DB_USER=usuario_mysql
DB_PASS=contraseña_mysql

APP_URL=https://midominio.com
APP_ENV=production
```

> **Nota:** No subas `config/.env` al repositorio.

### 4) Publicación en servidor web (Apache)

✅ **Recomendado:** el `DocumentRoot` debe apuntar a `public/` (evita exponer `config/`, `app/`, etc.)

**Ejemplo de VirtualHost:**

```apache
<VirtualHost *:80>
    ServerName midominio.com
    DocumentRoot "/var/www/inmobiliaria/public"

    <Directory "/var/www/inmobiliaria/public">
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/inmobiliaria_error.log
    CustomLog ${APACHE_LOG_DIR}/inmobiliaria_access.log combined
</VirtualHost>
```

### 5) (Opcional) Publicación en Nginx

**Ejemplo básico:**

```nginx
server {
    server_name midominio.com;
    root /var/www/inmobiliaria/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock; # ajusta versión/socket
    }

    # Bloquear acceso a archivos ocultos
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 6) Permisos de carpetas

Asegúrate de que el usuario del servidor web (ej.: `www-data`) tenga permisos de escritura en:

* `logs/` (logs de autenticación/correo)
* `storage/logs/` (si se usa para logs adicionales)
* `public/uploads/` (imágenes de inmuebles y perfiles)

**Ejemplo (Linux):**

```bash
sudo chown -R www-data:www-data logs storage/logs public/uploads
sudo find logs storage/logs public/uploads -type d -exec chmod 775 {} \;
sudo find logs storage/logs public/uploads -type f -exec chmod 664 {} \;
```

### 7) Hardening recomendado (producción)

* Protege o elimina `public/test/` (contiene diagnósticos y páginas de debug).
* Verifica que la reescritura hacia `public/index.php` funciona y que no hay listado de directorios.

---

## 🗁️ Estructura actual del proyecto

## 🗁️ Estructura actual del proyecto

```bash
/inmobiliaria/
├── app/                         # Código de la aplicación (MVC)
│   ├── Autoloader.php           # Carga automática de clases
│   ├── Controllers/             # Controladores (Auth, Clientes, Inmuebles, Demandas, Tasación, etc.)
│   ├── Core/                    # Núcleo del framework (Router, Config, Database, Env, CSRF...)
│   ├── Lib/                     # Librerías auxiliares (PHPMailer, SimpleSMTP, etc.)
│   ├── Models/                  # Modelos de dominio (User, Cliente, Inmueble, Demanda...)
│   ├── Services/                # Servicios de aplicación (MailService, ...)
│   └── views/                   # Vistas (admin, auth, propiedades, tasación, legal, layouts, partials...)
│
├── config/                      # Configuración centralizada (.env, BD, rutas...)
├── database/                    # Esquema y migraciones de la base de datos
│   ├── migrations/              # Scripts incrementales (CRM, imágenes, etc.)
│   ├── p261985_inmobiliaria.sql # Dump de referencia
│   └── schema.sql               # Esquema general
│
├── docs/                        # Documentación del proyecto
│   ├── memoria_proyecto.md      # Memoria oficial para el módulo de Proyecto
│   ├── presentacion_tribunal.md # Guion de la defensa
│   ├── documentacion_*.md       # Módulos (BD, inmuebles, demandas, tasación, etc.)
│   └── fct/ ...                 # Documentación específica de las FCT (empresa, tasador widget, evidencias)
│
├── logs/                        # Logs de aplicación (auth.log, mail.log, ...)
├── public/                      # DocumentRoot (única carpeta accesible desde la web)
│   ├── index.php                # Front controller
│   ├── assets/                  # Recursos estáticos (CSS, JS, imágenes)
│   ├── uploads/                 # Archivos subidos (inmuebles, perfiles)
│   └── test/ ...                # Scripts de diagnóstico (solo en desarrollo)
│
├── storage/                     # Carpeta reservada para datos temporales / futuros backups
├── tests/ ...                   # Scripts de prueba (verificación de esquema, mocks, etc.)
├── index.php                    # Redirección / bootstrap mínimo hacia /public (opcional)
└── README.md                    # Documentación principal del repositorio


---

## 🎨 Diseño en Figma

El diseño visual del portal, incluyendo versiones para móvil (393×849), tablet (1280×800) y escritorio (1440×1024), se desarrolla en **Figma** siguiendo una línea moderna, limpia y coherente.

🔗 **Enlace al prototipo en Figma:**  
https://www.figma.com/design/69B6hKjCAikIMAUKihlpLt/Inmobiliaria?node-id=0-1

> El prototipo está en modo lectura para preservar la integridad del diseño y evitar modificaciones no autorizadas.

---

## 🚀 Funcionalidades Implementadas

El proyecto se encuentra en una fase avanzada de desarrollo, con los módulos críticos operativos:

### 🔐 Núcleo y Seguridad
- **Router MVC propio:** Gestión de rutas limpias, parámetros y métodos HTTP.
- **Seguridad:** Protección CSRF, sanitización de inputs, hash de contraseñas y prevención de fuerza bruta.
- **Configuración:** Sistema robusto basado en variables de entorno (`.env`) nativo.

### 🏢 Gestión e Intranet (Backoffice)
- **Autenticación:** Login seguro, gestión de sesiones y roles (Admin, Coordinador, Comercial).
- **Usuarios:** Gestión de empleados con fotos de perfil y control de accesos.
- **CRM Clientes:** Cartera de clientes, asignación a comerciales y ficha detallada.
- **Inmuebles:** CRUD completo, galería de imágenes, asignación de propietarios y comerciales.
- **Demandas:** Registro de preferencias de búsqueda asociadas a clientes (operación, precio, zonas, características…), base para cruce futuro con inmuebles.

### 📏 Tasador Online
- **Formulario avanzado:** Herramienta de valoración integrada en el portal.
- **Envío de informes:** Generación y envío de datos por correo a la agencia y al cliente.
- **Seguridad:** Validación y sanitización exhaustiva para evitar inyecciones.
- **Versión FCT (widget independiente):** Además del módulo integrado en el MVC, existe una versión del tasador desarrollada específicamente para la empresa de prácticas e incrustada en su CRM Inmovilla. En este entorno, sin backend propio, la lógica se ejecuta íntegramente en JavaScript, leyendo datos de mercado desde Google Sheets (CSV) y enviando correos mediante EmailJS.


### 🌍 Portal Público
- **Landing Page y Buscador:** Página de inicio con destacados.
- **Catálogo de Propiedades:** Listado paginado y ficha de detalle.
- **Legal:** Módulo de cumplimiento RGPD (Cookies, Privacidad, Aviso Legal).

---

## 🦯 Estado actual del proyecto

Actualmente el proyecto cuenta con:

- ✔ Arquitectura MVC sólida y segura.
- ✔ Backend 100% funcional (Auth, CRM, CMS Inmuebles).
- ✔ Frontend público integrado.
- ✔ Base de datos optimizada y relacional.

El proyecto se encuentra en fase de **pulido final y ampliación de funcionalidades públicas**.

---

## 📄 Documentación

Toda la documentación del proyecto (memoria, anexos, diagramas, avances diarios…) se encuentra en `docs/`.
Toda la información detallada de la FCT (empresa, contexto, tareas, evidencias y presentación) se encuentra en la carpeta: `docs/fct/`.

El proyecto sigue las fases establecidas por el IES Abastos para el módulo de Proyecto:

1️⃣ **Fase I:** Identificación y análisis de necesidades  
2️⃣ **Fase II:** Diseño del proyecto  
3️⃣ **Fase III:** Desarrollo  
4️⃣ **Fase IV:** Control y evaluación  
5️⃣ **Fase V:** Defensa del proyecto  

---

## 🧠 Filosofía de trabajo

- Metodología incremental en fases  
- Control de versiones con Git  
- Commits documentados y estructurados  
- Uso de `avances.md` como diario de desarrollo  
- Documentación continua y actualizada  

---

## 👨‍💻 Autor

**Oswaldo Domingo Pérez**  
📧 [oswaldo.domingo@gmail.com](mailto:oswaldo.domingo@gmail.com)  
🌐 https://github.com/OswaldoDomingo/inmobiliaria
🌐 https://inmobiliaria.oswaldo.dev

---

© 2025 Oswaldo Domingo Pérez — *Proyecto Fin de Ciclo DAW (IES Abastos, Valencia)*

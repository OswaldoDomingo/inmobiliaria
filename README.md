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

## 🛠 Puesta en marcha rápida

1. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/OswaldoDomingo/inmobiliaria.git
   cd inmobiliaria
   ```

2. **Base de datos:**
   - Crear una base de datos en MySQL/MariaDB.
   - Importar los scripts SQL ubicados en `database/`.

3. **Configuración del entorno:**
   - Copiar el archivo de ejemplo: `cp config/.env.example config/.env`
   - Editar `config/.env` con tus credenciales de base de datos.
   - Establecer `APP_ENV=local` para desarrollo.

4. **Servidor Web:**
   - Configurar VirtualHost en Apache apuntando a la carpeta `/public`.
   - Acceder a `http://inmobiliaria.loc/` (Portal) o `http://inmobiliaria.loc/login` (Admin).

---

## 🗁️ Estructura actual del proyecto

```bash
/inmobiliaria/
├── app/
│   ├── Controllers/     # Lógica de negocio y gestión de peticiones
│   ├── Core/            # Núcleo del framework (Router, Database, Env...)
│   ├── Lib/             # Librerías auxiliares (PDF, Utilidades...)
│   ├── Models/          # Acceso a datos y lógica de dominio
│   ├── Services/        # Servicios de aplicación (Email...)
│   └── views/           # Plantillas HTML/PHP (admin, auth, layouts, partials...)
│
├── config/              # Configuración centralizada (.env, constantes)
├── database/            # Scripts SQL, seeds, migraciones
├── public/              # DocumentRoot (assets, uploads, index.php)
├── logs/                # Logs y archivos de depuración
└── docs/                # Documentación del proyecto
```

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
Toda la información detallada de la FCT (empresa, contexto, tareas, evidencias y presentación) se encuentra en la carpeta: `docs/fct/`

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

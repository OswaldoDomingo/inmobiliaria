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

## 🗁️ Estructura actual del proyecto

```bash
/inmobiliaria/
├── app/
│   ├── controllers/     # Controladores de la aplicación
│   ├── core/            # Núcleo del MVC (Database, Autoloader, Router más adelante)
│   ├── models/          # Modelos y acceso a datos
│   └── views/           # Plantillas HTML/PHP
│
├── config/
│   ├── env.php          # Carga del archivo .env
│   ├── database.php     # Configuración de conexión
│   └── paths.php        # Constantes de rutas absolutas
│
├── public/
│   ├── index.php        # Punto de entrada del MVC
│   └── assets/
│       ├── css/
│       ├── js/
│       └── img/
│
├── storage/             # Logs, archivos temporales, etc.
│
├── docs/                # Documentación técnica y académica
│
├── .env                 # Variables de entorno (no se sube a GitHub)
└── README.md
```

---

## 🎨 Diseño en Figma

El diseño visual del portal, incluyendo versiones para móvil (393×849), tablet (1280×800) y escritorio (1440×1024), se desarrolla en **Figma** siguiendo una línea moderna, limpia y coherente.

🔗 **Enlace al prototipo en Figma:**  
https://www.figma.com/design/69B6hKjCAikIMAUKihlpLt/Inmobiliaria?node-id=0-1

> El prototipo está en modo lectura para preservar la integridad del diseño y evitar modificaciones no autorizadas.

---

## 🦯 Estado actual del proyecto

Actualmente el proyecto incluye:

- ✔ Estructura MVC inicial organizada  
- ✔ Sistema de configuración basado en `.env`  
- ✔ Archivos de configuración (`env.php`, `paths.php`, `database.php`)  
- ✔ Clase `Database` modernizada y completamente funcional  
- ✔ Punto de entrada (`public/index.php`) operativo  
- ✔ Conexión a la base de datos probada exitosamente  
- ✔ Documentación inicial (`avances.md` y `memoria.md`)  

El siguiente paso será implementar el **Router**, seguido de los primeros controladores y vistas.

---

## 📄 Documentación

Toda la documentación del proyecto (memoria, anexos, diagramas, avances diarios…) se encuentra en `docs/`.

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
📧 [oswaldodomingop@gmail.com](mailto:oswaldo.domingop@gmail.com)  
🌐 https://github.com/OswaldoDomingo/inmobiliaria
🌐 https://inmobiliaria.oswaldo.dev

---

© 2025 Oswaldo Domingo Pérez — *Proyecto Fin de Ciclo DAW (IES Abastos, Valencia)*

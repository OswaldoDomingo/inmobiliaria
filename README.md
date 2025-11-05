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
El objetivo es crear una aplicación web funcional y profesional que permita mostrar, gestionar y publicar propiedades en venta y en alquiler.

El proyecto se plantea como el desarrollo individual de un sistema completo de **frontend**, **backend** y **base de datos**, siguiendo la estructura y metodología propias de un entorno de desarrollo real.

---

## 🌟 Objetivos

* Desarrollar un **portal inmobiliario completo y funcional**.
* Aplicar los principios básicos de la **arquitectura MVC (Modelo–Vista–Controlador)**.
* Integrar una **base de datos MySQL/MariaDB** para la gestión de propiedades, clientes y operaciones.
* Implementar una interfaz clara, moderna y **responsive** mediante HTML, CSS y JavaScript.
* Documentar el proceso de desarrollo según las fases establecidas por el IES Abastos (identificación, diseño, desarrollo, control y defensa).

---

## 🧩 Alcance del proyecto

### 🔹 Frontend

* Páginas principales: Inicio, Acerca de, Propiedades en venta, Propiedades en alquiler, Vende tu piso.
* Formularios de contacto y alta de inmuebles.
* Diseño adaptable a distintos dispositivos (móvil, tablet, monitor).

### 🔹 Backend

* Gestión de propiedades y usuarios desde el panel administrativo.
* Validación de formularios y tratamiento de datos con PHP.
* Generación dinámica de vistas y comunicación con la base de datos.

### 🔹 Base de datos

* Diseño relacional en **MySQL/MariaDB**.
* Tablas relacionadas para propiedades, clientes, usuarios y operaciones.
  *(El diseño concreto se definirá más adelante.)*

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

## 🗁️ Estructura inicial del proyecto

```bash
/inmobiliaria/
├── /app/
│   ├── /models/          # Lógica de datos y consultas SQL
│   ├── /views/           # Plantillas HTML/PHP
│   └── /controllers/     # Controladores de flujo de la aplicación
│
├── /public/
│   ├── /css/             # Hojas de estilo
│   ├── /js/              # Scripts JavaScript
│   └── /images/          # Recursos gráficos
│
├── /config/              # Configuración general y conexión a BD
│
├── /docs/                # Documentación para el tribunal (memoria, anexos, etc.)
│
└── README.md
```

---

## 🎨 Diseño en Figma

El diseño visual y las maquetas responsivas del portal inmobiliario se están desarrollando en **Figma**.
Incluyen las versiones para **móvil (393×849)**, **tablet (1280×800)** y **monitor (1440×1024)**, siguiendo la identidad visual definida para el proyecto.

💎 **Enlace al diseño:**
[🔗 Ver prototipo en Figma](https://www.figma.com/design/69B6hKjCAikIMAUKihlpLt/Inmobiliaria?node-id=0-1&t=4vVK0OMVWpbpNsSG-1)

> ⚠️ El enlace se mantiene en modo *solo lectura* para garantizar la integridad del diseño.

---

## 🦯 Estado actual

Este repositorio contiene la **estructura base del proyecto** y el archivo `README.md` inicial.
A medida que avance el desarrollo, se añadirán los componentes del modelo, las vistas, los controladores y la base de datos correspondiente.

---

## 📄 Documentación

Toda la documentación técnica y académica se incluirá en la carpeta `/docs/`, conforme a las **instrucciones del IES Abastos** para la presentación del módulo de Proyecto.
Las fases se seguirán según lo descrito en el documento oficial:

> * Fase I: Identificación y análisis de necesidades
> * Fase II: Diseño del proyecto
> * Fase III: Desarrollo
> * Fase IV: Control y evaluación
> * Fase V: Defensa y presentación

---

## 🧠 Filosofía de trabajo

El desarrollo se realiza de forma **modular, documentada y controlada mediante Git**, siguiendo el flujo descrito en `00_inicio_proyecto.md`:

* Control de versiones y registro de progreso (`DEVLOG.md` o `notas_proyecto.md`).
* Trabajo incremental en fases (análisis, diseño, desarrollo, evaluación).
* Revisión y validación antes de cada commit importante.

---

## 👨‍💻 Autor

**Oswaldo Domingo Pérez**
📧 [oswaldodomingop@gmail.com](mailto:oswaldo.domingo@gmail.com)
🌐 [github.com/OswaldoDomingo/inmobiliaria](https://github.com/OswaldoDomingo/inmobiliaria)

---

© 2025 Oswaldo Domingo Pérez — *Proyecto Fin de Ciclo DAW (IES Abastos, Valencia)*

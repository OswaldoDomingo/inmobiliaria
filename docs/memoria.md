# Proyecto Inmobiliaria  
## Memoria del Proyecto DAW – IES Abastos  
### Autor: Oswaldo Domingo  
### Curso: 2025/2026  
### Tutor individual: 

---

# 1. Identificación del proyecto

El presente documento corresponde a la memoria oficial del Proyecto de Fin de Ciclo del ciclo formativo de Desarrollo de Aplicaciones Web (DAW), realizado en el IES Abastos durante el curso académico 2025/2026.

El proyecto desarrollado es una **plataforma web inmobiliaria**, diseñada siguiendo una arquitectura MVC en PHP, que permite gestionar propiedades, usuarios y operaciones relacionadas con la actividad de una agencia inmobiliaria.  
El sistema se implementa utilizando PHP 8, MySQL, JavaScript, HTML5 y CSS3, y sigue criterios de escalabilidad, seguridad y buenas prácticas de programación.

---

# 2. Justificación del proyecto

La elección de este proyecto responde a la necesidad real de disponer de una herramienta que facilite la gestión interna de una inmobiliaria: administración de propiedades, clientes, usuarios y procesos comerciales.  
El proyecto permite aplicar de forma práctica los conocimientos adquiridos a lo largo del ciclo en áreas como:

- Programación backend con PHP (POO + MVC)
- Diseño y consulta de bases de datos MySQL
- Creación de interfaces web con HTML, CSS y JavaScript
- Arquitectura de software realista y seguridad básica
- Gestión de rutas, controladores y modelos

De este modo, el proyecto reproduce una aplicación web profesional con una estructura mantenible y escalable, adecuada para un entorno laboral real.

---

# 3. Objetivos del proyecto

### 3.1 Objetivo general
Desarrollar una aplicación web inmobiliaria completa, basada en arquitectura MVC, que permita gestionar propiedades, clientes y usuarios mediante un panel administrativo seguro y funcional.

### 3.2 Objetivos específicos
- Diseñar una base de datos relacional coherente y normalizada.
- Implementar un sistema MVC en PHP sin frameworks, aplicando POO.
- Crear un panel de usuario para la gestión de entidades.
- Implementar un sistema modular, escalable y seguro.
- Aplicar buenas prácticas de desarrollo y documentación.

---

# 4. FASE I – Identificación y contextualización  
*(Según “Instrucciones para el desarrollo de proyectos – IES Abastos”)*

## 4.1 Descripción del contexto
El proyecto se plantea como una aplicación web para la gestión operativa de una agencia inmobiliaria. Aunque no depende de una empresa real, se inspira en los procesos habituales del sector inmobiliario: publicación de inmuebles, gestión de clientes y administración interna.

## 4.2 Actividad y necesidad detectada
Una plataforma de este tipo permite centralizar en un único sistema:
- Inventario de propiedades
- Datos de clientes
- Fotografías y documentación
- Proceso básico de compraventa/alquiler

El desarrollo del proyecto permitirá demostrar competencias en diseño de software, modelos de datos, seguridad, y estructuración MVC profesional.

---

# 5. Avances técnicos realizados (resumen incorporado a la memoria)

Durante la sesión del **21/11/2025**, se llevó a cabo la primera fase técnica de preparación del proyecto —FASE 1: Seguridad básica y configuración del entorno— que incluye:

- Estructuración definitiva del proyecto en carpetas MVC.
- Configuración del archivo `.env` con credenciales externas al repositorio.
- Creación de los archivos `env.php`, `paths.php` y `database.php` en la carpeta `config/`.
- Rediseño completo de la clase `Database` para integrarla con el sistema de variables de entorno.
- Ajuste del punto de entrada `public/index.php` para cargar el entorno y validar el funcionamiento.
- Prueba satisfactoria de conexión a la base de datos.

Estos avances aseguran una base sólida y segura sobre la que continuar el desarrollo.

---

*(El resto de secciones —Fase II, Fase III, Fase IV, conclusiones, anexos, etc.— se complementarán conforme avance el proyecto.)*

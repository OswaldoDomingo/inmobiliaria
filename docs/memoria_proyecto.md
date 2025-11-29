Nombre del alumno: Oswaldo Domingo
Ciclo: Desarrollo de Aplicaciones Web (DAW)
Memoria del Proyecto de DAW
IES Abastos. Curso 2024/25. Grupo 2DAW. 29 de Noviembre de 2025
Tutor individual: [Nombre del Tutor]

---

# ÍNDICE

1. [Identificación, justificación y objetivos del proyecto](#1-identificación-justificación-y-objetivos-del-proyecto)
2. [Diseño del proyecto](#2-diseño-del-proyecto)
3. [Desarrollo del proyecto](#3-desarrollo-del-proyecto)
4. [Evaluación y conclusiones finales](#4-evaluación-y-conclusiones-finales)
5. [Referencias](#5-referencias)

---

# 1. Identificación, justificación y objetivos del proyecto

## 1.1. Identificación
**Título del Proyecto:** Plataforma de Gestión Inmobiliaria y Tasación Online.
**Alumno:** Oswaldo Domingo.
**Ciclo Formativo:** Desarrollo de Aplicaciones Web (DAW).

## 1.2. Justificación
El sector inmobiliario demanda herramientas digitales ágiles que permitan no solo la visualización de propiedades, sino también la captación de leads cualificados y la gestión eficiente de los mismos. Este proyecto nace de la necesidad de modernizar los procesos de una agencia inmobiliaria tradicional, integrando una herramienta de tasación online automatizada y un panel de administración robusto para la gestión de usuarios y propiedades.

## 1.3. Objetivos
*   **Objetivo General:** Desarrollar una aplicación web completa (Full Stack) que permita la gestión integral de una inmobiliaria.
*   **Objetivos Específicos:**
    *   Implementar una arquitectura MVC sólida y segura en PHP sin frameworks pesados.
    *   Desarrollar una herramienta de tasación online que genere valor inmediato al usuario final y capture leads para la agencia.
    *   Crear un sistema de autenticación y autorización basado en roles (Admin, Coordinador, Comercial).
    *   Asegurar la aplicación contra vulnerabilidades comunes (XSS, SQL Injection, CSRF).
    *   Implementar un sistema de despliegue y configuración basado en variables de entorno.

---

# 2. Diseño del proyecto

## 2.1. Arquitectura del Sistema
Se ha optado por una arquitectura **Modelo-Vista-Controlador (MVC)** personalizada, favoreciendo el control total sobre el flujo de la aplicación y el rendimiento.

*   **Frontend:** HTML5, CSS3 (con Tailwind CSS y Bootstrap 5), JavaScript (Vanilla).
*   **Backend:** PHP 8.2+ (Estricto tipado).
*   **Base de Datos:** MySQL / MariaDB.
*   **Servidor Web:** Apache 2.4.

## 2.2. Estructura de Directorios
El proyecto sigue una estructura segura donde solo el directorio `public/` es accesible desde la web:

```
/
├── app/                # Núcleo de la aplicación
│   ├── Controllers/    # Lógica de control
│   ├── Models/         # Acceso a datos
│   ├── Views/          # Plantillas HTML
│   └── Core/           # Router, Database, Config
├── config/             # Configuración del entorno
├── public/             # Entry point (index.php) y assets
└── docs/               # Documentación
```

## 2.3. Diseño de Base de Datos
El esquema relacional se ha diseñado para garantizar la integridad de los datos.
*   **Tabla `usuarios`:** Gestión de acceso y roles. Incluye campos de auditoría (`created_at`, `updated_at`) y seguridad (`intentos_fallidos`, `cuenta_bloqueada`).
*   **Soft Deletes:** Se implementa el borrado lógico mediante columnas `activo` y `archivado` para preservar el histórico de datos.

---

# 3. Desarrollo del proyecto

## 3.1. Configuración del Entorno
Se ha implementado un sistema de carga de variables de entorno (`.env`) para separar la configuración sensible (credenciales de BD, SMTP) del código fuente. La clase `App\Core\Config` centraliza el acceso a estas variables.

## 3.2. Núcleo (Core)
*   **Router:** Se desarrolló un enrutador personalizado que despacha las peticiones a los controladores correspondientes basándose en la URI.
*   **Database:** Clase Singleton que gestiona la conexión PDO, configurada para lanzar excepciones (`PDOException`) en caso de error, facilitando el manejo global de fallos.

## 3.3. Módulos Implementados

### 3.3.1. Autenticación y Seguridad
*   **Login Seguro:** Verificación de hash de contraseñas (`password_verify`).
*   **Gestión de Sesiones:** Regeneración de ID de sesión tras login para prevenir fijación de sesión. Cookies con flags `HttpOnly` y `Secure`.
*   **Control de Acceso (RBAC):** Middleware en los constructores de los controladores para restringir el acceso según el rol del usuario.

### 3.3.2. Herramienta de Tasación
*   Formulario interactivo para la valoración de inmuebles.
*   Envío de correos electrónicos transaccionales (al cliente y a la agencia) utilizando una librería SMTP personalizada (`SimpleSMTP`).
*   Sanitización estricta de todos los datos de entrada para prevenir inyección de código.

### 3.3.3. Gestión de Usuarios (CRUD)
*   **Listado:** Visualización de usuarios con filtros de estado.
*   **Creación/Edición:** Formularios validados en servidor.
*   **Baja Lógica:** Implementación de "Soft Delete" para desactivar usuarios sin perder sus datos históricos.
*   **Protección Anti-Suicidio:** Lógica que impide que un administrador se desactive a sí mismo.
*   **Fotos de Perfil:** Sistema de subida de imágenes seguro con validación de tipo MIME, renombrado aleatorio y limpieza automática de archivos antiguos.
*   **Sistema de Auditoría:** Implementación de logs de seguridad en archivo de texto (Flat-File) para registrar accesos, fallos y bloqueos, con visor integrado en el panel de administración.
*   **Mejora de UX en Dashboard:** Personalización de la interfaz (Header y Dashboard) para mostrar la foto y el email del usuario logueado, mejorando la orientación y el feedback visual.

## 3.4. Manejo de Errores
He implementado un manejador global de excepciones (`set_exception_handler`) en el punto de entrada. Esto asegura que, en producción, los errores técnicos (como fallos de BD) se registren en el log del servidor pero se muestre un mensaje genérico y amigable al usuario final, evitando la fuga de información sensible.

## 3.5. Justificación de Decisiones Técnicas
*   **¿Por qué PDO?** He elegido PDO sobre MySQLi porque me permite trabajar con una capa de abstracción de base de datos, facilitando una futura migración a otro motor si fuera necesario, y por su soporte nativo para sentencias preparadas, cruciales para evitar inyecciones SQL.
*   **¿Por qué `password_hash`?** Utilizo el algoritmo `PASSWORD_DEFAULT` (actualmente Bcrypt) porque es el estándar de la industria para el hashing seguro, incorporando "salt" automáticamente y haciendo computacionalmente costosos los ataques de fuerza bruta.
*   **¿Por qué `uniqid` en archivos?** Para evitar colisiones de nombres y prevenir ataques donde un usuario malicioso intenta sobrescribir archivos del sistema subiendo ficheros con nombres conocidos (ej. `index.php`).
*   **¿Por qué `try-catch` en subidas?** La manipulación de archivos es propensa a errores (permisos, disco lleno). He encapsulado esta lógica para garantizar que un fallo en el sistema de archivos no detenga la ejecución del script ni muestre errores fatales al usuario, mejorando la robustez.

---

# 4. Evaluación y conclusiones finales

## 4.1. Grado de cumplimiento de objetivos
Se han alcanzado todos los objetivos técnicos y funcionales propuestos. La aplicación es funcional, segura y escalable. La arquitectura MVC permite la fácil incorporación de nuevos módulos en el futuro.

## 4.2. Dificultades encontradas
*   **Configuración de Entornos:** La diferencia de sensibilidad a mayúsculas/minúsculas entre Windows (Desarrollo) y Linux (Producción) requirió ajustes en el Autoloader.
*   **Seguridad en Correos:** La configuración de SPF/DKIM para evitar que los correos de tasación cayeran en SPAM fue un reto de configuración de DNS y SMTP.

## 4.3. Conclusiones
El desarrollo de este proyecto ha permitido consolidar conocimientos avanzados de PHP y arquitectura web. La implementación de medidas de seguridad "Security by Design" desde el inicio ha resultado en un producto robusto y preparado para un entorno productivo real.

---

# 5. Referencias
*   **PHP Documentation:** https://www.php.net/docs.php
*   **PSR Standards (PHP-FIG):** https://www.php-fig.org/psr/
*   **OWASP Top 10:** https://owasp.org/www-project-top-ten/
*   **Bootstrap 5 Docs:** https://getbootstrap.com/docs/5.0/getting-started/introduction/

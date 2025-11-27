Nombre del alumno
Ciclo Desarrollo de Aplicaciones Web (DAW)
Memoria del Proyecto de DAW
IES Abastos. Curso 2025/26
Tutor individual: 

---

# Índice

1. [Identificación, Justificación y Objetivos](#1-identificación-justificación-y-objetivos)
2. [Diseño del Proyecto](#2-diseño-del-proyecto)
3. [Desarrollo del Proyecto](#3-desarrollo-del-proyecto)
4. [Evaluación y Conclusiones Finales](#4-evaluación-y-conclusiones-finales)
5. [Referencias](#5-referencias)
6. [Anexos](#6-anexos)

---

# 1. Identificación, Justificación y Objetivos

## 1.1. Identificación
**Título del Proyecto:** Plataforma Inmobiliaria con Herramienta de Tasación Online.
**Descripción:** Desarrollo de una aplicación web para la gestión y visualización de propiedades inmobiliarias, integrando una herramienta avanzada de valoración online basada en datos de mercado.

## 1.2. Justificación
El sector inmobiliario demanda herramientas digitales que agilicen la captación de clientes (leads). La integración de un tasador online permite ofrecer valor inmediato al usuario (una estimación de precio) a cambio de su contacto, modernizando la captación tradicional. Además, la necesidad de una arquitectura escalable y mantenible justifica la migración de soluciones "legacy" a una arquitectura MVC robusta.

## 1.3. Objetivos
*   **Objetivo General:** Desarrollar una plataforma web inmobiliaria moderna, segura y escalable.
*   **Objetivos Específicos:**
    *   Implementar una arquitectura **MVC (Modelo-Vista-Controlador)** en PHP nativo sin frameworks.
    *   Desarrollar un **Router** personalizado para el manejo de URLs amigables.
    *   Integrar una herramienta de **Tasación Online** que calcule precios en tiempo real basándose en algoritmos predefinidos y datos CSV.
    *   Implementar un sistema de envío de correos transaccionales (**SMTP**) para notificar a clientes y agentes.
    *   Asegurar la compatibilidad entre entornos de desarrollo (Windows) y producción (Linux).

---

# 2. Diseño del Proyecto

## 2.1. Análisis de Requisitos
*   **Frontend:** Interfaz responsive, uso de Bootstrap 5 para la estructura general y Tailwind CSS para componentes específicos (Tasador).
*   **Backend:** PHP 8.x, orientado a objetos, siguiendo estándares PSR-4.
*   **Base de Datos:** MySQL para persistencia de inmuebles y usuarios (aunque la tasación usa CSVs para flexibilidad de reglas).
*   **Servidor:** Apache con `mod_rewrite` activado.

## 2.2. Arquitectura del Sistema
Se ha optado por una arquitectura **MVC** para separar la lógica de negocio de la presentación:
*   **Modelo:** Gestión de datos y reglas de negocio.
*   **Vista:** Plantillas HTML/PHP separadas (Layouts, Vistas parciales).
*   **Controlador:** Intermediario que procesa las peticiones y selecciona la vista adecuada.

### Estructura de Directorios
```
/inmobiliaria
├── app/
│   ├── Controllers/   (Lógica de control: HomeController, TasacionController)
│   ├── Core/          (Núcleo: Router, Database)
│   ├── Lib/           (Librerías auxiliares: SimpleSMTP)
│   ├── Models/        (Lógica de datos)
│   ├── Views/         (Plantillas: home, tasacion/formulario)
│   └── Autoloader.php (Carga automática de clases)
├── config/            (Configuración global y credenciales)
├── public/            (Document Root: index.php, assets, .htaccess)
└── docs/              (Documentación y memoria)
```

## 2.3. Recursos y Herramientas
*   **IDE:** Visual Studio Code.
*   **Control de Versiones:** Git y GitHub.
*   **Servidor Local:** Apache.
*   **Librerías Frontend:** Lucide (iconos), PapaParse (CSV), Tailwind CSS (CDN), Boostrap.

---

# 3. Desarrollo del Proyecto

## 3.1. Configuración del Entorno
Se configuró un **Virtual Host** (`inmobiliaria.loc`) para simular un entorno de producción real. Se creó un archivo `.htaccess` en la carpeta `public/` para redirigir todo el tráfico al **Front Controller** (`index.php`), permitiendo URLs limpias como `/tasacion` en lugar de `index.php?page=tasacion`.

## 3.2. Implementación del Núcleo (Core)
### 3.2.1. Autoloader
Se implementó una clase `Autoloader` siguiendo el estándar **PSR-4**.
*   **Desafío:** Diferencias de *case-sensitivity* entre Windows y Linux.
*   **Solución:** Se añadió una lógica de *fallback* para buscar archivos en directorios en minúsculas si la ruta estándar no existe, asegurando el despliegue correcto en el servidor de producción.

### 3.2.2. Router
Se desarrolló una clase `Router` que mapea verbos HTTP (GET, POST) y URIs a controladores específicos.
```php
$router->get('/', [HomeController::class, 'index']);
$router->post('/tasacion/enviar', [TasacionController::class, 'enviar']);
```

## 3.3. Módulo de Tasación
Se migró una herramienta heredada (HTML/JS monolítico) a la nueva arquitectura:
1.  **Vista:** Se extrajo el HTML a `app/views/tasacion/formulario.php`, limpiando estilos inline y scripts.
2.  **Controlador:** `TasacionController` gestiona la carga de la vista y inyecta configuraciones específicas (como desactivar el *preflight* de Tailwind para no romper Bootstrap).
3.  **Lógica Cliente:** JavaScript (Vanilla) para cálculos en tiempo real y `fetch` para envío de datos.
4.  **Envío de Emails:** Se creó la librería `SimpleSMTP` para enviar correos autenticados, reemplazando soluciones de terceros (EmailJS) que tuve que usar ya que originalmente no disponía de servidor SMTP para enviar correos, por una solución propia más robusta y privada.

## 3.4. Consolidación de Vistas (Layouts)
Para evitar duplicidad de código, se implementó un sistema de **Layouts**:
*   `header.php`: Contiene la navegación y metadatos.
*   `footer.php`: Pie de página común.
*   Los controladores inyectan variables (ej: `$showHero`, `$extraCss`) para personalizar el layout base según la página.

---

# 4. Evaluación y Conclusiones Finales

## 4.1. Pruebas Realizadas
*   **Pruebas de Rutas:** Verificación de acceso a `/`, `/tasacion` y manejo de errores 404.
*   **Pruebas de Integración:** Envío exitoso de formularios de tasación, recepción de correos por cliente y agencia.
*   **Pruebas de Despliegue:** Detección y corrección de errores de *case-sensitivity* al subir al servidor Linux.

## 4.2. Solución de Problemas (Hotfixes)
Durante el desarrollo surgieron conflictos entre los estilos de **Bootstrap** (usado en la web principal) y **Tailwind CSS** (usado en el tasador).
*   **Problema:** Tailwind reseteaba los estilos base, ocultando la barra de navegación de Bootstrap.
*   **Solución:** Se configuró Tailwind con `preflight: false` y se forzó la visibilidad de clases críticas (`.collapse`) mediante CSS específico en el controlador.

## 4.3. Conclusiones
El proyecto ha cumplido con los objetivos de modernización y estructuración. La arquitectura MVC facilita la escalabilidad futura (añadir nuevas secciones es tan sencillo como crear un controlador y una ruta). La herramienta de tasación funciona como un potente captador de leads, integrado totalmente en la identidad visual de la marca.

---

# 5. Referencias
*   Documentación PHP: https://www.php.net/docs.php
*   Estándares PSR (PHP-FIG): https://www.php-fig.org/psr/psr-4/
*   Bootstrap 5 Docs: https://getbootstrap.com/docs/5.3/
*   Tailwind CSS Docs: https://tailwindcss.com/docs/

---

# 6. Anexos
*   **Anexo A:** Estructura de Base de Datos (Diagrama E-R).
*   **Anexo B:** Código fuente del Router.
*   **Anexo C:** Configuración del Servidor (.htaccess).

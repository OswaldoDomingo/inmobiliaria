# Implementación Técnica: Módulo de Tasación

## 1. Arquitectura
El módulo sigue el patrón MVC (Modelo-Vista-Controlador) del framework propio.

### Componentes Principales
| Componente | Archivo | Responsabilidad |
|------------|---------|-----------------|
| **Controlador** | `App\Controllers\TasacionController.php` | Procesa el formulario, calcula precio y orquesta el envío de emails. |
| **Vista Formulario** | `app/Views/tasacion/formulario.php` | Interfaz de usuario con validación JS y envío AJAX. |
| **Servicio Email** | `App\Services\MailService.php` | Wrapper de PHPMailer para envíos SMTP. |
| **Templates Email** | `app/Views/emails/*.php` | Vistas HTML para los correos. |

## 2. Detalle de Implementación

### TasacionController::enviar()
Método principal que recibe el POST vía AJAX.
1.  **Validación CSRF**: Protege contra ataques Cross-Site Request Forgery.
2.  **Sanitización**: Limpia inputs (strip_tags, filter_var).
3.  **Cálculo**: Aplica lógica de precio por m² según zona.
4.  **Envío de Correos**:
    ```php
    // Ejemplo simplificado
    MailService::send($emailCliente, 'Tu Valoración', [
        'template' => 'tasacion_cliente',
        'data' => $datosValoracion
    ]);
    ```
5.  **Respuesta**: Retorna JSON `{ status: 'success', ... }` al frontend.

### Sistema de Correos (MailService)
Se ha migrado de `SimpleSMTP` (sockets planos) a `PHPMailer` para soportar:
- **Cifrado TLS/SSL**: Necesario para Gmail/Outlook/cPanel modernos.
- **HTML/CSS**: Emails con diseño corporativo.
- **Logging**: Registro de errores en `logs/mail.log`.

#### Configuración (.env)
Se utiliza `config/.env` para separar credenciales del código:
```env
SMTP_HOST=mail.oswaldo.dev
SMTP_PORT=465
SMTP_SECURE=ssl
SMTP_USER=oficina@...
```

#### Templates
Ubicados en `app/Views/emails/`.
- `layout.php`: Estructura base (Logo, Header, Footer).
- `tasacion_cliente.php`: Contenido específico para el usuario.
- `tasacion_agencia.php`: Contenido específico para el comercial.

**Nota sobre Layouts**: Las plantillas incluyen explícitamente `layout.php` al final de su renderizado `ob_get_clean()` para permitir flexibilidad y previsualización.

## 3. Seguridad
- **Validación de Input**: Se validan tipos de datos (entero para superficie, email válido, etc.).
- **Protección XSS**: Todos los outputs en HTML utilizan `htmlspecialchars()`.
- **Credenciales**: Fuera del código fuente, en archivo `.env` bloqueado por `.htaccess` y no versionado.

## 4. Pruebas
Se dispone de script de prueba local en `/test/email.php` para validar conectividad SMTP aislada del flujo de negocio.

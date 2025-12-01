# Guía de Configuración: EmailJS

Esta guía explica cómo configurar el servicio de correos **EmailJS** para que la calculadora de tasación envíe los avisos correctamente.

## 1. Crear Cuenta en EmailJS

1.  Vaya a [https://www.emailjs.com/](https://www.emailjs.com/) y pulse en **"Sign Up Free"**.
2.  Rellene el formulario con su nombre, email y contraseña.
3.  Una vez dentro del panel (Dashboard), vaya a la sección **"Email Services"** en la barra lateral izquierda.
4.  Pulse **"Add New Service"** y seleccione su proveedor de correo (ej: Gmail, Outlook).
5.  Conecte su cuenta y pulse **"Create Service"**.
    *   *Nota:* Si usa Gmail, es posible que deba activar la verificación en dos pasos y crear una "Contraseña de aplicación" si no funciona directamente.

## 2. Crear Plantillas de Correo (Templates)

Necesitamos crear dos plantillas: una para el aviso que recibe la inmobiliaria y otra para el cliente.

Vaya a la sección **"Email Templates"** en la barra lateral izquierda y pulse **"Create New Template"**.

### Plantilla A: Aviso Inmobiliaria (Para usted)

Esta plantilla es la que recibirá usted con los datos del lead.

1.  Pulse en la pestaña **"Content"** o **"Source Code"** (icono `< >`) del editor.
2.  Borre todo el contenido y pegue el siguiente código HTML:

```html
<div style="font-family: Arial, sans-serif; color: #333;">
    <h2 style="color: #4f46e5;">🚀 Nuevo Cliente desde la Web</h2>
    <hr>
    <h3>👤 Datos de Contacto</h3>
    <p><strong>Teléfono:</strong> <a href="tel:{{user_phone}}">{{user_phone}}</a></p>
    <p><strong>Email:</strong> <a href="mailto:{{to_email}}">{{to_email}}</a></p>
    <p><strong>Fecha:</strong> {{date}}</p>
    
    <h3>🏠 Datos del Inmueble</h3>
    <ul>
        <li><strong>Zona:</strong> {{barrio}}, {{zona}} (CP: {{cp}})</li>
        <li><strong>Superficie:</strong> {{superficie}} m²</li>
        <li><strong>Valoración:</strong> {{precio_min}} - {{precio_max}}</li>
    </ul>
    
    <div style="background: #f4f4f5; padding: 10px; border-radius: 5px;">
        <strong>Características detectadas:</strong><br>
        {{caracteristicas}}
    </div>
</div>
```

3.  En **Subject** (Asunto), ponga algo como: `Nuevo Lead Tasación: {{barrio}}`.
4.  Pulse **"Save"**.

---

### Plantilla B: Confirmación Cliente (Para el usuario)

Cree una **nueva plantilla** (Create New Template) para el correo que recibirá el cliente.

1.  Pulse en el icono `< >` (Source Code).
2.  Pegue el siguiente código HTML:

```html
<div style="font-family: Arial, sans-serif; text-align: center; color: #333;">
    <h1 style="color: #4f46e5;">¡Informe recibido!</h1>
    <p>Gracias por confiar en nosotros. Aquí tienes el resumen de tu valoración online:</p>
    
    <div style="background: #eef2ff; padding: 20px; border-radius: 10px; margin: 20px 0;">
        <h2 style="margin:0; color: #312e81;">{{precio_min}} - {{precio_max}}</h2>
        <p style="margin:5px 0 0 0; font-size: 12px; color: #666;">Valor estimado de mercado</p>
    </div>

    <div style="text-align: left; max-width: 400px; margin: 0 auto;">
        <p><strong>Zona:</strong> {{barrio}} - {{zona}}</p>
        <p><strong>Superficie:</strong> {{superficie}} m²</p>
        <p><strong>Tus extras:</strong> {{caracteristicas}}</p>
    </div>

    <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">
    <p>Un agente experto te contactará al <strong>{{user_phone}}</strong> para afinar este precio.</p>
</div>
```

3.  En **Subject** (Asunto), ponga: `Tu Valoración Inmobiliaria`.
4.  En el campo **"To Email"** (a la derecha), asegúrese de poner `{{to_email}}`.
5.  Pulse **"Save"**.

## 3. Conectar con la Web

Para que la web funcione con su cuenta, necesitará copiar 3 códigos de EmailJS y dárselos a su programador o pegarlos en el código si sabe hacerlo:

1.  **Public Key:** Está en la sección **"Account"** (click en su nombre arriba a la derecha).
2.  **Service ID:** Está en la sección **"Email Services"** (ej: `service_xxxx`).
3.  **Template IDs:** Están en la sección **"Email Templates"** (ej: `template_xxxx`).

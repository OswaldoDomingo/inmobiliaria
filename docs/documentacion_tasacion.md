# Documentación Funcional: Módulo de Tasación Online

## 1. Descripción General
El módulo de Tasación Online permite a los usuarios visitantes obtener una valoración estimada de su inmueble de forma inmediata a través de la web.

El sistema funciona como un "gancho" comercial (Lead Magnet) para captar datos de potenciales vendedores.

## 2. Flujo de Usuario
1.  **Acceso**: El usuario navega a `/tasacion`.
2.  **Formulario**: Introduce datos básicos del inmueble:
    *   Ubicación (Barrio, Zona, CP).
    *   Características (Superficie, Habitaciones, etc.).
    *   Datos de contacto (Email, Teléfono).
3.  **Resultado**:
    *   Se muestra en pantalla un rango de precio estimado (ej: 150.000€ - 180.000€).
    *   Se informa que un agente revisará la valoración.
4.  **Confirmación**:
    *   El usuario recibe un email con el resumen de la valoración.
    *   La agencia recibe un email con los datos del nuevo lead.

## 3. Lógica de Negocio

### Cálculo de Valoración
El sistema utiliza un algoritmo base simplificado para esta fase:
- **Precio base por m²**: Definido por zona (ej: Salamanca = 4000€/m²).
- **Ajustes**:
    - Ascensor: +10%
    - Exterior: +5%
    - Reformado: +15%
- **Rango**: Se calcula un +/- 10% sobre el precio base para ofrecer una horquilla realista.

### Gestión de Leads
Cada solicitud se registra (actualmente vía email, fase futura BD) para seguimiento comercial.

## 4. Comunicaciones (Emails)

### Al Cliente
- **Asunto**: "Tu Valoración Inmobiliaria"
- **Contenido**:
    - Saludo personalizado.
    - Rango de precio destacado en verde.
    - Resumen de datos introducidos.
    - Próximos pasos (contacto de agente).

### A la Agencia
- **Asunto**: "Nuevo Lead de Tasación Online"
- **Contenido**:
    - Datos de contacto del cliente (Email, Teléfono clicable).
    - Datos del inmueble.
    - Valoración ofrecida.
    - Fecha y hora.

<?php
/**
 * Template: Email de Confirmación para el Cliente
 * Se envía al cliente que solicitó una tasación online
 * 
 * Variables disponibles:
 * - $precio_min (string): Precio mínimo formateado
 * - $precio_max (string): Precio máximo formateado
 * - $barrio (string): Barrio del inmueble
 * - $zona (string): Zona específica
 * - $cp (string): Código postal
 * - $superficie (string): Superficie en m²
 * - $caracteristicas (string): Características adicionales
 */

$title = 'Tu Valoración Inmobiliaria';
ob_start();
?>

<h2 style="color: #191A2E; margin-top: 0;">Hola <?= htmlspecialchars($nombre ?? '') ?>,</h2>

<p>Gracias por utilizar nuestro <strong>Tasador Online</strong>. A continuación, encontrarás el resumen de tu valoración inmobiliaria.</p>

<!-- Resultado de Valoración -->
<div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
            padding: 25px; 
            border-radius: 10px; 
            margin: 25px 0; 
            text-align: center;
            color: white;">
    <p style="margin: 0 0 5px 0; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9;">
        Valoración Estimada
    </p>
    <p style="margin: 0; font-size: 28px; font-weight: bold;">
        <?= htmlspecialchars($precio_min ?? '0 €') ?> - <?= htmlspecialchars($precio_max ?? '0 €') ?>
    </p>
</div>

<!-- Detalles del Inmueble -->
<div style="background-color: #f9fafb; 
            border-left: 4px solid #191A2E; 
            padding: 20px; 
            margin: 20px 0;
            border-radius: 5px;">
    <h3 style="color: #191A2E; margin-top: 0; font-size: 16px;">📍 Detalles de tu inmueble</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px 0; color: #666; font-size: 14px;"><strong>Ubicación:</strong></td>
            <td style="padding: 8px 0; color: #333; font-size: 14px;">
                <?= htmlspecialchars($barrio ?? '') ?>, <?= htmlspecialchars($zona ?? '') ?> (CP: <?= htmlspecialchars($cp ?? '') ?>)
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; color: #666; font-size: 14px;"><strong>Superficie:</strong></td>
            <td style="padding: 8px 0; color: #333; font-size: 14px;"><?= htmlspecialchars($superficie ?? '') ?> m²</td>
        </tr>
        <?php if (!empty($caracteristicas)): ?>
        <tr>
            <td style="padding: 8px 0; color: #666; font-size: 14px; vertical-align: top;"><strong>Características:</strong></td>
            <td style="padding: 8px 0; color: #333; font-size: 14px;"><?= htmlspecialchars($caracteristicas) ?></td>
        </tr>
        <?php endif; ?>
    </table>
</div>

<!-- Próximos Pasos -->
<div style="background-color: #fef3c7; 
            border-left: 4px solid #f59e0b; 
            padding: 15px; 
            margin: 20px 0;
            border-radius: 5px;">
    <p style="margin: 0; color: #92400e; font-size: 14px;">
        ℹ️ <strong>Próximos pasos:</strong> Un agente de nuestro equipo se pondrá en contacto contigo pronto para validar estos datos y ofrecerte una valoración más precisa y personalizada.
    </p>
</div>

<!-- Mensaje de Cierre -->
<p style="margin-top: 30px;">
    Si tienes alguna pregunta o necesitas más información, no dudes en contactarnos.
</p>

<p style="margin-bottom: 0;">
    Atentamente,<br>
    <strong>El equipo de CRM Inmobiliaria</strong>
</p>


<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';

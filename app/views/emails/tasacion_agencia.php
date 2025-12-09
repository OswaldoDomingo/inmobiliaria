<?php
/**
 * Template: Email para la Agencia (Lead Interno)
 * Se envía a la agencia cuando se recibe un nuevo lead de tasación
 * 
 * Variables disponibles:
 * - $fecha (string): Fecha y hora de la solicitud
 * - $email_cliente (string): Email del cliente
 * - $telefono (string): Teléfono del cliente
 * - $cp (string): Código postal
 * - $barrio (string): Barrio del inmueble
 * - $zona (string): Zona específica
 * - $superficie (string): Superficie en m²
 * - $caracteristicas (string): Características adicionales
 * - $precio_min (string): Precio mínimo formateado
 * - $precio_max (string): Precio máximo formateado
 */

$title = 'Nuevo Lead de Tasación Online';
ob_start();
?>

<div style="background-color: #dbeafe; 
            border-left: 4px solid #3b82f6; 
            padding: 15px; 
            margin: 0 0 25px 0;
            border-radius: 5px;">
    <h2 style="margin: 0; color: #1e40af; font-size: 20px;">
        🆕 Nuevo Lead de Tasación Online
    </h2>
</div>

<p style="color: #666; font-size: 14px; margin: 10px 0 25px 0;">
    <strong>Fecha de recepción:</strong> <?= htmlspecialchars($fecha ?? date('d/m/Y H:i')) ?>
</p>

<!-- Datos del Cliente -->
<div style="background-color: #f9fafb; 
            border: 1px solid #e5e7eb;
            padding: 20px; 
            margin: 20px 0;
            border-radius: 8px;">
    <h3 style="color: #191A2E; margin-top: 0; font-size: 16px; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
        👤 Datos del Cliente
    </h3>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px 0; color: #666; font-size: 14px; width: 120px;"><strong>Nombre:</strong></td>
            <td style="padding: 8px 0; color: #333; font-size: 14px;">
                <?= htmlspecialchars($nombre ?? '') ?>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; color: #666; font-size: 14px; width: 120px;"><strong>Email:</strong></td>
            <td style="padding: 8px 0; color: #333; font-size: 14px;">
                <a href="mailto:<?= htmlspecialchars($email_cliente ?? '') ?>" style="color: #3b82f6; text-decoration: none;">
                    <?= htmlspecialchars($email_cliente ?? '') ?>
                </a>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; color: #666; font-size: 14px;"><strong>Teléfono:</strong></td>
            <td style="padding: 8px 0; color: #333; font-size: 14px;">
                <a href="tel:<?= htmlspecialchars($telefono ?? '') ?>" style="color: #3b82f6; text-decoration: none;">
                    <?= htmlspecialchars($telefono ?? '') ?>
                </a>
            </td>
        </tr>
    </table>
</div>

<!-- Datos del Inmueble -->
<div style="background-color: #f9fafb; 
            border: 1px solid #e5e7eb;
            padding: 20px; 
            margin: 20px 0;
            border-radius: 8px;">
    <h3 style="color: #191A2E; margin-top: 0; font-size: 16px; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
        🏠 Datos del Inmueble
    </h3>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px 0; color: #666; font-size: 14px; width: 120px;"><strong>Código Postal:</strong></td>
            <td style="padding: 8px 0; color: #333; font-size: 14px;"><?= htmlspecialchars($cp ?? '') ?></td>
        </tr>
        <tr>
            <td style="padding: 8px 0; color: #666; font-size: 14px;"><strong>Barrio:</strong></td>
            <td style="padding: 8px 0; color: #333; font-size: 14px;"><?= htmlspecialchars($barrio ?? '') ?></td>
        </tr>
        <tr>
            <td style="padding: 8px 0; color: #666; font-size: 14px;"><strong>Zona:</strong></td>
            <td style="padding: 8px 0; color: #333; font-size: 14px;"><?= htmlspecialchars($zona ?? '') ?></td>
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

<!-- Valoración Estimada -->
<div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
            padding: 20px; 
            border-radius: 8px; 
            margin: 20px 0; 
            text-align: center;
            color: white;">
    <h3 style="margin: 0 0 10px 0; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9;">
        💰 Valoración Estimada
    </h3>
    <p style="margin: 0; font-size: 24px; font-weight: bold;">
        <?= htmlspecialchars($precio_min ?? '0 €') ?> - <?= htmlspecialchars($precio_max ?? '0 €') ?>
    </p>
</div>

<!-- Call to Action -->
<div style="background-color: #fef3c7; 
            border-left: 4px solid #f59e0b; 
            padding: 15px; 
            margin: 25px 0;
            border-radius: 5px;">
    <p style="margin: 0; color: #92400e; font-size: 14px;">
        ⚡ <strong>Acción requerida:</strong> Contacta con el cliente lo antes posible para cerrar la oportunidad de negocio.
    </p>
</div>

<p style="color: #666; font-size: 13px; margin-top: 30px;">
    Este lead fue generado automáticamente desde el Tasador Online el <?= htmlspecialchars($fecha ?? date('d/m/Y H:i')) ?>.
</p>


<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';

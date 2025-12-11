<?php
/**
 * Plantilla de email: contacto_cliente
 * Variables disponibles: $nombre, $inmueble (array|null)
 */
$title = 'Hemos recibido tu consulta';
ob_start();
?>
<h2>Hola <?= htmlspecialchars($nombre) ?>,</h2>

<p>Gracias por contactar con nosotros.</p>

<p>Hemos recibido tu mensaje correctamente. Nuestro equipo revisará tu consulta y se pondrá en contacto contigo a la mayor brevedad posible.</p>

<?php if (!empty($inmueble) && is_array($inmueble)): ?>
<div style="background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #eee;">
    <h3 style="margin-top: 0; font-size: 16px; color: #333;">Detalles de tu consulta:</h3>
    <p style="margin: 0;">
        <strong>Referencia:</strong> <?= htmlspecialchars($inmueble['ref'] ?? 'N/A') ?><br>
        <strong>Tipo:</strong> <?= htmlspecialchars(ucfirst($inmueble['tipo'] ?? 'Inmueble')) ?><br>
        <strong>Ubicación:</strong> <?= htmlspecialchars($inmueble['localidad'] ?? '') ?>
    </p>
</div>
<?php endif; ?>

<br>
<p style="font-size: 14px; color: #666;">
    Atentamente,<br>
    <strong>El equipo de CRM Inmobiliaria</strong>
</p>
<p style="font-size: 12px; color: #999; margin-top: 20px;">
    Este es un correo automático, por favor no respondas directamente a este mensaje.
</p>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';

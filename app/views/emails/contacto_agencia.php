<?php
/**
 * Plantilla de email: contacto_agencia
 * Variables disponibles: $nombre, $email, $telefono, $mensaje, $fecha, $inmueble (array|null)
 */
$title = 'Nuevo Contacto Web';
ob_start();
?>
<h2>✉️ Nuevo Contacto Recibido</h2>
<p style="color: #666; font-size: 14px;">Fecha: <?= $fecha ?></p>

<div style="background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
    <h3 style="margin-top: 0; color: #333;">Datos del cliente</h3>
    <ul style="list-style: none; padding: 0;">
        <li style="margin-bottom: 10px;">
            <strong>Nombre:</strong> <?= htmlspecialchars($nombre) ?>
        </li>
        <li style="margin-bottom: 10px;">
            <strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($email) ?>" style="color: #0066cc;"><?= htmlspecialchars($email) ?></a>
        </li>
        <li style="margin-bottom: 10px;">
            <strong>Teléfono:</strong> <a href="tel:<?= htmlspecialchars($telefono) ?>" style="color: #0066cc;"><?= htmlspecialchars($telefono) ?></a>
        </li>
    </ul>
</div>

<?php if (!empty($mensaje)): ?>
<div style="margin-bottom: 20px;">
    <h3 style="color: #333;">Mensaje:</h3>
    <p style="background-color: #fff; border: 1px solid #eee; padding: 15px; border-radius: 4px; white-space: pre-line;">
        <?= nl2br(htmlspecialchars($mensaje)) ?>
    </p>
</div>
<?php endif; ?>

<?php if (!empty($inmueble) && is_array($inmueble)): ?>
<div style="background-color: #e8f4fd; padding: 15px; border-radius: 5px; border-left: 4px solid #0066cc;">
    <h3 style="margin-top: 0; color: #0066cc;">Inmueble de interés:</h3>
    <p style="margin: 0;">
        <strong>Ref:</strong> <?= htmlspecialchars($inmueble['ref'] ?? 'N/A') ?><br>
        <strong>Tipo:</strong> <?= htmlspecialchars(ucfirst($inmueble['tipo'] ?? '')) ?><br>
        <strong>Ubicación:</strong> <?= htmlspecialchars($inmueble['localidad'] ?? '') ?>
        <?php if (!empty($inmueble['precio'])): ?>
            <br><strong>Precio:</strong> <?= number_format((float)$inmueble['precio'], 0, ',', '.') ?> €
        <?php endif; ?>
    </p>
</div>
<?php endif; ?>

<p style="font-size: 12px; color: #999; margin-top: 30px; border-top: 1px solid #eee; padding-top: 10px;">
    Este mensaje se envió desde el formulario de contacto público de la web.
</p>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';

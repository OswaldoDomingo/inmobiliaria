<?php
require VIEW . '/layouts/header.php';

$clienteId = $clienteId ?? 0;
$nombreCliente = '';
if ($cliente) {
   $nombreCliente = is_object($cliente) 
        ? ($cliente->nombre . ' ' . $cliente->apellidos) 
        : ($cliente['nombre'] . ' ' . $cliente['apellidos']);
}
?>
<div class="container py-5">
    <h1>Nueva demanda para <?= htmlspecialchars($nombreCliente) ?> (ID #<?= (int)$clienteId ?>)</h1>
    
    <div class="alert alert-info my-4">
        Aquí irá el formulario de alta de demandas.
    </div>

    <form>
        <input type="hidden" name="cliente_id" value="<?= (int)$clienteId ?>">
        <!-- Placeholder form fields -->
    </form>

    <div class="mt-4">
        <a href="/admin/clientes/editar?id=<?= (int)$clienteId ?>" class="btn btn-outline-secondary">← Volver al cliente</a>
    </div>
</div>

<?php require VIEW . '/layouts/footer.php'; ?>

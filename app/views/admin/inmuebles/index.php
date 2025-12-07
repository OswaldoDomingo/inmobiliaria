<?php
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$data = $result['data'] ?? [];
$total = (int)($result['total'] ?? 0);
$page = (int)($result['page'] ?? 1);
$perPage = (int)($result['perPage'] ?? 15);
?>
<h1>Inmuebles</h1>

<p><a href="/admin/inmuebles/nuevo">+ Nuevo inmueble</a></p>

<form method="get" action="/admin/inmuebles">
  <input name="ref" placeholder="Ref" value="<?= e($_GET['ref'] ?? '') ?>">
  <input name="tipo" placeholder="Tipo (piso, casa...)" value="<?= e($_GET['tipo'] ?? '') ?>">
  <input name="operacion" placeholder="Operacion (venta...)" value="<?= e($_GET['operacion'] ?? '') ?>">
  <input name="localidad" placeholder="Localidad" value="<?= e($_GET['localidad'] ?? '') ?>">
  <button type="submit">Filtrar</button>
</form>

<table border="1" cellpadding="6" cellspacing="0">
  <thead>
    <tr>
      <th>Ref</th><th>Tipo</th><th>Operación</th><th>Precio</th><th>Localidad</th><th>Estado</th><th>Prop. / Com.</th><th>Acciones</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($data as $row): 
      $isArr = is_array($row);
      $id = $isArr ? $row['id_inmueble'] : $row->id_inmueble;
      $ref = $isArr ? $row['ref'] : $row->ref;
      $tipo = $isArr ? $row['tipo'] : $row->tipo;
      $operacion = $isArr ? $row['operacion'] : $row->operacion;
      $precio = $isArr ? $row['precio'] : $row->precio;
      $localidad = $isArr ? $row['localidad'] : $row->localidad;
      $estado = $isArr ? $row['estado'] : $row->estado;
      
      $prop = trim(($isArr ? ($row['propietario_nombre'] ?? '') : ($row->propietario_nombre ?? '')) . ' ' . ($isArr ? ($row['propietario_apellidos'] ?? '') : ($row->propietario_apellidos ?? '')));
      $com = $isArr ? ($row['comercial_nombre'] ?? '-') : ($row->comercial_nombre ?? '-');
  ?>
    <tr>
      <td><?= e($ref) ?></td>
      <td><?= e($tipo) ?></td>
      <td><?= e($operacion) ?></td>
      <td><?= number_format((float)$precio, 2, ',', '.') ?> €</td>
      <td><?= e($localidad) ?></td>
      <td><?= e($estado) ?></td>
      <td>
        Prop: <?= e($prop) ?><br>
        Com: <?= e($com) ?>
      </td>
      <td>
        <a href="/admin/inmuebles/editar?id=<?= (int)$id ?>">Editar</a>
        <form method="post" action="/admin/inmuebles/borrar" style="display:inline" onsubmit="return confirm('¿Borrar inmueble?');">
          <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
          <input type="hidden" name="id" value="<?= (int)$id ?>">
          <button type="submit">Borrar</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<p>Total: <?= $total ?></p>

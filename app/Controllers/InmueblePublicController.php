<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Inmueble;

final class InmueblePublicController
{
    private Inmueble $inmuebles;

    public function __construct()
    {
        $this->inmuebles = new Inmueble();
    }

    public function index(): void
    {
        // Allowed values (allowlist)
        $operacionesPermitidas = ['venta', 'alquiler', 'vacacional'];
        $tiposPermitidos = ['piso','casa','chalet','adosado','duplex','local','oficina','terreno','otros'];

        // Read and validate operacion
        $operacionRaw = strtolower(trim((string)filter_input(INPUT_GET, 'operacion', FILTER_UNSAFE_RAW)));
        $operacion = in_array($operacionRaw, $operacionesPermitidas, true) ? $operacionRaw : '';

        // Read and validate tipo
        $tipoRaw = strtolower(trim((string)filter_input(INPUT_GET, 'tipo', FILTER_UNSAFE_RAW)));
        $tipo = in_array($tipoRaw, $tiposPermitidos, true) ? $tipoRaw : '';

        // Read and validate m2_min (integer with range)
        $m2Min = filter_input(INPUT_GET, 'm2_min', FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 0, 'max_range' => 100000]
        ]);
        if ($m2Min === false) $m2Min = null;

        // Read and validate precio_max (integer with range)
        $precioMax = filter_input(INPUT_GET, 'precio_max', FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 0, 'max_range' => 100000000]
        ]);
        if ($precioMax === false) $precioMax = null;

        // Read existing filters
        $localidad = trim((string)filter_input(INPUT_GET, 'localidad', FILTER_UNSAFE_RAW));
        $precioMin = filter_input(INPUT_GET, 'precio_min', FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 0, 'max_range' => 100000000]
        ]);
        if ($precioMin === false) $precioMin = null;

        // Build filters array (only non-empty values)
        $filters = [];
        if ($localidad !== '') $filters['localidad'] = $localidad;
        if ($tipo !== '') $filters['tipo'] = $tipo;
        if ($operacion !== '') $filters['operacion'] = $operacion;
        if ($precioMin !== null) $filters['precio_min'] = $precioMin;
        if ($precioMax !== null) $filters['precio_max'] = $precioMax;
        if ($m2Min !== null) $filters['m2_min'] = $m2Min;

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;

        $result = $this->inmuebles->paginatePublic($filters, $page, $perPage);

        // Pass normalized filters to view for form persistence
        $filtersNormalized = [
            'localidad' => $localidad,
            'tipo' => $tipo,
            'operacion' => $operacion,
            'precio_min' => $precioMin,
            'precio_max' => $precioMax,
            'm2_min' => $m2Min,
        ];

        // Header and Footer inclusion handled by View or Main Controller logic?
        // User asked to match Public layout. 
        // In previous steps I saw HomeController including header/footer manually.
        // I will do the same here to ensure it looks good.
        
        require VIEW . '/layouts/header.php';
        require VIEW . '/propiedades/index.php';
        require VIEW . '/layouts/footer.php';
    }

    public function show(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->redirect('/propiedades');
            return;
        }

        $inmueble = $this->inmuebles->findById($id);
        if (!$inmueble) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        // Validación extra de seguridad (publicable)
        // estado='activo' AND activo=1 AND archivado=0
        $estado    = is_array($inmueble) ? ($inmueble['estado'] ?? '') : ($inmueble->estado ?? '');
        $activo    = is_array($inmueble) ? (int)($inmueble['activo'] ?? 0) : (int)($inmueble->activo ?? 0);
        $archivado = is_array($inmueble) ? (int)($inmueble['archivado'] ?? 0) : (int)($inmueble->archivado ?? 0);

        if ($estado !== 'activo' || $activo !== 1 || $archivado !== 0) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }


        // Obtener coordinador para fallback de teléfono
        $userModel = new \App\Models\User();
        $coordinador = $userModel->getCoordinadorGeneral();
        
        $contacto = null;
        $comercialId = is_array($inmueble) ? ($inmueble['comercial_id'] ?? null) : ($inmueble->comercial_id ?? null);
        
        if (!$comercialId) {
            // No hay comercial asignado → Mostrar todos los datos del coordinador
            if ($coordinador) {
                $contacto = [
                    'nombre'   => is_array($coordinador) ? $coordinador['nombre'] : $coordinador->nombre,
                    'email'    => is_array($coordinador) ? $coordinador['email'] : $coordinador->email,
                    'telefono' => is_array($coordinador) ? $coordinador['telefono'] : $coordinador->telefono,
                ];
            }
        } else {
            // Hay comercial asignado → Usar sus datos
            $comercialTelefono = is_array($inmueble) ? ($inmueble['comercial_telefono'] ?? '') : ($inmueble->comercial_telefono ?? '');
            
            // Si el comercial no tiene teléfono, usar el del coordinador como fallback
            if (empty($comercialTelefono) && $coordinador) {
                $comercialTelefono = is_array($coordinador) ? $coordinador['telefono'] : $coordinador->telefono;
            }
            
            $contacto = [
                'nombre'   => is_array($inmueble) ? ($inmueble['comercial_nombre'] ?? '') : ($inmueble->comercial_nombre ?? ''),
                'email'    => is_array($inmueble) ? ($inmueble['comercial_email'] ?? '') : ($inmueble->comercial_email ?? ''),
                'telefono' => $comercialTelefono,
            ];
        }


        require VIEW . '/layouts/header.php';
        require VIEW . '/propiedades/show.php';
        require VIEW . '/layouts/footer.php';
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}

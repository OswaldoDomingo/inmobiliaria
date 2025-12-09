<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Email') ?></title>
    <style>
        /* Reset básico */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        
        /* Contenedor principal */
        .email-wrapper {
            width: 100%;
            background-color: #f4f4f4;
            padding: 20px 0;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        /* Header */
        .email-header {
            background: linear-gradient(135deg, #191A2E 0%, #242642 100%);
            padding: 30px 20px;
            text-align: center;
            color: #ffffff;
        }
        
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        .email-header .logo {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        /* Contenido */
        .email-content {
            padding: 30px 20px;
            color: #333333;
            line-height: 1.6;
        }
        
        /* Footer */
        .email-footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666666;
            border-top: 1px solid #e0e0e0;
        }
        
        .email-footer p {
            margin: 5px 0;
        }
        
        /* Utilidades */
        .text-center { text-align: center; }
        .text-muted { color: #666666; }
        .mt-0 { margin-top: 0; }
        .mb-0 { margin-bottom: 0; }
        
        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                border-radius: 0 !important;
            }
            .email-content {
                padding: 20px 15px !important;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <div class="logo">🏡</div>
                <h1>CRM Inmobiliaria</h1>
            </div>
            
            <!-- Contenido principal -->
            <div class="email-content">
                <?= $content ?? '' ?>
            </div>
            
            <!-- Footer -->
            <div class="email-footer">
                <p><strong>CRM Inmobiliaria</strong></p>
                <p>Expertos en tasaciones y gestión inmobiliaria</p>
                <p style="margin-top: 15px; font-size: 11px;">
                    Este correo ha sido enviado automáticamente. Por favor, no responda a este mensaje.
                </p>
            </div>
        </div>
    </div>
</body>
</html>

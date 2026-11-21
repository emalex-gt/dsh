<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Nuevo brief recibido</title>
</head>
<body style="margin:0;background:#f1f5f9;color:#0f172a;font-family:Arial,sans-serif;">
    <div style="max-width:640px;margin:0 auto;padding:32px 16px;">
        <div style="overflow:hidden;border-radius:20px;background:#ffffff;box-shadow:0 12px 32px rgba(15,23,42,.12);">
            <div style="padding:28px 32px;background:#071525;color:#ffffff;">
                <p style="margin:0;color:#67e8f9;font-size:12px;font-weight:700;letter-spacing:2px;text-transform:uppercase;">DSH Client Area</p>
                <h1 style="margin:14px 0 0;font-size:28px;line-height:1.2;">Nuevo brief recibido</h1>
            </div>
            <div style="padding:32px;">
                <p style="margin:0 0 22px;font-size:16px;line-height:1.6;">Se ha registrado un nuevo brief y está pendiente de revisión comercial.</p>
                <table role="presentation" style="width:100%;border-collapse:collapse;font-size:14px;">
                    <tr><td style="padding:10px 0;border-bottom:1px solid #e2e8f0;color:#64748b;">Empresa</td><td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-weight:700;text-align:right;">{{ data_get($brief->data, 'legal_name', 'No indicado') }}</td></tr>
                    <tr><td style="padding:10px 0;border-bottom:1px solid #e2e8f0;color:#64748b;">Marca</td><td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-weight:700;text-align:right;">{{ data_get($brief->data, 'brand_name', 'No indicado') }}</td></tr>
                    <tr><td style="padding:10px 0;border-bottom:1px solid #e2e8f0;color:#64748b;">Contacto</td><td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-weight:700;text-align:right;">{{ data_get($brief->data, 'contact_name', 'No indicado') }}</td></tr>
                    <tr><td style="padding:10px 0;border-bottom:1px solid #e2e8f0;color:#64748b;">Correo</td><td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-weight:700;text-align:right;">{{ data_get($brief->data, 'contact_email', 'No indicado') }}</td></tr>
                    <tr><td style="padding:10px 0;color:#64748b;">Servicio</td><td style="padding:10px 0;font-weight:700;text-align:right;">{{ data_get($brief->data, 'selected_service_summary', 'No indicado') }}</td></tr>
                </table>
                <p style="margin:28px 0 0;text-align:center;">
                    <a href="{{ url('/admin/briefs/'.$brief->id) }}" style="display:inline-block;border-radius:999px;background:#a3e635;color:#071525;padding:13px 22px;font-size:14px;font-weight:700;text-decoration:none;">Revisar brief en administración</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>

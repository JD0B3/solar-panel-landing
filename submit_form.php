<?php
// Configuración del correo
$to = "tu_correo@ejemplo.com"; // Reemplaza con tu correo
$subject = "Nueva Solicitud de Cotización";
$headers = "From: no-reply@tu-dominio.com\r\n";

// Verifica que el formulario se haya enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $address = htmlspecialchars($_POST['address']);

    // Adjuntar información
    $message = "Detalles de la solicitud:\n";
    $message .= "Nombre: $name\n";
    $message .= "Correo: $email\n";
    $message .= "Teléfono: $phone\n";
    $message .= "Dirección: $address\n";

    // Manejo de archivo adjunto
    if (isset($_FILES['bill']) && $_FILES['bill']['error'] == 0) {
        $file_tmp = $_FILES['bill']['tmp_name'];
        $file_name = $_FILES['bill']['name'];
        $file_data = file_get_contents($file_tmp);

        // Crear email con archivo adjunto
        $boundary = md5("random"); // Límite del contenido
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

        $body = "--$boundary\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $body .= $message . "\r\n";
        
        $body .= "--$boundary\r\n";
        $body .= "Content-Type: application/octet-stream; name=\"$file_name\"\r\n";
        $body .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($file_data)) . "\r\n";
        $body .= "--$boundary--";

        // Enviar email
        if (mail($to, $subject, $body, $headers)) {
            echo "Solicitud enviada exitosamente.";
        } else {
            echo "Error al enviar la solicitud.";
        }
    } else {
        echo "Error al cargar el archivo.";
    }
} else {
    echo "Método de solicitud no permitido.";
}
?>

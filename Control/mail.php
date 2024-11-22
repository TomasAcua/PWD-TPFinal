<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

//Create an instance; passing `true` enables exceptions

class mail
{
    private $mail;


    public function __construct()
    {
        $this->mail = new PHPMailer(true);
    }

    public function getMail()
    {
        return $this->mail;
    }
    public function enviarMail($email, $nombreUsr, $tipo, $productos)
    {

        try {
            $mail = $this->getMail();
            //Server settings
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'metzgergerman@gmail.com';                     //SMTP username
            $mail->Password   = 'biow xszd dpxm aqsd';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
            $mail->Port       = 587;   //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`


            // 
            //Recipients
            $mail->setFrom('CoffeeStore@gmail.com', 'Coffe Store');
            $mail->addAddress($email, $nombreUsr);     //Add a recipient


            // Plantilla base del correo electrónico
            $htmlTemplate = <<<HTML
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <style>
                        body {
                            background-color: #f1f1f1;
                            padding: 20px;
                            font-family: Arial, sans-serif;
                        }
                        .container {
                           width: 100%;
                           max-width: 600px;
                           margin: 0 auto;
                           background-color: #ffffff;
                           padding: 20px;
                           box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                       }
                       h1 {
                           text-align: center;
                           color: #333;
                       }
                       h2{
                        text-align: center;
                        color: #333;
                       }
                       footer {
                           text-align: center;
                           padding: 10px;
                           background-color: #f4f4f4;
                           font-size: 12px;
                           color: #888;
                       }
                    </style>
                </head>
                <body>
                <div class="container">
                    <h1>{h1_content}</h1>
                    <h2>{h2_content}</h2>
                    <hr>
                    <ul>
                        {ul_content}
                    </ul>
                    <h1>{total}</h1>
                </div>
                </body>
                </html>
                HTML;

            // Configuración de contenidos específicos dependiendo del tipo de mail
            $totalTxt = "TOTAL: $ ";
            $total = 0;
            $ulContent = "";
            switch ($tipo) {
                case "registro":
                    $titulo = 'Registro de cuenta realizado con exito';
                    $h1Content = 'Te has registrado exitosamente!';
                    $h2Content = "Bienvenido $nombreUsr a nuestra cafetería! ❤️";
                    break;

                case "revisando":
                    $titulo = 'Reviso cargado con exito';
                    $h1Content = 'Estamos revisando tu pedido';
                    $h2Content = "Hola $nombreUsr, estamos procesando tu solicitud. Pronto te daremos más información. 🔍🕵️‍♂️";
                    foreach($productos as $producto) {
                        $ulContent .= "<li>Nombre: {$producto["nombre"]}   <strong>X{$producto["cantidad"]}</strong>   <strong>$ {$producto["precio"]}</strong></li><br>";
                        $total += $producto["precio"] * $producto["cantidad"];
                    }
                    break;

                case "confirmado":
                    $titulo = 'Su compra fue confirmada';
                    $h1Content = '¡Compra confirmada!';
                    $h2Content = "Gracias por tu compra, $nombreUsr. Tu pedido está en camino. 📦✈️";
                    break;

                default:
                    $titulo = "Se te dio de ALTA en el rol de $tipo";
                    $h1Content = "Nuevo rol asignado: $tipo";
                    $h2Content = "Hola $nombreUsr, se te dio de ALTA el rol de $tipo. ⬆️";
                    break;
            }
            $totalTxt .= $total;

            // Reemplazar el contenido dinámico en la plantilla
            $html = str_replace('{h1_content}', $h1Content, $htmlTemplate);
            $html = str_replace('{h2_content}', $h2Content, $html);
            if($tipo == 'revisando'){
                $html = str_replace('{ul_content}', $ulContent, $html);
                $html = str_replace('{total}', $totalTxt, $html);
            }else{
                $html = str_replace('{ul_content}', "", $html);
                $html = str_replace('{total}', "", $html);
            }


            //Attachments

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $titulo;
            $mail->Body    = $html;

            if ($mail->send()) {
                $msg = true;
            } else {
                $msg = false;
            }
        } catch (Exception $e) {
            // nada otra vez
        }
        return $msg;
    }
}

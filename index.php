<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pagar con tarjeta de forma segura Demo</title>
<meta name="description" content="Pagar con tarjeta de forma segura. Redsys y TPV Virtual de caixabank."/>
<meta name="author" content="Jose Aguilar">
<link rel="shortcut icon" href="https://www.jose-aguilar.com/blog/wp-content/themes/jaconsulting/favicon.ico" />
<link rel="stylesheet" href="css/font-awesome.min.css">
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link rel="stylesheet" href="css/styles.css">
<script src="https://code.jquery.com/jquery-3.2.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>

<body>

<div class="container">
    <h1>Pagar con tarjeta de forma segura Demo</h1>
    <h2 class="lead">Pagar con tarjeta de forma segura. Redsys y TPV Virtual de caixabank.</h2>
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="https://www.jose-aguilar.com/blog/">Blog</a></li>
          <li class="breadcrumb-item"><a href="https://www.jose-aguilar.com/blog/como-implementar-una-pasarela-de-pago-mediante-tarjeta-de-credito-con-php/">Como implementar una pasarela de pago mediante tarjeta de credito utilizando PHP</a></li>
          <li class="breadcrumb-item active" aria-current="page">Pagar con tarjeta de forma segura Demo</li>
        </ol>
    </nav>
    
    <div class="row">
        <div id="content" class="col-lg-12">
<?php
$error = false;
$amount = false;

if (isset($_GET['error']))
    $error = $_GET['error'];

if (isset($_GET['amount']))
    $amount = $_GET['amount'];

if (isset($_POST['submitPayment'])) {
    
    $amount = $_POST['amount']; 
    
    if (!is_numeric($amount)) {
        header('Location: https://www.jose-aguilar.com/scripts/php/redsys-pago-con-tarjeta/?error=1');
    }
    
    include "api/apiRedsys.php";  
    $miObj = new RedsysAPI;

    //$url_tpv = 'https://sis.redsys.es/sis/realizarPago';
    $url_tpv = 'https://sis-t.redsys.es:25443/sis/realizarPago';  //https://sis-t.redsys.es:25443/sis/realizarPago
    //$url_tpv = 'http://localhost/pago/index.php';
    //$url_tpv = 'https://sis-t.redsys.es:25443/sis/realizarPago';
    $version = "HMAC_SHA256_V1"; 
    $clave = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7'; //poner la clave SHA-256
    $name = 'Said Guerra'; //cambiar este dato
    $code = '999008881'; //cambiar este dato
    $terminal = '001';
    $order = date('ymdHis');
    $amount = $amount * 100;    
    $currency = '978';
    $consumerlng = '001';
    $transactionType = '0';
    //$urlMerchant = 'https://www.jose-aguilar.com/scripts/php/redsys-pago-con-tarjeta/'; //cambiar este dato
    $urlMerchant = 'http://localhost/pago/index.php'; //cambiar este dato
    $urlweb_ok = 'http://localhost/pago/tpv_ok.php'; //cambiar este dato
    $urlweb_ko = 'http://localhost/pago/tpv_ko.php'; //cambiar este dato

    $miObj->setParameter("DS_MERCHANT_AMOUNT", $amount);
    $miObj->setParameter("DS_MERCHANT_CURRENCY", $currency);
    $miObj->setParameter("DS_MERCHANT_ORDER", $order);
    $miObj->setParameter("DS_MERCHANT_MERCHANTCODE", $code);
    $miObj->setParameter("DS_MERCHANT_TERMINAL", $terminal);
    $miObj->setParameter("DS_MERCHANT_TRANSACTIONTYPE", $transactionType);
    $miObj->setParameter("DS_MERCHANT_MERCHANTURL", $urlMerchant);
    $miObj->setParameter("DS_MERCHANT_URLOK", $urlweb_ok);      
    $miObj->setParameter("DS_MERCHANT_URLKO", $urlweb_ko);
    $miObj->setParameter("DS_MERCHANT_MERCHANTNAME", $name); 
    $miObj->setParameter("DS_MERCHANT_CONSUMERLANGUAGE", $consumerlng);    

    $params = $miObj->createMerchantParameters();
    $signature = $miObj->createMerchantSignature($clave);
    ?>
    <form id="realizarPago" action="<?php echo $url_tpv; ?>" method="post">
        <input type='hidden' name='Ds_SignatureVersion' value='<?php echo $version; ?>'> 
        <input type='hidden' name='Ds_MerchantParameters' value='<?php echo $params; ?>'> 
        <input type='hidden' name='Ds_Signature' value='<?php echo $signature; ?>'> 
    </form>
    <p>Un momento por favor...</p>
    <script>
    $(document).ready(function () {
        $("#realizarPago").submit();
    });
    </script>
<?php
}
else {   
?>
<div class="jumbotron">
    <h3>Formulario de pago</h3>
    <form class="form-amount" action="index.php" method="post">
        <?php if ($error) { ?><div class="alert alert-danger">El valor introducido no es correcto. Debe introducir por ejemplo: 50.99</div><?php } ?>
        <div class="form-group">
            <label for="amount">Importe</label>
            <input type="text" id="amount" name="amount" class="form-control"<?php if ($amount) { ?> value="<?php echo $amount; ?>"<?php }else{ ?> placeholder="Por ejemplo: 50.00"<?php } ?>>
        </div>
        <input class="btn btn-lg btn-primary btn-block" name="submitPayment" type="submit" value="Pagar">
    </form> 
</div>    
<?php
}
?>
        </div>
    </div>
    
   
    
   
    
    <div class="card">
        <h5 class="card-header">Comparte en las redes sociales</h5>
        <div class="card-body">
            <h5 class="card-title">¿Te ha servido este ejemplo? Comparte con tus amigos</h5>
            <!-- Go to www.addthis.com/dashboard to customize your tools -->
            <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4ecc1a47193e29e4" async="async"></script>
            <!-- Go to www.addthis.com/dashboard to customize your tools -->
            <div class="addthis_sharing_toolbox"></div>
        </div>
    </div>

    <div class="footer-content row">
        <div class="col-lg-12">
            <div class="pull-right">
                <a href="https://www.jose-aguilar.com/blog/como-implementar-una-pasarela-de-pago-mediante-tarjeta-de-credito-con-php/" class="btn btn-secondary">
                    <i class="fa fa-reply"></i> volver al tutorial
                </a>
                <a href="https://www.jose-aguilar.com/scripts/php/redsys-pago-con-tarjeta/redsys-pago-con-tarjeta.zip" class="btn btn-primary">
                    <i class="fa fa-download"></i> Descargar
                </a>
            </div>
        </div>
    </div>
    
</div>
<footer class="footer bg-dark">
    <div class="container">
        <span class="text-muted"><a href="https://www.jose-aguilar.com/">&copy; Jose Aguilar</a></span>
    </div>
</footer>
</body>
</html>

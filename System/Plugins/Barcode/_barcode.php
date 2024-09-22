<?php
require_once('tcpdf_barcodes_1d.php');
$barcodeobj = new TCPDFBarcode($_GET['barcode'], 'C128');
$barcodeobj->getBarcodePNG(2, 30, array(0,0,0));
?>
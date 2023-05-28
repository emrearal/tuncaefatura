<?php
require 'ortak.php';
require 'webservisfonksiyonlari.php';
$cevap=maililefaturagonder ($WsdlAdres,$WsdlKullaniciAdi,$Wsdlsifre,$_POST['faturano'],$_POST['email']);
$sonuc="";
if (gettype($cevap) === 'object' && get_class($cevap) === 'stdClass') { // obje döndürdüyse sorun yok demektir
    $sonuc="{$_POST['faturano']} Nolu Fatura {$_POST['email']} 'ye Gönderilmiştir.";
}else {
    $sonuc=$cevap; // hata varsa hatayı bildiren string dönmüştür.
}   
echo $sonuc;
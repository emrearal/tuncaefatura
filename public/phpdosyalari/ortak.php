<?php
if (PHP_OS=='Linux') { // Linux ise bu ayarları kullan
    require ($_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php'); 
    $mysqlurl='localhost';
    $mysqlusername='root'; 
    $mysqlpassword='12345678';
    $mysqldatabase='tuncafatura';
    $numaratorbaslangici=0;
} else {
    require ('C:\Users\emrea\vendor\autoload.php');   
    $mysqlurl='localhost';
    $mysqlusername='root';  
    $mysqlpassword='12345678';
    $mysqldatabase='tuncafatura';
    $numaratorbaslangici=1000;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

/* Canlıda sadece bunlar. Yukarıdakilerin hepsini sil
    $mysqlurl='localhost';
    $mysqlusername='id20731575_emrearal';  
    $mysqlpassword='Tunca123,';
    $mysqldatabase='id20731575_tuncafatura';
    $numaratorbaslangici=2000;
*/

date_default_timezone_set('Europe/Istanbul'); // zaman dilimini ayarla
// efinans webservis bilgileri
$WsdlAdres 			= "https://erpefaturatest.cs.com.tr:8043/efatura/ws/connectorService?wsdl";
$WsdlKullaniciAdi 	= "kullanıcıadınızıyazın";
$Wsdlsifre			= "sifreniziyazın";

$baglanti = new mysqli($mysqlurl,$mysqlusername,$mysqlpassword,$mysqldatabase);
$baglanti->set_charset("utf8");
if($baglanti->connect_error) {exit('Bağlantı Hatası');}

function tarihitersinecevir($tarih) {
    $yil=substr($tarih, 0,4);
    $ay=substr($tarih, 5,2);
    $gun=substr($tarih, 8,2);
    $donmustarih= "$gun-$ay-$yil";
    return $donmustarih;
}
   
function firmabilgilerigetir($firmakodu) {
    $baglanti=$GLOBALS['baglanti'];
    $komut="select * from firmakartlari where sirano = '$firmakodu'";
    $result = $baglanti->query($komut);
    if ($result->num_rows==0) {  exit('Sonuç Bulunamadı');}
    $sonuc=array();
    while($row = $result->fetch_assoc()){
    $sonuc = ['sirano' => $row['sirano'],'firmaunvani' => $row['firmaunvani'],'adres' => $row['adres']
               ,'vergino' => $row['vergino'],'vergidairesi'=>$row['vergidairesi'],'sehir'=>$row['sehir']];
   }
    return $sonuc;
}

function stokadinigetir($stokkodu) {
    $baglanti=$GLOBALS['baglanti'];
    $komut="select stokadi from stoklar where sirano = '$stokkodu'";
    $result = $baglanti->query($komut);
    if ($result->num_rows==0) {  exit('Sonuç Bulunamadı');}
    $sonuc='';
    while($row = $result->fetch_assoc()){
    $sonuc = $row['stokadi'];
   }
    return $sonuc;
}

function arsivkaydiyap($tarih,$alici,$faturano,$toplamvergi,$toplamtutar) {
    $baglanti=$GLOBALS['baglanti'];$komut="insert into arsiv (tarih,alici,faturano,toplamvergi,toplamtutar) values ('$tarih','$alici','$faturano','$toplamvergi','$toplamtutar')";
    $baglanti->query($komut);
}

function uuiduret() { // 128 bitlik benzersiz numara üretmek için kullanılır.
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

function faturanumarasiuret() {
    $baglanti=$GLOBALS['baglanti'];
    $uuid=uuiduret();
    $komut="insert into numarator (uuid) values ('$uuid')";
    $baglanti->query($komut); // uuidyi numaratöre kaydet , otomatik sırano'nun oluşmasını sağla. 
    $komut="select sirano from numarator where uuid='$uuid'"; // bu uuid 'den oluşmuş sırano'yu getir.
    $result = $baglanti->query($komut);
    if ($result->num_rows==0) {  exit('Sonuç Bulunamadı');}
    $sonek='';
    while($row = $result->fetch_assoc()){
    $sonek=((int)$row['sirano'])+$GLOBALS['numaratorbaslangici']; // fatura numaramızın soneki.
    }
    $sonek=str_pad($sonek, 9, '0', STR_PAD_LEFT); // sola sıfır ekleyerek 9 basamağa tamamla. 
    $onek='TNC';
    $yileki=date("Y"); // içinde bulunduğumuz yıl. 
    $faturano=$onek.$yileki.$sonek;
    return (['sirano'=>$sonek,'faturano'=>$faturano,'uuid'=>$uuid]); // oluşturduğumuz fatura no ve uuid yi gönder. 
}
?>
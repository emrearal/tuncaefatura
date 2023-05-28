<?php
require 'ortak.php';
$neyapacagiz=$_POST['neyapacagiz'];

if (substr($neyapacagiz,-3)=='sil') { // silme işlemi ise  durumsil,rakipsil  urunsil  clientsil olabilir
    $colnumbertodelete=$_POST['colnumbertodelete'];
    $neneyedenk =['musterisil'=>'firmakartlari','stoksil'=>'stoklar'];
    
    $komut="delete from ".$neneyedenk[$neyapacagiz]." where sirano='$colnumbertodelete'";
    if ($baglanti->query($komut) === FALSE) {
        echo ("Database Error !");
        echo $baglanti->error;
    } else {
        echo ("Silindi");
    }
}

 if ($neyapacagiz=='listelemusteri') {
    $komut="select * from firmakartlari order by sirano asc";
    $result = $baglanti->query($komut);
    if ($result->num_rows==0) {  exit('Sonuç Bulunamadı');}
    $sonuc=array();
    while($row = $result->fetch_assoc()){
    $gecici = ['sirano' => $row['sirano'],'firmaunvani' => $row['firmaunvani'],'adres' => $row['adres']
               ,'vergino' => $row['vergino']];
    array_push($sonuc,$gecici);
    }
    echo json_encode($sonuc);
}

if ($neyapacagiz=='yenimusterikaydet') {
    $komut1="select firmaunvani from firmakartlari where firmaunvani='".addslashes($_POST['firmaunvani'])."'";
    $komut2="insert into firmakartlari (firmaunvani,adres,vergino,vergidairesi,sehir) 
    values ('".addslashes($_POST['firmaunvani'])."','".addslashes($_POST['adres'])."','".addslashes($_POST['vergino'])."'
    ,'".addslashes($_POST['vergidairesi'])."','".addslashes($_POST['sehir'])."')";
    $result = $baglanti->query($komut1);
 if ($result->num_rows==0) {  // eğer benzeri yoksa kaydet
     $sonuc='';
     if ($baglanti->query($komut2) === FALSE) {
         echo ("Database Error !");
         echo $baglanti->error;
     } else {
         $sonuc='Yeni Veri Başarıyla Kaydedildi';
     }
 }else{
     $sonuc='Bu Veri Zaten Kayıtlıydı!';
 }
 echo $sonuc;
}

if ($neyapacagiz=='listelestok') {
    $komut="select * from stoklar order by sirano asc";
    $result = $baglanti->query($komut);
    if ($result->num_rows==0) {  exit('Sonuç Bulunamadı');}
    $sonuc=array();
    while($row = $result->fetch_assoc()){
    $gecici = ['sirano' => $row['sirano'],'stokadi' => $row['stokadi'],'kdvorani' => $row['kdvorani']];
    array_push($sonuc,$gecici);
    }
    echo json_encode($sonuc);
}

if ($neyapacagiz=='yenistokkaydet') {
    $komut1="select stokadi from stoklar where stokadi='".addslashes($_POST['stokadi'])."'";
    $komut2="insert into stoklar (stokadi,kdvorani) 
    values ('".addslashes($_POST['stokadi'])."','".addslashes($_POST['kdvorani'])."')";
    $result = $baglanti->query($komut1);
 if ($result->num_rows==0) {  // eğer benzeri yoksa kaydet
     $sonuc='';
     if ($baglanti->query($komut2) === FALSE) {
         echo ("Database Error !");
         echo $baglanti->error;
     } else {
         $sonuc='Yeni Veri Başarıyla Kaydedildi';
     }
 }else{
     $sonuc='Bu Veri Zaten Kayıtlıydı!';
 }
 echo $sonuc;
}

if ($neyapacagiz=='sehirlerigetir') {
    $komut="select * from sehirler order by sehiradi asc";
    $result = $baglanti->query($komut);
    if ($result->num_rows==0) {  exit('Sonuç Bulunamadı');}
    $sonuc=array();
    while($row = $result->fetch_assoc()){
    array_push($sonuc,$row['sehiradi']);
    }
    echo json_encode($sonuc);
}

if ($neyapacagiz=='arsivgetir') {
    $komut="select * from arsiv order by faturano desc";
    $result = $baglanti->query($komut);
    if ($result->num_rows==0) {  exit('Sonuç Bulunamadı');}
    $sonuc=array();
    while($row = $result->fetch_assoc()){
        $gecici = ['sirano' => $row['sirano'],'tarih' => tarihitersinecevir($row['tarih']),'musteri' => mb_substr($row['alici'], 0, 14)."..."
        ,'fatno' => $row['faturano'],'vergi' => $row['toplamvergi'],'toplam' => $row['toplamtutar']];
        array_push($sonuc,$gecici);
        }
        echo json_encode($sonuc);
    }
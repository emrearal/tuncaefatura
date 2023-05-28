<template>
<transition name="modal">  
<div v-show="modalgoster" class="modalekran">
  <div><button type="button" @click="modalgoster=false"  class="btn btn-info resetdugmesi"><i class="fa fa-times" aria-hidden="true"></i></button></div>
  <br><br>
  <div class="modalmetni"><p v-html="modalmetni" ></p></div>
</div>
</transition>

<div :class="{ disabled: modalgoster }">
 <nav class="navbar navbar-warning bg-light navbar-fixed-top" >
  <div class="container-fluid  bg-warning">
    <div class="navbar-header ">
      <img src="resimler/tuncalogo.png" @click="openurl('tuncamyo.trakya.edu.tr')" class="tuncalogo"> 
      <div style="width:180px"><i><b>TUNCA E-FATURA</b></i><br><i class='fa fa-user' style='font-size:16px;color:blue'></i> {{ aktifkullanici }}</div>
    </div>
    <ul class="nav navbar-nav">
      <li class="nav-item menudugmesibosluklari">
         <button  :disabled="menukilitli" :class="{'active': aktifmenudugmesi.giris}"  type="button" @click="goto('giris')" class="menuElement btn btn-warning btn-block">Giriş</button>
      </li>
      <li class="nav-item menudugmesibosluklari">
         <button  :disabled="menukilitli" :class="{'active': aktifmenudugmesi.sabitler}" type="button" @click="goto('sabitler')" class="menuElement btn btn-info btn-block">Sabitler</button>
      </li>
      <li class="nav-item menudugmesibosluklari">
         <button  :disabled="menukilitli" :class="{'active': aktifmenudugmesi.faturaduzenle}"  type="button" @click="goto('faturaduzenle')" class="menuElement btn btn-success btn-block">Fatura Düzenle</button>
      </li>
      <li class="nav-item menudugmesibosluklari">
         <button  :disabled="menukilitli" :class="{'active': aktifmenudugmesi.arsiv}"  type="button" @click="goto('arsiv')" class="menuElement btn btn-danger btn-block">Arşiv</button>
      </li>
    </ul>
  </div>
 </nav>
  
 <div class="komponentboyutu">
  <GirisDep v-if="aktifmenudugmesi.giris"
  :aktifkullanici="aktifkullanici" @aktifkullanicidegistir="aktifkullanicidegistir" 
   @openurl="openurl" @mesajgoster="mesajgoster"/>

  <FaturaduzenleDep v-if="aktifmenudugmesi.faturaduzenle" :yol="yol"
   @goto="goto" @mesajgoster="mesajgoster"/>

  <SabitlerDep v-if="aktifmenudugmesi.sabitler" :yol="yol" :sehirler="sehirler" 
   @openurl="openurl" @mesajgoster="mesajgoster"/>

  <ArsivDep v-if="aktifmenudugmesi.arsiv" :yol="yol" @goto="goto" 
   @mesajgoster="mesajgoster"/>
</div>
</div>
</template>

<style>
@import "vue-select/dist/vue-select.css";
</style>

<script>
import axios from 'axios';
import GirisDep from './components/GirisDep.vue';
import FaturaduzenleDep from './components/FaturaduzenleDep.vue';
import SabitlerDep from './components/SabitlerDep.vue';
import ArsivDep from './components/ArsivDep.vue';

export default {
  name: 'App',
  components: {
    GirisDep,
    FaturaduzenleDep,
    SabitlerDep,
    ArsivDep
  },

  data: function () {
    return {
      aktifmenudugmesi:{giris:true,sabitler:false,faturaduzenle:false,arsiv:false},
      aktifkullanici:'Misafir',
      yol:'',
      sehirler:[],
      menukilitli:false,
      modalgoster:false,
      modalmetni:"",
      }
   },

   methods: {
   goto(nereye) {
    if (nereye=='kilitle') {
      this.menukilitli=true;
      return;
    }
    if (nereye=='kilitac') {
      this.menukilitli=false;
      return;
    }
    if(nereye!='giris' && this.aktifkullanici=='Misafir'){
      return; // giriş yapılmadıysa menülerde gezemezsin
    }
    this.menukilitli=false;
    for (let anahtar in this.aktifmenudugmesi) { // hepsini false yap. 
      this.aktifmenudugmesi[anahtar] = false;
    }
    this.aktifmenudugmesi[nereye]=true; // basılan düğmeyi aktif yap
    },

    mesajgoster (mesaj) {
      this.modalgoster=true;
      this.modalmetni=mesaj;
    },

    aktifkullanicidegistir(kim) {
    this.aktifkullanici=kim;
    this.mesajgoster('Hoşgeldin :'+this.aktifkullanici);
    this.goto('faturaduzenle'); // şifre girildikten sonra fatura düzenleme ekranına git.
    },

    openurl(url) {
      window.open('https://'+url);
    },

    async sehirlerigetir () {
    var phpyegidecekmetin="neyapacagiz=sehirlerigetir";
     try {
        const response =await axios.post(this.yol+'sabitislemleri.php',phpyegidecekmetin);
        if(response.data!='Sonuç Bulunamadı'){
        this.sehirler=response.data;
        }  
        } catch (error) {console.log('Error: ', error);        
      }
  },
   }, // methods sonu

   mounted () {
    this.goto('giris'); // açılışta giriş ekranına git.
    this.menukilitli=true;
  },  

  created () { // a.ılmadan önce bunları çalıştır
   var xmlhttp = new XMLHttpRequest();
   xmlhttp.open("GET", "/phpyolu.xml", false); 
   xmlhttp.send();
   var xmlDoc = xmlhttp.responseXML;
   var textElement = xmlDoc.getElementsByTagName("text")[0];
   this.yol = textElement.textContent;
   this.sehirlerigetir();
   }, // created sonu 
}  // export sonu
</script>
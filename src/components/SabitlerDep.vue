<template>
<transition name="modal">    
<div v-show="confirmboxgoster" class="modalekran">
   <div class="modalmetni">
    <p v-html="cbmetni" ></p>
  </div>
  <div>
    <button class="btn btn-danger"  @click="cbcevabi(false)" type="button" style='width:9em;float:right;margin-right:12px'>HAYIR</button>
    <button class="btn btn-success" @click="cbcevabi(true)" type="button" style='width:9em;margin-left:12px'>EVET</button>
  </div>
</div>
</transition>


<div :class="{ disabled: confirmboxgoster }">
<div style="width:90%;margin:auto;overflow: hidden;">
    <img src="resimler/logo.png" @click="this.$emit('openurl','www.trakya.edu.tr')" class="trakyalogo">
      <div v-for='deger in kutucukbilgi' :key='deger' style="width:100%;margin:auto" >
         <div><button type="button" class="btn btn-info btn-md btn-block menudugmesibosluklari" @click="sabitislemleri('listele' + deger[1])"><i :class=deger[2] style='font-size:20px;float:left;'></i>{{deger[0]}}</button></div>
      </div>
</div>
<br><br><br>

<div v-if="acikkapali.musterilisteleekranigoster" class="ekranstili">
        <button style='width:10em;margin-bottom:5px;' type="button" class="btn btn-info" @click="sabitislemleri('yenimusteriolustur')" ><i  class="fa fa-plus artiisareti" aria-hidden="true"></i>Yeni Müşteri</button>
        <table class="table table-striped table-light sabittablosu"> 
          <thead class="text-center listeletablokafasi"> 
            <tr>  
                <th scope="col" >No</th> 
                <th scope="col" >Ünvanı</th> 
                <th scope="col" >Vergi No</th> 
                <th scope="col" >Sil</th>
            </tr>  
          </thead>
          <tbody>
            <tr v-for="(deger,index) in ajaxcevabi" :key="deger.sirano"> 
                <td style='width:40px'>{{deger.sirano}}</td>
                <td style='width:70px'>{{deger.firmaunvani}}</td>
                <td style='width:80px'>{{deger.vergino}}</td>
                <td style='width:15px'>
                  <div style='width:100%;margin:auto'>
                    <i @click='silonayiiste("musteri",deger.sirano,index)' class='fa fa-trash silikonu' title='Sil' ></i>
                 </div>
                </td>  
            </tr>
          </tbody>
        </table>
</div>

<div v-if="acikkapali.yenimusteriolusturekranigoster" style='clear:both;margin-top:1em'>
     <div class="ekranstili">
      <h5>Lütfen Yeni Müşteri Bilgilerini Giriniz</h5> 
      <table class="table table-light sabittablosu">
        <tr>
          <td >Ünvan</td>
          <td style="padding:5px">
            <input v-model="unvan" class="form-control" type='text'>
          </td>
        </tr>
        <tr>
          <td>Adres</td>
          <td style="padding:5px"><input v-model="adres" class="form-control" type='text'></td>
        </tr>
        <tr>
          <td>Şehir</td>
          <td style="padding:5px">
            <vue-select v-model="sehir" :options="sehirler" style="width: 15em;"></vue-select>
          </td>
        </tr>
        <tr>
          <td>Vergi Dairesi</td>
          <td style="padding:5px"><input v-model="vergidairesi" class="form-control" type='text'></td>
        </tr>
        <tr>
          <td >Vergi No</td>
          <td style="padding:5px"><input v-model="vergino" class="form-control" type='text'>
          </td>
        </tr>
      </table> 
      <button class='btn btn-info btn-lg btn-block' @click='sabitislemleri("yenimusterikaydet")' >
            <i class="fa fa-save kaydetikonu" ></i>KAYDET</button>
     </div>      
</div>

<div v-if="acikkapali.stoklisteleekranigoster " class="ekranstili">
        <button style='width:10em;margin-bottom: 5px;' type="button" class="btn btn-info" @click="sabitislemleri('yenistokolustur')" ><i class="fa fa-plus artiisareti" aria-hidden="true"></i>Yeni Mal/Hizmet</button>
        <table class="table table-striped table-light sabittablosu"> 
          <thead class="text-center listeletablokafasi"> 
            <tr>  
                <th scope="col"  >No</th> 
                <th scope="col" >Mal Hizmet Adı</th> 
                <th scope="col" >KDV Oranı</th> 
                <th scope="col">Sil</th>
            </tr>  
          </thead>
          <tbody>
            <tr v-for="(deger,index) in ajaxcevabi" :key="deger.sirano"> 
                <td style='width:40px'>{{deger.sirano}}</td>
                <td style='width:70px'>{{deger.stokadi}}</td>
                <td style='width:80px'>{{deger.kdvorani}}</td>
                <td style='width:15px'>
                  <div style='width:100%;margin:auto'>
                    <i @click='silonayiiste("stok",deger.sirano,index)' class='fa fa-trash silikonu' title='Sil'></i>
                 </div>
                </td>  
            </tr>
          </tbody>
        </table>
</div>

<div v-if="acikkapali.yenistokolusturekranigoster" style='clear:both;margin-top:1em'>
     <div class="ekranstili">
      <h5>Lütfen Yeni Mal/Hizmet Bilgilerini Giriniz</h5> 
      <table class="table table-light sabittablosu">
        <tr>
          <td >Mal/Hizmet Adı</td>
          <td style="padding:5px">
            <input v-model="stokadi" class="form-control" type='text'>
          </td>
        </tr>
        <tr>
          <td>KDV Oranı</td>
          <td style="padding:5px">
            <select v-model="kdvorani" style="height:30px">
              <option value=18>% 18</option>
              <option value=8>% 8</option>
              <option value=1>% 1</option>
            </select>
          </td>
        </tr>
      </table>
      <button class='btn btn-info btn-lg btn-block' @click='sabitislemleri("yenistokkaydet")' >
            <i class="fa fa-save kaydetikonu" ></i>KAYDET</button>
     </div>      
</div>
</div>
</template>
  
  <script>
  import axios from "axios";
  import VueSelect from 'vue-select';

  export default {
    name: 'SabitlerDep',
    props: ["yol","sehirler"],
    emits:["openurl","mesajgoster"],

    components: {
      VueSelect,
    },
    
    data: function () {
    return {
      kutucukbilgi:[['Müşteriler','musteri','fa fa-industry',false],['Mal/Hizmet','stok','fa fa-cubes',false]],
      acikkapali : {musterilisteleekranigoster:false,stoklisteleekranigoster:false
                   ,yenimusteriolusturekranigoster:false,yenistokolusturekranigoster:false},
      ajaxcevabi:'',
      unvan:'',
      adres:'',
      vergino:'',
      vergidairesi:'',
      stokadi:'',
      kdvorani:18,
      sehir:'İSTANBUL',
      confirmboxgoster:false,
      cbmetni:"",
     }
  }, 

methods: {

cbcevabi(cevap) { // aslında bir fonksiyon bu. referans amaçlı. promislerde kullanılıyor. hem fonk hem değişken gibi.
return(cevap);
},

async confirmboxolustur (mesaj) { // asenkron promise kullanıyoruz. 
  this.confirmboxgoster=true; // cb göster
  this.cbmetni=mesaj; // metni bas   
  const cevap = await new Promise((resolve) => { // cevap bekle
  this.cbcevabi = resolve;  // cbcevabi değişince promis'i boz.
});
// cevap geldikten sonra yapılacaklar aşağıda
this.confirmboxgoster = false;
return cevap;
},

silonayiiste(sabitcinsi, silinecekeleman, sira) { // buradaki işlemler senkron. promis çözülene kadar program durur.
  return new Promise((resolve) => {
    this.confirmboxolustur(silinecekeleman + ' Nolu Kayıt Silinecektir. Emin Misiniz ?')
      .then((cevap) => {
        if (cevap) {
          this.sil(sabitcinsi, silinecekeleman, sira);
        }
        resolve(cevap); // promisi sonlandır
      });
  });
},

async sil(sabitcinsi,silinecekeleman,sira) {
      sira=parseInt(sira);
      silinecekeleman=parseInt(silinecekeleman);
      var phpyegidecekmetin='';
      phpyegidecekmetin="neyapacagiz="+sabitcinsi+"sil&colnumbertodelete="+silinecekeleman+"&token="+this.token;
      try {
        const response =await axios.post(this.yol+'sabitislemleri.php',phpyegidecekmetin);
        this.$emit('mesajgoster',response.data);
        if ((response.data).slice(-7)=='Silindi') {
               this.ajaxcevabi.splice(sira,1);
        }
      } catch (error) {console.log('Error: ', error);}
}, 

async sabitislemleri(neyapacagiz) {
   var phpyegidecekmetin='';
      // müşteri listele//
      if (neyapacagiz=='listelemusteri') {
        this.sadecebirekraniac('musterilisteleekranigoster');
        phpyegidecekmetin="neyapacagiz="+neyapacagiz;
        try {
            const response =await axios.post(this.yol+'sabitislemleri.php',phpyegidecekmetin);
            if(response.data!='Sonuç Bulunamadı'){
            this.ajaxcevabi=response.data; 
            }  
        } catch (error) {console.log('Error: ', error);        
        }
      }
      //yenimusteriolustur//
      if (neyapacagiz=='yenimusteriolustur') {
        this.tumverilerisifirla ();
        this.sadecebirekraniac('yenimusteriolusturekranigoster');
      }
      //yenimusterikaydet//
      if (neyapacagiz=='yenimusterikaydet') {
        if (this.unvan.length<5 || this.adres.length<5 || this.vergino.length<5 || this.vergidairesi.length<5) {
          this.$emit('mesajgoster','Her Veri En Az 5 Karakter Olmalıdır');
          return;
        }
        phpyegidecekmetin="neyapacagiz="+neyapacagiz+"&firmaunvani="+this.unvan+"&adres="+this.adres+"&vergino="+this.vergino
        +"&vergidairesi="+this.vergidairesi+"&sehir="+this.sehir;
        try {
          const response =await axios.post(this.yol+'sabitislemleri.php',phpyegidecekmetin);
          if(response.data!='Sonuç Bulunamadı'){
            this.$emit('mesajgoster',response.data);
          if(response.data=='Yeni Veri Başarıyla Kaydedildi') {
           this.sabitislemleri('listelemusteri');
           this.tumverilerisifirla ();
          }
        }  
         } catch (error) {console.log('Error: ', error);        
        }
      }
      //stok listele//
      if (neyapacagiz=='listelestok') {
        this.sadecebirekraniac('stoklisteleekranigoster');
        phpyegidecekmetin="neyapacagiz="+neyapacagiz;
      try {
        const response =await axios.post(this.yol+'sabitislemleri.php',phpyegidecekmetin);
        if(response.data!='Sonuç Bulunamadı'){
        this.ajaxcevabi=response.data; 
        }  
      } catch (error) {console.log('Error: ', error);        
      }
      }
      //yenistokolustur//
      if (neyapacagiz=='yenistokolustur') {
        this.tumverilerisifirla ();
        this.sadecebirekraniac('yenistokolusturekranigoster');
      }
      //yenistokkaydet//
      if (neyapacagiz=='yenistokkaydet') {
        if (this.stokadi.length<5 ) {
          this.$emit('mesajgoster','Mal/Hizmet Adı En Az 5 Karakter Olmalıdır');
          return;
        }
        phpyegidecekmetin="neyapacagiz="+neyapacagiz+"&stokadi="+this.stokadi+"&kdvorani="+this.kdvorani;
        try {
          const response =await axios.post(this.yol+'sabitislemleri.php',phpyegidecekmetin);
          if(response.data!='Sonuç Bulunamadı'){
            this.$emit('mesajgoster',response.data);
          if(response.data=='Yeni Veri Başarıyla Kaydedildi') {
            this.sabitislemleri('listelestok');
            this.tumverilerisifirla ();
          }
          }  
        } catch (error) {console.log('Error: ', error);        
        }
      }
},// sabitislemleri fonksiyonu sonu

tumverilerisifirla () {
      this.unvan=this.adres=this.vergino=this.vergidairesi=this.stokadi="";
      this.kdvorani=18;
      this.sehir='İSTANBUL';
},

sadecebirekraniac(acilacakekran)  { // tek ekranı açık tutup diğerlerini kapatır
     const hasOwn = Object.prototype.hasOwnProperty;
     for (const key in this.acikkapali) {
        if (hasOwn.call(this.acikkapali, key)) {
          this.acikkapali[key] = false;
          if (key==acilacakekran){
            this.acikkapali[key] = true;
         }
       }
      }
},

}, // methods sonu
} // export sonu
  </script>
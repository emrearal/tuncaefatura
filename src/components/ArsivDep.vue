<template>
  <div v-show="yukleniyor" class="bekleyiniz">
    <img src="resimler/lutfenbekleyiniz.gif"/>
  </div>

<transition name="modal" style="height: 175px;" >    
<div v-show="promtboxgoster" class="modalekran">
  <div><button type="button" @click="promtboxgoster=false" class="btn btn-info resetdugmesi"><i class="fa fa-times" aria-hidden="true"></i></button></div>
  
   <div class="modalmetni" style="height:30%">
    <span v-html="promtmetni" ></span>
  </div>
  <div  style="width:100%">
    <input type="text" class="form-control" style="width:100%" v-model="pbcevabi"/> 
    <button class="btn btn-success btn-sm emailgonderdugmesi" @click="emailgonder" type="button">Gönder</button>
  </div>
</div>
</transition>

   <div :class="{ disabled: yukleniyor || promtboxgoster }" style="width:100%;overflow: auto;margin-top:9.3em">
      <div style="width:100%">
      <button type="button" @click="reset()"  class="btn btn-danger resetdugmesi" ><i class="fa fa-undo" aria-hidden="true"></i></button>
      <span style="font-size:18px;">Gönderilmiş Faturalar Arşivi</span><br>
      <span style="font-size:14px;">E-mail Göndermek İçin Fatura Üzerine Tıklayın</span>
      </div>
      <table class="table table-striped table-light"> 
          <thead class="text-center" style='position: sticky;background: #dba9a9'> 
            <tr>
                <th>Tarih<br><input v-model="aramakutulari.tarih" style="width:50px" type='text' /></th>   
                <th>Müşteri<br><input v-model="aramakutulari.musteri" style="width:118px" type='text' /></th> 
                <th>Fatura No.<br><input v-model="aramakutulari.fatno" style="width:135px" type='text' /></th> 
                <th>Toplam<br><input v-model="aramakutulari.toplam" style="width:75px" type='text' /></th> 
                <th>Kdv<br><input v-model="aramakutulari.vergi" style="width:75px" type='text' /></th>
            </tr>  
          </thead>
          <tbody>
            <tr v-for="(deger,index) in aramasonuclari" :key="index" class="arsivlistesi" @click="promtboxgoxter(deger.fatno)"> 
                <td style='width:50px'>{{deger.tarih}}</td>
                <td style='width:118px'>{{deger.musteri}}</td>
                <td style='width:135px'>{{deger.fatno}}</td>
                <td style='width:75px'>{{deger.toplam}}</td>
                <td style='width:75px'>{{deger.vergi}}</td>
            </tr>
          </tbody>
        </table>
    </div>
  </template>
  <script>
  import axios from 'axios';
  export default {
  name: 'ArsivDep',
  props: ["yol"],
  emits:["goto","mesajgoster"],

data: function () {
  return {
    aramakutulari :{tarih:'',musteri:'',fatno:'',vergi:'',toplam:''},
    arsivdizini:[],
    aramasonuclari:[],
    yukleniyor:false,
    promtboxgoster:false,
    promtmetni:"",
    pbcevabi:"",
    maililegonderilecekfaturano:"",
   }
}, 

methods: {
  async arsivgetir () {
    var phpyegidecekmetin="neyapacagiz=arsivgetir";
     try {
        const response =await axios.post(this.yol+'sabitislemleri.php',phpyegidecekmetin);
        if(response.data!='Sonuç Bulunamadı'){
        this.arsivdizini=response.data;
        this.sirala();
        }  
        } catch (error) {console.log('Error: ', error);        
      }
  },

  promtboxgoxter (faturano){
    this.pbcevabi="hemrearal@trakya.edu.tr";
    var promtmetni=" numaralı faturanın gönderileceği E-Mail adresini giriniz."
    this.promtboxgoster=true;
    this.promtmetni=faturano+promtmetni;
    this.maililegonderilecekfaturano=faturano;
},
  
  async emailgonder () {
    if (!this.emailgecerlimi(this.pbcevabi)) { 
    this.$emit('mesajgoster','Geçerli Bir Mail Adresi Giriniz');
    return;
    }
    this.promtboxgoster=false;
    this.yukleniyor=true;
    this.$emit('goto','kilitle');
    var phpyegidecekmetin="faturano="+this.maililegonderilecekfaturano+"&email="+this.pbcevabi;
     try {
        const response =await axios.post(this.yol+'emailgonder.php',phpyegidecekmetin);
        this.yukleniyor = false;
        this.$emit('goto','kilitac');
        this.$emit('mesajgoster',response.data); 
      } catch (error) {console.log('Error: ', error);        
      }
  },

  emailgecerlimi (email) {
  return email.match(
    /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
  );
  },

  sirala () {
    this.aramasonuclari=[];
    var tabloorjinal=this.arsivdizini;
      for (var i=0;i<tabloorjinal.length;i++) {
      if((tabloorjinal[i].tarih.toUpperCase().indexOf(this.aramakutulari.tarih.toUpperCase().trim()) > -1) 
      && (tabloorjinal[i].musteri.toUpperCase().indexOf(this.aramakutulari.musteri.toUpperCase().trim()) > -1)
      && (tabloorjinal[i].fatno.toUpperCase().indexOf(this.aramakutulari.fatno.toUpperCase().trim()) > -1)
      && (tabloorjinal[i].vergi.toUpperCase().indexOf(this.aramakutulari.vergi.toUpperCase().trim()) > -1)
      && (tabloorjinal[i].toplam.toUpperCase().indexOf(this.aramakutulari.toplam.toUpperCase().trim()) > -1)){
        this.aramasonuclari.push(tabloorjinal[i]); // 5 şartı da karşılıyorsa sepete ekle
      }
    }
  },

  reset () {
    this.aramakutulari={tarih:'',musteri:'',fatno:'',vergi:'',toplam:''};
  },
},

watch: {
   aramakutulari: {
      deep: true,
      handler: function() {
        this.sirala();
      }
    }
  },

mounted () {
  this.arsivgetir();
},

}
</script>
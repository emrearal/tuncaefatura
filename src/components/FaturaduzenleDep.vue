<template>
 <div v-show="yukleniyor && !modalgoster" class="bekleyiniz">
    <img src="resimler/lutfenbekleyiniz.gif"/>
</div>

<div :class="{ disabled: yukleniyor}" style="margin:auto;overflow:auto">
          <table class="table table-light  table-sm" > 
            <thead class="text-center" > 
             <th  colspan="2" style="background-color:rgb(107 155 119)">
              <div>
                <span >FATURA BİLGİLERİNİ GİRİN</span>
              </div>
              </th>  
            </thead>
            <tbody>
             <tr >
              <td style="width:16.8em">Fatura Tarihi</td>
              <td><input style='width:12.7em' type="date" v-model="faturatarihi"></td>
             </tr>
             <tr>
                <td>Müşteri Ünvanı</td>
                <td><vue-select v-model="secilenfirmaunvani" :options="firmalar" label="firmaunvani" :reduce="option => option.sirano" style="width: 12.7em"></vue-select>
                </td>
             </tr>
            </tbody>
          </table>
              <!--   FATURA SATIRI SAYISI ARTI EKSİSİ -->
          <table class="table table-striped table-success table-sm" > 
           <tbody>
             <tr> 
              <td>
                  <div class="btn-group mr-2 btn-group-sm" role="group" aria-label="Second group">
                    <div >Fatura Satırı Sayısı : {{satirsayisi}}</div>
                    <button style="margin-right:0.5em" @click="arti" type="button" class="btn btn-success"><i class="fa fa-plus" style="font-size:8px"></i></button>
                    <button @click="eksi" type="button" class="btn btn-success"><i class="fa fa-minus" style="font-size:8px"></i></button>
                  </div>
                  <div style="float:right;margin-left:5px">
                    <div >Genel Toplam</div>
                    <input type="text" :value="(parseFloat(kdvsizgeneltoplam)+parseFloat(kdvtoplami)).toFixed(2)" style="width:6.8em" disabled>
                  </div>
                  <div style="float:right;margin-left:5px">
                    <div >Kdv'siz Toplam</div>
                    <input type="text" v-model="kdvsizgeneltoplam" style="width:6.8em" disabled>
                  </div>
                  <div style="float:right">
                    <div >Kdv Toplamı</div>
                    <input type="text" v-model="kdvtoplami" style="width:6.8em" disabled>
                  </div>
               </td>
            </tr>
            </tbody>
          </table>
              <!--   FATURA SATIRLARI -->
          <table v-for="(deger,index) in faturasatirlaridizini" :key="index" class="table table-success table-sm"> 
           <tbody>
            <tr style="background-color:rgb(219, 241, 219)">
              <td >Mal/Hizmet #{{index+1}}</td>
              <td><vue-select v-model="deger[0]" @update:modelValue="malhizmetsecildi(index,deger[0])" :options="malvehizmetler" label="stokadi" :reduce="option => option.sirano" style="width: 12.8em;"></vue-select>
              </td>
             </tr>
             <tr>
              <td>Açıklama</td>
              <td>
                <input style="float:left;" type="text" v-model="deger[7]">
              </td>
              </tr>
              <tr>
              <td>Miktar</td>
              <td >
                <input style="float:left;width:7.6em" @keyup="satirhesaplariniyap(index)" type="text" v-model="deger[1]">
                <select v-model="deger[2]" style="width:5em;float:left;height:1.83em;">
                  <option v-for="(degerA,indexA) in olcubirimleri" :key="indexA" :value="degerA">{{ degerA }}</option> 
                </select> 
              </td>
              </tr>
              <tr>
              <td>Birim Fiyat(TL)</td>
              <td>
                <input style="float:left;" type="text" v-model="deger[3]" @keyup="satirhesaplariniyap(index)">
              </td>
              </tr>
               <tr>
              <td>Satır Kdv'si %{{ deger[6] }}</td>
              <td>
                <input disabled style="float:left" type="text" v-model="deger[5]">
              </td>
              </tr>
              <tr>
              <td>Kdv'siz Satır Toplamı</td>
              <td>
                <input disabled style="float:left" type="text" v-model="deger[4]">
              </td>
              </tr>
            </tbody>
           </table>
           <button class='btn btn-md btn-block' style="background-color:rgb(107 155 119)" @click='yenifaturakes()' >
            <i class="fa fa-save kaydetikonu" ></i>
              <span ><b>KAYDET</b></span>
           </button>
  </div>  

  </template>
  
  <script>
  import VueSelect from 'vue-select';
  import axios from 'axios';
  
  export default {
  name: 'FaturaduzenleDep', 
  
  components: {
      VueSelect,
  },
  
  props: ["yol"],
  emits:["goto","mesajgoster"],
  
  data: function () {
    return {
      satirsayisi:1,
      faturatarihi:'',
      firmalar:[],
      secilenfirmaunvani:'',
      olcubirimleri:['Adet','Kg','Litre','Ton'],
      malvehizmetler:[],
      faturasatirlaridizini:[["","","Adet","",0,0,0,""]], // o-malhizmetkodu,1miktar,2birim,3birimfiyat,4kdvtoplami,5kdvsiztoplam,6kdvorani,7açıklama
      kdvsizgeneltoplam:0,
      kdvtoplami:0,
      yukleniyor:false,
     } // return sonu
  }, // data sonu
  
  methods: {
 
  async malvehizmetlerigetir () {
    var phpyegidecekmetin="neyapacagiz=listelestok";
     try {
        const response =await axios.post(this.yol+'sabitislemleri.php',phpyegidecekmetin);
        if(response.data!='Sonuç Bulunamadı'){
        this.malvehizmetler=response.data;
        }  
        } catch (error) {console.log('Error: ', error);        
      }
  },

  async firmalarigetir () {
    var phpyegidecekmetin="neyapacagiz=listelemusteri";
     try {
        const response =await axios.post(this.yol+'sabitislemleri.php',phpyegidecekmetin);
        if(response.data!='Sonuç Bulunamadı'){
        this.firmalar=response.data;
        }  
        } catch (error) {console.log('Error: ', error);        
      }
  },

  async yenifaturakes() {
    //önce form kontrol
    if (this.secilenfirmaunvani=='') {this.$emit('mesajgoster',' Müşteri Seçimi Yapınız');return;}
    for (var i=0;i<this.faturasatirlaridizini.length;i++) { // her fatura satırını kontrole edeceğiz. 
      if (this.faturasatirlaridizini[i][0]=='' 
      || isNaN(this.faturasatirlaridizini[i][1]) || !this.faturasatirlaridizini[i][1] 
      || isNaN(this.faturasatirlaridizini[i][3]) || !this.faturasatirlaridizini[i][3])
      { this.$emit('mesajgoster','Mal/Hizmet, Miktar ve Fiyat Alanlarını Boş Bırakmayınız ve Geçerli Değerler Giriniz');
        return;
      }
    }
    this.yukleniyor=true;
    this.$emit('goto','kilitle');
    var phpyegidecekmetin="secilenfirmaunvani="+this.secilenfirmaunvani
    +"&faturasatirlaridizini="+JSON.stringify(this.faturasatirlaridizini)
    +"&faturatarihi="+this.faturatarihi+"&kdvsizgeneltoplam="+this.kdvsizgeneltoplam
    +"&kdvtoplami="+this.kdvtoplami;
     try {
        const response =await axios.post(this.yol+'fatura.php',phpyegidecekmetin);
        this.yukleniyor = false;
        this.$emit('goto','kilitac');
        this.$emit('mesajgoster',response.data); // sayfanın yüklenmesini için 1 saniye bekliyoruz.
        if((response.data).substr(0,4)!='HATA'){ // hata yoksa verileri sıfırla ve fatura kesme ekranına dön
          this.satirsayisi=1,
          this.faturatarihi=new Date().toJSON().slice(0, 10);
          this.secilenfirmaunvani='',
          this.faturasatirlaridizini=[["","","Adet","",0,0,0,""]],
          this.kdvsizgeneltoplam=this.kdvtoplami=0;
       }
       } catch (error) {console.log('Error: ', error);        
      } 
  },

  arti() {
    this.faturasatirlaridizini.push(["","","Adet","",0,0,0,""]);
    this.satirsayisi++;
  },

  eksi() {
    if (this.faturasatirlaridizini.length==1) {return;}
    this.faturasatirlaridizini.pop();
    this.satirsayisi--;
    this.satirhesaplariniyap(this.satirsayisi-1);
  },

 malhizmetsecildi(index,sirano) {
  //konu siranolu malhizmetin kdv orani nedir. tüm malhizmetlere bakacağız 
  for (var j=0;j<this.malvehizmetler.length;j++) { // siranosu x olan değerin kdv oranini bul. 
            if (this.malvehizmetler[j]['sirano']==sirano) { // bulduysak...
              this.faturasatirlaridizini[index][6]=this.malvehizmetler[j]['kdvorani']; // onun kdv orani bizimkidir. 
          }
  }
  this.satirhesaplariniyap(index);
 },

 satirhesaplariniyap(satirindeksi) {
   //önce satır kvdsiz tutar toplamı
  this.faturasatirlaridizini[satirindeksi][4]=(parseFloat(this.faturasatirlaridizini[satirindeksi][1])*parseFloat(this.faturasatirlaridizini[satirindeksi][3])).toFixed(2); 
  if(isNaN(this.faturasatirlaridizini[satirindeksi][4]) || !this.faturasatirlaridizini[satirindeksi][4]) { // NaN ise sıfır yaz
    this.faturasatirlaridizini[satirindeksi][4]=0;
 }
   // satırın kdv toplamı
   this.faturasatirlaridizini[satirindeksi][5]=(parseFloat(this.faturasatirlaridizini[satirindeksi][4])*((parseFloat(this.faturasatirlaridizini[satirindeksi][6])/100))).toFixed(2);
   // tüm faturasatırları dizinini dolaşarak genel toplamları al. 
   this.kdvsizgeneltoplam=this.kdvtoplami=0;
  for(var i=0;i<this.faturasatirlaridizini.length;i++) {
    this.kdvsizgeneltoplam+=(parseFloat(this.faturasatirlaridizini[i][4]));
    this.kdvtoplami+=(parseFloat(this.faturasatirlaridizini[i][5]));
   }
  },
}, // methods sonu
 
  created () {
  this.faturatarihi=new Date().toJSON().slice(0, 10);
  this.firmalarigetir();
  this.malvehizmetlerigetir();
  } // created sonu
} // export sonu
  </script>
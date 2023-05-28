<template>
<div>
  <img src="resimler/logo.png" @click="this.$emit('openurl','www.trakya.edu.tr')" class="trakyalogo">
  <div class="container" style="width:80%" >
<form>
  <div class="form-outline mb-4">
    <input v-model="kullaniciadi" type="email" id="form2Example1" class="form-control" />
    <label class="form-label" for="form2Example1">Kullanıcı Adı</label>
  </div>
  <div class="form-outline mb-4">
    <input v-model="sifre" type="password" id="form2Example2" class="form-control" />
    <label class="form-label" for="form2Example2">Şifre</label>
  </div>
  <div class="row mb-4">
    <div class="col d-flex justify-content-center">
    </div>
  </div>
  <button type="button" @click="login"  class="btn btn-warning btn-block mb-4">Giriş</button>
</form>
</div>
</div>

</template>

<script>
export default {
  name: 'GirisDep',
  props: ["aktifkullanici"],
  emits: ["aktifkullanicidegistir","openurl","mesajgoster"],

  data: function () {
    return {
    kullaniciadi:'tunca',
    sifre:'tunca',
    }
  },

  methods: {
   login() {
    if (!(this.kullaniciadi=='tunca' && this.sifre=='tunca')) {
      this.$emit('mesajgoster','Kullanıcı Adı ve Şifre Hatalı');
      return;
    }
    this.$emit('aktifkullanicidegistir',this.kullaniciadi); // ana kompdaki aktif kullanıcıyı değiştir.
    },
  }, // methods sonu
}



</script>

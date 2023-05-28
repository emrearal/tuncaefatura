<?php
function soapClientWSSecurityHeader($user, $password){
	    $tm_created = gmdate('Y-m-d\TH:i:s\Z');
	    $tm_expires = gmdate('Y-m-d\TH:i:s\Z', gmdate('U') + 180);
	    $simple_nonce = mt_rand();
	    $encoded_nonce = base64_encode($simple_nonce);
	    $ns_wsse = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
	    $ns_wsu = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
	    $password_type = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest';
	    $encoding_type = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary';
	    $root = new SimpleXMLElement('<root/>');
	    $security = $root->addChild('wsse:Security', null, $ns_wsse);
	    $timestamp = $security->addChild('wsu:Timestamp', null, $ns_wsu);
	    $timestamp->addAttribute('wsu:Id', 'Timestamp-28');
	    $timestamp->addChild('wsu:Created', $tm_created, $ns_wsu);
	    $timestamp->addChild('wsu:Expires', $tm_expires, $ns_wsu);
	    $usernameToken = $security->addChild('wsse:UsernameToken', null, $ns_wsse);
	    $usernameToken->addChild('wsse:Username', $user, $ns_wsse);
	    $usernameToken->addChild('wsse:Password', $password, $ns_wsse)->addAttribute('Type', $password_type);
	    $usernameToken->addChild('wsse:Nonce', $encoded_nonce, $ns_wsse)->addAttribute('EncodingType', $encoding_type);
	    $usernameToken->addChild('wsu:Created', $tm_created, $ns_wsu);
	    $root->registerXPathNamespace('wsse', $ns_wsse);
	    $full = $root->xpath('/root/wsse:Security');
	    $auth = $full[0]->asXML();
	    return new SoapHeader($ns_wsse, 'Security', new SoapVar($auth, XSD_ANYXML), true);
	}
				
   function faturaGonder($WsdlAdres,$WsdlKullaniciAdi,$Wsdlsifre,$belgeNo,$vergiTcKimlikNo,$uuid,$xmldosyasi,$hash) {
	  try {
		$opts = array('ssl' => array('verify_peer' => false,'verify_peer_name' => false));
		$context = stream_context_create($opts);
		$client = new SoapClient($WsdlAdres, array('stream_context' => $context));
	        $client->__setSoapHeaders(soapClientWSSecurityHeader($WsdlKullaniciAdi,$Wsdlsifre));
	        $result = $client->belgeGonder(
	            array(
	                'belgeNo'                   => $belgeNo,
	                'vergiTcKimlikNo'           => $vergiTcKimlikNo,
	                'belgeTuru'                 => 'FATURA_UBL',
	                'veri'                      => $xmldosyasi,
	                'belgeHash'                 => $hash,
	                'mimeType'                  => 'application/xml',
	                'belgeVersiyon'             => '3.0'
	            ));
	    }
	    catch(Exception $e) {
	        die($e->getMessage());
	    }
	    $cevap=eFaturaDurumNe($WsdlAdres,$WsdlKullaniciAdi,$Wsdlsifre,$result->belgeOid,$vergiTcKimlikNo,);
        return $cevap;
	  }
	
	function eFaturaDurumNe($WsdlAdres,$WsdlKullaniciAdi,$Wsdlsifre,$belgeOid,$vergiTcKimlikNo) {
	    sleep(10);
	    try {
			$opts = array('ssl' => array('verify_peer' => false,'verify_peer_name' => false));
			$context = stream_context_create($opts);
			$client = new SoapClient($WsdlAdres, array('stream_context' => $context));
	        $client->__setSoapHeaders(soapClientWSSecurityHeader($WsdlKullaniciAdi,$Wsdlsifre));
	        $result = $client->gidenBelgeDurumSorgula(
	            array(
	                'vergiTcKimlikNo'           => $vergiTcKimlikNo,
	                'belgeOid'                  =>  $belgeOid,
	            ));
	    }
	    catch(Exception $e) {
	        die($e->getMessage());
	    }
	    $efatno=$result->return->belgeNo;
	    $efatid=$result->return->ettn; // Bu fonksiyonda kullanmadık. Gönderdiğimiz uuid numarası ile aynı
	    if( !empty($efatno) && strlen($efatno)>5){  // Eğer belge no döndüyse efatura numarasını ve ettn yi fiş kayıtları veri tabanına kaydediyoruz.
            return ("BAŞARILI : Fatura No : $efatno Düzenlendi <br>'Arşiv' Menüsünden Faturayı E-Mail İle Gönderebilirsiniz");
        }
	    else {
	        return ("HATA : Fatura Numarası Dönmedi, Fatura Kesilmedi");
	    }
	} // fonk sonu

    function maililefaturagonder ($WsdlAdres,$WsdlKullaniciAdi,$Wsdlsifre,$faturano,$email) {
		sleep(3);
        try {
			$opts = array('ssl' => array('verify_peer' => false,'verify_peer_name' => false));
			$context = stream_context_create($opts);
			$client = new SoapClient($WsdlAdres, array('stream_context' => $context));
	        $client->__setSoapHeaders(soapClientWSSecurityHeader($WsdlKullaniciAdi,$Wsdlsifre));
	        $result = $client->faturaMailGonder(
	            array(
	                'vknTckn'   => '0860300750',
	                'inOut'     => 'GIDEN',
                    'faturaNo' => $faturano,
                    'alicilar' => $email,
                    'belgeFormati' => 'PDF',
               ));
	    }
	    catch(Exception $e) {
	        $result=$e->getMessage();
	    }
		return $result;
	}
?>
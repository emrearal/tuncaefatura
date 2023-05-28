<?php
require 'ortak.php';
require 'webservisfonksiyonlari.php';

@ini_set('default_charset', 'UTF-8'); // sunucu karakter kodlaması ayarı
ini_set("soap.wsdl_cache_enabled", "0"); // soap başlığı için sunucu ayarı

// Satıcı firmanın bilgileri objesi
$bizimfirma=['ad'=>'Asgard Taşımacılık ve Ticaret Limited Şirketi',
             'adres'=>'Barış Mah. 7. cadde Semaver Plaza',
             'vergino'=>'0860300750',
             'vergidairesi'=>'Beylikdüzü',
             'binano'=>'3/23',
             'ilce'=>'Beylikdüzü',
             'il'=>'İSTANBUL',
             'ulke'=>'TÜRKİYE',
             'tel'=>'05128767099',
             'eposta'=>'info@asgard.web.tr'
            ];

$faturakimligi=faturanumarasiuret(); // bu bir obje. anahtarlar : sirano,faturano,uuid .. sirano faturano'nun son 9 hanesi
$tarih=$_POST['faturatarihi'];
$musteribilgileri=firmabilgilerigetir($_POST['secilenfirmaunvani']);// bu bir obje. anahtarlar: sirano ,firmaunvani,adres,vergino,vergidairesi,sehir
$musteribilgileri['vergino']='0860300751'; // demoda alıcı vergi için sadece 0860300751 kullanabiliyoruz. O yüzden değiştiriyoruz bu değeri
$satirlar=json_decode($_POST['faturasatirlaridizini']);// fatura satırı bilgilerimiz dizini o-malhizmetkodu,1miktar,2birim,3birimfiyat,4kdvtoplami,5kdvsiztoplam,6kdvorani,7açıklama
$kdvsizgeneltoplam=floatval($_POST['kdvsizgeneltoplam']);
$kdvtoplami=floatval($_POST['kdvtoplami']);
$buyuktoplam=$kdvsizgeneltoplam+$kdvtoplami;
$satirsayisi=count($satirlar);
$faturasaati=substr(date('Y-m-d H:i:s'),-8);

$faturasatirlari='';
for ($i=0;$i<$satirsayisi;$i++) { // Her fatura satırı için döngüye giriyoruz. Arka arka arkaya ekleyip en son xml'e iliştireceğiz.
$satirno=$i+1;
$malhizmetadi=stokadinigetir($satirlar[$i][0]); // elimizde malhizmetin(stokun) sadece kodu vardı. Koddan malhizmet adı bulan fonksiyon.  
$faturasatirlari.="
<cac:InvoiceLine>
<cbc:ID>$satirno</cbc:ID>
<cbc:InvoicedQuantity unitCode='NIU'>{$satirlar[$i][1]}</cbc:InvoicedQuantity>
<cbc:LineExtensionAmount currencyID='TRY'>{$satirlar[$i][4]}</cbc:LineExtensionAmount>
<cac:TaxTotal>
	<cbc:TaxAmount currencyID='TRY'>{$satirlar[$i][5]}</cbc:TaxAmount>
	<cac:TaxSubtotal>
		<cbc:TaxAmount currencyID='TRY'>{$satirlar[$i][5]}</cbc:TaxAmount>
		 <cbc:Percent>{$satirlar[$i][6]}</cbc:Percent>
		 <cac:TaxCategory>
		 <cbc:Name>KDV</cbc:Name>
		 <cac:TaxScheme>
		 <cbc:Name>Katma Değer Vergisi</cbc:Name>
		 <cbc:TaxTypeCode>0015</cbc:TaxTypeCode>
		 </cac:TaxScheme>
	     </cac:TaxCategory>
	</cac:TaxSubtotal>
</cac:TaxTotal>
<cac:Item>
<cbc:Description>{$satirlar[$i][7]}</cbc:Description>
 <cbc:Name>$malhizmetadi</cbc:Name>
</cac:Item>
<cac:Price>
	 <cbc:PriceAmount currencyID='TRY'>{$satirlar[$i][3]}</cbc:PriceAmount>
</cac:Price>
</cac:InvoiceLine>";
} // fatura satırları döngüsünün sonu.

$xmldosyasi="<Invoice xmlns='urn:oasis:names:specification:ubl:schema:xsd:Invoice-2' xmlns:cac='urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2' xmlns:cbc='urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2' xmlns:ccts='urn:un:unece:uncefact:documentation:2' xmlns:ds='http://www.w3.org/2000/09/xmldsig#' xmlns:ext='urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2' xmlns:qdt='urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2' xmlns:ubltr='urn:oasis:names:specification:ubl:schema:xsd:TurkishCustomizationExtensionComponents' xmlns:udt='urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2' xmlns:xades='http://uri.etsi.org/01903/v1.3.2#' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:schemaLocation='urn:oasis:names:specification:ubl:schema:xsd:Invoice-2 UBL-Invoice-2.1.xsd'>
    <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
    <cbc:CustomizationID>TR1.2</cbc:CustomizationID>
    <cbc:ProfileID>TEMELFATURA</cbc:ProfileID>
    <cbc:ID>{$faturakimligi['faturano']}</cbc:ID>
    <cbc:CopyIndicator>false</cbc:CopyIndicator>
    <cbc:UUID>{$faturakimligi['uuid']}</cbc:UUID>
    <cbc:IssueDate>$tarih</cbc:IssueDate>
    <cbc:IssueTime>$faturasaati</cbc:IssueTime>
    <cbc:InvoiceTypeCode>SATIS</cbc:InvoiceTypeCode>
    <cbc:DocumentCurrencyCode>TRY</cbc:DocumentCurrencyCode>
    <cbc:PricingCurrencyCode>TRY</cbc:PricingCurrencyCode>
    <cbc:LineCountNumeric>$satirsayisi</cbc:LineCountNumeric>
    <cac:Signature>
        <cbc:ID schemeID='VKN_TCKN'>3250566851</cbc:ID>
        <cac:SignatoryParty>
            <cac:PartyIdentification>
                <cbc:ID schemeID='VKN'>3250566851</cbc:ID>
            </cac:PartyIdentification>
            <cac:PostalAddress>
                <cbc:CitySubdivisionName>ŞİŞLİ</cbc:CitySubdivisionName>
                <cbc:CityName>İSTANBUL</cbc:CityName>
                <cac:Country>
                    <cbc:Name>TÜRKİYE</cbc:Name>
                </cac:Country>
            </cac:PostalAddress>
        </cac:SignatoryParty>
        <cac:DigitalSignatureAttachment>
            <cac:ExternalReference>
                <cbc:URI>#Signature_</cbc:URI>
            </cac:ExternalReference>
        </cac:DigitalSignatureAttachment>
    </cac:Signature>
    <cac:AccountingSupplierParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID='VKN'>{$bizimfirma['vergino']}</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name>{$bizimfirma['ad']}</cbc:Name>
            </cac:PartyName>
            <cac:PostalAddress>
                <cbc:StreetName>{$bizimfirma['adres']}</cbc:StreetName>
                <cbc:BuildingNumber>{$bizimfirma['binano']}</cbc:BuildingNumber>
                <cbc:CitySubdivisionName>{$bizimfirma['ilce']}</cbc:CitySubdivisionName>
                <cbc:CityName>{$bizimfirma['il']} / {$bizimfirma['ulke']}</cbc:CityName>
                <cbc:PostalZone></cbc:PostalZone>
                <cac:Country>
                    <cbc:Name>{$bizimfirma['ulke']}</cbc:Name>
                </cac:Country>
            </cac:PostalAddress>
            <cac:PartyTaxScheme>
                <cac:TaxScheme>
                    <cbc:Name>{$bizimfirma['vergidairesi']}</cbc:Name>
                </cac:TaxScheme>
            </cac:PartyTaxScheme>
            <cac:Contact>
                <cbc:Telephone>{$bizimfirma['tel']}</cbc:Telephone>
                <cbc:ElectronicMail>{$bizimfirma['eposta']}</cbc:ElectronicMail>
            </cac:Contact>
        </cac:Party>
    </cac:AccountingSupplierParty>
    <cac:AccountingCustomerParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID='VKN'>{$musteribilgileri['vergino']}</cbc:ID>
            </cac:PartyIdentification>
			<cac:PartyName>
                <cbc:Name>{$musteribilgileri['firmaunvani']}</cbc:Name>
            </cac:PartyName>
			   <cac:PostalAddress>
                 <cbc:StreetName>{$musteribilgileri['adres']}</cbc:StreetName>
                <cbc:BuildingNumber>1114</cbc:BuildingNumber>
                <cbc:CitySubdivisionName></cbc:CitySubdivisionName>
                <cbc:CityName>{$musteribilgileri['sehir']}</cbc:CityName>
                <cbc:PostalZone></cbc:PostalZone>
                <cac:Country>
                    <cbc:Name>TÜRKİYE</cbc:Name>
                </cac:Country>
            </cac:PostalAddress>
            <cac:PartyTaxScheme>
                <cac:TaxScheme>
                    <cbc:Name>{$musteribilgileri['vergidairesi']}</cbc:Name>
                </cac:TaxScheme>
            </cac:PartyTaxScheme>
            <cac:Contact>
                <cbc:Telephone></cbc:Telephone>
            </cac:Contact>
        </cac:Party>
      </cac:AccountingCustomerParty>
<cac:PricingExchangeRate>
        <cbc:SourceCurrencyCode>TRY</cbc:SourceCurrencyCode>
        <cbc:TargetCurrencyCode>TRY</cbc:TargetCurrencyCode>
        <cbc:CalculationRate>1</cbc:CalculationRate>
</cac:PricingExchangeRate>
   <cac:TaxTotal>
        <cbc:TaxAmount currencyID='TRY'>$kdvtoplami</cbc:TaxAmount>
        <cac:TaxSubtotal>
            <cbc:TaxableAmount currencyID='TRY'>$kdvsizgeneltoplam</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID='TRY'>$kdvtoplami</cbc:TaxAmount>
            <cac:TaxCategory>
                <cbc:Name>KDV</cbc:Name>
                <cac:TaxScheme>
				<cbc:Name>Katma Değer Vergisi</cbc:Name>
				<cbc:TaxTypeCode>0015</cbc:TaxTypeCode>
			   </cac:TaxScheme>
			</cac:TaxCategory>
         </cac:TaxSubtotal>
    </cac:TaxTotal>
    <cac:LegalMonetaryTotal>
        <cbc:LineExtensionAmount currencyID='TRY'>$kdvsizgeneltoplam</cbc:LineExtensionAmount>
        <cbc:TaxExclusiveAmount currencyID='TRY'>$kdvsizgeneltoplam</cbc:TaxExclusiveAmount>
        <cbc:TaxInclusiveAmount currencyID='TRY'>$buyuktoplam</cbc:TaxInclusiveAmount>
        <cbc:AllowanceTotalAmount currencyID='TRY'>0</cbc:AllowanceTotalAmount>
        <cbc:PayableAmount currencyID='TRY'>$buyuktoplam</cbc:PayableAmount>
    </cac:LegalMonetaryTotal>
$faturasatirlari
</Invoice>"; // xml sonu

$hash = md5($xmldosyasi); // giden xml'in alıcıya doğru ulaştığının kontrol için kullanılır. Bir çeşit tek yönlü şifreleme
$cevap=faturaGonder($WsdlAdres,$WsdlKullaniciAdi,$Wsdlsifre,$faturakimligi['sirano'],$bizimfirma['vergino'],$faturakimligi['uuid'],$xmldosyasi,$hash);
if (!str_contains($cevap, 'HATA')) { // hata yoksa arşiv kaydını yap
    arsivkaydiyap($tarih,$musteribilgileri['firmaunvani'],$faturakimligi['faturano'],$kdvtoplami,$buyuktoplam);
}
echo $cevap;                                                    
?>
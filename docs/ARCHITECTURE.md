Mimari Plan ve Notlar

1. Altyapı (Docker)

Projeyi, ortam farklılıklarını ortadan kaldırmak amacıyla container yapısı üzerinde kuracağız.

* PHP 8.5
* Laravel 13
* Apache Web Server
Host Name: media-optimizer.local

2. Veritabanı (images tablosu)

Varyantlar artık tabloda saklanmayacak. Yalnızca orijinal resmin temel bilgileri tutulacaktır:

* id: primary unsigned int
* name: dosya isminden bağımsız isim
* description: opsiyonel resim tanımı
* mime_type: dosyanın türü
* folder: disk üzerinde yığılmayı önlemek için yıl/ay formatında klasörleme (örneğin 2026/08)
* file_checksum: mükerrer kayıtları önlemek amacıyla dosyanın hash imzası
* created_at ve updated_at

Not: Varyant ebatları ileride değişebileceğinden, veritabanında saklanmayacak ve yapılandırma dosyasından okunacaktır.

3. Klasör Parçalama (Sharding)

Sunucuda klasör okuma hızının düşmemesi için dosyalar alt klasörlere ayrılacaktır.
Örnek: ID’si 14598 olan resim için önce 6 haneye tamamlayıp (014598) klasörleri böleceğiz.
Erişim yolu şu şekilde olacaktır: variants/014/598/640xauto.jpg
Bu yöntemle, veritabanına sorgu yapmadan statik dosyaya doğrudan erişim sağlanacaktır.

4. İki Aşamalı Upload

Gereksiz dosya transferini önlemek amacıyla yükleme işlemi iki aşamalı olarak gerçekleştirilecektir:

1. JavaScript ile istemci tarafında dosyanın checksum’ı alınacak ve sunucuya iletilecektir.
2. Sunucu, bu checksum’a göre dosyanın daha önce yüklenip yüklenmediğini kontrol edecektir.
3. Dosya zaten mevcutsa, sistem dosyanın yüklü olduğu bilgisini iletecek; mevcut değilse, asıl yükleme işlemi başlatılacaktır.


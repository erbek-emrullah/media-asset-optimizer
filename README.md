# Media Asset Optimizer

## Problem
Medya sitelerinde editörler panele 10-15 MB boyutunda ham fotoğraflar yükler. Bu görseller sunucuyu zorlar, siteyi yavaşlatır ve diskte gereksiz yer tutar. Her haberde manşet, detay ve küçük resim gibi farklı boyutlar gerekir. Bunları elle oluşturmak çok zaman alır.

## Çözüm
Yüklenen görseli arka planda bir kuyruk (queue) yapısıyla otomatik olarak işleyen, kullanıcıyı bekletmeyen bir Laravel uygulaması:
- Config dosyasından okunan ebatlarda otomatik boyutlandırma (kesme yok, en-boy oranı korunur)
- Modern tarayıcılar için WebP formatına otomatik dönüşüm
- Orijinal görseli sıkıştırarak disk alanından tasarruf sağlama

## İşleyiş Akışı (Workflow)
1. **Yükleme:** Kullanıcı formdan orijinal görseli seçip yükler.
2. **Hızlı Kayıt:** Controller, dosyayı diske kaydeder. Veritabanına "bekliyor" (pending) durumuyla yazar ve kullanıcıya hemen başarılı mesajı döner.
3. **Kuyruk (Queue):** Zor ve uzun süren boyutlandırma işlemi, kullanıcıyı bekletmemek için arka plana, yani bir iş olarak, gönderilir.
4. **Arka Plan İşlemleri:** Kuyruktaki işçi, sıradaki görseli alır.
   Config dosyasında belirtilen boyutlara göre (Manşet, Detay, Thumbnail) boyutlandırır.
   Her boyutun bir de WebP versiyonunu oluşturur.
   Tüm işlemler bitince veritabanındaki durumu "tamamlandı" (completed) olarak günceller.
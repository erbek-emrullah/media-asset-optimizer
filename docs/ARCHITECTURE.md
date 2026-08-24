# Mimari Plan ve Notlar

Proje kodlamasına geçilmeden önce temel veritabanı ve klasör mimarisi kurgulandı.

## 1. Veritabanı Tablo Yapısı (images)
Sisteme yüklenen görsel verilerinin sağlıklı saklanması için `images` tablosu yapısı şu şekilde kurgulandı:

- **id:** Otomatik artan ID.
- **original_filename:** Yüklenen asıl dosyanın adı
- **stored_filename:** İsim çakışmalarını ve güvenlik zafiyetlerini önlemek amacıyla sistem tarafından üretilen benzersiz (UUID) isim.
- **file_size:** Dosya boyutu. (İleride boyut tasarrufu raporlaması yapılabilmesi için eklendi).
- **status:** İşlem durumunu takip etmek için 4 aşama kurgulandı: pending (kuyrukta), processing (işleniyor), completed (tamamlandı), failed (hata).
- **variants:** Boyutlandırılan görsellerin bilgileri ayrı bir tablo açmak yerine JSON formatında tek bir alanda toplandı. Amaç, ilişkili tablo karmaşasından kaçınmak ve veritabanı sorgularını yormamak.

## 2. Storage (Klasör) Yapısı
Yüklenen ve işlenen görsellerin sunucuda birbirine karışmaması için aşağıdaki dizin yapısı kurgulandı:

- storage/app/public/originals/ -> Yüklenen ham görseller.
- storage/app/public/variants/manset/ -> Manşet boyutuna getirilenler.
- storage/app/public/variants/detay/ -> Detay boyutuna getirilenler.
- storage/app/public/variants/thumbnail/ -> Küçük resimler.

Not: Sisteme yeni bir ebat eklenmek istendiğinde, koda müdahale edilmeden sadece config dosyası üzerinden yeni varyant klasörünün otomatik açılması planlandı.
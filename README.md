# ShipID
Woocommerce shipping plugin for Indonesian courier

So start from here, the docs will be in **Bahasa Indonesia**
---------------------------------------------------------------

## Apa Ini?
**ShipID** merupakan shipping plugin Woocommerce (2.2+) yang menyediakan fasilitas bagi user untuk dapat memilih layanan shipping dari perusahaan ekspedisi khusus Indonesia (JNE, TIKI, POS, dll)

Plugin ini mengasumsikan toko online hanya digunakan di kawasan Indonesia.

ShipID adalah sebuah proyek eksperimental yang belum sepenuhnya diuji keakuratan dan fungsionalitasnya secara menyeluruh. Untuk itu masih perlu banyak dikembangkan.

## Batasan
- ShipID bergantung sepenuhnya pada API dari [Raja Ongkir](http://rajaongkir.com)
- Sementara ini ShipID baru mendukung penghitungan tarif dari JNE saja

## Bug
- Bentrok dengan Local Delivery dan Local Pickup bawaan Woocommerce. (Tidak error, hanya akan menyebabkan salah perhitungan untuk beberapa daerah tujuan)
- Beberapa file Javascript ter-load otomatis di beberapa halaman admin

## Instalasi
Sebelumnya, pastikan Woocommerce telah aktif terlebih dulu.
Copy-kan seluruh folder ini ke dalam wp-content/plugins

Supaya plugin dapat mengakses API dari RajaOngkir, maka masukkan API KEY dari RajaOngkir. Jika Anda belum mempunyainya, silahkan mendaftar pada web RajaOngkir

Temukan baris kode berikut di file rajaongkiraccess.class.php, dan ganti sesuai dengan API KEY Anda : 
```php
// line 8
public static $api_key = 'API_KEY_ANDA';
```

Aktifkan plugin lewat halaman plugin di wordpress.
Aktifkan shipping method lewat halaman Woocommerce -> Settings -> Shipping -> ShipID
Pilih Propinsi dan Kota dari seller


## TODO
- Merapikan kode
- Mengijinkan admin memilih jenis shipping yang digunakan
- Tambah fitur yang mengijinkan proses COD (mengatasi bentrok dengan Local Delivery dan Local Pickup)

## Disclaimer
Plugin ini bukan merupakan official plugin dari RajaOngkir. Saya hanya membantu untuk mengimplementasikan layanan ini ke Woocommerce.
Plugin ini juga tidak berafiliasi dengan perusahaan apapun.
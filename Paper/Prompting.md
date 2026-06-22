# PROMPT AI  Penulisan Paper Jurnal JPTPP

## Topik: Pengembangan LMS Bimbingan Bahasa Arab Berbasis Web (Laravel + Vue.js)

## Target Jurnal: Jurnal Pendidikan: Teori, Penelitian, dan Pengembangan (JPtpp) — SINTA 3

---

> **Cara Penggunaan:**
> Salin setiap prompt di bawah ini ke Claude atau AI lainnya **satu per satu** secara berurutan.
> Setiap prompt menghasilkan satu bagian paper. Setelah semua selesai, gabungkan ke dalam template JPTPP.

---

## ═══════════════════════════════════════

## PROMPT 0 — KONTEKS AWAL (Kirim Pertama Kali)

## ═══════════════════════════════════════

```
Kamu adalah asisten akademik yang membantu saya menulis artikel ilmiah untuk diterbitkan di Jurnal Pendidikan: Teori, Penelitian, dan Pengembangan (JPtpp) — SINTA 3.

Berikut adalah konteks penelitian saya:

JUDUL PENELITIAN:
"Pengembangan Learning Management System Bimbingan Bahasa Arab Berbasis Web Menggunakan Framework Laravel pada Bait Tahfiz Al-Quran Ridhallah"

RINGKASAN PENELITIAN:
- Lembaga: Bait Tahfiz Al-Quran Ridhallah (BTQR), Aceh Besar, Aceh
- Masalah: Peserta program bimbingan bahasa Arab dari luar daerah tidak bisa hadir tatap muka
- Solusi: Membangun LMS berbasis web dengan Laravel (backend) dan Vue.js (frontend)
- Metode Pengembangan: Waterfall (Requirements → System Design → Implementation → Testing → Deployment → Maintenance)
- Pengguna sistem: Admin, Pengajar, Siswa
- Fitur utama: manajemen akun, kelola modul & materi, kelola ujian try out, belajar & kuis, pelaksanaan try out
- Kolaborasi: UI/UX dirancang oleh Muhammad Faqih Sofyan (penelitian terpisah), backend dikerjakan penulis
- Pengujian: Black-box testing pada fungsionalitas sistem

ATURAN PENULISAN JPTPP:
- Font: Times New Roman 12pt, double spasi, kertas A4
- Bahasa: Indonesia
- Abstrak: maks 150 kata (isi: tujuan, metode, temuan)
- Kata kunci: 3–5 kata
- Sistematika: Judul → Penulis → Abstrak → Kata Kunci → Pendahuluan (TANPA heading) → Metode → Hasil → Pembahasan → Kesimpulan dan Saran → Daftar Pustaka
- Heading Tingkat 1: KAPITAL, BOLD, CENTER (tanpa nomor)
- Referensi: APA 7th edition, min 20 referensi, maks 10 tahun terakhir, 80% jurnal internasional
- Pendahuluan TIDAK menggunakan heading/judul

Simpan konteks ini. Kita akan menulis paper bagian per bagian.
```

---

## ═══════════════════════════════════════

## PROMPT 1 — JUDUL DAN ABSTRAK

## ═══════════════════════════════════════

```
Berdasarkan konteks penelitian yang sudah kamu simpan, tuliskan:

1. JUDUL ARTIKEL dalam Bahasa Indonesia (dan versi Bahasa Inggrisnya)
   - Format: Kapital semua, tebal, mencerminkan: pengembangan + LMS + bahasa Arab + berbasis web + Laravel + BTQR
   - Maksimal 20 kata

2. ABSTRAK dalam Bahasa Indonesia
   - Maksimal 150 kata
   - Wajib memuat: (a) tujuan penelitian, (b) metode yang digunakan, (c) temuan/hasil, (d) signifikansi/kesimpulan singkat
   - Tidak ada singkatan yang tidak dijelaskan
   - Ditulis dalam satu paragraf

3. KATA KUNCI
   - 3–5 kata kunci
   - Pilih yang paling relevan dan sering dicari

Format output:
---JUDUL (ID)---
[judul Indonesia]

---JUDUL (EN)---
[judul Inggris]

---ABSTRAK---
[isi abstrak]

---KATA KUNCI---
[kata kunci 1], [kata kunci 2], [kata kunci 3], ...
```

---

## ═══════════════════════════════════════

## PROMPT 2 — PENDAHULUAN

## ═══════════════════════════════════════

```
Tuliskan bagian PENDAHULUAN untuk paper saya.

PENTING:
- Bagian Pendahuluan TIDAK menggunakan heading/judul (sesuai aturan JPTPP)
- Panjang: 4–6 paragraf
- Struktur internal pendahuluan:
  a) Paragraf 1: Konteks transformasi digital dalam pendidikan dan pentingnya LMS
  b) Paragraf 2: Gambaran BTQR dan permasalahan nyata yang dihadapi (peserta dari luar daerah)
  c) Paragraf 3: Tinjauan singkat penelitian-penelitian terdahulu yang relevan (LMS, bahasa Arab, Laravel)
  d) Paragraf 4: Gap penelitian — apa yang belum dilakukan peneliti sebelumnya
  e) Paragraf 5: Tujuan penelitian ini secara jelas dan spesifik

PENELITIAN TERDAHULU yang bisa dirujuk:
1. LMS berbasis Laravel + Tailwind CSS untuk SMA — validasi 90,95% (sangat baik)
2. LMS berbasis Kitab Al-Arabiyah Li An-Nasyi'in untuk pembelajaran bahasa Arab
3. Model LMS untuk pembelajaran bahasa Arab online di program Sastra Arab
4. LMS untuk pondok tahfiz Al-Quran
5. Tambahkan 3–5 referensi internasional terkait LMS dalam pendidikan Islam/bahasa Arab

PENTING: Sertakan sitasi dalam format (Penulis, Tahun) di dalam teks. Gunakan referensi nyata yang dapat diverifikasi.

Tulis dalam gaya akademik formal Bahasa Indonesia.
```

---

## ═══════════════════════════════════════

## PROMPT 3 — METODE

## ═══════════════════════════════════════

```
Tuliskan bagian METODE untuk paper saya.

Heading: METODE (kapital, bold, center)

Isi metode mencakup sub-bagian berikut (gunakan sub-heading Tingkat 2: Bold, Rata Tengah):

**Jenis Penelitian**
- Research and Development (R&D)
- Pendekatan: Waterfall (sekuensial linier)
- Alasan memilih Waterfall: kebutuhan sistem sudah terdefinisi jelas dari awal

**Subjek dan Lokasi Penelitian**
- Subjek: pengelola (Admin), pengajar (Ustadz), dan siswa BTQR
- Lokasi: Bait Tahfiz Al-Quran Ridhallah, Kabupaten Aceh Besar, Provinsi Aceh

**Tahapan Pengembangan (Waterfall)**
Jelaskan masing-masing 6 tahap secara singkat dan akademis:
1. Requirements — observasi, wawancara, adaptasi hasil analisis UI/UX kolaborator
2. System Design — Use Case Diagram (3 aktor: Admin, Pengajar, Siswa), ERD (tabel: admins, pengajars, siswas, moduls, materis, try_outs, try_out_hasil, progress_modul)
3. Implementation — coding Laravel (backend) + Vue.js (frontend)
4. Testing — Black-box testing pada seluruh fitur fungsional
5. Deployment — instalasi pada server, konfigurasi, pelatihan pengguna
6. Maintenance — perbaikan bug, pembaruan sesuai kebutuhan

**Instrumen Pengumpulan Data**
- Observasi lapangan di BTQR
- Wawancara dengan pengelola dan calon pengguna
- Dokumentasi analisis UI/UX dari penelitian kolaborasi

**Teknik Analisis**
- Analisis fungsionalitas sistem menggunakan hasil black-box testing
- Parameter: kesesuaian fungsi dengan spesifikasi yang dirancang

Gunakan bahasa akademik formal. Panjang: sekitar 500–700 kata.
```

---

## ═══════════════════════════════════════

## PROMPT 4 — HASIL

## ═══════════════════════════════════════

```
Tuliskan bagian HASIL untuk paper saya.

Heading: HASIL (kapital, bold, center)

Bagian hasil memaparkan produk pengembangan sistem LMS. Susun dengan sub-bagian berikut:

**Hasil Analisis Kebutuhan (Requirements)**
- Kebutuhan fungsional yang teridentifikasi: manajemen akun, pengelolaan modul/materi, pengelolaan ujian try out, fitur belajar & kuis, pelaksanaan try out, tracking progress siswa
- Sumber data: wawancara BTQR + hasil analisis UI/UX kolaborator

**Hasil Perancangan Sistem (System Design)**
- Jelaskan Use Case Diagram: 3 aktor dan hak aksesnya
  - Admin: kelola akun pengajar dan siswa, login/logout
  - Pengajar: kelola modul & materi, kelola ujian try out, login/logout
  - Siswa: belajar & kuis materi, pelaksanaan try out, login/logout
- Jelaskan ERD: struktur tabel dan relasi antar tabel utama
  - Tabel master: admins, pengajars, siswas
  - Tabel operasional: moduls, materis, try_outs, materi1 (sub-materi), progress_modul, hasil_try_out
- [Gambar 1. Use Case Diagram Sistem LMS BTQR]
- [Gambar 2. Entity Relationship Diagram Sistem LMS BTQR]

**Hasil Implementasi Sistem**
Deskripsikan fitur-fitur yang berhasil diimplementasikan:
- Fitur login multi-role (Admin, Pengajar, Siswa)
- Dashboard per peran pengguna
- Manajemen modul dan materi pembelajaran bahasa Arab (Nahwu, Sharaf, Mufradat, Qira'ah, Kitabah, Muhadatsah)
- Sistem kuis per modul
- Modul Try Out online dengan penilaian otomatis
- Tracking progress belajar siswa
- [Gambar 3. Tampilan Dashboard Siswa]
- [Gambar 4. Tampilan Halaman Materi Belajar]
- [Gambar 5. Tampilan Ujian Try Out]

**Hasil Pengujian Black-Box Testing**
Buat tabel hasil pengujian black-box seperti berikut:

Tabel 1. Hasil Pengujian Black-Box Testing Sistem LMS BTQR
| No | Fitur yang Diuji | Aksi/Input | Hasil yang Diharapkan | Hasil Aktual | Status |
|----|-----------------|------------|----------------------|--------------|--------|
| 1  | Login Admin | Input username & password valid | Masuk ke dashboard admin | Sesuai | ✓ Berhasil |
| 2  | Login Pengajar | Input kredensial pengajar | Masuk ke dashboard pengajar | Sesuai | ✓ Berhasil |
| 3  | Login Siswa | Input kredensial siswa | Masuk ke dashboard siswa | Sesuai | ✓ Berhasil |
| 4  | Kelola Akun Pengajar | Admin tambah akun baru | Data tersimpan di database | Sesuai | ✓ Berhasil |
| 5  | Upload Modul Materi | Pengajar upload file materi | File tersimpan, tampil di halaman siswa | Sesuai | ✓ Berhasil |
| 6  | Kuis Materi | Siswa jawab kuis | Skor dihitung otomatis | Sesuai | ✓ Berhasil |
| 7  | Pelaksanaan Try Out | Siswa ikut try out | Hasil tersimpan di tabel hasil_try_out | Sesuai | ✓ Berhasil |
| 8  | Progress Tracking | Siswa selesaikan modul | Progress terupdate di tabel progress_modul | Sesuai | ✓ Berhasil |

Tambahkan kalimat pengantar untuk setiap sub-bagian. Total panjang: 600–800 kata.

CATATAN: Untuk gambar, gunakan placeholder seperti [Gambar X. Keterangan] karena gambar akan dilampirkan secara terpisah.
```

---

## ═══════════════════════════════════════

## PROMPT 5 — PEMBAHASAN

## ═══════════════════════════════════════

```
Tuliskan bagian PEMBAHASAN untuk paper saya.

Heading: PEMBAHASAN (kapital, bold, center)

Struktur pembahasan:

**Paragraf 1 — Kesesuaian Metode Pengembangan**
Diskusikan mengapa metode Waterfall tepat untuk pengembangan LMS ini. Bandingkan dengan penelitian lain yang menggunakan Agile atau ADDIE. Hubungkan dengan karakteristik proyek (kebutuhan stabil, kolaborasi dengan desainer UI/UX terpisah).

**Paragraf 2 — Keunggulan Teknologi Laravel + Vue.js**
Bahas pilihan teknologi: Laravel sebagai backend (MVC, Eloquent ORM, keamanan) dan Vue.js sebagai frontend (SPA, reaktif, komponen reusable). Bandingkan dengan penelitian serupa yang menggunakan framework lain (CodeIgniter, Next.js, dll.). Sertakan referensi jurnal internasional.

**Paragraf 3 — Relevansi LMS untuk Lembaga Tahfiz**
Diskusikan konteks pembelajaran bahasa Arab di pesantren tahfiz: pentingnya penguasaan Nahwu, Sharaf, Mufradat untuk memahami Al-Quran. Bagaimana LMS yang dikembangkan menjawab keterbatasan akses geografis. Bandingkan dengan penelitian LMS di lembaga pendidikan Islam lainnya.

**Paragraf 4 — Hasil Pengujian dan Validitas Fungsional**
Interpretasikan hasil black-box testing (semua fitur berhasil). Apa artinya bagi kelayakan sistem? Bandingkan dengan standar pengujian sistem serupa dari penelitian terdahulu.

**Paragraf 5 — Keterbatasan Penelitian**
Sebutkan keterbatasan: (a) pengujian hanya pada fungsionalitas (belum uji usability/user satisfaction), (b) belum diuji pada skala pengguna besar, (c) fokus hanya pada fungsionalitas backend.

Panjang total: 600–800 kata. Sertakan minimal 8–10 sitasi dari penelitian terdahulu.
```

---

## ═══════════════════════════════════════

## PROMPT 6 — KESIMPULAN DAN SARAN

## ═══════════════════════════════════════

```
Tuliskan bagian KESIMPULAN DAN SARAN untuk paper saya.

Heading: KESIMPULAN DAN SARAN (kapital, bold, center)

**Kesimpulan** (3–4 kalimat padat):
- Jawab langsung rumusan masalah: sistem LMS berhasil dirancang dan dikembangkan menggunakan Laravel (backend) + Vue.js (frontend) dengan metode Waterfall
- Sistem memiliki 3 peran pengguna: Admin, Pengajar, Siswa
- Fitur lengkap: manajemen akun, modul materi bahasa Arab, kuis, try out, tracking progress
- Hasil black-box testing menunjukkan seluruh fitur berjalan sesuai spesifikasi

**Saran** (3–4 saran spesifik dan dapat ditindaklanjuti):
1. Penelitian selanjutnya perlu menguji sistem menggunakan metode usability testing (SUS/TAM) untuk mengukur kepuasan pengguna
2. Pengembangan fitur tambahan: video streaming materi, forum diskusi, notifikasi push
3. Perlu dilakukan uji coba dengan pengguna nyata dalam jumlah lebih besar untuk mengukur efektivitas pembelajaran
4. LMS dapat dikembangkan ke berbagai lembaga tahfiz lain di Indonesia yang memiliki masalah serupa

Gunakan bahasa akademik yang tegas dan tidak berulang dengan isi pembahasan.
```

---

## ═══════════════════════════════════════

## PROMPT 7 — DAFTAR PUSTAKA

## ═══════════════════════════════════════

```
Buatkan DAFTAR PUSTAKA untuk paper saya menggunakan format APA 7th Edition.

Ketentuan:
- Minimum 20 referensi
- Referensi tidak lebih dari 10 tahun terakhir (2015–2026)
- 80% dari jurnal internasional bereputasi (Scopus/WoS)
- 20% dari jurnal nasional terakreditasi (SINTA)
- Diurutkan secara alfabetis berdasarkan nama belakang penulis pertama

REFERENSI yang WAJIB masuk (dari proposal saya):
1. Yauma, A., Fitri, I., & Ningsih, S. (2021). Learning Management System (LMS) pada E-Learning Menggunakan Metode Agile dan Waterfall berbasis Website. Jurnal JTIK, 5(3), 323–328. https://doi.org/10.35870/jtik.v5i3.190
2. Ridha, M. R., Ayuni, S. K., & Shodiq, M. J. (2023). Pengembangan Media Learning Management System (LMS) Berbasis Kitāb Al-'Arabiyah Li An-Nāsyi'Īn. Al Mi'yar, 6(1), 1. https://doi.org/10.35931/am.v6i1.1842
3. Susilo, F. S., Hariyanto, E., & Utomo, R. B. Design and Development of a Learning Management System Information System using the Waterfall Method with the Laravel Framework. (pp. 6084–6089).
4. Perdana, F. P., Supratman, E., & Saputra, D. R. (2024). Designing a Modern Web Interface with Vue.js and Tailwind for University Information System. Brilliance: Research of Artificial Intelligence, 4(2), 956–963.
5. Wicaksono, G. W., et al. (2020). Analysis of Learning Management System Features based on Indonesian Higher Education National Standards. 2020 8th ICoICT.
6. Rahmani, A., Salistia, I. H., & Hizriani, N. (2023). Metode Pembelajaran Bahasa Arab Di Pondok Pesantren Nurul Hakim Kediri. Tatsqifiy, 4(1), 1–11.
7. Musyary, M., Kurniati, A., & Damarjati, C. (2023). Laravel Framework-Based Information System. Emerging Information Science and Technology, 4(2).
8. Baehaqi, A., et al. (2023). Front End Learning Management System Development Using the Nextjs Framework. Jurnal Teknik Informatika, 4(4), 899–911.
9. Fahmi, A. A., & Kholid, N. (2024). Manajemen Program Pembelajaran Bahasa Arab di Pesantren Darul Quran Aceh. Risalah, 10(4), 1519–1534.
10. Nugroho, R. P., Soepriyanto, Y., & Wedi, A. (2024). Development of Learning Management System with Gamification Approach. JTP, 26(3), 794–806.

TAMBAHKAN 10+ referensi internasional bereputasi yang relevan dengan topik berikut:
- LMS dalam pendidikan Islam / Islamic education technology
- Web-based learning system development
- Laravel framework in education
- Vue.js for educational applications
- Arabic language learning technology
- Software development methodology (Waterfall)
- Black-box testing methodology
- Learning management system evaluation

Pastikan semua referensi bisa diverifikasi dan dalam rentang tahun 2015–2026.
```

---

## ═══════════════════════════════════════

## PROMPT 8 — PENGECEKAN DAN PENYEMPURNAAN

## ═══════════════════════════════════════

```
Sekarang lakukan pengecekan akhir terhadap paper yang sudah kita tulis bersama.

Periksa dan perbaiki hal-hal berikut:

1. KONSISTENSI FORMAT:
   - Apakah semua heading sudah sesuai hierarki JPTPP?
   - Apakah Pendahuluan benar-benar tanpa heading?
   - Apakah semua gambar dan tabel sudah diberi keterangan yang benar?

2. KONSISTENSI KONTEN:
   - Apakah tujuan di pendahuluan sejalan dengan kesimpulan?
   - Apakah metode yang dijelaskan konsisten dengan yang dilaporkan di Hasil?
   - Apakah semua sitasi dalam teks ada di Daftar Pustaka dan sebaliknya?

3. KUALITAS BAHASA:
   - Perbaiki kalimat yang kurang akademis
   - Pastikan tidak ada repetisi yang tidak perlu
   - Pastikan penggunaan istilah teknis konsisten (LMS, framework, backend, frontend)

4. KELENGKAPAN:
   - Apakah abstrak sudah maks 150 kata?
   - Apakah ada minimal 20 referensi?
   - Apakah komposisi referensi 80% internasional sudah terpenuhi?

Berikan laporan singkat tentang apa yang perlu diperbaiki, lalu tampilkan versi final yang sudah disempurnakan.
```

---

## ═══════════════════════════════════════

## PROMPT BONUS — CAPTION GAMBAR (Placeholder)

## ═══════════════════════════════════════

```
Berikan keterangan gambar (figure caption) yang sesuai untuk paper saya dalam Bahasa Indonesia, untuk gambar-gambar berikut yang akan saya lampirkan:

1. Diagram Alir Tahapan Penelitian Waterfall (6 tahap)
2. Use Case Diagram Sistem LMS BTQR (3 aktor: Admin, Pengajar, Siswa)
3. Entity Relationship Diagram (ERD) Sistem LMS BTQR
4. Tampilan Dashboard Siswa
5. Tampilan Halaman Daftar Modul Materi
6. Tampilan Halaman Belajar & Kuis
7. Tampilan Halaman Try Out Online

Format setiap caption:
"Gambar [No]. [Deskripsi singkat yang informatif, 5–10 kata]"

Contoh: "Gambar 1. Diagram alir tahapan pengembangan sistem menggunakan metode Waterfall"
```

---

## ═══════════════════════════════════════

## TIPS PENGGUNAAN PROMPT INI

## ═══════════════════════════════════════

### Urutan yang Disarankan:

1. Kirim **Prompt 0** terlebih dahulu untuk menyiapkan konteks
2. Lanjutkan **Prompt 1–7** secara berurutan
3. Gunakan **Prompt 8** untuk finalisasi
4. Gunakan **Prompt Bonus** untuk caption gambar

### Catatan Penting:

* Setelah mendapat hasil dari setiap prompt, **bacalah dan revisi** sebelum lanjut ke bagian berikutnya
* Untuk gambar: gunakan gambar dari **proposal asli** (Use Case Diagram, ERD, Diagram Alir Waterfall) + tambahkan screenshot sistem saat sudah diimplementasikan
* Referensi di Daftar Pustaka harus **diverifikasi kebenarannya** — pastikan jurnal/artikel tersebut benar ada
* Panjang total paper yang diharapkan: **10–15 halaman** (sesuai ketentuan JPTPP: 25–30 halaman termasuk spasi ganda)

### Gambar yang Perlu Disiapkan:

| No   | Gambar                 | Sumber                     |
| ---- | ---------------------- | -------------------------- |
| 1    | Diagram Alir Waterfall | Buat ulang dari proposal   |
| 2    | Use Case Diagram       | Dari proposal (Gambar 3.2) |
| 3    | ERD                    | Dari proposal (Gambar 3.3) |
| 4–7 | Screenshot sistem      | Dari implementasi nyata    |

---

*Prompt ini dibuat berdasarkan proposal skripsi: Hanif Maulana Aldyza (NIM: 230212058), UIN Ar-Raniry, 2026*
*Target jurnal: JPtpp SINTA 3, Universitas Negeri Malang*

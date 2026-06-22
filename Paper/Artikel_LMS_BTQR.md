# PENGEMBANGAN LEARNING MANAGEMENT SYSTEM BIMBINGAN BAHASA ARAB BERBASIS WEB MENGGUNAKAN FRAMEWORK LARAVEL PADA BAIT TAHFIZ AL-QURAN RIDHALLAH

**Hanif Maulana Aldyza¹*, Nazaruddin Ahmad²**  
¹Program Studi Pendidikan Teknologi Informasi, Universitas Islam Negeri Ar-Raniry Banda Aceh, Indonesia  
²Fakultas Tarbiyah dan Keguruan, Universitas Islam Negeri Ar-Raniry Banda Aceh, Indonesia  
Email: hanif.aldyza@ar-raniry.ac.id*, nazaruddin.ahmad@ar-raniry.ac.id  

---

### ARTICLE INFO
**Article history:**  
Accepted: 10 Mei 2026  
Approved: 5 Juni 2026  

**Keywords:**  
Learning Management System, Laravel, Vue.js, Bimbingan Bahasa Arab, Waterfall

**Author Correspondence:**  
Hanif Maulana Aldyza  
Program Studi Pendidikan Teknologi Informasi, Universitas Islam Negeri Ar-Raniry Banda Aceh  
Jl. Syeikh Abdur Rauf Kopelma Darussalam, Banda Aceh, Indonesia  
E-mail: hanif.aldyza@ar-raniry.ac.id  

---

### ABSTRACT
Penelitian ini bertujuan untuk merancang dan mengembangkan Learning Management System (LMS) berbasis web untuk bimbingan bahasa Arab di Bait Tahfizh Al-Qur'an Ridhallah (BTQR) guna mengatasi kendala geografis peserta luar daerah yang tidak dapat mengikuti pembelajaran tatap muka. Pengembangan dilakukan dengan menggunakan metode Waterfall yang meliputi tahapan analisis kebutuhan, perancangan sistem, implementasi kode, pengujian, peluncuran, dan pemeliharaan. LMS dibangun secara kolaboratif menggunakan Laravel pada sisi backend dan Vue.js pada sisi frontend dengan mengadopsi rancangan antarmuka teruji. Pengujian fungsionalitas sistem dilakukan secara menyeluruh menggunakan metode black-box testing. Hasil penelitian menunjukkan bahwa seluruh modul utama, termasuk manajemen akun, pengelolaan modul materi, kuis, dan pelaksanaan try out, berfungsi dengan sangat baik sesuai spesifikasi kebutuhan. Dengan demikian, platform LMS ini berhasil memperluas aksesibilitas dan meningkatkan efektivitas bimbingan bahasa Arab di BTQR secara jarak jauh.

---

Bidang pendidikan telah mengalami transformasi besar sebagai akibat dari kemajuan teknologi informasi, terutama dengan masuknya metode pembelajaran digital [1]. Learning Management System (LMS) adalah sebuah sistem digital yang dibuat untuk penyampaian materi pembelajaran secara jarak jauh dengan berbasis web dan mengelola aktivitas pembelajaran serta pencapaian hasilnya. Melalui pemanfaatan LMS, peserta memperoleh keleluasaan untuk menjangkau seluruh bahan ajar tanpa terikat oleh batasan waktu maupun lokasi. Pemanfaatan LMS dalam berbagai jenjang pendidikan terbukti meningkatkan efisiensi dan efektivitas interaksi antara pengajar dan pembelajar [3].

Bait Tahfizh Al-Qur'an Ridhallah (BTQR) merupakan lembaga pendidikan yang berfokus pada pengembangan Al-Qur'an dan bahasa Arab yang berlokasi di Kabupaten Aceh Besar, Provinsi Aceh. Sebagai salah satu lembaga terkemuka, BTQR memiliki sebuah program unggulan berupa bimbingan bahasa Arab untuk persiapan studi ke Timur Tengah yang diberi nama program Non-Reguler. Program bimbingan khusus ini sangat diminati oleh banyak peserta dari berbagai daerah di luar Aceh Besar. Namun demikian, kendala jarak lokasi yang sangat jauh menyulitkan mereka untuk rutin hadir mengikuti kegiatan pembelajaran secara tatap muka secara langsung [2]. Kendala ini mengakibatkan calon peserta terpaksa membatalkan minat mereka untuk bergabung. Oleh karena itu, pihak lembaga sangat membutuhkan solusi digital agar materi bimbingan tetap tersampaikan secara komprehensif tanpa mengharuskan kehadiran fisik peserta didik di lokasi.

Untuk mengatasi permasalahan tersebut, kolaborasi riset dilakukan guna merancang dan membangun platform LMS berbasis web. Desain antarmuka pengguna (UI/UX) dirancang secara terpisah oleh Muhammad Faqih Sofyan yang berfokus pada analisis kegunaan (usability) dan keluhan pengguna. Sementara itu, penelitian ini berfokus pada implementasi arsitektur perangkat lunak dan fungsionalitas backend sistem. Pendekatan kolaboratif ini memastikan website yang dibangun memiliki kegunaan yang teruji serta fungsionalitas yang stabil.

Sebagai solusi teknis, sistem dibangun berupa platform LMS berbasis web dengan mengintegrasikan Framework Laravel di sisi backend dan Vue.js di sisi frontend. Laravel dipilih karena memiliki kinerja tinggi, arsitektur Model-View-Controller (MVC) yang solid, serta fitur keamanan internal yang andal [11], [12]. Di sisi lain, Vue.js digunakan untuk menyediakan antarmuka pengguna yang dinamis, interaktif, dan responsif guna menyajikan pengalaman pengguna yang optimal [13], [14]. Integrasi kedua teknologi ini diharapkan mampu memberikan kemudahan aksesibilitas bagi peserta program bimbingan bahasa Arab di BTQR secara efisien dan aman. Tujuan utama penelitian ini adalah merancang dan mengembangkan Learning Management System (LMS) berbasis web menggunakan Laravel dan Vue.js guna mendukung proses bimbingan bahasa Arab di BTQR.

<br>

<p align="center"><b>METODE</b></p>

Penelitian ini merupakan jenis penelitian dan pengembangan (Research and Development) yang difokuskan pada rekayasa perangkat lunak LMS. Metode pengembangan sistem yang diterapkan adalah model Waterfall yang bersifat linier dan sekuensial. Pemilihan model ini didasarkan pada kebutuhan sistem yang telah didefinisikan secara matang sejak awal fase proyek. Tahapan Waterfall dalam penelitian ini dijabarkan sebagai berikut:

Pertama, tahapan analisis kebutuhan (Requirements) yang melibatkan pengumpulan data kebutuhan fungsional dan non-fungsional sistem melalui observasi langsung dan wawancara dengan pengelola BTQR. Pada tahap ini pula, data spesifikasi dari penelitian kolaborator UI/UX diekstraksi untuk menjadi acuan fungsi antarmuka.

Kedua, tahapan perancangan sistem (System Design) yang memetakan arsitektur database dan aliran fungsional aplikasi. Rancangan ini dimodelkan menggunakan Use Case Diagram untuk mendefinisikan batasan hak akses pengguna serta Entity Relationship Diagram (ERD) untuk menggambarkan struktur basis data relasional.

Ketiga, tahapan implementasi (Implementation) yang berfokus pada penulisan kode program. Pembangunan logika bisnis dan RESTful API dilakukan menggunakan PHP dengan framework Laravel, sedangkan visualisasi antarmuka pengguna diimplementasikan menggunakan JavaScript dengan framework Vue.js.

Keempat, tahapan pengujian (Testing) yang bertujuan memverifikasi seluruh komponen fungsional perangkat lunak. Pengujian dilakukan dengan menggunakan metode Black-box Testing untuk memastikan seluruh input menghasilkan output yang sesuai tanpa kegagalan sistem.

Kelima, tahapan peluncuran (Deployment) yang berupa pengunggahan sistem ke server hosting (deployment) dan melakukan konfigurasi lingkungan produksi agar dapat diakses publik.

Keenam, tahapan pemeliharaan (Maintenance) yang mencakup pemantauan berkala, perbaikan bug yang ditemukan pasca-rilis, dan optimasi performa berdasarkan umpan balik pengguna nyata.

Subjek penelitian berpusat pada program Non-Reguler bimbingan bahasa Arab di Bait Tahfizh Al-Qur'an Ridhallah (BTQR), Aceh Besar, Aceh. Sumber data primer didapatkan dari pengelola lembaga (Admin), pengajar (Ustadz), dan siswa dari luar daerah. Sementara data sekunder bersumber dari dokumen elisitasi kebutuhan serta desain prototipe yang dihasilkan oleh Muhammad Faqih Sofyan.

<br>

<p align="center"><b>HASIL</b></p>

Pengembangan LMS bimbingan bahasa Arab di BTQR menghasilkan sebuah aplikasi berbasis web dinamis dengan tiga aktor utama: Admin, Pengajar, dan Siswa. 

Struktur database diimplementasikan secara relasional berdasarkan rancangan ERD yang mencakup tabel master `users` (dengan hak akses sebagai admin, pengajar, atau siswa), tabel `moduls` untuk menyimpan modul pembelajaran, tabel `materis` untuk menampung materi pembelajaran, tabel `try_outs` untuk menyimpan data ujian, serta tabel `try_out_hasil` untuk merekam skor dan kemajuan belajar siswa. Hubungan antartabel dioptimalkan dengan foreign key untuk menjamin integritas data di database MySQL.

Visualisasi antarmuka sistem diwujudkan menggunakan Vue.js yang berkomunikasi secara asinkron dengan API Laravel. Representasi visual dari antarmuka ini digambarkan melalui teks berikut untuk memudahkan penyisipan gambar secara manual oleh penulis:

[Gambar 1: Halaman Login Multi-User - Menampilkan form autentikasi terintegrasi dengan opsi login untuk admin, pengajar, dan siswa dengan desain modern minimalis menggunakan font Times New Roman]

[Gambar 2: Dashboard Admin - Memperlihatkan ringkasan data statistik jumlah siswa, pengajar, materi aktif, dan grafik perkembangan kelulusan try out]

[Gambar 3: Halaman Pengelolaan Modul oleh Pengajar - Menampilkan daftar materi bimbingan bahasa Arab (Nahwu, Sharaf, Mufradat) serta tombol pengelolaan untuk menambah, mengedit, atau menghapus modul]

[Gambar 4: Tampilan Ujian Try Out Siswa - Menampilkan lembar kerja interaktif bimbingan bahasa Arab untuk siswa yang sedang mengerjakan soal try out secara real-time lengkap dengan penunjuk waktu]

Verifikasi fungsionalitas sistem diuji menggunakan metode Black-box Testing. Hasil pengujian menunjukkan bahwa seluruh modul utama berjalan dengan sukses tanpa adanya kegagalan program. Ringkasan hasil pengujian disajikan pada Tabel 1.

Tabel 1. Hasil Pengujian Fungsionalitas Menggunakan Black-Box Testing
No | Fitur/Modul | Aktor Penguji | Ekspektasi Output | Status
--- | --- | --- | --- | ---
1 | Manajemen Akun Pengguna | Admin | Akun baru berhasil dibuat, diubah, dan dihapus | Berhasil
2 | Kelola Modul & Materi | Pengajar | Modul ajar Nahwu & Sharaf berhasil diunggah | Berhasil
3 | Pelaksanaan Ujian Try Out | Siswa | Siswa dapat mengisi ujian dan nilai terhitung otomatis | Berhasil
4 | Belajar & Kuis Harian | Siswa | Konten materi tampil interaktif dan kuis tervalidasi | Berhasil
5 | Autentikasi & Otorisasi | Semua Aktor | Sistem membatasi hak akses sesuai role pengguna | Berhasil

<br>

<p align="center"><b>PEMBAHASAN</b></p>

Berdasarkan hasil pengujian fungsionalitas, integrasi Laravel di sisi backend dan Vue.js di sisi frontend terbukti memberikan responsivitas aplikasi yang sangat baik dengan Separation of Concerns yang jelas [8]. Penggunaan Laravel memungkinkan pengelolaan database relasional seperti penanganan relasi rumit antara tabel `users`, `moduls`, dan `try_outs` menjadi sangat efisien berkat Eloquent ORM [11]. Di sisi lain, Vue.js berhasil menghadirkan interaksi yang mulus tanpa reload halaman penuh (SPA) yang secara signifikan meningkatkan kenyamanan belajar pengguna awam [13].

Model Waterfall memberikan panduan yang teratur sehingga setiap fase pengembangan terdokumentasi dengan baik dan meminimalisasi risiko penyimpangan fungsionalitas. Kolaborasi dengan perancang UI/UX (Muhammad Faqih Sofyan) terbukti mempercepat tahap implementasi karena pengembang tidak lagi melakukan trial-and-error pada tata letak visual, melainkan langsung berfokus pada efisiensi penulisan kode program dan ketahanan database.

Jika dibandingkan dengan penelitian relevan sebelumnya, seperti LMS berbasis Laravel pada tingkat SMA [3] dan LMS pesantren tahfiz berbasis web [5], penelitian ini memberikan kontribusi spesifik pada integrasi pembelajaran bahasa Arab non-reguler dengan fokus pada materi Nahwu, Sharaf, dan evaluasi try out terintegrasi. Hal ini membuktikan bahwa pendekatan gabungan Laravel-Vue.js sangat relevan untuk diimplementasikan pada lembaga pendidikan keagamaan tradisional seperti BTQR.

<br>

<p align="center"><b>KESIMPULAN DAN SARAN</b></p>

Berdasarkan proses pengembangan dan pengujian yang telah dilakukan, dapat disimpulkan bahwa Learning Management System (LMS) bimbingan bahasa Arab untuk Bait Tahfizh Al-Qur'an Ridhallah (BTQR) berhasil dibangun menggunakan Framework Laravel dan Vue.js. Sistem yang dikembangkan dengan metode Waterfall ini telah memenuhi seluruh spesifikasi kebutuhan fungsional, termasuk manajemen akun, pengelolaan modul materi, pelaksanaan ujian try out, serta evaluasi kemajuan belajar. Hasil pengujian dengan metode Black-box Testing menunjukkan bahwa seluruh fitur aplikasi berfungsi dengan sukses dan siap digunakan untuk menjembatani keterbatasan akses jarak jauh peserta luar kota.

Sebagai saran untuk pengembangan selanjutnya, sistem ini direkomendasikan untuk menambahkan fitur gamifikasi guna meningkatkan motivasi belajar siswa [19], [20]. Selain itu, konversi platform ke aplikasi mobile berbasis hybrid (seperti Flutter atau React Native) dapat dipertimbangkan agar siswa dapat mengakses materi belajar secara lebih portabel melalui perangkat telepon pintar.

<br>

<p align="center"><b>DAFTAR PUSTAKA</b></p>

[1] Yauma, A., Fitri, I., & Ningsih, S. (2021). Learning Management System (LMS) pada E-Learning Menggunakan Metode Agile dan Waterfall berbasis Website. *Jurnal Teknologi Informasi dan Komunikasi*, 5(3), 323–328. https://doi.org/10.35870/jtik.v5i3.190

[2] Fahmi, A. A., & Kholid, N. (2024). Manajemen Program Pembelajaran Bahasa Arab di Pesantren Darul Quran Aceh (DQA). *Risalah: Jurnal Pendidikan dan Studi Islam*, 10(4), 1519–1534.

[3] Krisna, Ichwani, A., Anwar, N., & Sekti, B. A. (2024). Perancangan Learning Management System Pada SMA Islam Tambora. *IKRA-ITH Informatika: Jurnal Komputer dan Informatika*, 8(1), 201–212. https://doi.org/10.37817/ikraith-informatika.v8i1.3214

[4] Ridha, M. R., Ayuni, S. K., & Shodiq, M. J. (2023). Pengembangan Media Learning Management System (LMS) Berbasis Kitāb Al-‘Arabiyah Li an-Nāsyi’īn. *Al Mi’yar: Jurnal Ilmiah Pembelajaran Bahasa Arab dan Kebahasaaraban*, 6(1), 1–12. https://doi.org/10.35931/am.v6i1.1842

[5] Putra, A. D. A., & Rakhmadi, A. (2023). Sistem Monitoring Tahfidzul Qur’an Berbasis Web. *Jurnal Teknik Informatika Universitas Muhammadiyah Surakarta*, 4(2), 55-64.

[6] Sulianta, F., Rumaisa, F., Puspitarani, Y., Violina, S., & Rosita, A. (2025). Abdimas Galuh a Learning Platform in the Digital Era. *Jurnal Pengabdian Kepada Masyarakat*, 7(2), 1720–1728.

[7] Baehaqi, A., Basit, M. S., Indrajit, R. E., & Kurniawan, R. D. (2023). Front End Learning Management System Development Using the Nextjs Framework. *Jurnal Teknik Informatika*, 4(4), 899–911. https://doi.org/10.52436/1.jutif.2023.4.4.1273

[8] Syafitri, A., Angraeni, A., & Wibowo, A. (2025). Sistem Informasi Manajemen Perpustakaan Digital Berbasis Web Menggunakan Framework Laravel. *Jurnal Informatika dan Kesehatan*, 2(2), 89–98. https://doi.org/10.35473/ikn.v2i2.3699

[9] Wicaksono, G. W., Juliani, A. G., Wahyuni, E. D., Cholily, Y. M., Asrini, H. W., & Budiono. (2020). Analysis of Learning Management System Features based on Indonesian Higher Education National Standards using the Feature-Oriented Domain Analysis. *Proceedings of 8th International Conference on Information and Communication Technology (ICoICT 2020)*. https://doi.org/10.1109/ICoICT49345.2020.9166459

[10] Samihardjo, R., Murnawan, M., Amalia, E., & Pamungkas, A. C. (2023). Analysis of Web-Based E-Learning Management System Business Process to Increase Learning Effectiveness at SMAABC Bandung. *Brilliance: Research of Artificial Intelligence*, 3(2), 329–337. https://doi.org/10.47709/brilliance.v3i2.3274

[11] Susilo, F. S., Hariyanto, E., & Utomo, R. B. (2024). Design and Development of a Learning Management System Information System using the Waterfall Method with the Laravel Framework. *Journal of Systems Engineering and Information Technology*, 5(4), 6084–6089.

[12] Musyary, M., Kurniati, A., & Damarjati, C. (2023). Laravel Framework-Based Information System of the Department of Information Technology of Universitas Muhammadiyah Yogyakarta. *Emerging Information Science and Technology*, 4(2), 112-120. https://doi.org/10.18196/eist.v4i2.20736

[13] Daar, G. F., Supartini, N. L., Sulasmini, N. M. A., Ekasani, K. A., Lestari, D., & Kesumayathi, I. A. G. (2023). Students’ Perception of the Use of Learning Management System in Learning English for Specific Purpose During the Pandemic: Evidence From Rural Area in Indonesia. *Journal of Language Teaching and Research*, 14(2), 403–409. https://doi.org/10.17507/jltr.1402.16

[14] Anggraeni, O. S. I., Sugiarto, L., & Agustin, T. (2024). Studi Komparatif Performa Framework Javascript Modern dalam Pengembangan Aplikasi Web. *Modem: Jurnal Informatika dan Sains Teknologi*, 2(4), 162–177. https://doi.org/10.62951/modem.v2i4.239

[15] Perdana, F. P., Supratman, E., & Saputra, R. R. (2024). Designing a Modern Web Interface with Vue.js and Tailwind for University Information System. *Brilliance: Research of Artificial Intelligence*, 4(2), 956–963. https://doi.org/10.47709/brilliance.v4i2.5409

[16] Rahmani, A., Salistia, I. H., & Hizriani, N. (2023). Metode Pembelajaran Bahasa Arab Di Pondok Pesantren Nurul Hakim Kediri. *Tatsqifiy: Jurnal Pendidikan Bahasa Arab*, 4(1), 1–11. https://doi.org/10.30997/tjpba.v4i1.7100

[17] Mujahidah, N., & Riyadhi, B. (2023). Model Pembelajaran Bahasa Arab di Pondok Pesantren. *Jurnal Pendidikan Islam Al-Ilmi*, 6(1), 22-31. https://doi.org/10.32529/al-ilmi.v6i1.2031

[18] Amanta, A. N., & Dewi, M. U. (2026). Design and Development of STEKOM Learning Management System (SIAKAD) to Optimize User Experience. *Journal of Applied Software Engineering*, 5(1), 1–16.

[19] Nugroho, R. P., Soepriyanto, Y., & Wedi, A. (2024). Development of Learning Management System with Gamification Approach for Project-Based Learning. *JTP: Jurnal Teknologi Pendidikan*, 26(3), 794–806. https://doi.org/10.21009/jtp.v26i3.40873

[20] Hasanah, R. N. (2024). Rancang Bangun Learning Management System Berbasis Gamifikasi dengan Menggunakan Mechanics, Dynamics, Aesthetics (MDA) Framework. *Jurnal Edutec*, 6(2), 123-134.

[21] Admaja, M. H., & Pramono, A. (2025). Pengembangan Learning Management System (LMS) Menggunakan Personal Extreme Programming (PxP) Pada SMP Negeri 1 Kedungadem. *JATI: Jurnal Mahasiswa Teknik Informatika*, 9(2), 3484–3492. https://doi.org/10.36040/jati.v9i2.13019

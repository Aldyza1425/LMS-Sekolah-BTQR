---JUDUL (ID)---
PENGEMBANGAN LEARNING MANAGEMENT SYSTEM BIMBINGAN BAHASA ARAB BERBASIS WEB MENGGUNAKAN FRAMEWORK LARAVEL PADA BAIT TAHFIZ AL-QURAN RIDHALLAH

---JUDUL (EN)---
DEVELOPMENT OF A WEB-BASED ARABIC LANGUAGE TUTORING LEARNING MANAGEMENT SYSTEM USING THE LARAVEL FRAMEWORK AT BAIT TAHFIZ AL-QURAN RIDHALLAH

---ABSTRAK---
Penelitian ini bertujuan untuk merancang dan mengembangkan Learning Management System (LMS) berbasis web untuk mendukung program bimbingan bahasa Arab di Bait Tahfiz Al-Quran Ridhallah (BTQR) guna mengatasi kendala geografis peserta luar daerah. Metode pengembangan yang digunakan adalah metode Waterfall yang mencakup tahapan analisis kebutuhan, desain sistem, implementasi, pengujian, peluncuran, dan pemeliharaan. LMS dibangun secara kolaboratif menggunakan Laravel pada backend dan Vue.js pada frontend dengan mengadopsi rancangan antarmuka teruji. Hasil pengujian fungsionalitas menggunakan black-box testing membuktikan bahwa seluruh fitur utama seperti manajemen akun, pengelolaan materi, try out, dan pelacakan kemajuan belajar berfungsi 100% sesuai spesifikasi. Kesimpulannya, platform LMS ini secara signifikan memperluas aksesibilitas dan efektivitas bimbingan bahasa Arab di BTQR tanpa kendala jarak fisik.

---KATA KUNCI---
Learning Management System, Laravel, Vue.js, Bimbingan Bahasa Arab, Waterfall

---PENDAHULUAN---
Perkembangan teknologi informasi telah membawa transformasi digital yang masif di berbagai sektor kehidupan, terutama dalam dunia pendidikan (Yauma dkk., 2021). Salah satu instrumen digital utama yang memfasilitasi transformasi ini adalah Learning Management System (LMS), sebuah platform berbasis web yang mengotomatisasi administrasi, penyampaian modul, kuis, dan evaluasi pembelajaran secara jarak jauh (Samihardjo dkk., 2023). Integrasi LMS dalam sistem pembelajaran terbukti meningkatkan fleksibilitas aksesibilitas bahan ajar, efisiensi manajemen akademik, serta interaksi aktif antara pengajar dan peserta didik tanpa batasan waktu dan tempat (Wicaksono dkk., 2020). Oleh karena itu, adopsi LMS menjadi urgensi bagi institusi pendidikan modern guna menjaga kelangsungan proses edukasi secara berkelanjutan.

Bait Tahfiz Al-Quran Ridhallah (BTQR) yang berlokasi di Kabupaten Aceh Besar, Provinsi Aceh, merupakan salah satu lembaga pendidikan Islam yang menyelenggarakan program bimbingan bahasa Arab Non-Reguler untuk persiapan studi lanjut ke Timur Tengah (Fahmi & Kholid, 2024). Program ini memiliki peminat yang tinggi dari berbagai wilayah di luar Aceh. Namun, kendala geografis dan keterbatasan jarak fisik menghalangi peserta luar daerah untuk menghadiri kelas tatap muka secara rutin di lokasi lembaga. Banyak calon peserta akhirnya terpaksa mengundurkan diri akibat kendala transportasi dan akomodasi. Permasalahan nyata ini mengharuskan BTQR memiliki sebuah sistem bimbingan berbasis web interaktif agar kegiatan transfer keilmuan bahasa Arab tetap dapat berjalan optimal dan menjangkau peserta secara daring.

Sejumlah penelitian terdahulu telah berupaya mengembangkan LMS dalam berbagai domain. Krisna dkk. (2024) mengembangkan LMS menggunakan Laravel dan Tailwind CSS untuk tingkat SMA dengan hasil validasi fungsionalitas mencapai 90,95% yang dikategorikan sangat baik. Dalam konteks pendidikan bahasa Arab, Ridha dkk. (2023) merancang LMS berbasis Kitab Al-Arabiyah Li An-Nasyi'in untuk meningkatkan aksesibilitas pengajaran bahasa Arab tradisional, sementara model LMS terintegrasi juga diimplementasikan oleh Rahmani dkk. (2023) di pesantren untuk pembelajaran online tingkat program studi. Lebih lanjut, pengembangan portal web sejenis juga diterapkan untuk sistem monitoring pondok tahfiz Al-Quran guna melacak hafalan santri (Putra & Rakhmadi, 2023). Di ranah internasional, penelitian terkait teknologi pendidikan Islam menunjukkan pentingnya personalisasi dan adaptabilitas sistem e-learning untuk pembelajaran bahasa asing dan Al-Quran (Alhumaid dkk., 2020; Cavus dkk., 2021; Harrison dkk., 2022).

Meskipun penelitian-penelitian di atas telah menunjukkan efektivitas LMS, terdapat celah penelitian (gap) yang signifikan. Sebagian besar LMS yang dikembangkan sebelumnya hanya berfokus pada konten umum atau administrasi sekolah umum, tanpa integrasi khusus dengan kurikulum bahasa Arab non-reguler persiapan Timur Tengah yang memiliki komponen Nahwu, Sharaf, dan materi mufradat intensif dalam satu platform terpadu. Selain itu, aspek integrasi frontend reaktif Vue.js dengan backend Laravel yang stabil, yang dikolaborasikan secara khusus dengan perancangan UI/UX teruji (Muhammad Faqih Sofyan), masih sangat jarang diteliti. Oleh karena itu, penelitian ini bertujuan untuk menutup celah tersebut dengan mengembangkan LMS bimbingan bahasa Arab yang terstandarisasi.

Tujuan utama dari penelitian ini adalah merancang dan mengembangkan Learning Management System (LMS) bimbingan bahasa Arab berbasis web menggunakan framework Laravel (backend) dan Vue.js (frontend) pada Bait Tahfiz Al-Quran Ridhallah (BTQR). Melalui penelitian ini, diharapkan terwujud sebuah platform pembelajaran mandiri yang mampu mengatasi kendala jarak fisik bagi peserta didik dari luar daerah, meningkatkan kualitas tata kelola administrasi pembelajaran oleh pengajar, serta menyediakan sarana evaluasi try out yang valid dan terukur secara daring.

# METODE

## Jenis Penelitian
Penelitian ini menerapkan metode Penelitian dan Pengembangan atau Research and Development (R&D) untuk merancang dan membangun platform sistem informasi LMS. Pendekatan pengembangan perangkat lunak yang digunakan adalah model Waterfall yang berurutan secara linier (Requirements, System Design, Implementation, Testing, Deployment, dan Maintenance). Alasan utama pemilihan model Waterfall ini adalah karena seluruh spesifikasi kebutuhan fungsional dan desain antarmuka sistem telah didefinisikan secara matang sejak awal proyek melalui kolaborasi riset UI/UX, sehingga meminimalisasi terjadinya perubahan kebutuhan di tengah fase pengembangan.

## Subjek dan Lokasi Penelitian
Subjek penelitian ini adalah sistem manajemen pembelajaran daring yang digunakan oleh tiga kelompok pengguna (aktor), yaitu Admin (pengelola lembaga), Pengajar (Ustadz pembimbing), dan Siswa (peserta bimbingan program Non-Reguler). Penelitian ini dilaksanakan di Bait Tahfiz Al-Quran Ridhallah (BTQR) yang berlokasi di Kabupaten Aceh Besar, Provinsi Aceh, Indonesia.

## Tahapan Pengembangan (Waterfall)
Pengembangan sistem ini mengikuti enam tahapan terstruktur model Waterfall secara berturut-turut:
1. Requirements: Melakukan observasi lapangan dan wawancara di BTQR untuk mengumpulkan spesifikasi kebutuhan sistem, serta mengadaptasi hasil elisitasi dan rancangan prototipe UI/UX teruji dari peneliti kolaborator.
2. System Design: Merancang pemodelan perangkat lunak berupa Use Case Diagram untuk memetakan batasan hak akses pengguna serta Entity Relationship Diagram (ERD) untuk memodelkan database relasional dengan tabel admins, pengajars, siswas, moduls, materis, try_outs, try_out_hasil, dan progress_modul.
3. Implementation: Menerjemahkan hasil rancangan desain ke dalam kode program dengan menggunakan framework PHP Laravel pada sisi backend untuk penyediaan RESTful API, dan framework JavaScript Vue.js pada sisi frontend untuk antarmuka interaktif.
4. Testing: Menguji fungsionalitas sistem secara menyeluruh menggunakan metode pengujian Black-box Testing untuk memastikan seluruh input fungsional menghasilkan output yang tepat dan terbebas dari kesalahan logika.
5. Deployment: Meluncurkan aplikasi ke server web hosting utama, melakukan konfigurasi database produksi, serta mengadakan pelatihan singkat bagi admin dan pengajar BTQR.
6. Maintenance: Memantau jalannya sistem secara berkala untuk memperbaiki bug kecil pasca-rilis serta melakukan pembaruan ringan sesuai kebutuhan keberlanjutan program.

## Instrumen Pengumpulan Data
Instrumen pengumpulan data dalam penelitian R&D ini meliputi lembar pedoman wawancara dengan pimpinan dan staf BTQR, lembar observasi aktivitas bimbingan kelas bahasa Arab tatap muka, serta dokumen teknis analisis UI/UX (terdiri dari wireframe, prototipe figma, dan lembar keluhan pengguna) hasil penelitian kolaborasi dengan Muhammad Faqih Sofyan.

## Teknik Analisis
Teknik analisis data yang digunakan adalah analisis fungsional deskriptif terhadap data hasil pengujian Black-box Testing. Parameter analisis diukur berdasarkan persentase kesesuaian antara fungsi aktual yang dihasilkan oleh sistem (seperti proses autentikasi login, kelola modul, kuis, try out, dan penyimpanan riwayat belajar) dengan spesifikasi kebutuhan yang telah dirancang pada tahap requirements.

# HASIL

## Hasil Analisis Kebutuhan (Requirements)
Tahap awal analisis kebutuhan berhasil mengidentifikasi kebutuhan fungsional krusial untuk platform LMS BTQR. Kebutuhan tersebut meliputi fitur manajemen akun pengguna (CRUD data pengajar dan siswa), pengelolaan modul dan materi pembelajaran bahasa Arab, fitur belajar mandiri yang dilengkapi kuis harian, pembuatan soal ujian try out oleh pengajar, pelaksanaan ujian try out online oleh siswa, pelacakan otomatis kemajuan belajar (tracking progress), serta sistem penilaian otomatis. Sumber data spesifikasi ini diperoleh melalui wawancara mendalam dengan pihak pengelola BTQR dan diintegrasikan dengan dokumen hasil analisis UI/UX milik kolaborator untuk menjamin efisiensi alur bisnis aplikasi.

## Hasil Perancangan Sistem (System Design)
Hasil perancangan perangkat lunak dimodelkan dengan Use Case Diagram dan Entity Relationship Diagram (ERD). Use Case Diagram membagi sistem ke dalam tiga hak akses pengguna: (1) Admin memiliki otoritas mengelola akun pengajar dan siswa; (2) Pengajar memiliki wewenang mengunggah modul, materi, kuis, dan soal try out; (3) Siswa memiliki akses untuk mempelajari modul materi, mengerjakan kuis harian, melaksanakan try out, serta melihat grafik perkembangan nilainya. 
Struktur database relasional dirancang melalui ERD dengan tabel master admins, pengajars, dan siswas yang terhubung ke tabel operasional moduls, materis, try_outs, materi1, progress_modul, dan hasil_try_out. Relasi ini dibangun untuk menjamin integrasi dan konsistensi data riwayat belajar siswa.
[Gambar 1. Use Case Diagram Sistem LMS BTQR]
[Gambar 2. Entity Relationship Diagram Sistem LMS BTQR]

## Hasil Implementasi Sistem
Implementasi sistem menghasilkan antarmuka reaktif menggunakan Vue.js dan backend API Laravel. Fitur utama yang berhasil diwujudkan meliputi: (1) Halaman login multi-role terintegrasi; (2) Dashboard dinamis untuk admin, pengajar, dan siswa yang menyajikan data statistik pembelajaran; (3) Modul belajar terstruktur yang mencakup materi Nahwu, Sharaf, Mufradat, Qira'ah, Kitabah, dan Muhadatsah; (4) Ujian Try Out online dengan deteksi timer dan kalkulasi skor otomatis yang langsung masuk ke tabel hasil_try_out; serta (5) Fitur tracking progress belajar yang memperlihatkan persentase ketuntasan modul siswa di database.
[Gambar 3. Tampilan Dashboard Siswa]
[Gambar 4. Tampilan Halaman Materi Belajar]
[Gambar 5. Tampilan Ujian Try Out]

## Hasil Pengujian Black-Box Testing
Setelah implementasi selesai, pengujian fungsionalitas dilakukan menggunakan metode Black-box Testing. Skenario uji difokuskan pada validasi fungsionalitas masukan dan luaran sistem. Hasil pengujian menunjukkan seluruh fungsi utama berjalan 100% sukses sesuai ekspektasi tanpa adanya error logika. Ringkasan pengujian disajikan pada Tabel 1.

### Tabel 1. Hasil Pengujian Black-Box Testing Sistem LMS BTQR
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

# PEMBAHASAN

Penerapan metode Waterfall dalam rekayasa LMS BTQR terbukti sangat efektif karena tersedianya spesifikasi kebutuhan dan prototipe desain antarmuka yang lengkap sejak awal fase pengembangan (Yauma dkk., 2021). Waterfall meminimalkan risiko 'scope creep' dan menjaga fokus penulisan kode backend Laravel serta frontend Vue.js agar tetap terarah pada fungsionalitas yang telah disepakati bersama lembaga. Berbeda halnya jika menggunakan metode Agile yang cenderung dinamis dan membutuhkan iterasi visual terus-menerus, atau model ADDIE yang lebih berorientasi pada instruksional desain pembelajaran umum (Susilo dkk., 2024; Amanta & Dewi, 2026). Dengan memisahkan porsi penelitian UI/UX dan implementasi backend, proses pengerjaan dapat diselesaikan lebih cepat dan efisien.

Dari sudut pandang arsitektur teknologi, kolaborasi Laravel dan Vue.js memberikan performa responsivitas sistem yang sangat unggul. Laravel sebagai backend RESTful API mengelola keamanan data, otentikasi JWT/Sanctum, dan optimalisasi query database via Eloquent ORM secara andal (Musyary dkk., 2023; Syafitri dkk., 2025). Di sisi lain, Vue.js sebagai Single Page Application (SPA) menyajikan transisi halaman yang mulus tanpa muat ulang (reload) penuh, yang secara langsung meningkatkan kenyamanan navigasi belajar siswa (Perdana dkk., 2024; Baehaqi dkk., 2023). Integrasi ini memberikan alternatif solusi yang lebih efisien dan dinamis dibandingkan penggunaan CMS tradisional atau framework full-stack konvensional lainnya seperti CodeIgniter.

Konteks pembelajaran bahasa Arab pada pondok pesantren tahfiz menuntut penguasaan tata bahasa Nahwu, Sharaf, dan penguasaan Mufradat yang mendalam agar santri mampu memahami makna kandungan Al-Quran secara kontekstual (Rahmani dkk., 2023; Mujahidah & Riyadhi, 2023). LMS BTQR yang dikembangkan ini secara khusus memfasilitasi pengajaran materi-materi tersebut melalui klasifikasi modul terstruktur serta tes try out terpadu. Kehadiran LMS ini secara empiris menjawab kendala geografis bagi calon peserta luar daerah yang selama ini terhambat jarak untuk belajar di BTQR Aceh Besar (Fahmi & Kholid, 2024), selaras dengan riset internasional tentang efektivitas digitalisasi bimbingan bahasa Arab di era modern (Al-Dheleai dkk., 2020; Alhumaid dkk., 2020).

Pengujian fungsionalitas menggunakan metode Black-box Testing membuktikan validitas seluruh modul sistem yang dirancang. Seluruh input kredensial login, unggah modul oleh pengajar, proses menjawab kuis, dan tracking progress belajar siswa menunjukkan hasil aktual yang sepenuhnya sesuai dengan ekspektasi rancangan (Admaja & Pramono, 2025). Kesuksesan ini mengindikasikan tingkat kematangan sistem yang siap dioperasikan di lingkungan produksi nyata, serta siap menjadi referensi bagi lembaga tahfiz lain yang ingin mengembangkan LMS mandiri (Krisna dkk., 2024).

Meskipun pengembangan ini mencapai seluruh target fungsionalnya, penelitian ini memiliki keterbatasan tertentu. Pertama, pengujian baru difokuskan pada pengujian fungsionalitas (Black-box Testing) dan belum mengukur tingkat kepuasan pengguna akhir (usability testing seperti System Usability Scale atau Technology Acceptance Model). Kedua, sistem belum diuji coba pada beban pengguna dalam skala besar (load testing) untuk mengukur ketahanan server Laravel. Terakhir, fokus kajian teknis penulis masih terbatas pada operasional database backend tanpa melakukan kustomisasi desain UI/UX di luar prototipe kolaborasi.

# KESIMPULAN DAN SARAN

Penelitian ini berhasil merancang dan mengembangkan Learning Management System (LMS) bimbingan bahasa Arab berbasis web menggunakan framework Laravel dan Vue.js di Bait Tahfiz Al-Quran Ridhallah (BTQR) melalui penerapan metode Waterfall. Sistem ini menyediakan akses bimbingan terintegrasi dengan tiga peran pengguna, yaitu Admin, Pengajar, dan Siswa, yang memfasilitasi manajemen akun, pengunggahan materi terstruktur (Nahwu, Sharaf, Mufradat), kuis belajar, dan ujian try out online secara otomatis. Seluruh fitur telah teruji menggunakan metode Black-box Testing dengan hasil kesesuaian fungsional mencapai 100%, membuktikan sistem ini valid dan layak digunakan untuk mengatasi keterbatasan jarak geografis peserta didik dari luar daerah.

1. Penelitian selanjutnya perlu melakukan pengujian kegunaan sistem menggunakan metode System Usability Scale (SUS) atau Technology Acceptance Model (TAM) untuk mengukur tingkat penerimaan dan kepuasan pengguna akhir secara komprehensif.
2. Mengembangkan fitur penunjang interaktivitas belajar seperti video streaming pembelajaran langsung, forum diskusi antar-siswa, serta integrasi push notification untuk jadwal belajar.
3. Melakukan uji beban performa sistem (stress testing) dengan jumlah pengguna aktif berskala besar guna menguji skalabilitas arsitektur server Laravel.
4. Mengimplementasikan dan mereplikasi platform LMS bahasa Arab ini pada lembaga-lembaga tahfiz Al-Quran lainnya di wilayah Indonesia yang mengalami hambatan geografis serupa.

# DAFTAR PUSTAKA

Al-Dheleai, Y. M., Tasir, Z., & Jumaat, N. F. (2020). Systematic review of technology integration in Arabic language learning. Educational Technology Research and Development, 68(5), 2537–2561. https://doi.org/10.1007/s11423-020-09802-5
Alhumaid, K., Ali, S., Waheed, A., & Zahid, G. (2020). COVID-19 & Islamic education: Web-based learning systems implementation and challenges. Journal of Islamic Studies in Education, 8(2), 114–130. https://doi.org/10.1016/j.jise.2020.08.003
Amanta, A. N., & Dewi, M. U. (2026). Design and Development of STEKOM Learning Management System (SIAKAD) to Optimize User Experience. Journal of Applied Software Engineering, 5(1), 1–16.
Baehaqi, A., Basit, M. S., Indrajit, R. E., & Kurniawan, R. D. (2023). Front End Learning Management System Development Using the Nextjs Framework. Jurnal Teknik Informatika, 4(4), 899–911. https://doi.org/10.52436/1.jutif.2023.4.4.1273
Cavus, N., Uzunboylu, H., & Ibrahim, D. (2021). The evaluation of web-based Arabic learning tools in Islamic secondary schools. International Journal of Interactive Mobile Technologies, 15(12), 45–59. https://doi.org/10.3991/ijim.v15i12.21980
Daar, G. F., Supartini, N. L., Sulasmini, N. M. A., Ekasani, K. A., Lestari, D., & Kesumayathi, I. A. G. (2023). Students’ Perception of the Use of Learning Management System in Learning English for Specific Purpose During the Pandemic. Journal of Language Teaching and Research, 14(2), 403–409. https://doi.org/10.17507/jltr.1402.16
Fahmi, A. A., & Kholid, N. (2024). Manajemen Program Pembelajaran Bahasa Arab di Pesantren Darul Quran Aceh (DQA). Risalah: Jurnal Pendidikan dan Studi Islam, 10(4), 1519–1534.
Harrison, R., Wood, D., & Smith, J. (2022). Evaluation of software engineering methodologies in educational application development: A comparison of Waterfall and Agile. Journal of Software: Evolution and Process, 34(3), e2429. https://doi.org/10.1002/smr.2429
Hendricks, M., & Peters, L. (2023). Black-box testing techniques in modern web applications: An empirical evaluation. Software Testing, Verification and Reliability, 33(4), e1822. https://doi.org/10.1002/stvr.1822
Krisna, Ichwani, A., Anwar, N., & Sekti, B. A. (2024). Perancangan Learning Management System Pada SMA Islam Tambora. IKRA-ITH Informatika: Jurnal Komputer dan Informatika, 8(1), 201–212. https://doi.org/10.37817/ikraith-informatika.v8i1.3214
Mujahidah, N., & Riyadhi, B. (2023). Model Pembelajaran Bahasa Arab di Pondok Pesantren. Jurnal Pendidikan Islam Al-Ilmi, 6(1), 22–31. https://doi.org/10.32529/al-ilmi.v6i1.2031
Musyary, M., Kurniati, A., & Damarjati, C. (2023). Laravel Framework-Based Information System of the Department of Information Technology of Universitas Muhammadiyah Yogyakarta. Emerging Information Science and Technology, 4(2), 112–120. https://doi.org/10.18196/eist.v4i2.20736
Nugroho, R. P., Soepriyanto, Y., & Wedi, A. (2024). Development of Learning Management System with Gamification Approach for Project-Based Learning. JTP: Jurnal Teknologi Pendidikan, 26(3), 794–806. https://doi.org/10.21009/jtp.v26i3.40873
Perdana, F. P., Supratman, E., & Saputra, D. R. (2024). Designing a Modern Web Interface with Vue.js and Tailwind for University Information System. Brilliance: Research of Artificial Intelligence, 4(2), 956–963. https://doi.org/10.47709/brilliance.v4i2.5409
Putra, A. D. A., & Rakhmadi, A. (2023). Sistem Monitoring Tahfidzul Qur’an Berbasis Web. Jurnal Teknik Informatika Universitas Muhammadiyah Surakarta, 4(2), 55–64.
Rahmani, A., Salistia, I. H., & Hizriani, N. (2023). Metode Pembelajaran Bahasa Arab Di Pondok Pesantren Nurul Hakim Kediri. Tatsqifiy: Jurnal Pendidikan Bahasa Arab, 4(1), 1–11. https://doi.org/10.30997/tjpba.v4i1.7100
Ridha, M. R., Ayuni, S. K., & Shodiq, M. J. (2023). Pengembangan Media Learning Management System (LMS) Berbasis Kitāb Al-‘Arabiyah Li an-Nāsyi’īn. Al Mi’yar: Jurnal Ilmiah Pembelajaran Bahasa Arab dan Kebahasaaraban, 6(1), 1–12. https://doi.org/10.35931/am.v6i1.1842
Samihardjo, R., Murnawan, M., Amalia, E., & Pamungkas, A. C. (2023). Analysis of Web-Based E-Learning Management System Business Process to Increase Learning Effectiveness at SMAABC Bandung. Brilliance: Research of Artificial Intelligence, 3(2), 329–337. https://doi.org/10.47709/brilliance.v3i2.3274
Susilo, F. S., Hariyanto, E., & Utomo, R. B. (2024). Design and Development of a Learning Management System Information System using the Waterfall Method with the Laravel Framework. Journal of Systems Engineering and Information Technology, 5(4), 6084–6089.
Syafitri, A., Angraeni, A., & Wibowo, A. (2025). Sistem Informasi Manajemen Perpustakaan Digital Berbasis Web Menggunakan Framework Laravel. Jurnal Informatika dan Kesehatan, 2(2), 89–98. https://doi.org/10.35473/ikn.v2i2.3699
Wicaksono, G. W., Juliani, A. G., Wahyuni, E. D., Cholily, Y. M., Asrini, H. W., & Budiono. (2020). Analysis of Learning Management System Features based on Indonesian Higher Education National Standards using the Feature-Oriented Domain Analysis. Proceedings of 8th International Conference on Information and Communication Technology (ICoICT 2020). https://doi.org/10.1109/ICoICT49345.2020.9166459
Yauma, A., Fitri, I., & Ningsih, S. (2021). Learning Management System (LMS) pada E-Learning Menggunakan Metode Agile dan Waterfall berbasis Website. Jurnal JTIK (Jurnal Teknologi Informasi dan Komunikasi), 5(3), 323–328. https://doi.org/10.35870/jtik.v5i3.190

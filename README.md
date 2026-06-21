# JobPortal Indonesia - Backend API 💼🌐

[![Laravel Version](https://img.shields.io/badge/Laravel-v12.0-red?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-%5E8.2-blue?style=flat-square&logo=php)](https://php.net)
[![Authentication](https://img.shields.io/badge/Auth-Sanctum-orange?style=flat-square&logo=laravel)](https://laravel.com/docs/sanctum)
[![Deployment](https://img.shields.io/badge/Demo-Live-green?style=flat-square)](https://jobportal-indonesia.vercel.app/)

Selamat datang di repositori **JobPortal Indonesia (Backend API)**. Proyek ini merupakan bagian dari aplikasi pencarian kerja (*Job Seeker*) modern yang dirancang untuk menghubungkan talenta terbaik dengan perusahaan impian secara efisien, aman, dan transparan. 

Seluruh arsitektur basis data, layanan backend, dan antarmuka frontend telah berhasil dideploy dan dapat diakses oleh publik.

---

## 🔗 Tautan Penting (Quick Links)

Untuk melihat demonstrasi aplikasi secara langsung atau mempelajari kode sumber di sisi frontend, silakan kunjungi tautan berikut:

| Akses Layanan | Tautan Resmi |
| --- | --- |
| **Aplikasi Live (Demo)** | [https://jobportal-indonesia.vercel.app/](https://jobportal-indonesia.vercel.app/) |
| **Repositori Backend** | [https://github.com/Muhirabazram/jobportal-backend](https://github.com/Muhirabazram/jobportal-backend) |
| **Repositori Frontend** | [https://github.com/Muhirabazram/jobportal-frontend](https://github.com/Muhirabazram/jobportal-frontend) |

---

## 📌 Deskripsi Proyek

**JobPortal Indonesia** adalah platform pencarian kerja yang dirancang untuk menghubungkan para pencari kerja (Job Seekers) dengan perusahaan penyedia lowongan kerja (Employers/HRD). Aplikasi ini menyediakan pengalaman yang mulus bagi pengguna untuk mencari pekerjaan sesuai keahlian, melamar secara online, melacak status lamaran secara real-time, serta memudahkan HRD untuk mengelola lowongan pekerjaan dan memproses dokumen pelamar.

Dalam proyek ini, **Backend API** bertindak sebagai penyedia layanan RESTful API utama yang mengelola logika bisnis, autentikasi berbasis peran (*role-based access control*), manajemen berkas (seperti unggah CV dan avatar), serta relasi database yang kompleks.

---

## 🚀 Fitur Utama

Sistem backend ini mengimplementasikan berbagai fitur unggulan:

*   🔒 **Autentikasi & Keamanan (Laravel Sanctum)**: Sistem login, registrasi untuk dua tipe peran, pemeriksaan email aktif, pengaturan ulang kata sandi, serta perlindungan route API menggunakan token bearer.
*   👥 **Multi-Role Middleware**: Pemisahan otoritas rute yang ketat antara `job_seeker` dan `employer` guna memastikan keamanan data operasional.
*   📂 **Manajemen Berkas & Berkas Media**: Layanan unggah, pembaruan, dan penghapusan foto profil (avatar) dan berkas CV (PDF) untuk pelamar secara asinkronus dan aman.
*   💼 **Manajemen Lowongan Pekerjaan (Job Listings)**: Fungsionalitas CRUD (Create, Read, Update, Delete) lowongan pekerjaan lengkap dengan status gaji, deskripsi, slug ramah SEO, kategori, serta tipe pekerjaan.
*   📝 **Sistem Lamaran Pekerjaan (Job Applications)**: Alur lamaran terintegrasi yang memungkinkan pelamar mengirim CV serta pengalaman kerja mereka, dan perusahaan dapat memproses status lamaran secara real-time lengkap dengan alasan penolakan (*rejection reason*) jika lamaran ditolak.
*   ⭐ **Bookmark & Saved Jobs**: Memungkinkan pencari kerja untuk menandai lowongan yang diminati untuk diakses kembali di kemudian hari.
*   📊 **Dashboard Statistik Terintegrasi**: Statistik agregat untuk memantau ringkasan aktivitas di aplikasi (jumlah lamaran baru, lowongan aktif, statistik wawancara) baik bagi perusahaan maupun data statistik beranda.

---

## 🛠️ Teknologi yang Digunakan

Backend dirancang dengan arsitektur modern menggunakan teknologi berikut:

*   **Framework Core**: [Laravel 12.x](https://laravel.com/) (Framework PHP terbaik dengan performa dan keamanan tinggi)
*   **Bahasa Pemrograman**: PHP v8.2 atau lebih tinggi
*   **Autentikasi**: Laravel Sanctum (Token-Based RESTful API)
*   **Database**: PostgreSQL (Production di Supabase) / SQLite atau MySQL (Lokal)
*   **Containerization**: Docker (Dikonfigurasi dengan PHP-CLI dan ekstensi `pdo_pgsql` untuk Supabase)
*   **Asset Bundler & Server**: Vite & PHP-CLI Server

---

## 📂 Struktur Direktori Utama

Berikut adalah gambaran arsitektur folder utama pada repositori backend ini:

```bash
jobportal-backend/
├── app/
│   ├── Http/Controllers/Api/  # Logika Controller RESTful API (Auth, Jobs, Applications, dll)
│   ├── Middleware/            # Middleware kustom (termasuk Role-checking)
│   └── Models/                # Model database (User, Company, JobListing, Category, JobType, dll)
├── bootstrap/                 # Bootstrap konfigurasi aplikasi
├── config/                    # Seluruh konfigurasi Laravel
├── database/
│   ├── migrations/            # Struktur skema tabel basis data
│   └── seeders/               # Seeder data awal untuk Kategori dan Tipe Pekerjaan
├── routes/
│   ├── api.php                # Semua endpoint RESTful API terdaftar di sini
│   └── web.php                # Rute web standar
├── Dockerfile                 # Konfigurasi container Docker untuk deployment backend
├── vercel.json                # Konfigurasi rewrite untuk deployment backend
└── composer.json              # Daftar pustaka PHP dan dependensi proyek
```

---

## 📡 Daftar API Endpoints

Aplikasi ini menggunakan prefix `/api` untuk semua rute. Berikut adalah daftar endpoint yang tersedia:

### 1. Endpoint Publik (Tanpa Autentikasi)

| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `POST` | `/api/register/job-seeker` | Pendaftaran akun pencari kerja (*Job Seeker*) |
| `POST` | `/api/register/employer` | Pendaftaran akun pemberi kerja (*Employer / Company*) |
| `POST` | `/api/login` | Autentikasi pengguna untuk mendapatkan token akses |
| `POST` | `/api/check-email` | Mengecek apakah email sudah terdaftar |
| `POST` | `/api/reset-password` | Melakukan reset kata sandi |
| `GET` | `/api/categories` | Mendapatkan semua kategori pekerjaan |
| `GET` | `/api/job-types` | Mendapatkan semua tipe pekerjaan (Full-time, Remote, dll) |
| `GET` | `/api/jobs` | Mendapatkan daftar lowongan dengan filter & pencarian |
| `GET` | `/api/jobs/{slug}` | Mendapatkan detail informasi satu lowongan kerja berdasarkan slug |
| `GET` | `/api/home-stats` | Mendapatkan data statistik beranda publik |

### 2. Endpoint Terproteksi (Membutuhkan Bearer Token)

Semua rute di bawah ini wajib melampirkan header `Authorization: Bearer <token_anda>`.

#### A. Endpoint Profil Umum (Job Seeker & Employer)
| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `POST` | `/api/logout` | Menghapus token aktif dan keluar dari sistem |
| `GET` | `/api/profile` | Mendapatkan biodata profil pengguna yang sedang login |
| `PUT` | `/api/profile` | Memperbarui biodata profil |
| `POST` | `/api/change-password` | Mengubah kata sandi pengguna |
| `POST` | `/api/profile/avatar` | Mengunggah / memperbarui foto profil |
| `DELETE` | `/api/profile/avatar` | Menghapus foto profil |
| `POST` | `/api/profile/cv` | Mengunggah / memperbarui berkas CV (PDF) |
| `DELETE` | `/api/profile/cv` | Menghapus berkas CV dari sistem |
| `DELETE` | `/api/profile/delete-account`| Menghapus akun secara permanen |
| `GET` | `/api/dashboard/stats` | Statistik dashboard personal sesuai perannya |

#### B. Khusus Peran: Pencari Kerja (Role: `job_seeker`)
| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/api/saved-jobs` | Mendapatkan daftar lowongan kerja yang disimpan |
| `POST` | `/api/jobs/{slug}/save` | Menyimpan lowongan kerja (*bookmark*) |
| `DELETE` | `/api/jobs/{slug}/unsave` | Menghapus lowongan kerja dari daftar simpanan |
| `POST` | `/api/jobs/{slug}/apply` | Mengirim lamaran pekerjaan ke lowongan tujuan |
| `GET` | `/api/my-applications` | Mendapatkan daftar riwayat lamaran yang dikirim |
| `DELETE` | `/api/applications/{id}`| Membatalkan lamaran pekerjaan |

#### C. Khusus Peran: Perusahaan / Employer (Role: `employer`)
| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `POST` | `/api/jobs` | Membuat dan memposting lowongan kerja baru |
| `PUT` | `/api/jobs/{id}` | Memperbarui informasi lowongan pekerjaan |
| `DELETE` | `/api/jobs/{id}` | Menghapus lowongan pekerjaan |
| `GET` | `/api/my-jobs` | Mendapatkan semua lowongan yang diposting perusahaan ini |
| `GET` | `/api/jobs/{id}/applications` | Mendapatkan semua pelamar untuk lowongan tertentu |
| `PUT` | `/api/applications/{id}/status`| Memperbarui status lamaran pelamar (Terima, Tolak, Wawancara) |
| `GET` | `/api/my-interviews` | Mendapatkan daftar wawancara terjadwal perusahaan |

---

## 💻 Panduan Instalasi Lokal

Jika Anda ingin menjalankan proyek backend ini di lingkungan lokal Anda, silakan ikuti langkah-langkah berikut:

### Prasyarat
Pastikan komputer Anda sudah terpasang:
*   PHP (versi minimal 8.2)
*   Composer
*   Node.js & NPM
*   Database Engine (MySQL, PostgreSQL, atau SQLite)

### Langkah-langkah
1.  **Clone Repositori**:
    ```bash
    git clone https://github.com/Muhirabazram/jobportal-backend.git
    cd jobportal-backend
    ```

2.  **Instal Dependensi PHP**:
    ```bash
    composer install
    ```

3.  **Salin Konfigurasi Environment**:
    ```bash
    copy .env.example .env
    ```
    *Catatan: Atur konfigurasi database pada berkas `.env` sesuai dengan database lokal Anda.*

4.  **Buat Application Key**:
    ```bash
    php artisan key:generate
    ```

5.  **Jalankan Migrasi Database dan Seeder**:
    ```bash
    php artisan migrate --seed
    ```
    *Perintah ini akan membuat semua tabel basis data serta mengisi tipe pekerjaan dan kategori awal.*

6.  **Buat Simbolik Link Storage**:
    ```bash
    php artisan storage:link
    ```
    *Langkah penting agar berkas avatar dan CV yang diunggah dapat diakses secara publik melalui URL.*

7.  **Instal & Build Aset (Opsional/Vite)**:
    ```bash
    npm install
    npm run build
    ```

8.  **Jalankan Server Lokal**:
    ```bash
    php artisan serve
    ```
    Server lokal Anda akan berjalan secara default di `http://127.0.0.1:8000`.

---

## ✍️ Penulis & Kontributor

Proyek ini dikembangkan oleh **Muhirabazram** sebagai bagian dari pemenuhan tugas praktikum kuliah Full Stack Semester 4 (Pak Rena). Terima kasih yang sebesar-besarnya atas bimbingan dan dukungan yang diberikan selama pengembangan proyek ini.

Jika Anda memiliki pertanyaan, saran, atau ingin berkolaborasi terkait proyek ini, silakan hubungi melalui akun GitHub resmi penulis.

---
*Dibuat dengan penuh dedikasi untuk mendukung ekosistem pencarian kerja digital yang lebih baik di Indonesia.*

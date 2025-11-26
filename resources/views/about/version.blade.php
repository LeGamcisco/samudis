@extends('layouts.app')
@section('title','About Samu DIS')
@section('subtitle','Version')
@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Version 3.1.0 | {{ app()->version() }} <span class="text-info">EWS Edition</span></h5>
    </div>
    <div class="card-body">
        <p>
            <strong>Samu DIS</strong> (Data Interfacing System) merupakan sistem pengelola dan integrator data <i>Continuous Emission Monitoring System</i> (CEMS).
            Aplikasi ini dirancang untuk memudahkan perusahaan dalam mengelola, memantau, dan mengirimkan data emisi secara otomatis yang terhubung langsung dengan <strong>SISPEK</strong>
            (<i>Sistem Informasi Pemantauan Emisi Industri Kontinyu</i>) milik Kementerian Lingkungan Hidup dan Kehutanan (KLHK).
        </p>

        <p>
            <strong>SISPEK</strong> berfungsi sebagai sistem nasional yang menerima dan mengelola data hasil pemantauan emisi cerobong industri yang diukur secara terus-menerus (CEMS).
            Sistem ini diterapkan pada sepuluh sektor industri wajib CEMS, antara lain: peleburan besi dan baja, pulp dan kertas, rayon, carbon black, migas, pertambangan, pengolahan sampah termal, semen, pembangkit listrik tenaga termal, serta pupuk dan amonium nitrat.
        </p>

        <p>
            Proses integrasi antara CEMS, DIS, dan SISPEK dilakukan melalui komunikasi data <i>Machine-to-Machine</i> (M2M) dengan tahapan sebagai berikut:
        </p>
        <ol class="list-numbered">
            <li>Data emisi dari cerobong diukur menggunakan peralatan CEMS dan diakuisisi oleh <i>Data Acquisition System</i> (DAS) setiap 5 menit.</li>
            <li>DAS mengirimkan data tersebut ke <strong>DIS</strong>, yaitu sistem aplikasi dan penyimpanan data yang memproses serta meneruskan data ke server <strong>SISPEK</strong>.</li>
            <li>DIS melakukan autentikasi ke server SISPEK menggunakan file otorisasi yang berisi kode unik agar dapat mengirimkan data setiap satu jam.</li>
            <li>Setelah data berhasil dikirim, DIS akan menerima notifikasi bahwa data telah diterima di server SISPEK.</li>
            <li>SISPEK kemudian mengolah data industri menjadi laporan baku mutu, grafik tren emisi real time, dan evaluasi kepatuhan industri.</li>
            <li>Informasi mengenai status integrasi dan profil industri dapat dilihat melalui situs resmi: <a href="https://ditppu.menlhk.go.id/sispek/display" target="_blank">ditppu.menlhk.go.id/sispek/display</a>.</li>
            <li>Panduan lengkap mekanisme integrasi SISPEK tersedia di: <a href="https://ditppu.menlhk.go.id/portal/registrasi-detail/registrasi-sispek" target="_blank">ditppu.menlhk.go.id/portal/registrasi-detail/registrasi-sispek</a>.</li>
        </ol>

        <p class="mt-3">Skema integrasi SISPEK:</p>
        <div class="row col-md-6 mx-auto">
            <img src="{{ asset('assets/images/skema_sispek.jpg') }}" class="img img-fluid" alt="Skema integrasi SISPEK">
            <p class="text-center mt-2">
                <small>Sumber: <a href="https://ditppu.menlhk.go.id/portal/sispek/" target="_blank">ditppu.menlhk.go.id/portal/sispek/</a></small>
            </p>
        </div>
    </div>
</div>
@endsection

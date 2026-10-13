<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    peserta: {
        type: Object,
        required: true,
    },
    pelatihanDiikuti: {
        type: Array,
        default: () => [],
    },
    nilai: {
        type: Array,
        default: () => [],
    },
})
</script>

<template>
    <main class="page">
        <header class="header">
            <h1>Dashboard Peserta</h1>
            <p class="welcome">
                Selamat datang, {{ peserta.nama }}!
            </p>
        </header>

        <!-- Pelatihan yang diikuti -->
        <section class="card">
            <h2>Pelatihan Saya</h2>

            <div
                v-if="pelatihanDiikuti.length"
                class="training-list"
            >
                <article
                    v-for="pelatihan in pelatihanDiikuti"
                    :key="pelatihan.pendaftaran_id"
                    class="training-item"
                >
                    <h3>{{ pelatihan.nama_pelatihan }}</h3>

                    <p>
                        <b>Tahap:</b>
                        {{ pelatihan.tahap || '-' }}
                    </p>

                    <p>
                        <b>Kelas:</b>
                        {{ pelatihan.kelas || '-' }}
                    </p>

                    <p>
                        <b>Status pendaftaran:</b>
                        {{ pelatihan.status || '-' }}
                    </p>
                </article>
            </div>

            <p v-else class="empty">
                Anda belum terdaftar pada pelatihan apa pun.
            </p>
        </section>

        <!-- Menu LMS -->
        <section class="card">
            <h2>Menu Pembelajaran</h2>

            <nav class="menu">
                <Link :href="route('peserta.materi.index')">
                    Materi
                </Link>

                <Link :href="route('peserta.tugas.index')">
                    Tugas
                </Link>

                <Link :href="route('peserta.pengumpulan.index')">
                    Pengumpulan Tugas
                </Link>

                <Link :href="route('peserta.kuis.index')">
                    Kuis
                </Link>

                <Link :href="route('peserta.progres.index')">
                    Progres Belajar
                </Link>

                <Link :href="route('peserta.sertifikat.index')">
                    Sertifikat
                </Link>
            </nav>
        </section>

        <!-- Nilai peserta -->
        <section class="card">
            <h2>Nilai Saya</h2>

            <div v-if="nilai.length" class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Pelatihan</th>
                            <th>Tahap</th>
                            <th>Komponen</th>
                            <th>Nilai</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="(item, index) in nilai"
                            :key="item.id"
                        >
                            <td>{{ index + 1 }}</td>
                            <td>{{ item.nama_pelatihan }}</td>
                            <td>{{ item.tahap }}</td>
                            <td>{{ item.komponen }}</td>
                            <td class="score">{{ item.nilai }}</td>
                            <td>{{ item.catatan || '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p v-else class="empty">
                Belum ada nilai yang diberikan oleh instruktur.
            </p>
        </section>

        <!-- Informasi peserta -->
        <section class="card">
            <h2>Informasi Peserta</h2>

            <p>
                <b>Nama:</b>
                {{ peserta.nama }}
            </p>

            <p>
                <b>Alamat:</b>
                {{ peserta.alamat || '-' }}
            </p>

            <p>
                <b>Kamar:</b>
                {{ peserta.kamar || '-' }}
            </p>

            <p>
                <b>Lembaga:</b>
                {{ peserta.lembaga || '-' }}
            </p>
        </section>
    </main>
</template>

<style scoped>
.page {
    max-width: 1100px;
    margin: 30px auto;
    padding: 20px;
    font-family: Arial, sans-serif;
    color: #1e293b;
}

.header {
    margin-bottom: 24px;
}

h1 {
    font-size: 28px;
    font-weight: 700;
}

.welcome {
    margin-top: 8px;
    color: #64748b;
}

.card {
    margin-top: 22px;
    padding: 20px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #fff;
}

.card h2 {
    margin-bottom: 16px;
    font-size: 20px;
    font-weight: 600;
}

.training-list {
    display: grid;
    grid-template-columns: repeat(
        auto-fit,
        minmax(230px, 1fr)
    );
    gap: 14px;
}

.training-item {
    padding: 16px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #f8fafc;
}

.training-item h3 {
    margin-bottom: 10px;
    font-size: 17px;
    font-weight: 600;
}

.training-item p,
.card > p {
    margin-top: 8px;
}

.menu {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.menu a {
    padding: 10px 14px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    color: #334155;
    text-decoration: none;
    transition: background 0.2s;
}

.menu a:hover {
    background: #f1f5f9;
}

.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    white-space: nowrap;
}

th,
td {
    padding: 12px;
    border-bottom: 1px solid #e2e8f0;
    text-align: left;
}

th {
    background: #f8fafc;
    font-size: 12px;
    text-transform: uppercase;
    color: #475569;
}

.score {
    font-weight: 700;
}

.empty {
    color: #64748b;
}

@media (max-width: 640px) {
    .page {
        margin: 12px auto;
        padding: 12px;
    }

    .card {
        padding: 14px;
    }
}
</style>

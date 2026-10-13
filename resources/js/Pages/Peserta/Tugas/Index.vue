
<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    tugas: { type: Object, required: true },
})
</script>

<template>
    <main class="page">
        <Link :href="route('peserta.dashboard')">← Dashboard</Link>
        <h1>Daftar Tugas</h1>

        <p v-if="!tugas.data.length">Belum ada tugas.</p>

        <table v-else>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Tugas</th>
                    <th>Materi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in tugas.data" :key="item.id">
                    <td>{{ tugas.from + index }}</td>
                    <td>{{ item.judul || item.nama_tugas || item.nama || '-' }}</td>
                    <td>{{ item.materi?.judul || '-' }}</td>
                    <td>
                        <Link :href="route('peserta.tugas.show', item.id)">Detail / Kumpulkan</Link>
                    </td>
                </tr>
            </tbody>
        </table>

        <nav class="pagination">
            <template v-for="link in tugas.links" :key="link.label">
                <Link v-if="link.url" :href="link.url" v-html="link.label" />
                <span v-else v-html="link.label" />
            </template>
        </nav>
    </main>
</template>

<style scoped>
.page { max-width: 1000px; margin: 30px auto; padding: 20px; font-family: Arial, sans-serif; }
h1 { margin: 20px 0; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
th { background: #eee; }
a { color: #075bbb; }
.pagination { display: flex; gap: 8px; margin-top: 20px; }
.pagination a, .pagination span { padding: 6px 10px; border: 1px solid #ccc; }
</style>

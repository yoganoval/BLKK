
<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    materi: { type: Object, required: true },
})
</script>

<template>
    <main class="page">
        <Link :href="route('peserta.dashboard')">← Dashboard</Link>
        <h1>Daftar Materi</h1>

        <p v-if="materi.data.length === 0">Belum ada materi tersedia.</p>

        <table v-else>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Judul</th>
                    <th>Pelatihan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in materi.data" :key="item.id">
                    <td>{{ materi.from + index }}</td>
                    <td>{{ item.judul }}</td>
                    <td>{{ item.pelatihan?.nama || item.pelatihan?.nama_pelatihan || '-' }}</td>
                    <td>
                        <Link :href="route('peserta.materi.show', item.id)">Lihat</Link>
                    </td>
                </tr>
            </tbody>
        </table>

        <nav class="pagination">
            <template v-for="link in materi.links" :key="link.label">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    v-html="link.label"
                    :class="{ active: link.active }"
                />
                <span v-else v-html="link.label" />
            </template>
        </nav>
    </main>
</template>

<style scoped>
.page { max-width: 1000px; margin: 30px auto; padding: 20px; font-family: Arial, sans-serif; }
h1 { margin: 20px 0; }
table { width: 100%; border-collapse: collapse; }
th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
th { background: #eee; }
a { color: #075bbb; }
.pagination { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 20px; }
.pagination a, .pagination span { padding: 6px 10px; border: 1px solid #ccc; text-decoration: none; }
.pagination .active { background: #ddd; }
</style>

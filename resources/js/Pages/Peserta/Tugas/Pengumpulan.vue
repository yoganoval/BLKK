
<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    pengumpulan: { type: Object, required: true },
})
</script>

<template>
    <main class="page">
        <Link :href="route('peserta.dashboard')">← Dashboard</Link>
        <h1>Riwayat Pengumpulan Tugas</h1>

        <p v-if="!pengumpulan.data.length">Belum ada tugas yang dikumpulkan.</p>

        <table v-else>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Tugas</th>
                    <th>Jawaban</th>
                    <th>File</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in pengumpulan.data" :key="item.id">
                    <td>{{ pengumpulan.from + index }}</td>
                    <td>{{ item.tugas?.judul || item.tugas?.nama_tugas || '-' }}</td>
                    <td>{{ item.jawaban || '-' }}</td>
                    <td>
                        <a v-if="item.file_path" :href="`/storage/${item.file_path}`" target="_blank">
                            Lihat file
                        </a>
                        <span v-else>-</span>
                    </td>
                    <td>{{ item.created_at ? new Date(item.created_at).toLocaleString('id-ID') : '-' }}</td>
                </tr>
            </tbody>
        </table>
    </main>
</template>

<style scoped>
.page { max-width: 1000px; margin: 30px auto; padding: 20px; font-family: Arial, sans-serif; }
h1 { margin: 20px 0; }
table { width: 100%; border-collapse: collapse; }
th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
th { background: #eee; }
a { color: #075bbb; }
</style>

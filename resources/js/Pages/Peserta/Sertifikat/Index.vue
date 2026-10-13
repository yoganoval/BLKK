
<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    sertifikat: { type: Object, required: true },
})
</script>

<template>
    <main class="page">
        <Link :href="route('peserta.dashboard')">← Dashboard</Link>
        <h1>Sertifikat Saya</h1>

        <p v-if="!sertifikat.data.length">Belum ada sertifikat yang diterbitkan.</p>

        <table v-else>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Sertifikat</th>
                    <th>Tanggal Terbit</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in sertifikat.data" :key="item.id">
                    <td>{{ sertifikat.from + index }}</td>
                    <td>{{ item.nama || item.nama_sertifikat || item.nomor_sertifikat || `Sertifikat ${item.id}` }}</td>
                    <td>{{ item.tanggal_terbit || (item.created_at ? new Date(item.created_at).toLocaleDateString('id-ID') : '-') }}</td>
                    <td>
                        <a
                            :href="route('peserta.sertifikat.download', item.id)"
                        >
                            Unduh
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </main>
</template>

<style scoped>
.page { max-width: 950px; margin: 30px auto; padding: 20px; font-family: Arial, sans-serif; }
h1 { margin: 20px 0; }
table { width: 100%; border-collapse: collapse; }
th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
th { background: #eee; }
a { color: #075bbb; }
</style>

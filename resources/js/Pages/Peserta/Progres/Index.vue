
<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    progres: { type: Object, required: true },
})
</script>

<template>
    <main class="page">
        <Link :href="route('peserta.dashboard')">← Dashboard</Link>
        <h1>Progres Belajar</h1>

        <p v-if="!progres.data.length">Belum ada progres belajar.</p>

        <table v-else>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Materi</th>
                    <th>Status</th>
                    <th>Progres</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in progres.data" :key="item.id">
                    <td>{{ progres.from + index }}</td>
                    <td>{{ item.materi?.judul || '-' }}</td>
                    <td>{{ item.status || '-' }}</td>
                    <td>
                        <progress :value="Number(item.persentase || item.progres || 0)" max="100"></progress>
                        {{ item.persentase ?? item.progres ?? 0 }}%
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
progress { width: 100px; }
</style>

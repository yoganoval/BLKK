
<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    kuis: { type: Object, required: true },
})
</script>

<template>
    <main class="page">
        <Link :href="route('peserta.dashboard')">← Dashboard</Link>
        <h1>Daftar Kuis</h1>

        <p v-if="!kuis.data.length">Belum ada kuis.</p>

        <section v-for="item in kuis.data" :key="item.id" class="card">
            <h2>{{ item.judul || item.nama || item.nama_kuis || 'Kuis' }}</h2>
            <p>{{ item.deskripsi || '-' }}</p>
            <Link :href="route('peserta.kuis.show', item.id)">Buka Kuis</Link>
        </section>
    </main>
</template>

<style scoped>
.page { max-width: 850px; margin: 30px auto; padding: 20px; font-family: Arial, sans-serif; }
h1 { margin: 20px 0; }
.card { border: 1px solid #ddd; border-radius: 8px; padding: 18px; margin: 15px 0; }
a { color: #075bbb; }
</style>

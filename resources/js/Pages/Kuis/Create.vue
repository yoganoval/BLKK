<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    programPelatihans: { type: Array, default: () => [] },
    kelas: { type: Array, default: () => [] },
    materi: { type: Array, default: () => [] },
})

const form = useForm({
    program_pelatihan_id: '',
    kelas_id: '',
    materi_id: '',
    judul: '',
    deskripsi: '',
    waktu_menit: 60,
    nilai_lulus: 70,
    percobaan_maksimal: 1,
    status: 'draft',
})

function submit() {
    form.post(route('kuis.store'))
}
</script>

<template>
    <div class="min-h-screen bg-gray-50 p-4 md:p-8 dark:bg-gray-950">
        <div class="mx-auto max-w-3xl">
            <div class="mb-6">
                <Link :href="route('kuis.index')" class="text-sm text-blue-600 hover:underline">
                    ← Kembali ke daftar kuis
                </Link>
                <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">
                    Tambah Kuis
                </h1>
            </div>

            <form
                @submit.prevent="submit"
                class="space-y-5 rounded-xl border border-gray-200 bg-white p-5 md:p-7 dark:border-gray-800 dark:bg-gray-900"
            >
                <div>
                    <label class="mb-1 block text-sm font-medium">Program Pelatihan</label>
                    <select v-model="form.program_pelatihan_id" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                        <option value="">Pilih program pelatihan</option>
                        <option v-for="item in programPelatihans" :key="item.id" :value="item.id">
                            {{ item.pelatihan?.nama ?? item.nama ?? `Program #${item.id}` }}
                            {{ item.tahap?.nama ? ` - ${item.tahap.nama}` : '' }}
                        </option>
                    </select>
                    <p v-if="form.errors.program_pelatihan_id" class="mt-1 text-sm text-red-600">
                        {{ form.errors.program_pelatihan_id }}
                    </p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Kelas</label>
                    <select v-model="form.kelas_id" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                        <option value="">Pilih kelas</option>
                        <option v-for="item in kelas" :key="item.id" :value="item.id">
                            {{ item.nama }}
                        </option>
                    </select>
                    <p v-if="form.errors.kelas_id" class="mt-1 text-sm text-red-600">
                        {{ form.errors.kelas_id }}
                    </p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Materi (Opsional)</label>
                    <select v-model="form.materi_id" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                        <option value="">Tidak terkait materi tertentu</option>
                        <option v-for="item in materi" :key="item.id" :value="item.id">
                            {{ item.judul }}
                        </option>
                    </select>
                    <p v-if="form.errors.materi_id" class="mt-1 text-sm text-red-600">
                        {{ form.errors.materi_id }}
                    </p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Judul Kuis</label>
                    <input v-model="form.judul" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                    <p v-if="form.errors.judul" class="mt-1 text-sm text-red-600">
                        {{ form.errors.judul }}
                    </p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Deskripsi</label>
                    <textarea v-model="form.deskripsi" rows="4" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                    <p v-if="form.errors.deskripsi" class="mt-1 text-sm text-red-600">
                        {{ form.errors.deskripsi }}
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Waktu (menit)</label>
                        <input v-model="form.waktu_menit" type="number" min="1" max="1440" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                        <p v-if="form.errors.waktu_menit" class="mt-1 text-sm text-red-600">
                            {{ form.errors.waktu_menit }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium">Nilai Lulus</label>
                        <input v-model="form.nilai_lulus" type="number" min="0" max="100" step="0.01" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                        <p v-if="form.errors.nilai_lulus" class="mt-1 text-sm text-red-600">
                            {{ form.errors.nilai_lulus }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium">Maksimal Percobaan</label>
                        <input v-model="form.percobaan_maksimal" type="number" min="1" max="100" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                        <p v-if="form.errors.percobaan_maksimal" class="mt-1 text-sm text-red-600">
                            {{ form.errors.percobaan_maksimal }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium">Status</label>
                        <select v-model="form.status" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                            <option value="draft">Draft</option>
                            <option value="published">Dipublikasikan</option>
                            <option value="closed">Ditutup</option>
                        </select>
                        <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">
                            {{ form.errors.status }}
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t border-gray-200 pt-5 dark:border-gray-800">
                    <Link :href="route('kuis.index')" class="rounded-lg border px-4 py-2 text-sm dark:border-gray-700">
                        Batal
                    </Link>
                    <button :disabled="form.processing" class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white disabled:opacity-50">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Kuis' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
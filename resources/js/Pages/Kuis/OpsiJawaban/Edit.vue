<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    soal: { type: Object, required: true },
    opsiJawaban: { type: Object, required: true },
})

const form = useForm({
    opsi: props.opsiJawaban.opsi ?? '',
    teks: props.opsiJawaban.teks ?? '',
    is_correct: Boolean(props.opsiJawaban.is_correct),
    urutan: props.opsiJawaban.urutan ?? 1,
})

function submit() {
    form.put(route('soal.opsi-jawaban.update', [
        props.soal.id,
        props.opsiJawaban.id,
    ]))
}
</script>

<template>
    <div class="min-h-screen bg-gray-50 p-4 md:p-8 dark:bg-gray-950">
        <div class="mx-auto max-w-3xl">
            <Link :href="route('soal.opsi-jawaban.index', soal.id)" class="text-sm text-blue-600 hover:underline">
                ← Kembali ke opsi jawaban
            </Link>

            <h1 class="my-5 text-2xl font-bold dark:text-white">Edit Opsi Jawaban</h1>

            <form @submit.prevent="submit" class="space-y-5 rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <div>
                    <label class="mb-1 block text-sm font-medium">Kode Opsi</label>
                    <input v-model="form.opsi" maxlength="5" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                    <p v-if="form.errors.opsi" class="mt-1 text-sm text-red-600">{{ form.errors.opsi }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Teks Jawaban</label>
                    <textarea v-model="form.teks" rows="4" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                    <p v-if="form.errors.teks" class="mt-1 text-sm text-red-600">{{ form.errors.teks }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Urutan</label>
                    <input v-model="form.urutan" type="number" min="1" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                    <p v-if="form.errors.urutan" class="mt-1 text-sm text-red-600">{{ form.errors.urutan }}</p>
                </div>

                <label class="flex items-start gap-3 rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                    <input v-model="form.is_correct" type="checkbox" class="mt-1 rounded border-gray-300 text-blue-600" />
                    <span>
                        <span class="block text-sm font-semibold dark:text-white">Jawaban benar</span>
                        <span class="text-xs text-gray-500">Menandai opsi ini sebagai jawaban yang benar.</span>
                    </span>
                </label>

                <p v-if="form.errors.is_correct" class="text-sm text-red-600">{{ form.errors.is_correct }}</p>

                <div class="flex justify-end gap-3 border-t border-gray-200 pt-5 dark:border-gray-800">
                    <Link :href="route('soal.opsi-jawaban.index', soal.id)" class="rounded-lg border px-4 py-2 text-sm dark:border-gray-700">Batal</Link>
                    <button :disabled="form.processing" class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white disabled:opacity-50">
                        {{ form.processing ? 'Memperbarui...' : 'Simpan Perubahan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
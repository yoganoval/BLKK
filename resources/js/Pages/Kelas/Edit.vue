<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    kelas: {
        type: Object,
        required: true,
    },
})

const form = useForm({
    nama: props.kelas.nama ?? '',
    durasi_hari: props.kelas.durasi_hari ?? '',
    keterangan: props.kelas.keterangan ?? '',
})

const submit = () => {
    form.put(route('kelas.update', {
        kelas: props.kelas.id,
    }))
}
</script>

<template>
    <Head title="Edit Kelas" />

    <div class="min-h-screen bg-slate-50 px-4 py-6 dark:bg-slate-950 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl">

            <!-- HEADER -->
            <div class="mb-6">
                <div class="mb-2 flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                    <Link
                        :href="route('kelas.index')"
                        class="transition hover:text-indigo-600 dark:hover:text-indigo-400"
                    >
                        Kelas
                    </Link>

                    <span>/</span>

                    <span class="font-medium text-slate-700 dark:text-slate-200">
                        Edit
                    </span>
                </div>

                <h1 class="text-2xl font-bold tracking-tight text-slate-800 dark:text-white">
                    Edit Kelas
                </h1>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Perbarui informasi kelas {{ props.kelas.nama }}.
                </p>
            </div>

            <!-- FORM -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">

                <div class="border-b border-slate-200 px-5 py-5 dark:border-slate-800">
                    <h2 class="font-semibold text-slate-800 dark:text-white">
                        Informasi Kelas
                    </h2>

                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Silakan ubah data yang diperlukan.
                    </p>
                </div>

                <form @submit.prevent="submit" class="p-5 sm:p-6">

                    <!-- NAMA -->
                    <div class="mb-5">
                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                            Nama Kelas
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            v-model="form.nama"
                            type="text"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                            :class="{ 'border-red-500': form.errors.nama }"
                        />

                        <p
                            v-if="form.errors.nama"
                            class="mt-2 text-xs font-medium text-red-500"
                        >
                            {{ form.errors.nama }}
                        </p>
                    </div>

                    <!-- DURASI -->
                    <div class="mb-5">
                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                            Durasi Pelatihan
                            <span class="text-red-500">*</span>
                        </label>

                        <div class="relative">
                            <input
                                v-model="form.durasi_hari"
                                type="number"
                                min="1"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pr-20 text-sm text-slate-700 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                                :class="{ 'border-red-500': form.errors.durasi_hari }"
                            />

                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm text-slate-400">
                                Hari
                            </span>
                        </div>

                        <p
                            v-if="form.errors.durasi_hari"
                            class="mt-2 text-xs font-medium text-red-500"
                        >
                            {{ form.errors.durasi_hari }}
                        </p>
                    </div>

                    <!-- QUICK -->
                    <div class="mb-5">
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Pilihan Cepat
                        </label>

                        <div class="flex flex-wrap gap-2">
                            <button
                                type="button"
                                @click="form.durasi_hari = 5"
                                class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                            >
                                5 Hari
                            </button>

                            <button
                                type="button"
                                @click="form.durasi_hari = 10"
                                class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-400 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                            >
                                10 Hari
                            </button>
                        </div>
                    </div>

                    <!-- KETERANGAN -->
                    <div class="mb-6">
                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                            Keterangan
                        </label>

                        <textarea
                            v-model="form.keterangan"
                            rows="4"
                            placeholder="Masukkan keterangan kelas..."
                            class="w-full resize-none rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                            :class="{ 'border-red-500': form.errors.keterangan }"
                        ></textarea>

                        <p
                            v-if="form.errors.keterangan"
                            class="mt-2 text-xs font-medium text-red-500"
                        >
                            {{ form.errors.keterangan }}
                        </p>
                    </div>

                    <!-- ACTION -->
                    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 dark:border-slate-800 sm:flex-row sm:justify-end">
                        <Link
                            :href="route('kelas.index')"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                        >
                            Batal
                        </Link>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
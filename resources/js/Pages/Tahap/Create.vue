<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const form = useForm({
    nama: '',
    periode: '',
    keterangan: '',
})

const submit = () => {
    form.post(route('tahaps.store'))
}
</script>

<template>
    <Head title="Tambah Tahap" />

    <div class="min-h-screen bg-slate-50 px-4 py-6 dark:bg-slate-950 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl">

            <!-- HEADER -->
            <div class="mb-6">
                <div class="mb-2 flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                    <Link
                        :href="route('tahaps.index')"
                        class="transition hover:text-indigo-600 dark:hover:text-indigo-400"
                    >
                        Tahap
                    </Link>

                    <span>/</span>

                    <span class="font-medium text-slate-700 dark:text-slate-200">
                        Tambah
                    </span>
                </div>

                <h1 class="text-2xl font-bold tracking-tight text-slate-800 dark:text-white">
                    Tambah Tahap
                </h1>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Tambahkan tahapan baru untuk proses pelatihan.
                </p>
            </div>

            <!-- FORM CARD -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">

                <div class="border-b border-slate-200 px-5 py-5 dark:border-slate-800">
                    <h2 class="font-semibold text-slate-800 dark:text-white">
                        Informasi Tahap
                    </h2>

                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Tentukan nama dan periode tahap.
                    </p>
                </div>

                <form
                    @submit.prevent="submit"
                    class="p-5 sm:p-6"
                >

                    <!-- NAMA -->
                    <div class="mb-5">
                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                            Nama Tahap
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            v-model="form.nama"
                            type="text"
                            placeholder="Contoh: Tahap 1"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                            :class="{ 'border-red-500': form.errors.nama }"
                        />

                        <p
                            v-if="form.errors.nama"
                            class="mt-2 text-xs font-medium text-red-500"
                        >
                            {{ form.errors.nama }}
                        </p>
                    </div>

                    <!-- periode -->
                    <div class="mb-5">
                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                            periode
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            v-model="form.periode"
                            type="number"
                            min="1"
                            placeholder="Contoh: 1"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                            :class="{ 'border-red-500': form.errors.periode }"
                        />

                        <p class="mt-2 text-xs text-slate-400">
                            periode digunakan untuk menentukan Tahap 1, Tahap 2, Tahap 3, dan seterusnya.
                        </p>

                        <p
                            v-if="form.errors.periode"
                            class="mt-2 text-xs font-medium text-red-500"
                        >
                            {{ form.errors.periode }}
                        </p>
                    </div>

                    <!-- KETERANGAN -->
                    <div class="mb-6">
                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                            Keterangan
                        </label>

                        <textarea
                            v-model="form.keterangan"
                            rows="4"
                            placeholder="Masukkan keterangan tahap..."
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
                            :href="route('tahaps.index')"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                        >
                            Batal
                        </Link>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Tahap' }}
                        </button>

                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
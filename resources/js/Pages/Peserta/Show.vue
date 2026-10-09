<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    peserta: {
        type: Object,
        required: true,
    },
})

const formatDate = (date) => {
    if (!date) return '-'

    return new Date(date).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    })
}
</script>

<template>
    <Head :title="`Peserta - ${props.peserta.nama}`" />

    <div class="min-h-screen bg-slate-50 px-4 py-6 dark:bg-slate-950 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-5xl">

            <!-- HEADER -->
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <div class="mb-2 flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">

                        <Link
                            :href="route('pesertas.index')"
                            class="transition hover:text-indigo-600 dark:hover:text-indigo-400"
                        >
                            Peserta
                        </Link>

                        <span>/</span>

                        <span class="font-medium text-slate-700 dark:text-slate-200">
                            Detail
                        </span>

                    </div>

                    <h1 class="text-2xl font-bold tracking-tight text-slate-800 dark:text-white">
                        Detail Peserta
                    </h1>

                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Informasi lengkap peserta.
                    </p>
                </div>

                <div class="flex gap-2">

                    <Link
                        :href="route('pesertas.index')"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                    >
                        Kembali
                    </Link>

                    <Link
                        :href="route('pesertas.edit', props.peserta.id)"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
                    >
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M11 5h2m-1-1v2m8.5 7.5L13 20H5v-8l6.5-7.5a2.121 2.121 0 013 0l4 4a2.121 2.121 0 010 3z"
                            />
                        </svg>

                        Edit
                    </Link>

                </div>
            </div>

            <!-- CONTENT -->
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

                <!-- PROFILE -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">

                    <div class="flex flex-col items-center text-center">

                        <div class="flex h-24 w-24 items-center justify-center rounded-2xl bg-indigo-50 text-3xl font-bold text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                            {{ props.peserta.nama?.charAt(0)?.toUpperCase() }}
                        </div>

                        <h2 class="mt-4 text-xl font-bold text-slate-800 dark:text-white">
                            {{ props.peserta.nama }}
                        </h2>

                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Peserta #{{ props.peserta.id }}
                        </p>

                        <div
                            v-if="props.peserta.lembaga"
                            class="mt-3 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300"
                        >
                            {{ props.peserta.lembaga }}
                        </div>

                    </div>
                </div>

                <!-- DETAIL -->
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 lg:col-span-2">

                    <div class="border-b border-slate-200 px-6 py-5 dark:border-slate-800">
                        <h2 class="font-semibold text-slate-800 dark:text-white">
                            Informasi Peserta
                        </h2>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-slate-800">

                        <!-- ID -->
                        <div class="flex flex-col gap-1 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                            <span class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                ID Peserta
                            </span>

                            <span class="font-semibold text-slate-800 dark:text-white">
                                #{{ props.peserta.id }}
                            </span>
                        </div>

                        <!-- NAMA -->
                        <div class="flex flex-col gap-1 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                            <span class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Nama
                            </span>

                            <span class="font-semibold text-slate-800 dark:text-white">
                                {{ props.peserta.nama }}
                            </span>
                        </div>

                        <!-- KAMAR -->
                        <div class="flex flex-col gap-1 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                            <span class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Kamar
                            </span>

                            <span class="text-sm text-slate-700 dark:text-slate-300">
                                {{ props.peserta.kamar || '-' }}
                            </span>
                        </div>

                        <!-- LEMBAGA -->
                        <div class="flex flex-col gap-1 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                            <span class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Lembaga
                            </span>

                            <span class="text-sm text-slate-700 dark:text-slate-300">
                                {{ props.peserta.lembaga || '-' }}
                            </span>
                        </div>

                        <!-- ALAMAT -->
                        <div class="px-6 py-5">
                            <span class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Alamat
                            </span>

                            <p class="mt-2 text-sm leading-6 text-slate-700 dark:text-slate-300">
                                {{ props.peserta.alamat || 'Tidak ada alamat.' }}
                            </p>
                        </div>

                        <!-- CREATED -->
                        <div class="flex flex-col gap-1 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                            <span class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Terdaftar
                            </span>

                            <span class="text-sm text-slate-700 dark:text-slate-300">
                                {{ formatDate(props.peserta.created_at) }}
                            </span>
                        </div>

                        <!-- UPDATED -->
                        <div class="flex flex-col gap-1 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                            <span class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Terakhir diperbarui
                            </span>

                            <span class="text-sm text-slate-700 dark:text-slate-300">
                                {{ formatDate(props.peserta.updated_at) }}
                            </span>
                        </div>

                    </div>
                </div>
            </div>

            <!-- INFO -->
            <div class="mt-5 rounded-2xl border border-indigo-100 bg-indigo-50 p-5 dark:border-indigo-500/20 dark:bg-indigo-500/10">

                <div class="flex gap-3">

                    <svg
                        class="mt-0.5 h-5 w-5 shrink-0 text-indigo-600 dark:text-indigo-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M12 21a9 9 0 100-18 9 9 0 000 18z"
                        />
                    </svg>

                    <div>
                        <p class="text-sm font-semibold text-indigo-800 dark:text-indigo-300">
                            Informasi Peserta
                        </p>

                        <p class="mt-1 text-sm leading-6 text-indigo-700 dark:text-indigo-400">
                            Data peserta ini nantinya dapat digunakan untuk
                            pendaftaran pelatihan, pembayaran, presensi, dan
                            registrasi wajah.
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</template>
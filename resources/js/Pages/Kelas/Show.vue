<script setup>
import { Head, Link } from '@inertiajs/vue3'
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
    <Head :title="`Kelas - ${props.kelas.nama}`" />

    <div class="min-h-screen bg-slate-50 px-4 py-6 dark:bg-slate-950 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-5xl">

            <!-- HEADER -->
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="mb-2 flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                        <Link
                            :href="route('kelas.index')"
                            class="transition hover:text-indigo-600 dark:hover:text-indigo-400"
                        >
                            Kelas
                        </Link>

                        <span>/</span>

                        <span class="font-medium text-slate-700 dark:text-slate-200">
                            Detail
                        </span>
                    </div>

                    <h1 class="text-2xl font-bold tracking-tight text-slate-800 dark:text-white">
                        Detail Kelas
                    </h1>

                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Informasi lengkap kelas {{ props.kelas.nama }}.
                    </p>
                </div>

                <div class="flex gap-2">
                    <Link
                        :href="route('kelas.index')"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                    >
                        Kembali
                    </Link>

                    <Link
                        :href="route('kelas.edit', props.kelas.id)"
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

            <!-- MAIN CARD -->
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

                <!-- PROFILE -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex flex-col items-center text-center">

                        <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                            <svg
                                class="h-10 w-10"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.7"
                                    d="M12 14l9-5-9-5-9 5 9 5z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.7"
                                    d="M12 14l6.16-3.422A12.083 12.083 0 0118 14.5c0 2.485-2.686 4.5-6 4.5s-6-2.015-6-4.5c0-1.37.82-2.605 2.16-3.422L12 14z"
                                />
                            </svg>
                        </div>

                        <h2 class="mt-4 text-xl font-bold text-slate-800 dark:text-white">
                            {{ props.kelas.nama }}
                        </h2>

                        <span class="mt-2 inline-flex rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                            {{ props.kelas.durasi_hari }} Hari
                        </span>
                    </div>
                </div>

                <!-- DETAIL -->
                <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">

                    <div class="border-b border-slate-200 px-6 py-5 dark:border-slate-800">
                        <h2 class="font-semibold text-slate-800 dark:text-white">
                            Informasi Kelas
                        </h2>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-slate-800">

                        <!-- ID -->
                        <div class="flex flex-col gap-1 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                            <span class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                ID Kelas
                            </span>

                            <span class="font-semibold text-slate-800 dark:text-white">
                                #{{ props.kelas.id }}
                            </span>
                        </div>

                        <!-- NAMA -->
                        <div class="flex flex-col gap-1 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                            <span class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Nama Kelas
                            </span>

                            <span class="font-semibold text-slate-800 dark:text-white">
                                {{ props.kelas.nama }}
                            </span>
                        </div>

                        <!-- DURASI -->
                        <div class="flex flex-col gap-1 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                            <span class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Durasi
                            </span>

                            <span class="font-semibold text-slate-800 dark:text-white">
                                {{ props.kelas.durasi_hari }} Hari
                            </span>
                        </div>

                        <!-- KETERANGAN -->
                        <div class="px-6 py-5">
                            <span class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Keterangan
                            </span>

                            <p class="mt-2 text-sm leading-6 text-slate-700 dark:text-slate-300">
                                {{ props.kelas.keterangan || 'Tidak ada keterangan.' }}
                            </p>
                        </div>

                        <!-- CREATED -->
                        <div class="flex flex-col gap-1 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                            <span class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Dibuat
                            </span>

                            <span class="text-sm text-slate-700 dark:text-slate-300">
                                {{ formatDate(props.kelas.created_at) }}
                            </span>
                        </div>

                        <!-- UPDATED -->
                        <div class="flex flex-col gap-1 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                            <span class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Terakhir diperbarui
                            </span>

                            <span class="text-sm text-slate-700 dark:text-slate-300">
                                {{ formatDate(props.kelas.updated_at) }}
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
                            Informasi
                        </p>

                        <p class="mt-1 text-sm leading-6 text-indigo-700 dark:text-indigo-400">
                            Durasi kelas digunakan sebagai acuan jumlah hari pelaksanaan
                            pelatihan. Contohnya kelas Regular memiliki durasi 5 hari,
                            sedangkan kelas Private memiliki durasi 10 hari.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>
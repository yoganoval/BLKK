<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    pelatihan: {
        type: Object,
        required: true,
    },
})
</script>

<template>
    <Head :title="`Detail - ${pelatihan.nama}`" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
        <div class="mx-auto max-w-5xl space-y-6 p-4 sm:p-6 lg:p-8">

            <!-- HEADER -->
            <div>
                <div
                    class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400"
                >
                    <Link
                        :href="route('pelatihans.index')"
                        class="transition hover:text-indigo-600 dark:hover:text-indigo-400"
                    >
                        Pelatihan
                    </Link>

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 5l7 7-7 7"
                        />
                    </svg>

                    <span>Detail</span>
                </div>

                <div
                    class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-lg font-bold text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400"
                        >
                            {{ pelatihan.nama?.charAt(0)?.toUpperCase() ?? 'P' }}
                        </div>

                        <div>
                            <h1
                                class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white"
                            >
                                {{ pelatihan.nama }}
                            </h1>

                            <p
                                class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                            >
                                Detail informasi pelatihan
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <Link
                            :href="route('pelatihans.index')"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15 19l-7-7 7-7"
                                />
                            </svg>

                            Kembali
                        </Link>

                        <Link
                            :href="route('pelatihans.edit', pelatihan.id)"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-600/20 transition hover:bg-indigo-700"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"
                                />
                            </svg>

                            Edit
                        </Link>
                    </div>
                </div>
            </div>

            <!-- DETAIL CARD -->
            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >
                <div
                    class="border-b border-slate-200 px-6 py-5 dark:border-slate-800"
                >
                    <h2
                        class="text-base font-semibold text-slate-900 dark:text-white"
                    >
                        Informasi Pelatihan
                    </h2>

                    <p
                        class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                    >
                        Informasi lengkap mengenai pelatihan.
                    </p>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800">

                    <!-- ID -->
                    <div
                        class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3 sm:gap-6"
                    >
                        <div>
                            <p
                                class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500"
                            >
                                ID Pelatihan
                            </p>
                        </div>

                        <div class="sm:col-span-2">
                            <span
                                class="inline-flex items-center rounded-lg bg-slate-100 px-3 py-1.5 text-sm font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                            >
                                #{{ pelatihan.id }}
                            </span>
                        </div>
                    </div>

                    <!-- NAMA -->
                    <div
                        class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3 sm:gap-6"
                    >
                        <div>
                            <p
                                class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500"
                            >
                                Nama Pelatihan
                            </p>
                        </div>

                        <div class="sm:col-span-2">
                            <p
                                class="text-sm font-semibold text-slate-800 dark:text-white"
                            >
                                {{ pelatihan.nama }}
                            </p>
                        </div>
                    </div>

                    <!-- KETERANGAN -->
                    <div
                        class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3 sm:gap-6"
                    >
                        <div>
                            <p
                                class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500"
                            >
                                Keterangan
                            </p>
                        </div>

                        <div class="sm:col-span-2">
                            <p
                                v-if="pelatihan.keterangan"
                                class="whitespace-pre-line text-sm leading-6 text-slate-600 dark:text-slate-400"
                            >
                                {{ pelatihan.keterangan }}
                            </p>

                            <p
                                v-else
                                class="text-sm italic text-slate-400 dark:text-slate-500"
                            >
                                Tidak ada keterangan.
                            </p>
                        </div>
                    </div>

                    <!-- CREATED -->
                    <div
                        v-if="pelatihan.created_at"
                        class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3 sm:gap-6"
                    >
                        <div>
                            <p
                                class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500"
                            >
                                Dibuat
                            </p>
                        </div>

                        <div class="sm:col-span-2">
                            <p
                                class="text-sm text-slate-600 dark:text-slate-400"
                            >
                                {{ pelatihan.created_at }}
                            </p>
                        </div>
                    </div>

                    <!-- UPDATED -->
                    <div
                        v-if="pelatihan.updated_at"
                        class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-3 sm:gap-6"
                    >
                        <div>
                            <p
                                class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500"
                            >
                                Terakhir Diperbarui
                            </p>
                        </div>

                        <div class="sm:col-span-2">
                            <p
                                class="text-sm text-slate-600 dark:text-slate-400"
                            >
                                {{ pelatihan.updated_at }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- INFO -->
            <div
                class="flex gap-3 rounded-2xl border border-indigo-100 bg-indigo-50/70 p-5 dark:border-indigo-500/20 dark:bg-indigo-500/5"
            >
                <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <circle
                            cx="12"
                            cy="12"
                            r="9"
                        />
                        <path
                            stroke-linecap="round"
                            d="M12 11v5"
                        />
                        <path
                            stroke-linecap="round"
                            d="M12 8h.01"
                        />
                    </svg>
                </div>

                <div>
                    <h3
                        class="text-sm font-semibold text-indigo-900 dark:text-indigo-300"
                    >
                        Informasi
                    </h3>

                    <p
                        class="mt-1 text-sm leading-6 text-indigo-700 dark:text-indigo-400"
                    >
                        Data pelatihan ini dapat digunakan sebagai dasar
                        untuk membuat program pelatihan dan tahapan pelatihan.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
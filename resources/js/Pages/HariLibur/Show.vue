<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    hariLibur: {
        type: Object,
        required: true,
    },
})

const formatTanggal = (tanggal) => {
    if (!tanggal) return '-'

    const date = new Date(`${tanggal}T00:00:00`)

    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    }).format(date)
}

const formatHari = (tanggal) => {
    if (!tanggal) return '-'

    const date = new Date(`${tanggal}T00:00:00`)

    return new Intl.DateTimeFormat('id-ID', {
        weekday: 'long',
    }).format(date)
}

const hapus = () => {
    if (
        confirm(
            `Yakin ingin menghapus "${props.hariLibur.nama}"?`
        )
    ) {
        router.delete(
            route(
                'hari-libur.destroy',
                props.hariLibur.id
            )
        )
    }
}
</script>

<template>
    <Head title="Detail Hari Libur" />

    <div class="mx-auto max-w-3xl space-y-6">
        <!-- HEADER -->
        <div>
            <Link
                :href="route('hari-libur.index')"
                class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400"
            >
                ← Kembali ke Hari Libur
            </Link>

            <div
                class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1
                        class="text-2xl font-bold text-slate-800 dark:text-white"
                    >
                        {{ props.hariLibur.nama }}
                    </h1>

                    <p
                        class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                    >
                        Detail hari libur.
                    </p>
                </div>

                <div class="flex gap-2">
                    <Link
                        :href="
                            route(
                                'hari-libur.edit',
                                props.hariLibur.id
                            )
                        "
                        class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700"
                    >
                        Edit
                    </Link>

                    <button
                        type="button"
                        @click="hapus"
                        class="rounded-xl bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400"
                    >
                        Hapus
                    </button>
                </div>
            </div>
        </div>

        <!-- DETAIL -->
        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900"
        >
            <div
                class="border-b border-slate-200 bg-slate-50 px-6 py-5 dark:border-slate-700 dark:bg-slate-800/70"
            >
                <h2
                    class="font-semibold text-slate-800 dark:text-white"
                >
                    Informasi Hari Libur
                </h2>
            </div>

            <div class="divide-y divide-slate-200 dark:divide-slate-700">
                <!-- TANGGAL -->
                <div
                    class="grid gap-2 px-6 py-5 sm:grid-cols-3"
                >
                    <div
                        class="text-sm font-medium text-slate-500 dark:text-slate-400"
                    >
                        Tanggal
                    </div>

                    <div class="sm:col-span-2">
                        <div
                            class="font-semibold text-slate-800 dark:text-white"
                        >
                            {{ formatTanggal(props.hariLibur.tanggal) }}
                        </div>

                        <div
                            class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                        >
                            {{ formatHari(props.hariLibur.tanggal) }}
                        </div>
                    </div>
                </div>

                <!-- NAMA -->
                <div
                    class="grid gap-2 px-6 py-5 sm:grid-cols-3"
                >
                    <div
                        class="text-sm font-medium text-slate-500 dark:text-slate-400"
                    >
                        Nama
                    </div>

                    <div
                        class="font-semibold text-slate-800 dark:text-white sm:col-span-2"
                    >
                        {{ props.hariLibur.nama }}
                    </div>
                </div>

                <!-- STATUS -->
                <div
                    class="grid gap-2 px-6 py-5 sm:grid-cols-3"
                >
                    <div
                        class="text-sm font-medium text-slate-500 dark:text-slate-400"
                    >
                        Status
                    </div>

                    <div class="sm:col-span-2">
                        <span
                            v-if="props.hariLibur.status"
                            class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400"
                        >
                            <span
                                class="h-2 w-2 rounded-full bg-emerald-500"
                            ></span>
                            Aktif
                        </span>

                        <span
                            v-else
                            class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-400"
                        >
                            <span
                                class="h-2 w-2 rounded-full bg-slate-400"
                            ></span>
                            Nonaktif
                        </span>
                    </div>
                </div>

                <!-- KETERANGAN -->
                <div
                    class="grid gap-2 px-6 py-5 sm:grid-cols-3"
                >
                    <div
                        class="text-sm font-medium text-slate-500 dark:text-slate-400"
                    >
                        Keterangan
                    </div>

                    <div
                        class="whitespace-pre-line text-sm text-slate-700 dark:text-slate-300 sm:col-span-2"
                    >
                        {{ props.hariLibur.keterangan || '-' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- INFO ABSENSI -->
        <div
            class="rounded-2xl border border-amber-100 bg-amber-50 p-5 dark:border-amber-900/50 dark:bg-amber-950/20"
        >
            <div class="flex gap-3">
                <div class="text-xl">⚠️</div>

                <div>
                    <h3
                        class="text-sm font-semibold text-amber-900 dark:text-amber-300"
                    >
                        Dampak terhadap absensi
                    </h3>

                    <p
                        class="mt-1 text-sm leading-6 text-amber-700 dark:text-amber-400"
                    >
                        <template
                            v-if="props.hariLibur.status"
                        >
                            Pada tanggal ini sistem akan menganggap hari
                            sebagai hari libur dan absensi staff tidak
                            tersedia.
                        </template>

                        <template v-else>
                            Hari libur ini berstatus nonaktif sehingga tidak
                            akan memblokir absensi.
                        </template>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
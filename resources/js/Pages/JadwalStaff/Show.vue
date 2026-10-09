<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    jadwal: {
        type: Object,
        required: true,
    },
})

const hariLabel = {
    senin: 'Senin',
    selasa: 'Selasa',
    rabu: 'Rabu',
    kamis: 'Kamis',
    jumat: 'Jumat',
    sabtu: 'Sabtu',
    minggu: 'Minggu',
}

const formatJam = (jam) => {
    if (!jam) return '-'
    return jam.substring(0, 5)
}

const hapus = () => {
    if (
        confirm(
            `Yakin ingin menghapus jadwal "${props.jadwal.nama}"?`
        )
    ) {
        router.delete(
            route('jadwal-staff.destroy', props.jadwal.id)
        )
    }
}
</script>

<template>
    <Head title="Detail Jadwal Staff" />

    <div class="mx-auto max-w-3xl space-y-6">
        <!-- HEADER -->
        <div>
            <Link
                :href="route('jadwal-staff.index')"
                class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400"
            >
                ← Kembali ke Jadwal Staff
            </Link>

            <div
                class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1
                        class="text-2xl font-bold text-slate-800 dark:text-white"
                    >
                        {{ props.jadwal.nama }}
                    </h1>

                    <p
                        class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                    >
                        Detail pengaturan jadwal kerja staff.
                    </p>
                </div>

                <div class="flex gap-2">
                    <Link
                        :href="
                            route(
                                'jadwal-staff.edit',
                                props.jadwal.id
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
                    Informasi Jadwal
                </h2>
            </div>

            <div class="divide-y divide-slate-200 dark:divide-slate-700">
                <!-- NAMA -->
                <div
                    class="grid gap-2 px-6 py-5 sm:grid-cols-3"
                >
                    <div
                        class="text-sm font-medium text-slate-500 dark:text-slate-400"
                    >
                        Nama Jadwal
                    </div>

                    <div
                        class="font-semibold text-slate-800 dark:text-white sm:col-span-2"
                    >
                        {{ props.jadwal.nama }}
                    </div>
                </div>

                <!-- HARI -->
                <div
                    class="grid gap-2 px-6 py-5 sm:grid-cols-3"
                >
                    <div
                        class="text-sm font-medium text-slate-500 dark:text-slate-400"
                    >
                        Hari
                    </div>

                    <div class="sm:col-span-2">
                        <span
                            class="inline-flex rounded-lg bg-indigo-50 px-3 py-1.5 text-sm font-semibold text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400"
                        >
                            {{ hariLabel[props.jadwal.hari] }}
                        </span>
                    </div>
                </div>

                <!-- JAM MASUK -->
                <div
                    class="grid gap-2 px-6 py-5 sm:grid-cols-3"
                >
                    <div
                        class="text-sm font-medium text-slate-500 dark:text-slate-400"
                    >
                        Jam Masuk
                    </div>

                    <div
                        class="text-sm font-semibold text-slate-800 dark:text-white sm:col-span-2"
                    >
                        {{ formatJam(props.jadwal.jam_masuk) }}
                    </div>
                </div>

                <!-- JAM PULANG -->
                <div
                    class="grid gap-2 px-6 py-5 sm:grid-cols-3"
                >
                    <div
                        class="text-sm font-medium text-slate-500 dark:text-slate-400"
                    >
                        Jam Pulang
                    </div>

                    <div
                        class="text-sm font-semibold text-slate-800 dark:text-white sm:col-span-2"
                    >
                        {{ formatJam(props.jadwal.jam_pulang) }}
                    </div>
                </div>

                <!-- TOLERANSI -->
                <div
                    class="grid gap-2 px-6 py-5 sm:grid-cols-3"
                >
                    <div
                        class="text-sm font-medium text-slate-500 dark:text-slate-400"
                    >
                        Toleransi
                    </div>

                    <div
                        class="text-sm font-semibold text-slate-800 dark:text-white sm:col-span-2"
                    >
                        {{ props.jadwal.toleransi_menit }} menit
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
                            v-if="props.jadwal.status"
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
                        {{ props.jadwal.keterangan || '-' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- ATURAN ABSENSI -->
        <div
            class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900"
        >
            <h2
                class="font-semibold text-slate-800 dark:text-white"
            >
                Aturan Absensi
            </h2>

            <div class="mt-4 space-y-3 text-sm">
                <div
                    class="flex gap-3 text-slate-600 dark:text-slate-300"
                >
                    <span>🟢</span>

                    <span>
                        Absensi masuk dimulai pukul
                        <strong>
                            {{ formatJam(props.jadwal.jam_masuk) }}
                        </strong>.
                    </span>
                </div>

                <div
                    class="flex gap-3 text-slate-600 dark:text-slate-300"
                >
                    <span>🟡</span>

                    <span>
                        Toleransi keterlambatan:
                        <strong>
                            {{ props.jadwal.toleransi_menit }}
                            menit
                        </strong>.
                    </span>
                </div>

                <div
                    class="flex gap-3 text-slate-600 dark:text-slate-300"
                >
                    <span>🔴</span>

                    <span>
                        Setelah batas toleransi, staff masih dapat
                        melakukan absensi tetapi statusnya
                        <strong>terlambat</strong>.
                    </span>
                </div>

                <div
                    class="flex gap-3 text-slate-600 dark:text-slate-300"
                >
                    <span>🔵</span>

                    <span>
                        Absensi pulang hanya dapat dilakukan setelah
                        pukul
                        <strong>
                            {{ formatJam(props.jadwal.jam_pulang) }}
                        </strong>.
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
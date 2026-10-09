<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    programPelatihan: {
        type: Object,
        required: true,
    },
})

const pesertaAktif = computed(() => {
    return (props.programPelatihan.pendaftarans || [])
        .filter(item => item.status === 'aktif')
})

const sisaKuota = computed(() => {
    return Math.max(
        Number(props.programPelatihan.kuota || 0) -
        pesertaAktif.value.length,
        0
    )
})

const isFull = computed(() => {
    return pesertaAktif.value.length >=
        Number(props.programPelatihan.kuota || 0)
})

const formatTanggal = (tanggal) => {
    if (!tanggal) return '-'

    return new Date(tanggal).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    })
}

const statusClass = (status) => {
    if (status === 'dibuka') {
        return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400'
    }

    return 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'
}
</script>

<template>
    <Head title="Detail Program Pelatihan" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">

        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

            <!-- HEADER -->
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div>

                    <div class="mb-3 flex items-center gap-2 text-sm text-slate-500">

                        <Link
                            :href="route('program-pelatihans.index')"
                            class="hover:text-indigo-600"
                        >
                            Program Pelatihan
                        </Link>

                        <span>/</span>

                        <span>
                            Detail
                        </span>

                    </div>

                    <h1 class="text-xl font-bold text-slate-800 dark:text-white">
                        Detail Program Pelatihan
                    </h1>

                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Informasi program dan peserta yang terdaftar
                    </p>

                </div>

                <div class="flex gap-2">

                    <Link
                        :href="route('program-pelatihans.index')"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                    >
                        Kembali
                    </Link>

                    <Link
                        :href="route('program-pelatihans.edit', programPelatihan.id)"
                        class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700"
                    >
                        Edit
                    </Link>

                </div>

            </div>

            <!-- PROGRAM INFO -->
            <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">

                <div class="border-b border-slate-200 p-6 dark:border-slate-800">

                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                        <div class="flex items-center gap-4">

                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">

                                <svg
                                    class="h-7 w-7"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.8"
                                        d="M12 6.5a3.5 3.5 0 100-7 3.5 3.5 0 000 7zM4 21a8 8 0 0116 0"
                                    />
                                </svg>

                            </div>

                            <div>

                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                    Program
                                </p>

                                <h2 class="mt-1 text-2xl font-bold text-slate-800 dark:text-white">
                                    {{ programPelatihan.nama }}
                                </h2>

                                <p class="mt-1 text-sm text-slate-500">
                                    ID #{{ programPelatihan.id }}
                                </p>

                            </div>

                        </div>

                        <span
                            class="inline-flex w-fit rounded-full px-4 py-2 text-sm font-semibold"
                            :class="statusClass(programPelatihan.status)"
                        >
                            {{ programPelatihan.status }}
                        </span>

                    </div>

                </div>

                <!-- DETAIL -->
                <div class="grid grid-cols-1 gap-5 p-6 sm:grid-cols-2 lg:grid-cols-4">

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Pelatihan
                        </p>

                        <p class="mt-1 font-semibold text-slate-800 dark:text-white">
                            {{ programPelatihan.pelatihan?.nama || '-' }}
                        </p>

                    </div>

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Tahap
                        </p>

                        <p class="mt-1 font-semibold text-slate-800 dark:text-white">
                            Tahap {{ programPelatihan.tahap?.urutan || '-' }}
                            -
                            {{ programPelatihan.tahap?.nama || '-' }}
                        </p>

                    </div>

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Kuota
                        </p>

                        <p class="mt-1 font-semibold text-slate-800 dark:text-white">
                            {{ programPelatihan.kuota }} Peserta
                        </p>

                    </div>

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Sisa Kuota
                        </p>

                        <p
                            class="mt-1 font-bold"
                            :class="isFull
                                ? 'text-red-600'
                                : 'text-emerald-600'"
                        >
                            {{
                                isFull
                                    ? 'Penuh'
                                    : `${sisaKuota} Peserta`
                            }}
                        </p>

                    </div>

                </div>

            </div>

            <!-- STATISTIK PESERTA -->
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">

                    <p class="text-sm text-slate-500">
                        Total Pendaftar
                    </p>

                    <p class="mt-1 text-2xl font-bold text-slate-800 dark:text-white">
                        {{ programPelatihan.pendaftarans?.length || 0 }}
                    </p>

                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">

                    <p class="text-sm text-slate-500">
                        Peserta Aktif
                    </p>

                    <p class="mt-1 text-2xl font-bold text-emerald-600">
                        {{ pesertaAktif.length }}
                    </p>

                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">

                    <p class="text-sm text-slate-500">
                        Kapasitas
                    </p>

                    <p class="mt-1 text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                        {{ pesertaAktif.length }} / {{ programPelatihan.kuota }}
                    </p>

                </div>

            </div>

            <!-- PESERTA -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">

                <div class="border-b border-slate-200 px-6 py-5 dark:border-slate-800">

                    <h3 class="font-bold text-slate-800 dark:text-white">
                        Daftar Peserta
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Peserta yang terdaftar pada program ini
                    </p>

                </div>

                <div class="overflow-x-auto">

                    <table class="w-full text-left">

                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-800/50">

                                <th class="px-6 py-4 text-xs font-semibold uppercase text-slate-500">
                                    #
                                </th>

                                <th class="px-6 py-4 text-xs font-semibold uppercase text-slate-500">
                                    Peserta
                                </th>

                                <th class="px-6 py-4 text-xs font-semibold uppercase text-slate-500">
                                    Kelas
                                </th>

                                <th class="px-6 py-4 text-xs font-semibold uppercase text-slate-500">
                                    Tanggal Daftar
                                </th>

                                <th class="px-6 py-4 text-center text-xs font-semibold uppercase text-slate-500">
                                    Status
                                </th>

                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">

                            <tr
                                v-for="(pendaftaran, index) in programPelatihan.pendaftarans"
                                :key="pendaftaran.id"
                                class="hover:bg-slate-50 dark:hover:bg-slate-800/40"
                            >

                                <td class="px-6 py-4 text-sm text-slate-500">
                                    {{ index + 1 }}
                                </td>

                                <td class="px-6 py-4">

                                    <div class="font-semibold text-slate-800 dark:text-white">
                                        {{ pendaftaran.peserta?.nama || '-' }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-500">
                                        ID #{{ pendaftaran.peserta?.id || '-' }}
                                    </div>

                                </td>

                                <td class="px-6 py-4">

                                    <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                        {{ pendaftaran.kelas?.nama || '-' }}
                                    </span>

                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                    {{ formatTanggal(pendaftaran.tanggal_daftar) }}
                                </td>

                                <td class="px-6 py-4 text-center">

                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="pendaftaran.status === 'aktif'
                                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400'
                                            : pendaftaran.status === 'selesai'
                                                ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400'
                                                : 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400'"
                                    >
                                        {{ pendaftaran.status }}
                                    </span>

                                </td>

                            </tr>

                            <tr
                                v-if="!programPelatihan.pendaftarans?.length"
                            >

                                <td
                                    colspan="5"
                                    class="px-6 py-14 text-center"
                                >

                                    <p class="font-semibold text-slate-700 dark:text-slate-200">
                                        Belum ada peserta
                                    </p>

                                    <p class="mt-1 text-sm text-slate-500">
                                        Belum ada peserta yang mendaftar pada program ini.
                                    </p>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>
</template>
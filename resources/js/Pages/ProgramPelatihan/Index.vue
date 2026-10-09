<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    programPelatihans: {
        type: Array,
        default: () => [],
    },
})

const search = ref('')
const statusFilter = ref('')

const filteredPrograms = computed(() => {
    const keyword = search.value.toLowerCase().trim()

    return props.programPelatihans.filter((item) => {
        const nama = item.nama?.toLowerCase() || ''
        const pelatihan =
            item.pelatihan?.nama?.toLowerCase() || ''
        const tahap =
            item.tahap?.nama?.toLowerCase() || ''

        const matchesSearch =
            !keyword ||
            nama.includes(keyword) ||
            pelatihan.includes(keyword) ||
            tahap.includes(keyword)

        const matchesStatus =
            !statusFilter.value ||
            item.status === statusFilter.value

        return matchesSearch && matchesStatus
    })
})

const totalDibuka = computed(() => {
    return props.programPelatihans.filter(
        item => item.status === 'dibuka'
    ).length
})

const totalDitutup = computed(() => {
    return props.programPelatihans.filter(
        item => item.status === 'ditutup'
    ).length
})

const totalKuota = computed(() => {
    return props.programPelatihans.reduce(
        (total, item) => total + Number(item.kuota || 0),
        0
    )
})

const pesertaAktif = (item) => {
    return Number(item.peserta_aktif_count || 0)
}

const sisaKuota = (item) => {
    return Math.max(
        Number(item.kuota || 0) - pesertaAktif(item),
        0
    )
}

const isFull = (item) => {
    return pesertaAktif(item) >= Number(item.kuota || 0)
}

const deleteProgram = (id) => {
    if (!confirm('Apakah Anda yakin ingin menghapus program pelatihan ini?')) {
        return
    }

    router.delete(route('program-pelatihans.destroy', id))
}

const statusClass = (status) => {
    if (status === 'dibuka') {
        return 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400'
    }

    return 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-400'
}
</script>

<template>
    <Head title="Program Pelatihan" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

            <!-- HEADER -->
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <div class="mb-2 flex items-center gap-3">

                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400"
                        >
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M12 6.5a3.5 3.5 0 100-7 3.5 3.5 0 000 7zM4 21a8 8 0 0116 0M8 12h8M12 8v8"
                                />
                            </svg>
                        </div>

                        <div>
                            <h1 class="text-xl font-bold text-slate-800 dark:text-white">
                                Program Pelatihan
                            </h1>

                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                Kelola program dan kuota pelatihan
                            </p>
                        </div>

                    </div>
                </div>

                <Link
                    :href="route('program-pelatihans.create')"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4v16m8-8H4"
                        />
                    </svg>

                    Tambah Program
                </Link>

            </div>

            <!-- STATISTIK -->
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                <!-- TOTAL -->
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Total Program
                    </p>

                    <p class="mt-1 text-2xl font-bold text-slate-800 dark:text-white">
                        {{ programPelatihans.length }}
                    </p>
                </div>

                <!-- DIBUKA -->
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Program Dibuka
                    </p>

                    <p class="mt-1 text-2xl font-bold text-emerald-600">
                        {{ totalDibuka }}
                    </p>
                </div>

                <!-- DITUTUP -->
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Program Ditutup
                    </p>

                    <p class="mt-1 text-2xl font-bold text-slate-500">
                        {{ totalDitutup }}
                    </p>
                </div>

                <!-- KUOTA -->
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Total Kuota
                    </p>

                    <p class="mt-1 text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                        {{ totalKuota }}
                    </p>
                </div>

            </div>

            <!-- TABLE -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">

                <!-- FILTER -->
                <div class="border-b border-slate-200 p-5 dark:border-slate-800">

                    <div class="grid grid-cols-1 gap-3 md:grid-cols-3">

                        <div class="relative md:col-span-2">

                            <svg
                                class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M21 21l-4.35-4.35m2.35-5.65a8 8 0 11-16 0 8 8 0 0116 0z"
                                />
                            </svg>

                            <input
                                v-model="search"
                                type="text"
                                placeholder="Cari program, pelatihan, atau tahap..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                            />

                        </div>

                        <select
                            v-model="statusFilter"
                            class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                        >
                            <option value="">
                                Semua Status
                            </option>

                            <option value="dibuka">
                                Dibuka
                            </option>

                            <option value="ditutup">
                                Ditutup
                            </option>
                        </select>

                    </div>

                </div>

                <!-- TABLE -->
                <div class="overflow-x-auto">

                    <table class="w-full text-left">

                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-800/50">

                                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    #
                                </th>

                                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Program
                                </th>

                                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Pelatihan
                                </th>

                                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Tahap
                                </th>

                                <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Kuota
                                </th>

                                <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Status
                                </th>

                                <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Aksi
                                </th>

                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">

                            <tr
                                v-for="(item, index) in filteredPrograms"
                                :key="item.id"
                                class="transition hover:bg-slate-50 dark:hover:bg-slate-800/40"
                            >

                                <td class="px-5 py-4 text-sm text-slate-500">
                                    {{ index + 1 }}
                                </td>

                                <!-- PROGRAM -->
                                <td class="px-5 py-4">

                                    <div class="font-semibold text-slate-800 dark:text-white">
                                        {{ item.nama }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-500">
                                        ID #{{ item.id }}
                                    </div>

                                </td>

                                <!-- PELATIHAN -->
                                <td class="px-5 py-4">

                                    <span class="font-medium text-slate-700 dark:text-slate-200">
                                        {{ item.pelatihan?.nama || '-' }}
                                    </span>

                                </td>

                                <!-- TAHAP -->
                                <td class="px-5 py-4">

                                    <div class="flex items-center gap-2">

                                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-50 text-xs font-bold text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                                            {{ item.tahap?.periode || '-' }}
                                        </span>

                                        <span class="text-sm text-slate-700 dark:text-slate-300">
                                            {{ item.tahap?.nama || '-' }}
                                        </span>

                                    </div>

                                </td>

                                <!-- KUOTA -->
                                <td class="px-5 py-4 text-center">

                                    <div class="font-bold text-slate-800 dark:text-white">
                                        {{ pesertaAktif(item) }}
                                        <span class="font-normal text-slate-400">
                                            / {{ item.kuota }}
                                        </span>
                                    </div>

                                    <div
                                        class="mt-1 text-xs font-medium"
                                        :class="isFull(item)
                                            ? 'text-red-500'
                                            : 'text-emerald-600'"
                                    >
                                        {{
                                            isFull(item)
                                                ? 'Kuota Penuh'
                                                : `Sisa ${sisaKuota(item)}`
                                        }}
                                    </div>

                                </td>

                                <!-- STATUS -->
                                <td class="px-5 py-4 text-center">

                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset"
                                        :class="statusClass(item.status)"
                                    >
                                        {{ item.status }}
                                    </span>

                                </td>

                                <!-- AKSI -->
                                <td class="px-5 py-4">

                                    <div class="flex justify-center gap-2">

                                        <Link
                                            :href="route('program-pelatihans.show', item.id)"
                                            class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-indigo-200 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:text-indigo-400"
                                        >
                                            Detail
                                        </Link>

                                        <Link
                                            :href="route('program-pelatihans.edit', item.id)"
                                            class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-indigo-200 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:text-indigo-400"
                                        >
                                            Edit
                                        </Link>

                                        <button
                                            type="button"
                                            @click="deleteProgram(item.id)"
                                            class="rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50 dark:border-red-900/50 dark:bg-slate-800 dark:text-red-400 dark:hover:bg-red-500/10"
                                        >
                                            Hapus
                                        </button>

                                    </div>

                                </td>

                            </tr>

                            <!-- EMPTY -->
                            <tr v-if="filteredPrograms.length === 0">

                                <td colspan="7" class="px-5 py-14 text-center">

                                    <div class="flex flex-col items-center">

                                        <div class="mb-3 rounded-2xl bg-slate-100 p-4 text-slate-400 dark:bg-slate-800">

                                            <svg
                                                class="h-8 w-8"
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

                                        <p class="font-semibold text-slate-700 dark:text-slate-200">
                                            Tidak ada program pelatihan
                                        </p>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Data program belum ditemukan.
                                        </p>

                                    </div>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>
    </div>
</template>
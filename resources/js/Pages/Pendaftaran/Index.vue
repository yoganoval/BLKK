<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    pendaftarans: {
        type: Array,
        default: () => [],
    },
})

const search = ref('')
const statusFilter = ref('')

const filteredPendaftarans = computed(() => {
    const keyword = search.value.toLowerCase().trim()

    return props.pendaftarans.filter((item) => {
        const namaPeserta = item.peserta?.nama?.toLowerCase() || ''
        const namaProgram = item.program_pelatihan?.nama?.toLowerCase() || ''
        const namaPelatihan =
            item.program_pelatihan?.pelatihan?.nama?.toLowerCase() || ''
        const namaKelas = item.kelas?.nama?.toLowerCase() || ''

        const matchesSearch =
            !keyword ||
            namaPeserta.includes(keyword) ||
            namaProgram.includes(keyword) ||
            namaPelatihan.includes(keyword) ||
            namaKelas.includes(keyword)

        const matchesStatus =
            !statusFilter.value ||
            item.status === statusFilter.value

        return matchesSearch && matchesStatus
    })
})

const totalAktif = computed(() => {
    return props.pendaftarans.filter(item => item.status === 'aktif').length
})

const totalSelesai = computed(() => {
    return props.pendaftarans.filter(item => item.status === 'selesai').length
})

const totalBatal = computed(() => {
    return props.pendaftarans.filter(item => item.status === 'batal').length
})

const deletePendaftaran = (id) => {
    if (!confirm('Apakah Anda yakin ingin menghapus pendaftaran ini?')) {
        return
    }

    router.delete(route('pendaftarans.destroy', id))
}

const formatTanggal = (tanggal) => {
    if (!tanggal) return '-'

    return new Date(tanggal).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    })
}

const statusClass = (status) => {
    switch (status) {
        case 'aktif':
            return 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400'
        case 'selesai':
            return 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400'
        case 'batal':
            return 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400'
        default:
            return 'bg-slate-50 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-400'
    }
}
</script>

<template>
    <Head title="Pendaftaran" />

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
                                    d="M18 8A6 6 0 106 8a6 6 0 0012 0zM4 21a8 8 0 0116 0"
                                />
                            </svg>
                        </div>

                        <div>
                            <h1 class="text-xl font-bold text-slate-800 dark:text-white">
                                Pendaftaran
                            </h1>

                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                Kelola data pendaftaran peserta pelatihan
                            </p>
                        </div>
                    </div>
                </div>

                <Link
                    :href="route('pendaftarans.create')"
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

                    Tambah Pendaftaran
                </Link>
            </div>

            <!-- STATISTIK -->
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                Total Pendaftaran
                            </p>

                            <p class="mt-1 text-2xl font-bold text-slate-800 dark:text-white">
                                {{ pendaftarans.length }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-indigo-50 p-3 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zm9 2a3 3 0 10-2.83-4"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Aktif
                    </p>

                    <p class="mt-1 text-2xl font-bold text-emerald-600">
                        {{ totalAktif }}
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Selesai
                    </p>

                    <p class="mt-1 text-2xl font-bold text-blue-600">
                        {{ totalSelesai }}
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Batal
                    </p>

                    <p class="mt-1 text-2xl font-bold text-red-600">
                        {{ totalBatal }}
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
                                placeholder="Cari peserta, program, pelatihan, atau kelas..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                            />
                        </div>

                        <select
                            v-model="statusFilter"
                            class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                        >
                            <option value="">Semua Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="selesai">Selesai</option>
                            <option value="batal">Batal</option>
                        </select>
                    </div>
                </div>

                <!-- RESPONSIVE TABLE -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-800/50">
                                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    #
                                </th>

                                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Peserta
                                </th>

                                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Program
                                </th>

                                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Kelas
                                </th>

                                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Tanggal Daftar
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
                                v-for="(item, index) in filteredPendaftarans"
                                :key="item.id"
                                class="transition hover:bg-slate-50 dark:hover:bg-slate-800/40"
                            >
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-500">
                                    {{ index + 1 }}
                                </td>

                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-800 dark:text-white">
                                        {{ item.peserta?.nama || '-' }}
                                    </div>

                                    <div class="mt-0.5 text-xs text-slate-500">
                                        ID #{{ item.peserta?.id || '-' }}
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="font-medium text-slate-700 dark:text-slate-200">
                                        {{ item.program_pelatihan?.nama || '-' }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-500">
                                        {{ item.program_pelatihan?.pelatihan?.nama || '-' }}
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                        {{ item.kelas?.nama || '-' }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                    {{ formatTanggal(item.tanggal_daftar) }}
                                </td>

                                <td class="px-5 py-4 text-center">
                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset"
                                        :class="statusClass(item.status)"
                                    >
                                        {{ item.status }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex justify-center gap-2">

                                        <Link
                                            :href="route('pendaftarans.show', item.id)"
                                            class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-indigo-200 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:text-indigo-400"
                                        >
                                            Detail
                                        </Link>

                                        <Link
                                            :href="route('pendaftarans.edit', item.id)"
                                            class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-indigo-200 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:text-indigo-400"
                                        >
                                            Edit
                                        </Link>

                                        <button
                                            type="button"
                                            @click="deletePendaftaran(item.id)"
                                            class="rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50 dark:border-red-900/50 dark:bg-slate-800 dark:text-red-400 dark:hover:bg-red-500/10"
                                        >
                                            Hapus
                                        </button>

                                    </div>
                                </td>
                            </tr>

                            <tr v-if="filteredPendaftarans.length === 0">
                                <td colspan="7" class="px-5 py-14 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="mb-3 rounded-2xl bg-slate-100 p-4 text-slate-400 dark:bg-slate-800">
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1.8"
                                                    d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v14H5V6a2 2 0 012-2z"
                                                />
                                            </svg>
                                        </div>

                                        <p class="font-semibold text-slate-700 dark:text-slate-200">
                                            Tidak ada data pendaftaran
                                        </p>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Data yang sesuai dengan pencarian belum ditemukan.
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
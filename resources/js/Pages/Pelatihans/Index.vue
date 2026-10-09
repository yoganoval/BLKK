<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    pelatihans: {
        type: Array,
        default: () => [],
    },
})

const search = ref('')
const statusFilter = ref('')

const filteredPelatihans = computed(() => {
    return props.pelatihans.filter((pelatihan) => {
        const keyword = search.value.toLowerCase().trim()

        const nama = pelatihan.nama ?? pelatihan.nama ?? ''
        const deskripsi = pelatihan.deskripsi ?? ''

        const matchSearch =
            nama.toLowerCase().includes(keyword) ||
            deskripsi.toLowerCase().includes(keyword)

        const matchStatus =
            !statusFilter.value ||
            pelatihan.status === statusFilter.value

        return matchSearch && matchStatus
    })
})

const totalPelatihan = computed(() => props.pelatihans.length)

const pelatihanAktif = computed(() =>
    props.pelatihans.filter(
        (pelatihan) => pelatihan.status === 'aktif'
    ).length
)

const pelatihanSelesai = computed(() =>
    props.pelatihans.filter(
        (pelatihan) => pelatihan.status === 'selesai'
    ).length
)

const pelatihanNonaktif = computed(() =>
    props.pelatihans.filter(
        (pelatihan) => pelatihan.status === 'nonaktif'
    ).length
)

const hapusPelatihan = (pelatihan) => {
    const nama =
        pelatihan.nama ??
        pelatihan.nama ??
        'pelatihan ini'

    if (
        confirm(
            `Apakah Anda yakin ingin menghapus pelatihan "${nama}"?`
        )
    ) {
        router.delete(
            route('pelatihans.destroy', pelatihan.id)
        )
    }
}

const statusClass = (status) => {
    switch (status) {
        case 'aktif':
            return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400'

        case 'nonaktif':
            return 'bg-gray-100 text-gray-700 dark:bg-gray-700/50 dark:text-gray-300'

        case 'selesai':
            return 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400'

        default:
            return 'bg-gray-100 text-gray-700 dark:bg-gray-700/50 dark:text-gray-300'
    }
}

const statusDot = (status) => {
    switch (status) {
        case 'aktif':
            return 'bg-emerald-500'

        case 'selesai':
            return 'bg-blue-500'

        case 'nonaktif':
            return 'bg-gray-400'

        default:
            return 'bg-gray-400'
    }
}

const resetFilter = () => {
    search.value = ''
    statusFilter.value = ''
}
</script>

<template>
    <div
        class="min-h-screen bg-gray-50 p-4 transition-colors duration-200
        dark:bg-gray-950 sm:p-6"
    >

        <!-- ========================================== -->
        <!-- HEADER -->
        <!-- ========================================== -->
        <div class="mb-6">
            <div
                class="flex flex-col gap-4 lg:flex-row lg:items-center
                lg:justify-between"
            >

                <div>
                    <div class="flex items-center gap-3">

                        <!-- Icon -->
                        <div
                            class="flex h-11 w-11 shrink-0 items-center
                            justify-center rounded-xl bg-blue-600 text-white
                            shadow-lg shadow-blue-600/20"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"
                                />
                            </svg>
                        </div>

                        <div>
                            <h1
                                class="text-xl font-bold tracking-tight
                                text-gray-900 dark:text-white sm:text-2xl"
                            >
                                Data Pelatihan
                            </h1>

                            <p
                                class="mt-0.5 text-sm text-gray-500
                                dark:text-gray-400"
                            >
                                Kelola program pelatihan yang tersedia
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tambah -->
                <Link
                    :href="route('pelatihans.create')"
                    class="inline-flex items-center justify-center gap-2
                    rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold
                    text-white shadow-lg shadow-blue-600/20 transition
                    hover:bg-blue-700 hover:shadow-blue-600/30
                    focus:outline-none focus:ring-2 focus:ring-blue-500
                    focus:ring-offset-2 dark:focus:ring-offset-gray-950"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 4v16m8-8H4"
                        />
                    </svg>

                    Tambah Pelatihan
                </Link>
            </div>
        </div>


        <!-- ========================================== -->
        <!-- STATISTIK -->
        <!-- ========================================== -->
        <div
            class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2
            xl:grid-cols-4"
        >

            <!-- Total -->
            <div
                class="rounded-2xl border border-gray-200 bg-white p-5
                shadow-sm transition hover:-translate-y-0.5 hover:shadow-md
                dark:border-gray-800 dark:bg-gray-900"
            >
                <div class="flex items-center justify-between">

                    <div>
                        <p
                            class="text-sm font-medium text-gray-500
                            dark:text-gray-400"
                        >
                            Total Pelatihan
                        </p>

                        <p
                            class="mt-2 text-2xl font-bold text-gray-900
                            dark:text-white"
                        >
                            {{ totalPelatihan }}
                        </p>
                    </div>

                    <div
                        class="flex h-11 w-11 items-center justify-center
                        rounded-xl bg-blue-100 text-blue-600
                        dark:bg-blue-500/10 dark:text-blue-400"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"
                            />
                        </svg>
                    </div>
                </div>
            </div>


            <!-- Aktif -->
            <div
                class="rounded-2xl border border-gray-200 bg-white p-5
                shadow-sm transition hover:-translate-y-0.5 hover:shadow-md
                dark:border-gray-800 dark:bg-gray-900"
            >
                <div class="flex items-center justify-between">

                    <div>
                        <p
                            class="text-sm font-medium text-gray-500
                            dark:text-gray-400"
                        >
                            Aktif
                        </p>

                        <p
                            class="mt-2 text-2xl font-bold text-gray-900
                            dark:text-white"
                        >
                            {{ pelatihanAktif }}
                        </p>
                    </div>

                    <div
                        class="flex h-11 w-11 items-center justify-center
                        rounded-xl bg-emerald-100 text-emerald-600
                        dark:bg-emerald-500/10 dark:text-emerald-400"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                </div>
            </div>


            <!-- Selesai -->
            <div
                class="rounded-2xl border border-gray-200 bg-white p-5
                shadow-sm transition hover:-translate-y-0.5 hover:shadow-md
                dark:border-gray-800 dark:bg-gray-900"
            >
                <div class="flex items-center justify-between">

                    <div>
                        <p
                            class="text-sm font-medium text-gray-500
                            dark:text-gray-400"
                        >
                            Selesai
                        </p>

                        <p
                            class="mt-2 text-2xl font-bold text-gray-900
                            dark:text-white"
                        >
                            {{ pelatihanSelesai }}
                        </p>
                    </div>

                    <div
                        class="flex h-11 w-11 items-center justify-center
                        rounded-xl bg-blue-100 text-blue-600
                        dark:bg-blue-500/10 dark:text-blue-400"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>
                    </div>
                </div>
            </div>


            <!-- Nonaktif -->
            <div
                class="rounded-2xl border border-gray-200 bg-white p-5
                shadow-sm transition hover:-translate-y-0.5 hover:shadow-md
                dark:border-gray-800 dark:bg-gray-900"
            >
                <div class="flex items-center justify-between">

                    <div>
                        <p
                            class="text-sm font-medium text-gray-500
                            dark:text-gray-400"
                        >
                            Nonaktif
                        </p>

                        <p
                            class="mt-2 text-2xl font-bold text-gray-900
                            dark:text-white"
                        >
                            {{ pelatihanNonaktif }}
                        </p>
                    </div>

                    <div
                        class="flex h-11 w-11 items-center justify-center
                        rounded-xl bg-gray-100 text-gray-600
                        dark:bg-gray-700/50 dark:text-gray-300"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"
                            />
                        </svg>
                    </div>
                </div>
            </div>

        </div>


        <!-- ========================================== -->
        <!-- FILTER + TABLE -->
        <!-- ========================================== -->
        <div
            class="overflow-hidden rounded-2xl border border-gray-200
            bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900"
        >

            <!-- Toolbar -->
            <div
                class="border-b border-gray-200 p-4 dark:border-gray-800
                sm:p-5"
            >
                <div
                    class="flex flex-col gap-4 lg:flex-row lg:items-end
                    lg:justify-between"
                >

                    <div>
                        <h2
                            class="font-semibold text-gray-900
                            dark:text-white"
                        >
                            Daftar Pelatihan
                        </h2>

                        <p
                            class="mt-1 text-xs text-gray-500
                            dark:text-gray-400"
                        >
                            Menampilkan {{ filteredPelatihans.length }}
                            dari {{ totalPelatihan }} pelatihan
                        </p>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-3 sm:grid-cols-2
                        lg:min-w-[600px]"
                    >

                        <!-- Search -->
                        <div class="relative">
                            <div
                                class="pointer-events-none absolute inset-y-0
                                left-0 flex items-center pl-3.5"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-gray-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"
                                    />
                                </svg>
                            </div>

                            <input
                                v-model="search"
                                type="text"
                                placeholder="Cari pelatihan..."
                                class="w-full rounded-xl border border-gray-300
                                bg-gray-50 py-2.5 pl-10 pr-10 text-sm
                                text-gray-900 outline-none transition
                                placeholder:text-gray-400
                                focus:border-blue-500
                                focus:bg-white
                                focus:ring-2 focus:ring-blue-500/10
                                dark:border-gray-700 dark:bg-gray-800
                                dark:text-white dark:placeholder:text-gray-500
                                dark:focus:border-blue-500"
                            />

                            <button
                                v-if="search"
                                type="button"
                                @click="search = ''"
                                class="absolute inset-y-0 right-0 flex
                                items-center pr-3 text-gray-400
                                hover:text-gray-600
                                dark:hover:text-gray-200"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>


                        <!-- Status -->
                        <select
                            v-model="statusFilter"
                            class="w-full rounded-xl border border-gray-300
                            bg-gray-50 px-4 py-2.5 text-sm text-gray-900
                            outline-none transition
                            focus:border-blue-500
                            focus:bg-white
                            focus:ring-2 focus:ring-blue-500/10
                            dark:border-gray-700 dark:bg-gray-800
                            dark:text-white
                            dark:focus:border-blue-500"
                        >
                            <option value="">
                                Semua Status
                            </option>

                            <option value="aktif">
                                Aktif
                            </option>

                            <option value="nonaktif">
                                Nonaktif
                            </option>

                            <option value="selesai">
                                Selesai
                            </option>
                        </select>

                    </div>
                </div>

                <!-- Filter aktif -->
                <div
                    v-if="search || statusFilter"
                    class="mt-3 flex items-center justify-between"
                >
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Filter sedang diterapkan
                    </p>

                    <button
                        type="button"
                        @click="resetFilter"
                        class="text-xs font-semibold text-blue-600
                        hover:text-blue-700 dark:text-blue-400
                        dark:hover:text-blue-300"
                    >
                        Reset Filter
                    </button>
                </div>
            </div>


            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full min-w-[850px] text-left text-sm">

                    <!-- Head -->
                    <thead
                        class="bg-gray-50 text-xs uppercase tracking-wider
                        text-gray-500 dark:bg-gray-800/70
                        dark:text-gray-400"
                    >
                        <tr>

                            <th class="px-5 py-4 font-semibold">
                                No
                            </th>

                            <th class="px-5 py-4 font-semibold">
                                Nama Pelatihan
                            </th>

                            <th class="px-5 py-4 font-semibold">
                                Durasi
                            </th>

                            <th class="px-5 py-4 font-semibold">
                                Status
                            </th>

                            <th class="px-5 py-4 text-center font-semibold">
                                Aksi
                            </th>

                        </tr>
                    </thead>


                    <!-- Body -->
                    <tbody
                        class="divide-y divide-gray-100
                        dark:divide-gray-800"
                    >

                        <tr
                            v-for="(pelatihan, index) in filteredPelatihans"
                            :key="pelatihan.id"
                            class="transition hover:bg-gray-50
                            dark:hover:bg-gray-800/50"
                        >

                            <!-- No -->
                            <td class="px-5 py-4">
                                <span
                                    class="flex h-8 w-8 items-center
                                    justify-center rounded-lg bg-gray-100
                                    text-xs font-semibold text-gray-600
                                    dark:bg-gray-800 dark:text-gray-400"
                                >
                                    {{ index + 1 }}
                                </span>
                            </td>


                            <!-- Nama -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">

                                    <div
                                        class="flex h-10 w-10 shrink-0
                                        items-center justify-center rounded-xl
                                        bg-blue-100 text-blue-600
                                        dark:bg-blue-500/10
                                        dark:text-blue-400"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"
                                            />
                                        </svg>
                                    </div>

                                    <div class="min-w-0">
                                        <p
                                            class="font-semibold text-gray-900
                                            dark:text-white"
                                        >
                                            {{
                                                pelatihan.nama ??
                                                pelatihan.nama ??
                                                '-'
                                            }}
                                        </p>

                                        <p
                                            v-if="pelatihan.deskripsi"
                                            class="mt-1 max-w-lg truncate
                                            text-xs text-gray-500
                                            dark:text-gray-400"
                                        >
                                            {{ pelatihan.deskripsi }}
                                        </p>
                                    </div>

                                </div>
                            </td>


                            <!-- Durasi -->
                            <td class="px-5 py-4">
                                <div
                                    v-if="pelatihan.durasi"
                                    class="inline-flex items-center gap-2
                                    text-gray-700 dark:text-gray-300"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 text-gray-400"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>

                                    <span class="font-medium">
                                        {{ pelatihan.durasi }}
                                    </span>

                                    <span
                                        class="text-xs text-gray-400"
                                    >
                                        hari
                                    </span>
                                </div>

                                <span
                                    v-else
                                    class="text-gray-400"
                                >
                                    -
                                </span>
                            </td>


                            <!-- Status -->
                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex items-center gap-2
                                    rounded-full px-3 py-1.5 text-xs
                                    font-semibold capitalize"
                                    :class="
                                        statusClass(pelatihan.status)
                                    "
                                >
                                    <span
                                        class="h-1.5 w-1.5 rounded-full"
                                        :class="
                                            statusDot(
                                                pelatihan.status
                                            )
                                        "
                                    ></span>

                                    {{ pelatihan.status ?? '-' }}
                                </span>
                            </td>


                            <!-- Aksi -->
                            <td class="px-5 py-4">
                                <div
                                    class="flex items-center justify-center
                                    gap-1.5"
                                >

                                    <!-- Detail -->
                                    <Link
                                        :href="
                                            route(
                                                'pelatihans.show',
                                                pelatihan.id
                                            )
                                        "
                                        title="Detail"
                                        class="flex h-9 w-9 items-center
                                        justify-center rounded-lg
                                        bg-gray-100 text-gray-600
                                        transition hover:bg-gray-200
                                        hover:text-gray-900
                                        dark:bg-gray-800 dark:text-gray-400
                                        dark:hover:bg-gray-700
                                        dark:hover:text-white"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4.5 w-4.5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                            />
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                            />
                                        </svg>
                                    </Link>


                                    <!-- Edit -->
                                    <Link
                                        :href="
                                            route(
                                                'pelatihans.edit',
                                                pelatihan.id
                                            )
                                        "
                                        title="Edit"
                                        class="flex h-9 w-9 items-center
                                        justify-center rounded-lg
                                        bg-amber-100 text-amber-600
                                        transition hover:bg-amber-200
                                        dark:bg-amber-500/10
                                        dark:text-amber-400
                                        dark:hover:bg-amber-500/20"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4.5 w-4.5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.5-9.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"
                                            />
                                        </svg>
                                    </Link>


                                    <!-- Hapus -->
                                    <button
                                        type="button"
                                        title="Hapus"
                                        @click="
                                            hapusPelatihan(
                                                pelatihan
                                            )
                                        "
                                        class="flex h-9 w-9 items-center
                                        justify-center rounded-lg
                                        bg-red-100 text-red-600
                                        transition hover:bg-red-200
                                        dark:bg-red-500/10
                                        dark:text-red-400
                                        dark:hover:bg-red-500/20"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4.5 w-4.5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-8 0h10"
                                            />
                                        </svg>
                                    </button>

                                </div>
                            </td>

                        </tr>


                        <!-- Empty -->
                        <tr
                            v-if="
                                filteredPelatihans.length === 0
                            "
                        >
                            <td
                                colspan="5"
                                class="px-6 py-16"
                            >
                                <div
                                    class="flex flex-col items-center
                                    justify-center text-center"
                                >

                                    <div
                                        class="mb-4 flex h-16 w-16
                                        items-center justify-center
                                        rounded-2xl bg-gray-100
                                        text-gray-400
                                        dark:bg-gray-800
                                        dark:text-gray-500"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-8 w-8"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M9 13h6m-6 4h6m2 4H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414A1 1 0 0118 7.414V19a2 2 0 01-2 2z"
                                            />
                                        </svg>
                                    </div>

                                    <h3
                                        class="font-semibold text-gray-900
                                        dark:text-white"
                                    >
                                        Data pelatihan tidak ditemukan
                                    </h3>

                                    <p
                                        class="mt-1 max-w-sm text-sm
                                        text-gray-500 dark:text-gray-400"
                                    >
                                        {{
                                            search || statusFilter
                                                ? 'Coba ubah kata kunci atau filter yang digunakan.'
                                                : 'Belum ada data pelatihan yang tersedia.'
                                        }}
                                    </p>

                                    <button
                                        v-if="
                                            search ||
                                            statusFilter
                                        "
                                        type="button"
                                        @click="resetFilter"
                                        class="mt-4 rounded-lg bg-blue-600
                                        px-4 py-2 text-sm font-medium
                                        text-white transition
                                        hover:bg-blue-700"
                                    >
                                        Reset Filter
                                    </button>

                                    <Link
                                        v-else
                                        :href="
                                            route(
                                                'pelatihans.create'
                                            )
                                        "
                                        class="mt-4 rounded-lg
                                        bg-blue-600 px-4 py-2
                                        text-sm font-medium text-white
                                        transition hover:bg-blue-700"
                                    >
                                        Tambah Pelatihan
                                    </Link>

                                </div>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>


            <!-- Footer -->
            <div
                v-if="filteredPelatihans.length > 0"
                class="border-t border-gray-200 bg-gray-50 px-5 py-3
                dark:border-gray-800 dark:bg-gray-800/30"
            >
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Menampilkan
                    <span
                        class="font-semibold text-gray-700
                        dark:text-gray-300"
                    >
                        {{ filteredPelatihans.length }}
                    </span>
                    dari
                    <span
                        class="font-semibold text-gray-700
                        dark:text-gray-300"
                    >
                        {{ totalPelatihan }}
                    </span>
                    pelatihan
                </p>
            </div>

        </div>
    </div>
</template>
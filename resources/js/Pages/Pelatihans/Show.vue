```vue
<script setup>
import { Link } from '@inertiajs/vue3'
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

const formatRupiah = (value) => {
    if (value === null || value === undefined || value === '') {
        return '-'
    }

    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value)
}

const statusClass = (status) => {
    switch (status) {
        case 'aktif':
            return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'

        case 'nonaktif':
            return 'bg-gray-100 text-gray-700 dark:bg-gray-700/50 dark:text-gray-300'

        case 'selesai':
            return 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'

        default:
            return 'bg-gray-100 text-gray-700 dark:bg-gray-700/50 dark:text-gray-300'
    }
}
</script>

<template>
    <div class="min-h-full p-4 sm:p-6">

        <!-- Header -->
        <div
            class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <div
                    class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400"
                >
                    <Link
                        :href="route('pelatihans.index')"
                        class="hover:text-blue-600 dark:hover:text-blue-400"
                    >
                        Pelatihan
                    </Link>

                    <span>/</span>

                    <span class="text-gray-700 dark:text-gray-300">
                        Detail
                    </span>
                </div>

                <h1
                    class="mt-2 text-2xl font-bold text-gray-900 dark:text-white"
                >
                    Detail Pelatihan
                </h1>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Informasi lengkap mengenai pelatihan
                </p>
            </div>

            <div class="flex gap-2">
                <Link
                    :href="route('pelatihans.index')"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                >
                    Kembali
                </Link>

                <Link
                    :href="route('pelatihans.edit', props.pelatihan.id)"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700"
                >
                    Edit
                </Link>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            <!-- Main Information -->
            <div class="lg:col-span-2">
                <div
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
                >
                    <!-- Card Header -->
                    <div
                        class="border-b border-gray-200 px-6 py-5 dark:border-gray-700"
                    >
                        <div
                            class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"
                        >
                            <div>
                                <p
                                    class="text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500"
                                >
                                    Nama Pelatihan
                                </p>

                                <h2
                                    class="mt-1 text-xl font-bold text-gray-900 dark:text-white"
                                >
                                    {{ pelatihan.nama }}
                                </h2>
                            </div>

                            <span
                                class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-medium capitalize"
                                :class="statusClass(pelatihan.status)"
                            >
                                {{ pelatihan.status }}
                            </span>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="p-6">
                        <h3
                            class="mb-3 text-sm font-semibold text-gray-900 dark:text-white"
                        >
                            Deskripsi
                        </h3>

                        <div
                            v-if="pelatihan.deskripsi"
                            class="rounded-lg bg-gray-50 p-4 text-sm leading-7 text-gray-600 dark:bg-gray-700/50 dark:text-gray-300"
                        >
                            {{ pelatihan.deskripsi }}
                        </div>

                        <div
                            v-else
                            class="rounded-lg bg-gray-50 p-4 text-sm italic text-gray-400 dark:bg-gray-700/50 dark:text-gray-500"
                        >
                            Tidak ada deskripsi untuk pelatihan ini.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div>
                <div
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
                >
                    <div
                        class="border-b border-gray-200 px-6 py-5 dark:border-gray-700"
                    >
                        <h2
                            class="text-base font-semibold text-gray-900 dark:text-white"
                        >
                            Informasi Pelatihan
                        </h2>
                    </div>

                    <div class="divide-y divide-gray-100 dark:divide-gray-700">

                        <!-- Durasi -->
                        <div class="flex items-center justify-between px-6 py-5">
                            <div>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    Durasi
                                </p>

                                <p
                                    class="mt-1 font-semibold text-gray-900 dark:text-white"
                                >
                                    {{ pelatihan.durasi || '-' }}
                                    <span
                                        v-if="pelatihan.durasi"
                                        class="text-sm font-normal text-gray-500 dark:text-gray-400"
                                    >
                                        hari
                                    </span>
                                </p>
                            </div>

                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/30"
                            >
                                <svg
                                    class="h-5 w-5 text-blue-600 dark:text-blue-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4l3 2m6-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                    />
                                </svg>
                            </div>
                        </div>

                        <!-- Biaya -->
                        <!-- <div class="flex items-center justify-between px-6 py-5">
                            <div>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    Biaya
                                </p>

                                <p
                                    class="mt-1 font-semibold text-gray-900 dark:text-white"
                                >
                                    {{ formatRupiah(pelatihan.biaya) }}
                                </p>
                            </div>

                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 dark:bg-green-900/30"
                            >
                                <svg
                                    class="h-5 w-5 text-green-600 dark:text-green-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-10V5m0 14v-2m9-5a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                    />
                                </svg>
                            </div>
                        </div> -->

                        <!-- Status -->
                        <div class="flex items-center justify-between px-6 py-5">
                            <div>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    Status
                                </p>

                                <span
                                    class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-medium capitalize"
                                    :class="statusClass(pelatihan.status)"
                                >
                                    {{ pelatihan.status }}
                                </span>
                            </div>

                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-50 dark:bg-purple-900/30"
                            >
                                <svg
                                    class="h-5 w-5 text-purple-600 dark:text-purple-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0 1 12 2.944a11.955 11.955 0 0 1-8.618 3.04A12.02 12.02 0 0 0 3 9c0 5.591 3.824 10.29 9 11.622C17.176 19.29 21 14.591 21 9c0-1.044-.133-2.057-.382-3.016Z"
                                    />
                                </svg>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
```

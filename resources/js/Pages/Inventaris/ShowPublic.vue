<script setup>
import { Head } from '@inertiajs/vue3'

const props = defineProps({
    inventaris: {
        type: Object,
        required: true,
    },
})

const fotoUrl = (path) => {
    if (!path) {
        return null
    }

    return `/storage/${path}`
}

const fotoUtama = () => {
    const foto = props.inventaris.files?.find(
        (file) => file.jenis === 'foto'
    )

    return foto?.file_path
        ? fotoUrl(foto.file_path)
        : null
}

const kondisiLabel = (kondisi) => {
    const labels = {
        baik: 'Baik',
        rusak_ringan: 'Rusak Ringan',
        rusak_berat: 'Rusak Berat',
    }

    return labels[kondisi] ?? kondisi
}

const kondisiClass = (kondisi) => {
    const classes = {
        baik: 'bg-green-100 text-green-700',
        rusak_ringan: 'bg-yellow-100 text-yellow-700',
        rusak_berat: 'bg-red-100 text-red-700',
    }

    return classes[kondisi] ?? 'bg-gray-100 text-gray-700'
}
</script>

<template>
    <Head :title="inventaris.nama" />

    <div class="min-h-screen bg-gray-100 px-4 py-8 dark:bg-gray-950">
        <div class="mx-auto max-w-2xl">

            <!-- HEADER -->
            <div class="mb-6 text-center">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Inventaris BLKK
                </h1>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Informasi aset
                </p>
            </div>

            <!-- CARD -->
            <div
                class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900"
            >

                <!-- FOTO -->
                <div class="bg-gray-100 dark:bg-gray-800">
                    <img
                        v-if="fotoUtama()"
                        :src="fotoUtama()"
                        :alt="inventaris.nama"
                        class="h-72 w-full object-cover"
                    />

                    <div
                        v-else
                        class="flex h-72 items-center justify-center text-6xl"
                    >
                        📦
                    </div>
                </div>

                <!-- CONTENT -->
                <div class="p-6">

                    <!-- NAMA -->
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Nama Aset
                        </p>

                        <h2
                            class="mt-1 text-2xl font-bold text-gray-900 dark:text-white"
                        >
                            {{ inventaris.nama }}
                        </h2>
                    </div>

                    <!-- BARCODE -->
                    <div class="mt-5">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Kode Aset
                        </p>

                        <p
                            class="mt-1 font-mono text-lg font-semibold text-indigo-600 dark:text-indigo-400"
                        >
                            {{ inventaris.barcode }}
                        </p>
                    </div>

                    <!-- INFO -->
                    <div
                        class="mt-6 grid grid-cols-2 gap-4"
                    >
                        <div
                            class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800"
                        >
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Kategori
                            </p>

                            <p class="mt-1 font-medium text-gray-900 dark:text-white">
                                {{ inventaris.kategori?.nama ?? '-' }}
                            </p>
                        </div>

                        <div
                            class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800"
                        >
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Jumlah
                            </p>

                            <p class="mt-1 font-medium text-gray-900 dark:text-white">
                                {{ inventaris.jumlah }}
                                {{ inventaris.satuan }}
                            </p>
                        </div>

                        <div
                            class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800"
                        >
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Kondisi
                            </p>

                            <p class="mt-2">
                                <span
                                    class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                    :class="kondisiClass(inventaris.kondisi)"
                                >
                                    {{ kondisiLabel(inventaris.kondisi) }}
                                </span>
                            </p>
                        </div>

                        <div
                            class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800"
                        >
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Lokasi
                            </p>

                            <p class="mt-1 font-medium text-gray-900 dark:text-white">
                                {{ inventaris.lokasi ?? '-' }}
                            </p>
                        </div>

                        <div
                            class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800"
                        >
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Tahun Perolehan
                            </p>

                            <p class="mt-1 font-medium text-gray-900 dark:text-white">
                                {{ inventaris.tahun_perolehan ?? '-' }}
                            </p>
                        </div>

                        <div
                            class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800"
                        >
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Satuan
                            </p>

                            <p class="mt-1 font-medium text-gray-900 dark:text-white">
                                {{ inventaris.satuan }}
                            </p>
                        </div>
                    </div>

                    <!-- KETERANGAN -->
                    <div class="mt-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Keterangan
                        </p>

                        <p
                            class="mt-2 text-sm leading-6 text-gray-700 dark:text-gray-300"
                        >
                            {{ inventaris.keterangan || 'Tidak ada keterangan.' }}
                        </p>
                    </div>

                </div>
            </div>

            <!-- FOOTER -->
            <p class="mt-6 text-center text-xs text-gray-400 dark:text-gray-500">
                Sistem Inventaris BLKK
            </p>

        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import QrcodeVue from 'qrcode.vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    inventaris: {
        type: Object,
        required: true,
    },
})

const inventaris = props.inventaris

/*
|--------------------------------------------------------------------------
| QR CODE / PUBLIC URL
|--------------------------------------------------------------------------
*/


const publicUrl = computed(() => {
    if (!inventaris?.barcode) {
        return ''
    }

    return route(
        'inventaris.public',
        inventaris.barcode
    )
})


const downloadQr = () => {
    if (!publicUrl.value || !inventaris?.barcode) {
        return
    }

    const svg = document.querySelector(
        '.qr-code-container svg'
    )

    if (!svg) {
        alert('QR Code belum siap untuk didownload.')
        return
    }

    const serializer = new XMLSerializer()

    const svgData = serializer.serializeToString(svg)

    const svgBlob = new Blob(
        [svgData],
        {
            type: 'image/svg+xml;charset=utf-8',
        }
    )

    const url = URL.createObjectURL(svgBlob)

    const image = new Image()

    image.onload = () => {
        const canvas = document.createElement('canvas')

        // Ukuran PNG
        canvas.width = 1000
        canvas.height = 1000

        const context = canvas.getContext('2d')

        if (!context) {
            URL.revokeObjectURL(url)
            return
        }

        // Background putih
        context.fillStyle = '#ffffff'
        context.fillRect(
            0,
            0,
            canvas.width,
            canvas.height
        )

        // QR Code
        context.drawImage(
            image,
            50,
            50,
            900,
            900
        )

        canvas.toBlob(
            (blob) => {
                if (!blob) {
                    URL.revokeObjectURL(url)
                    return
                }

                const downloadUrl =
                    URL.createObjectURL(blob)

                const link =
                    document.createElement('a')

                link.href = downloadUrl

                link.download =
                    `${inventaris.barcode}.png`

                document.body.appendChild(link)
                link.click()
                document.body.removeChild(link)

                URL.revokeObjectURL(downloadUrl)
            },
            'image/png'
        )

        URL.revokeObjectURL(url)
    }

    image.src = url
}

/*
|--------------------------------------------------------------------------
| FILE
|--------------------------------------------------------------------------
*/

const fileUrl = (path) => {
    if (!path) {
        return '#'
    }

    return `/storage/${path}`
}

/*
|--------------------------------------------------------------------------
| FILTER FILE
|--------------------------------------------------------------------------
*/

const fotoFiles = computed(() => {
    return (inventaris.files ?? []).filter(
        (file) => file.jenis === 'foto'
    )
})

const notaFiles = computed(() => {
    return (inventaris.files ?? []).filter(
        (file) => file.jenis === 'nota'
    )
})

const dokumenFiles = computed(() => {
    return (inventaris.files ?? []).filter(
        (file) => file.jenis === 'dokumen'
    )
})

/*
|--------------------------------------------------------------------------
| IMAGE CHECK
|--------------------------------------------------------------------------
*/

const isImage = (path) => {
    if (!path) {
        return false
    }

    return /\.(jpg|jpeg|png|webp)$/i.test(path)
}

/*
|--------------------------------------------------------------------------
| FORMAT RUPIAH
|--------------------------------------------------------------------------
*/

const formatRupiah = (value) => {
    if (!value) {
        return '-'
    }

    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(Number(value))
}

/*
|--------------------------------------------------------------------------
| KONDISI
|--------------------------------------------------------------------------
*/

const formatKondisi = (kondisi) => {
    const labels = {
        baik: 'Baik',
        rusak_ringan: 'Rusak Ringan',
        rusak_berat: 'Rusak Berat',
    }

    return labels[kondisi] ?? kondisi
}

const kondisiClass = (kondisi) => {
    const classes = {
        baik: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        rusak_ringan: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
        rusak_berat: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
    }

    return classes[kondisi] ??
        'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
}
</script>

<template>
    <Head :title="`Detail ${inventaris.nama}`" />

    <div class="space-y-6">

        <!-- ====================================================== -->
        <!-- HEADER -->
        <!-- ====================================================== -->

        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <div
                    class="mb-2 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400"
                >
                    <Link
                        :href="route('inventaris.index')"
                        class="transition hover:text-indigo-600 dark:hover:text-indigo-400"
                    >
                        Inventaris
                    </Link>

                    <span>/</span>

                    <span class="text-gray-900 dark:text-white">
                        Detail
                    </span>
                </div>

                <h1
                    class="text-2xl font-bold text-gray-900 dark:text-white"
                >
                    {{ inventaris.nama }}
                </h1>

                <p
                    class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                >
                    Kode inventaris: {{ inventaris.kode }}
                </p>
            </div>

            <div class="flex gap-2">
                <Link
                    :href="route('inventaris.index')"
                    class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    ← Kembali
                </Link>

                <Link
                    :href="route('inventaris.edit', inventaris.id)"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
                >
                    ✏️ Edit
                </Link>
            </div>
        </div>

        <!-- ====================================================== -->
        <!-- INFORMASI UTAMA -->
        <!-- ====================================================== -->

        <div class="grid gap-6 lg:grid-cols-3">

            <!-- FOTO UTAMA -->
            <div
                class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 lg:col-span-1"
            >
                <div
                    v-if="fotoFiles.length"
                    class="bg-gray-100 dark:bg-gray-900"
                >
                    <a
                        v-for="(file, index) in fotoFiles"
                        :key="file.id ?? index"
                        :href="fileUrl(file.file_path)"
                        target="_blank"
                        class="block"
                    >
                        <img
                            :src="fileUrl(file.file_path)"
                            :alt="file.nama_file"
                            class="max-h-[420px] w-full object-contain"
                        />
                    </a>
                </div>

                <div
                    v-else
                    class="flex h-[350px] items-center justify-center bg-gray-100 dark:bg-gray-900"
                >
                    <div class="text-center">
                        <div class="text-6xl">
                            📦
                        </div>

                        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                            Belum ada foto aset
                        </p>
                    </div>
                </div>

                <div
                    class="border-t border-gray-200 p-5 dark:border-gray-700"
                >
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                        Nama Aset
                    </p>

                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                        {{ inventaris.nama }}
                    </p>
                </div>
            </div>

            <!-- INFORMASI -->
            <div
                class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 lg:col-span-2"
            >
                <div
                    class="border-b border-gray-200 px-6 py-5 dark:border-gray-700"
                >
                    <h2 class="font-semibold text-gray-900 dark:text-white">
                        Informasi Inventaris
                    </h2>
                </div>

                <div class="grid gap-6 p-6 sm:grid-cols-2">

                    <!-- KODE -->
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                            Kode
                        </p>

                        <p class="mt-1 font-medium text-gray-900 dark:text-white">
                            {{ inventaris.kode || '-' }}
                        </p>
                    </div>

                    <!-- BARCODE -->


                    <!-- KATEGORI -->
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                            Kategori
                        </p>

                        <p class="mt-1 font-medium text-gray-900 dark:text-white">
                            {{ inventaris.kategori?.nama ?? '-' }}
                        </p>
                    </div>

                    <!-- JUMLAH -->
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                            Jumlah
                        </p>

                        <p class="mt-1 font-medium text-gray-900 dark:text-white">
                            {{ inventaris.jumlah ?? 0 }}
                            {{ inventaris.satuan ?? '' }}
                        </p>
                    </div>

                    <!-- KONDISI -->
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                            Kondisi
                        </p>

                        <span
                            class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-medium"
                            :class="kondisiClass(inventaris.kondisi)"
                        >
                            {{ formatKondisi(inventaris.kondisi) }}
                        </span>
                    </div>

                    <!-- LOKASI -->
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                            Lokasi
                        </p>

                        <p class="mt-1 font-medium text-gray-900 dark:text-white">
                            {{ inventaris.lokasi || '-' }}
                        </p>
                    </div>

                    <!-- TAHUN -->
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                            Tahun Perolehan
                        </p>

                        <p class="mt-1 font-medium text-gray-900 dark:text-white">
                            {{ inventaris.tahun_perolehan || '-' }}
                        </p>
                    </div>

                    <!-- HARGA -->
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                            Harga Perolehan
                        </p>

                        <p class="mt-1 font-medium text-gray-900 dark:text-white">
                            {{ formatRupiah(inventaris.harga_perolehan) }}
                        </p>
                    </div>

                    <!-- SUMBER DANA -->
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                            Sumber Dana
                        </p>

                        <p class="mt-1 font-medium text-gray-900 dark:text-white">
                            {{ inventaris.sumber_dana || '-' }}
                        </p>
                    </div>

                    <!-- KETERANGAN -->
                    <div class="sm:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                            Keterangan
                        </p>

                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-600 dark:text-gray-300">
                            {{ inventaris.keterangan || 'Tidak ada keterangan.' }}
                        </p>
                    </div>

                </div>
            </div>

        </div>

        <!-- ====================================================== -->
        <!-- QR CODE -->
        <!-- ====================================================== -->


       <div
                        class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
                    >
                        <div
                            class="border-b border-gray-200 px-6 py-5 dark:border-gray-700"
                        >
                            <h2 class="font-semibold text-gray-900 dark:text-white">
                                QR Code Aset
                            </h2>

                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Scan QR Code untuk membuka informasi publik aset.
                            </p>
                        </div>

                        <div class="p-6">
                            <div
                                v-if="publicUrl"
                                class="flex flex-col items-center"
                            >
                                <div
                                    class="qr-code-container rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-300"
                                >
                                    <QrcodeVue
                                        :value="publicUrl"
                                        :size="220"
                                        level="H"
                                        render-as="svg"
                                    />
                                </div>

                                <p
                                    class="mt-4 font-mono text-sm font-semibold text-indigo-600 dark:text-indigo-400"
                                >
                                    {{ inventaris.barcode }}
                                </p>

                                <p
                                    class="mt-2 break-all text-center text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ publicUrl }}
                                </p>

                                <!-- BUTTON -->
                                <div class="mt-4 flex flex-wrap justify-center gap-2">
                                    <a
                                        :href="publicUrl"
                                        target="_blank"
                                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
                                    >
                                        🔗 Buka Halaman Publik
                                    </a>

                                    <button
                                        type="button"
                                        @click="downloadQr"
                                        class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                                    >
                                        ⬇️ Download QR
                                    </button>
                                </div>
                            </div>

                            <div
                                v-else
                                class="rounded-xl border border-dashed border-gray-300 py-10 text-center dark:border-gray-600"
                            >
                                <div class="text-4xl">
                                    ▦
                                </div>

                                <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                                    Barcode belum tersedia.
                                </p>
                            </div>
                        </div>
        </div>

        <!-- ====================================================== -->
        <!-- NOTA -->
        <!-- ====================================================== -->

        <div
            class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >
            <div
                class="border-b border-gray-200 px-6 py-5 dark:border-gray-700"
            >
                <h2 class="font-semibold text-gray-900 dark:text-white">
                    Nota Pembelian
                </h2>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Foto atau PDF nota yang tersimpan.
                </p>
            </div>

            <div class="p-6">

                <div
                    v-if="notaFiles.length"
                    class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <div
                        v-for="(file, index) in notaFiles"
                        :key="file.id ?? index"
                        class="overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700"
                    >

                        <!-- FOTO NOTA -->
                        <div
                            v-if="isImage(file.file_path)"
                            class="aspect-[4/3] bg-gray-100 dark:bg-gray-900"
                        >
                            <a
                                :href="fileUrl(file.file_path)"
                                target="_blank"
                            >
                                <img
                                    :src="fileUrl(file.file_path)"
                                    :alt="file.nama_file"
                                    class="h-full w-full object-cover"
                                />
                            </a>
                        </div>

                        <!-- PDF -->
                        <div
                            v-else
                            class="flex aspect-[4/3] items-center justify-center bg-gray-50 dark:bg-gray-900"
                        >
                            <div class="text-center">
                                <div class="text-5xl">
                                    📄
                                </div>

                                <p class="mt-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    PDF Nota
                                </p>
                            </div>
                        </div>

                        <div
                            class="border-t border-gray-200 p-4 dark:border-gray-700"
                        >
                            <p class="truncate text-sm font-medium text-gray-900 dark:text-white">
                                {{ file.nama_file }}
                            </p>

                            <a
                                :href="fileUrl(file.file_path)"
                                target="_blank"
                                class="mt-3 inline-flex text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400"
                            >
                                Buka Nota →
                            </a>
                        </div>

                    </div>
                </div>

                <div
                    v-else
                    class="rounded-xl border border-dashed border-gray-300 py-10 text-center dark:border-gray-600"
                >
                    <div class="text-4xl">
                        📄
                    </div>

                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                        Belum ada nota.
                    </p>
                </div>

            </div>
        </div>

        <!-- ====================================================== -->
        <!-- DOKUMEN -->
        <!-- ====================================================== -->

        <div
            class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >
            <div
                class="border-b border-gray-200 px-6 py-5 dark:border-gray-700"
            >
                <h2 class="font-semibold text-gray-900 dark:text-white">
                    Dokumen Pendukung
                </h2>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Dokumen pendukung inventaris.
                </p>
            </div>

            <div class="p-6">

                <div
                    v-if="dokumenFiles.length"
                    class="space-y-3"
                >
                    <a
                        v-for="(file, index) in dokumenFiles"
                        :key="file.id ?? index"
                        :href="fileUrl(file.file_path)"
                        target="_blank"
                        class="flex items-center gap-4 rounded-xl border border-gray-200 p-4 transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/50"
                    >
                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-xl dark:bg-blue-900/20"
                        >
                            📄
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-gray-900 dark:text-white">
                                {{ file.nama_file }}
                            </p>

                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Dokumen
                            </p>
                        </div>

                        <span
                            class="shrink-0 text-sm font-medium text-indigo-600 dark:text-indigo-400"
                        >
                            Buka →
                        </span>
                    </a>
                </div>

                <div
                    v-else
                    class="rounded-xl border border-dashed border-gray-300 py-10 text-center dark:border-gray-600"
                >
                    <div class="text-4xl">
                        📄
                    </div>

                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                        Belum ada dokumen pendukung.
                    </p>
                </div>

            </div>
        </div>

        <!-- ====================================================== -->
        <!-- RIWAYAT KONDISI -->
        <!-- ====================================================== -->

        <div
            class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >
            <div
                class="border-b border-gray-200 px-6 py-5 dark:border-gray-700"
            >
                <h2 class="font-semibold text-gray-900 dark:text-white">
                    Riwayat Kondisi
                </h2>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Riwayat perubahan kondisi aset.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Tanggal
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Kondisi
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Keterangan
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Biaya Perbaikan
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                User
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr
                            v-for="(item, index) in inventaris.riwayat"
                            :key="item.id ?? index"
                            class="transition hover:bg-gray-50 dark:hover:bg-gray-700/30"
                        >
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                {{ item.tanggal }}
                            </td>

                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                                    :class="kondisiClass(item.kondisi)"
                                >
                                    {{ formatKondisi(item.kondisi) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                {{ item.keterangan || '-' }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                {{ formatRupiah(item.biaya_perbaikan) }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                {{ item.user?.name || '-' }}
                            </td>
                        </tr>

                        <tr v-if="!inventaris.riwayat?.length">
                            <td
                                colspan="5"
                                class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400"
                            >
                                Belum ada riwayat kondisi.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</template>

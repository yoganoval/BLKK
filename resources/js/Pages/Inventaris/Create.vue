<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed, nextTick, onBeforeUnmount, ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    kategori: {
        type: Array,
        default: () => [],
    },
})

/*
|--------------------------------------------------------------------------
| FORM
|--------------------------------------------------------------------------
*/

const form = useForm({
    kode: '',
    nama: '',
    kategori_id: '',
    jumlah: 1,
    satuan: 'Unit',
    kondisi: 'baik',
    lokasi: '',
    tahun_perolehan: '',
    harga_perolehan: '',
    sumber_dana: '',
    keterangan: '',

    pengadaan: {
        tanggal: '',
        jumlah: 1,
        harga_satuan: '',
        total_harga: '',
        sumber_dana: '',
        nomor_nota: '',
        keterangan: '',
    },

    files: [],
})

/*
|--------------------------------------------------------------------------
| STATE FILE
|--------------------------------------------------------------------------
*/

const fotoBaru = ref([])
const notaBaru = ref([])

/*
|--------------------------------------------------------------------------
| STATE KAMERA
|--------------------------------------------------------------------------
*/

const cameraOpen = ref(false)
const cameraMode = ref('foto')

const video = ref(null)
const cameraStream = ref(null)

/*
|--------------------------------------------------------------------------
| OPTIONS
|--------------------------------------------------------------------------
*/

const satuanOptions = [
    'Unit',
    'Buah',
    'Set',
    'Paket',
]

const sumberDanaOptions = [
    'Pembelian',
    'Hibah',
    'Bantuan',
    'APBD',
    'APBN',
    'Lainnya',
]

const kondisiOptions = [
    {
        value: 'baik',
        label: 'Baik',
    },
    {
        value: 'rusak_ringan',
        label: 'Rusak Ringan',
    },
    {
        value: 'rusak_berat',
        label: 'Rusak Berat',
    },
]

/*
|--------------------------------------------------------------------------
| FORMAT
|--------------------------------------------------------------------------
*/

const formatRupiah = (value) => {
    if (!value) {
        return 'Rp0'
    }

    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(Number(value))
}

/*
|--------------------------------------------------------------------------
| HITUNG TOTAL PENGADAAN
|--------------------------------------------------------------------------
*/

const hitungTotal = () => {
    const jumlah = Number(form.pengadaan.jumlah || 0)
    const harga = Number(form.pengadaan.harga_satuan || 0)

    form.pengadaan.total_harga = jumlah * harga
}

const totalHargaText = computed(() => {
    return formatRupiah(form.pengadaan.total_harga)
})

/*
|--------------------------------------------------------------------------
| PREVIEW
|--------------------------------------------------------------------------
*/

const createPreview = (file) => {
    if (!file?.type?.startsWith('image/')) {
        return null
    }

    return URL.createObjectURL(file)
}

/*
|--------------------------------------------------------------------------
| TAMBAH FOTO
|--------------------------------------------------------------------------
*/

const tambahFoto = (file) => {
    if (!file) {
        return
    }

    if (!file.type.startsWith('image/')) {
        return
    }

    const item = {
        file,
        jenis: 'foto',
        keterangan: '',
        preview: createPreview(file),
    }

    fotoBaru.value.push(item)
    form.files.push(item)
}

/*
|--------------------------------------------------------------------------
| UPLOAD FOTO
|--------------------------------------------------------------------------
*/

const handleFotoFiles = (event) => {
    const files = Array.from(event.target.files || [])

    files.forEach((file) => {
        tambahFoto(file)
    })

    event.target.value = ''
}

/*
|--------------------------------------------------------------------------
| TAMBAH NOTA
|--------------------------------------------------------------------------
*/

const tambahNota = (file) => {
    if (!file) {
        return
    }

    const allowedTypes = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/webp',
    ]

    if (!allowedTypes.includes(file.type)) {
        return
    }

    const item = {
        file,
        jenis: 'nota',
        keterangan: '',
        preview: file.type.startsWith('image/')
            ? createPreview(file)
            : null,
    }

    notaBaru.value.push(item)
    form.files.push(item)
}

/*
|--------------------------------------------------------------------------
| UPLOAD NOTA
|--------------------------------------------------------------------------
*/

const handleNotaFiles = (event) => {
    const files = Array.from(event.target.files || [])

    files.forEach((file) => {
        tambahNota(file)
    })

    event.target.value = ''
}

/*
|--------------------------------------------------------------------------
| PREVIEW FILE
|--------------------------------------------------------------------------
*/

const getPreview = (item) => {
    return item?.preview ?? null
}

/*
|--------------------------------------------------------------------------
| HAPUS FOTO
|--------------------------------------------------------------------------
*/

const removeFoto = (index) => {
    const item = fotoBaru.value[index]

    if (!item) {
        return
    }

    const formIndex = form.files.findIndex(
        (file) => file.file === item.file
    )

    if (formIndex !== -1) {
        form.files.splice(formIndex, 1)
    }

    if (item.preview) {
        URL.revokeObjectURL(item.preview)
    }

    fotoBaru.value.splice(index, 1)
}

/*
|--------------------------------------------------------------------------
| HAPUS NOTA
|--------------------------------------------------------------------------
*/

const removeNota = (index) => {
    const item = notaBaru.value[index]

    if (!item) {
        return
    }

    const formIndex = form.files.findIndex(
        (file) => file.file === item.file
    )

    if (formIndex !== -1) {
        form.files.splice(formIndex, 1)
    }

    if (item.preview) {
        URL.revokeObjectURL(item.preview)
    }

    notaBaru.value.splice(index, 1)
}

/*
|--------------------------------------------------------------------------
| BUKA KAMERA
|--------------------------------------------------------------------------
*/

const bukaKamera = async (mode = 'foto') => {
    cameraMode.value = mode
    cameraOpen.value = true

    await nextTick()

    try {
        if (!navigator.mediaDevices?.getUserMedia) {
            throw new Error('Browser tidak mendukung kamera.')
        }

        cameraStream.value =
            await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: {
                        ideal: 'environment',
                    },
                    width: {
                        ideal: 1280,
                    },
                    height: {
                        ideal: 720,
                    },
                },
                audio: false,
            })

        if (video.value) {
            video.value.srcObject = cameraStream.value

            await video.value.play()
        }
    } catch (error) {
        console.error('Kamera gagal dibuka:', error)

        cameraOpen.value = false

        stopCamera()

        alert(
            'Kamera tidak dapat dibuka. Pastikan browser memiliki izin menggunakan kamera.'
        )
    }
}

/*
|--------------------------------------------------------------------------
| STOP KAMERA
|--------------------------------------------------------------------------
*/

const stopCamera = () => {
    if (cameraStream.value) {
        cameraStream.value
            .getTracks()
            .forEach((track) => track.stop())

        cameraStream.value = null
    }

    if (video.value) {
        video.value.srcObject = null
    }
}

/*
|--------------------------------------------------------------------------
| TUTUP KAMERA
|--------------------------------------------------------------------------
*/

const tutupKamera = () => {
    stopCamera()
    cameraOpen.value = false
}

/*
|--------------------------------------------------------------------------
| AMBIL FOTO DARI KAMERA
|--------------------------------------------------------------------------
*/

const ambilFoto = () => {
    if (!video.value) {
        return
    }

    if (
        !video.value.videoWidth ||
        !video.value.videoHeight
    ) {
        alert('Kamera belum siap. Tunggu sebentar.')
        return
    }

    const canvas = document.createElement('canvas')

    canvas.width = video.value.videoWidth
    canvas.height = video.value.videoHeight

    const context = canvas.getContext('2d')

    if (!context) {
        return
    }

    context.drawImage(
        video.value,
        0,
        0,
        canvas.width,
        canvas.height
    )

    canvas.toBlob(
        (blob) => {
            if (!blob) {
                return
            }

            const fileName =
                cameraMode.value === 'foto'
                    ? `foto-aset-${Date.now()}.jpg`
                    : `nota-${Date.now()}.jpg`

            const file = new File(
                [blob],
                fileName,
                {
                    type: 'image/jpeg',
                }
            )

            if (cameraMode.value === 'foto') {
                tambahFoto(file)
            } else {
                tambahNota(file)
            }

            tutupKamera()
        },
        'image/jpeg',
        0.9
    )
}

/*
|--------------------------------------------------------------------------
| SUBMIT
|--------------------------------------------------------------------------
*/

const submit = () => {
    form.post(route('inventaris.store'), {
        forceFormData: true,
        preserveScroll: true,
    })
}

/*
|--------------------------------------------------------------------------
| CLEANUP
|--------------------------------------------------------------------------
*/

onBeforeUnmount(() => {
    stopCamera()

    fotoBaru.value.forEach((item) => {
        if (item.preview) {
            URL.revokeObjectURL(item.preview)
        }
    })

    notaBaru.value.forEach((item) => {
        if (item.preview) {
            URL.revokeObjectURL(item.preview)
        }
    })
})
</script>

<template>
    <Head title="Tambah Inventaris" />

    <div class="space-y-6">

        <!-- HEADER -->
        <div>
            <div
                class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400"
            >
                <Link
                    :href="route('inventaris.index')"
                    class="transition hover:text-indigo-600 dark:hover:text-indigo-400"
                >
                    Inventaris
                </Link>

                <span>/</span>

                <span class="text-gray-900 dark:text-white">
                    Tambah
                </span>
            </div>

            <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">
                Tambah Inventaris
            </h1>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Tambahkan data aset, pengadaan, foto, dan dokumen.
            </p>
        </div>

        <!-- FORM -->
        <form
            @submit.prevent="submit"
            class="space-y-6"
        >

            <!-- ====================================================== -->
            <!-- INFORMASI ASET -->
            <!-- ====================================================== -->

            <div
                class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
            >
                <div
                    class="border-b border-gray-200 px-6 py-5 dark:border-gray-700"
                >
                    <h2 class="font-semibold text-gray-900 dark:text-white">
                        Informasi Aset
                    </h2>

                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Informasi utama barang atau aset BLKK.
                    </p>
                </div>

                <div class="grid gap-5 p-6 md:grid-cols-2">

                    <!-- KODE -->
                    <div>
                        <label class="label">
                            Kode Inventaris
                        </label>

                        <input
                            v-model="form.kode"
                            type="text"
                            placeholder="Contoh: INV-001"
                            class="input"
                        />

                        <p
                            v-if="form.errors.kode"
                            class="error"
                        >
                            {{ form.errors.kode }}
                        </p>
                    </div>

                    <!-- NAMA -->
                    <div>
                        <label class="label">
                            Nama Aset
                        </label>

                        <input
                            v-model="form.nama"
                            type="text"
                            placeholder="Contoh: Kamera Canon"
                            class="input"
                        />

                        <p
                            v-if="form.errors.nama"
                            class="error"
                        >
                            {{ form.errors.nama }}
                        </p>
                    </div>

                    <!-- KATEGORI -->
                    <div>
                        <label class="label">
                            Kategori
                        </label>

                        <select
                            v-model="form.kategori_id"
                            class="input"
                        >
                            <option value="">
                                Pilih kategori
                            </option>

                            <option
                                v-for="item in props.kategori"
                                :key="item.id"
                                :value="item.id"
                            >
                                {{ item.nama }}
                            </option>
                        </select>

                        <p
                            v-if="form.errors.kategori_id"
                            class="error"
                        >
                            {{ form.errors.kategori_id }}
                        </p>
                    </div>

                    <!-- JUMLAH + SATUAN -->
                    <div class="grid grid-cols-2 gap-3">

                        <div>
                            <label class="label">
                                Jumlah
                            </label>

                            <input
                                v-model="form.jumlah"
                                type="number"
                                min="1"
                                class="input"
                            />
                        </div>

                        <div>
                            <label class="label">
                                Satuan
                            </label>

                            <select
                                v-model="form.satuan"
                                class="input"
                            >
                                <option
                                    v-for="satuan in satuanOptions"
                                    :key="satuan"
                                    :value="satuan"
                                >
                                    {{ satuan }}
                                </option>
                            </select>
                        </div>

                    </div>

                    <!-- KONDISI -->
                    <div>
                        <label class="label">
                            Kondisi
                        </label>

                        <select
                            v-model="form.kondisi"
                            class="input"
                        >
                            <option
                                v-for="item in kondisiOptions"
                                :key="item.value"
                                :value="item.value"
                            >
                                {{ item.label }}
                            </option>
                        </select>
                    </div>

                    <!-- LOKASI -->
                    <div>
                        <label class="label">
                            Lokasi
                        </label>

                        <input
                            v-model="form.lokasi"
                            type="text"
                            placeholder="Contoh: Studio Multimedia"
                            class="input"
                        />
                    </div>

                    <!-- TAHUN -->
                    <div>
                        <label class="label">
                            Tahun Perolehan
                        </label>

                        <input
                            v-model="form.tahun_perolehan"
                            type="number"
                            min="1900"
                            :max="new Date().getFullYear()"
                            placeholder="2025"
                            class="input"
                        />

                        <p
                            v-if="form.errors.tahun_perolehan"
                            class="error"
                        >
                            {{ form.errors.tahun_perolehan }}
                        </p>
                    </div>

                    <!-- HARGA -->
                    <div>
                        <label class="label">
                            Harga Perolehan
                        </label>

                        <input
                            v-model="form.harga_perolehan"
                            type="number"
                            min="0"
                            placeholder="15000000"
                            class="input"
                        />

                        <p
                            v-if="form.errors.harga_perolehan"
                            class="error"
                        >
                            {{ form.errors.harga_perolehan }}
                        </p>
                    </div>

                    <!-- SUMBER DANA -->
                    <div class="md:col-span-2">
                        <label class="label">
                            Sumber Dana
                        </label>

                        <select
                            v-model="form.sumber_dana"
                            class="input"
                        >
                            <option value="">
                                Pilih sumber dana
                            </option>

                            <option
                                v-for="item in sumberDanaOptions"
                                :key="item"
                                :value="item"
                            >
                                {{ item }}
                            </option>
                        </select>
                    </div>

                    <!-- KETERANGAN -->
                    <div class="md:col-span-2">
                        <label class="label">
                            Keterangan
                        </label>

                        <textarea
                            v-model="form.keterangan"
                            rows="4"
                            placeholder="Keterangan tambahan..."
                            class="input"
                        ></textarea>
                    </div>

                </div>
            </div>

            <!-- ====================================================== -->
            <!-- FOTO & DOKUMEN -->
            <!-- ====================================================== -->

            <div class="grid gap-6 lg:grid-cols-2">

                <!-- ================================================== -->
                <!-- FOTO ASET -->
                <!-- ================================================== -->

                <div
                    class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
                >
                    <div
                        class="border-b border-gray-200 px-6 py-5 dark:border-gray-700"
                    >
                        <h2 class="font-semibold text-gray-900 dark:text-white">
                            Foto Aset
                        </h2>

                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Upload foto atau ambil foto langsung dari kamera.
                        </p>
                    </div>

                    <div class="p-6">

                        <!-- PILIHAN -->
                        <div class="grid gap-3 sm:grid-cols-2">

                            <!-- UPLOAD -->
                            <label
                                class="flex cursor-pointer items-center justify-center gap-3 rounded-xl border-2 border-dashed border-gray-300 px-4 py-4 transition hover:border-indigo-500 hover:bg-indigo-50/50 dark:border-gray-600 dark:hover:bg-indigo-900/10"
                            >
                                <span class="text-2xl">
                                    📁
                                </span>

                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        Upload File
                                    </p>

                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        JPG, PNG, WEBP
                                    </p>
                                </div>

                                <input
                                    type="file"
                                    multiple
                                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                    class="hidden"
                                    @change="handleFotoFiles"
                                />
                            </label>

                            <!-- KAMERA -->
                            <button
                                type="button"
                                @click="bukaKamera('foto')"
                                class="flex items-center justify-center gap-3 rounded-xl border-2 border-dashed border-indigo-300 px-4 py-4 transition hover:border-indigo-500 hover:bg-indigo-50 dark:border-indigo-700 dark:hover:bg-indigo-900/20"
                            >
                                <span class="text-2xl">
                                    📷
                                </span>

                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        Buka Kamera
                                    </p>

                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Ambil foto langsung
                                    </p>
                                </div>
                            </button>

                        </div>

                        <!-- FOTO LIST -->
                        <div
                            v-if="fotoBaru.length > 0"
                            class="mt-6 space-y-3"
                        >
                            <div
                                v-for="(item, index) in fotoBaru"
                                :key="`${item.file.name}-${index}`"
                                class="flex items-center gap-4 rounded-xl border border-gray-200 p-3 dark:border-gray-700"
                            >

                                <!-- PREVIEW -->
                                <div
                                    class="h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-700"
                                >
                                    <img
                                        v-if="getPreview(item)"
                                        :src="getPreview(item)"
                                        :alt="item.file.name"
                                        class="h-full w-full object-cover"
                                    />
                                </div>

                                <!-- INFO -->
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-gray-900 dark:text-white">
                                        {{ item.file.name }}
                                    </p>

                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Foto Aset
                                    </p>
                                </div>

                                <!-- HAPUS -->
                                <button
                                    type="button"
                                    @click="removeFoto(index)"
                                    class="rounded-lg p-2 text-red-500 transition hover:bg-red-50 dark:hover:bg-red-900/20"
                                >
                                    🗑️
                                </button>

                            </div>
                        </div>

                        <div
                            v-else
                            class="mt-5 text-center text-sm text-gray-400 dark:text-gray-500"
                        >
                            Belum ada foto yang dipilih.
                        </div>

                    </div>
                </div>

                <!-- ================================================== -->
                <!-- NOTA & DOKUMEN -->
                <!-- ================================================== -->

                <div
                    class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
                >
                    <div
                        class="border-b border-gray-200 px-6 py-5 dark:border-gray-700"
                    >
                        <h2 class="font-semibold text-gray-900 dark:text-white">
                            Nota & Dokumen
                        </h2>

                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Upload nota/dokumen atau foto menggunakan kamera.
                        </p>
                    </div>

                    <div class="p-6">

                        <!-- PILIHAN -->
                        <div class="grid gap-3 sm:grid-cols-2">

                            <!-- UPLOAD -->
                            <label
                                class="flex cursor-pointer items-center justify-center gap-3 rounded-xl border-2 border-dashed border-gray-300 px-4 py-4 transition hover:border-emerald-500 hover:bg-emerald-50/50 dark:border-gray-600 dark:hover:bg-emerald-900/10"
                            >
                                <span class="text-2xl">
                                    📁
                                </span>

                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        Upload File
                                    </p>

                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        PDF, JPG, PNG, WEBP
                                    </p>
                                </div>

                                <input
                                    type="file"
                                    multiple
                                    accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/jpeg,image/png,image/webp"
                                    class="hidden"
                                    @change="handleNotaFiles"
                                />
                            </label>

                            <!-- KAMERA -->
                            <button
                                type="button"
                                @click="bukaKamera('nota')"
                                class="flex items-center justify-center gap-3 rounded-xl border-2 border-dashed border-emerald-300 px-4 py-4 transition hover:border-emerald-500 hover:bg-emerald-50 dark:border-emerald-700 dark:hover:bg-emerald-900/20"
                            >
                                <span class="text-2xl">
                                    📷
                                </span>

                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        Buka Kamera
                                    </p>

                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Foto nota langsung
                                    </p>
                                </div>
                            </button>

                        </div>

                        <!-- NOTA LIST -->
                        <div
                            v-if="notaBaru.length > 0"
                            class="mt-6 space-y-3"
                        >
                            <div
                                v-for="(item, index) in notaBaru"
                                :key="`${item.file.name}-${index}`"
                                class="flex items-center gap-4 rounded-xl border border-gray-200 p-3 dark:border-gray-700"
                            >

                                <!-- PREVIEW -->
                                <div
                                    class="h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-700"
                                >
                                    <img
                                        v-if="getPreview(item)"
                                        :src="getPreview(item)"
                                        :alt="item.file.name"
                                        class="h-full w-full object-cover"
                                    />

                                    <div
                                        v-else
                                        class="flex h-full w-full items-center justify-center text-2xl"
                                    >
                                        📄
                                    </div>
                                </div>

                                <!-- INFO -->
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-gray-900 dark:text-white">
                                        {{ item.file.name }}
                                    </p>

                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ item.file.type === 'application/pdf'
                                            ? 'PDF'
                                            : 'Nota' }}
                                    </p>
                                </div>

                                <!-- HAPUS -->
                                <button
                                    type="button"
                                    @click="removeNota(index)"
                                    class="rounded-lg p-2 text-red-500 transition hover:bg-red-50 dark:hover:bg-red-900/20"
                                >
                                    🗑️
                                </button>

                            </div>
                        </div>

                        <div
                            v-else
                            class="mt-5 text-center text-sm text-gray-400 dark:text-gray-500"
                        >
                            Belum ada nota atau dokumen yang dipilih.
                        </div>

                    </div>
                </div>

            </div>

            <!-- ====================================================== -->
            <!-- BUTTON -->
            <!-- ====================================================== -->

            <div class="flex items-center justify-end gap-3">

                <Link
                    :href="route('inventaris.index')"
                    class="rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                >
                    Batal
                </Link>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    {{
                        form.processing
                            ? 'Menyimpan...'
                            : 'Simpan Inventaris'
                    }}
                </button>

            </div>

        </form>
    </div>

    <!-- ============================================================ -->
    <!-- MODAL KAMERA -->
    <!-- ============================================================ -->

    <Teleport to="body">
        <div
            v-if="cameraOpen"
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/80 p-4"
        >
            <div
                class="w-full max-w-3xl overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-gray-800"
            >

                <!-- HEADER -->
                <div
                    class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-700"
                >
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">
                            {{
                                cameraMode === 'foto'
                                    ? 'Ambil Foto Aset'
                                    : 'Foto Nota'
                            }}
                        </h3>

                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Arahkan kamera ke objek yang ingin difoto.
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="tutupKamera"
                        class="rounded-lg px-3 py-2 text-gray-500 transition hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700"
                    >
                        ✕
                    </button>
                </div>

                <!-- VIDEO -->
                <div class="relative bg-black">
                    <video
                        ref="video"
                        autoplay
                        playsinline
                        muted
                        class="aspect-video w-full object-contain"
                    ></video>
                </div>

                <!-- FOOTER -->
                <div
                    class="flex justify-center gap-3 border-t border-gray-200 px-5 py-5 dark:border-gray-700"
                >
                    <button
                        type="button"
                        @click="tutupKamera"
                        class="rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                    >
                        Batal
                    </button>

                    <button
                        type="button"
                        @click="ambilFoto"
                        class="rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
                    >
                        📷 Ambil Foto
                    </button>
                </div>

            </div>
        </div>
    </Teleport>
</template>


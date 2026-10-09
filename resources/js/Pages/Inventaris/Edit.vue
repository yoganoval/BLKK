<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { computed, nextTick, onBeforeUnmount, ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    inventaris: {
        type: Object,
        required: true,
    },

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
    kode: props.inventaris.kode ?? '',
    nama: props.inventaris.nama ?? '',
    kategori_id: props.inventaris.kategori_id ?? '',
    jumlah: props.inventaris.jumlah ?? 1,
    satuan: props.inventaris.satuan ?? 'Unit',
    kondisi: props.inventaris.kondisi ?? 'baik',
    lokasi: props.inventaris.lokasi ?? '',
    tahun_perolehan: props.inventaris.tahun_perolehan ?? '',
    harga_perolehan: props.inventaris.harga_perolehan ?? '',
    sumber_dana: props.inventaris.sumber_dana ?? '',
    keterangan: props.inventaris.keterangan ?? '',

    // FILE BARU
    foto: null,
    nota: null,
    dokumen: null,
})

/*
|--------------------------------------------------------------------------
| FILE BARU
|--------------------------------------------------------------------------
*/

const fotoBaru = ref(null)
const fotoPreview = ref(null)

const notaBaru = ref(null)
const notaPreview = ref(null)

const dokumenBaru = ref(null)

/*
|--------------------------------------------------------------------------
| FILE LAMA
|--------------------------------------------------------------------------
*/

const fotoFiles = computed(() => {
    return (props.inventaris.files ?? []).filter(
        (file) => file.jenis === 'foto'
    )
})

const notaFiles = computed(() => {
    return (props.inventaris.files ?? []).filter(
        (file) => file.jenis === 'nota'
    )
})

const dokumenFiles = computed(() => {
    return (props.inventaris.files ?? []).filter(
        (file) => file.jenis === 'dokumen'
    )
})

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
| URL FILE
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
| HANDLE FOTO
|--------------------------------------------------------------------------
*/

const handleFoto = (event) => {
    const file = event.target.files?.[0]

    if (!file) {
        return
    }

    const allowedTypes = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ]

    if (!allowedTypes.includes(file.type)) {
        alert('Foto harus berformat JPG, JPEG, atau WEBP.')

        event.target.value = ''

        return
    }

    // Simpan file
    fotoBaru.value = file
    form.foto = file

    // Buat preview
    if (fotoPreview.value) {
        URL.revokeObjectURL(fotoPreview.value)
    }

    fotoPreview.value = URL.createObjectURL(file)

    console.log('FOTO BARU:', file)
    console.log('FORM FOTO:', form.foto)

    event.target.value = ''
}

/*
|--------------------------------------------------------------------------
| HAPUS FOTO BARU
|--------------------------------------------------------------------------
*/

const removeFotoBaru = () => {
    if (fotoPreview.value) {
        URL.revokeObjectURL(fotoPreview.value)
    }

    fotoBaru.value = null
    fotoPreview.value = null
    form.foto = null
}

/*
|--------------------------------------------------------------------------
| HANDLE NOTA
|--------------------------------------------------------------------------
*/

const handleNota = (event) => {
    const file = event.target.files?.[0]

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
        alert(
            'Nota harus berupa PDF atau foto JPG, JPEG, PNG, WEBP.'
        )

        event.target.value = ''

        return
    }

    // Simpan file
    notaBaru.value = file
    form.nota = file

    // Hapus preview lama
    if (notaPreview.value) {
        URL.revokeObjectURL(notaPreview.value)
        notaPreview.value = null
    }

    // Preview kalau gambar
    if (file.type.startsWith('image/')) {
        notaPreview.value = URL.createObjectURL(file)
    }

    console.log('NOTA BARU:', file)
    console.log('FORM NOTA:', form.nota)

    event.target.value = ''
}

/*
|--------------------------------------------------------------------------
| HAPUS NOTA BARU
|--------------------------------------------------------------------------
*/

const removeNotaBaru = () => {
    if (notaPreview.value) {
        URL.revokeObjectURL(notaPreview.value)
    }

    notaBaru.value = null
    notaPreview.value = null
    form.nota = null
}

/*
|--------------------------------------------------------------------------
| HANDLE DOKUMEN
|--------------------------------------------------------------------------
*/

const handleDokumen = (event) => {
    const file = event.target.files?.[0]

    if (!file) {
        return
    }

    if (file.type !== 'application/pdf') {
        alert('Dokumen harus berformat PDF.')

        event.target.value = ''

        return
    }

    dokumenBaru.value = file
    form.dokumen = file

    console.log('DOKUMEN BARU:', file)
    console.log('FORM DOKUMEN:', form.dokumen)

    event.target.value = ''
}

/*
|--------------------------------------------------------------------------
| HAPUS DOKUMEN BARU
|--------------------------------------------------------------------------
*/

const removeDokumenBaru = () => {
    dokumenBaru.value = null
    form.dokumen = null
}

/*
|--------------------------------------------------------------------------
| HAPUS FILE LAMA MANUAL
|--------------------------------------------------------------------------
*/

const hapusFileLama = (file) => {
    if (
        !confirm(
            `Yakin ingin menghapus file "${file.nama_file}"?`
        )
    ) {
        return
    }

    router.delete(
        route('inventaris-files.destroy', file.id),
        {
            preserveScroll: true,
        }
    )
}

/*
|--------------------------------------------------------------------------
| KAMERA
|--------------------------------------------------------------------------
*/

const cameraOpen = ref(false)
const cameraMode = ref('foto')
const video = ref(null)
const cameraStream = ref(null)

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
            throw new Error(
                'Browser tidak mendukung kamera.'
            )
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
            video.value.srcObject =
                cameraStream.value

            await video.value.play()
        }
    } catch (error) {
        console.error(
            'Kamera gagal dibuka:',
            error
        )

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
| AMBIL FOTO KAMERA
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
        alert(
            'Kamera belum siap. Tunggu sebentar.'
        )

        return
    }

    const canvas =
        document.createElement('canvas')

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

            /*
            |--------------------------------------------------------------------------
            | KAMERA FOTO ASET
            |--------------------------------------------------------------------------
            */

            if (cameraMode.value === 'foto') {
                fotoBaru.value = file
                form.foto = file

                if (fotoPreview.value) {
                    URL.revokeObjectURL(
                        fotoPreview.value
                    )
                }

                fotoPreview.value =
                    URL.createObjectURL(file)

                console.log(
                    'FOTO DARI KAMERA:',
                    file
                )
            }

            /*
            |--------------------------------------------------------------------------
            | KAMERA NOTA
            |--------------------------------------------------------------------------
            */

            else {
                notaBaru.value = file
                form.nota = file

                if (notaPreview.value) {
                    URL.revokeObjectURL(
                        notaPreview.value
                    )
                }

                notaPreview.value =
                    URL.createObjectURL(file)

                console.log(
                    'NOTA DARI KAMERA:',
                    file
                )
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
    console.log('==============================')
    console.log('SUBMIT INVENTARIS')
    console.log('FOTO:', form.foto)
    console.log('NOTA:', form.nota)
    console.log('DOKUMEN:', form.dokumen)
    console.log('==============================')

    form
        .transform((data) => ({
            ...data,
            _method: 'PUT',
        }))
        .post(
            route(
                'inventaris.update',
                props.inventaris.id
            ),
            {
                forceFormData: true,
                preserveScroll: true,

                onStart: () => {
                    console.log(
                        'Mengirim data inventaris...'
                    )
                },

                onSuccess: () => {
                    console.log(
                        'Inventaris berhasil diperbarui'
                    )
                },

                onError: (errors) => {
                    console.error(
                        'VALIDATION ERROR:',
                        errors
                    )
                },

                onFinish: () => {
                    console.log(
                        'Request selesai'
                    )
                },
            }
        )
}

/*
|--------------------------------------------------------------------------
| CLEANUP
|--------------------------------------------------------------------------
*/

onBeforeUnmount(() => {
    stopCamera()

    if (fotoPreview.value) {
        URL.revokeObjectURL(
            fotoPreview.value
        )
    }

    if (notaPreview.value) {
        URL.revokeObjectURL(
            notaPreview.value
        )
    }
})
</script>

<template>
    <Head title="Edit Inventaris" />

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
                    Edit
                </span>
            </div>

            <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">
                Edit Inventaris
            </h1>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Perbarui data aset, pengadaan, foto, dan dokumen.
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
                                v-for="item in kategori"
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
                                    v-for="item in satuanOptions"
                                    :key="item"
                                    :value="item"
                                >
                                    {{ item }}
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
                            class="input"
                        />
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
                            class="input"
                        />
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
                            class="input"
                        ></textarea>
                    </div>

                </div>
            </div>


            <!-- ====================================================== -->
            <!-- FOTO & DOKUMEN -->
            <!-- ====================================================== -->

            <div class="grid gap-6 lg:grid-cols-2">

                <!-- FOTO -->
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
                            Foto aset yang tersimpan dan foto baru.
                        </p>
                    </div>

                    <div class="p-6">

                        <!-- FOTO LAMA -->
                        <div
                            v-if="fotoFiles.length > 0"
                            class="mb-5"
                        >
                            <p class="section-label">
                                Foto Saat Ini
                            </p>

                            <div class="grid grid-cols-2 gap-3">
                                <div
                                    v-for="file in fotoFiles"
                                    :key="file.id"
                                    class="group relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700"
                                >
                                    <a
                                        :href="fileUrl(file.file_path)"
                                        target="_blank"
                                    >
                                        <img
                                            :src="fileUrl(file.file_path)"
                                            :alt="file.nama_file"
                                            class="h-40 w-full object-cover transition duration-300 group-hover:scale-105"
                                        />
                                    </a>

                                    <!-- <button
                                        type="button"
                                        @click="hapusFileLama(file)"
                                        class="absolute right-2 top-2 rounded-lg bg-red-600 px-2.5 py-1.5 text-xs font-medium text-white shadow hover:bg-red-700"
                                    >
                                        Hapus
                                    </button> -->

                                    <div class="absolute inset-x-0 bottom-0 bg-black/60 px-3 py-2">
                                        <p class="truncate text-xs text-white">
                                            {{ file.nama_file }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

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
                                    @change="handleFoto"
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

                        <!-- FOTO BARU -->
                        <div
                            v-if="fotoBaru"
                            class="mt-6 space-y-3"
                        >
                            <p class="section-label">
                                Foto Baru
                            </p>

                            <div
                                class="flex items-center gap-4 rounded-xl border border-indigo-200 bg-indigo-50/50 p-3 dark:border-indigo-800 dark:bg-indigo-900/10"
                            >
                                <!-- PREVIEW -->
                                <div
                                    class="h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-700"
                                >
                                    <img
                                        v-if="fotoPreview"
                                        :src="fotoPreview"
                                        :alt="fotoBaru.name"
                                        class="h-full w-full object-cover"
                                    />
                                </div>

                                <!-- INFO -->
                                <div class="min-w-0 flex-1">
                                    <p
                                        class="truncate text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ fotoBaru.name }}
                                    </p>

                                    <p
                                        class="mt-1 text-xs text-indigo-600 dark:text-indigo-400"
                                    >
                                        Foto baru akan menggantikan foto lama saat disimpan.
                                    </p>
                                </div>

                                <!-- HAPUS -->
                                <button
                                    type="button"
                                    @click="removeFotoBaru"
                                    class="rounded-lg p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20"
                                >
                                    🗑️
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- NOTA & DOKUMEN -->
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
                            Kelola nota dan dokumen pendukung.
                        </p>
                    </div>

                    <div class="p-6">

                        <!-- NOTA LAMA -->
                        <div
                            v-if="notaFiles.length > 0"
                            class="mb-5"
                        >
                            <p class="section-label">
                                Nota Saat Ini
                            </p>

                            <div class="space-y-3">
                                <div
                                    v-for="file in notaFiles"
                                    :key="file.id"
                                    class="flex items-center gap-3 rounded-xl border border-gray-200 p-3 dark:border-gray-700"
                                >
                                    <a
                                        :href="fileUrl(file.file_path)"
                                        target="_blank"
                                        class="flex min-w-0 flex-1 items-center gap-3"
                                    >
                                        <div
                                            class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-red-100 text-xl dark:bg-red-900/20"
                                        >
                                            <img
                                                v-if="
                                                    file.file_path &&
                                                    /\.(jpg|jpeg|png|webp)$/i.test(file.file_path)
                                                "
                                                :src="fileUrl(file.file_path)"
                                                :alt="file.nama_file"
                                                class="h-full w-full object-cover"
                                            />

                                            <span v-else>
                                                📄
                                            </span>
                                        </div>

                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-medium text-gray-900 dark:text-white">
                                                {{ file.nama_file }}
                                            </p>

                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                Nota
                                            </p>
                                        </div>
                                    </a>

                                    <!-- <button
                                        type="button"
                                        @click="hapusFileLama(file)"
                                        class="rounded-lg p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20"
                                    >
                                        🗑️
                                    </button> -->
                                </div>
                            </div>
                        </div>

                        <!-- DOKUMEN LAMA -->
                        <div
                            v-if="dokumenFiles.length > 0"
                            class="mb-5"
                        >
                            <p class="section-label">
                                Dokumen Saat Ini
                            </p>

                            <div class="space-y-3">
                                <div
                                    v-for="file in dokumenFiles"
                                    :key="file.id"
                                    class="flex items-center gap-3 rounded-xl border border-gray-200 p-3 dark:border-gray-700"
                                >
                                    <a
                                        :href="fileUrl(file.file_path)"
                                        target="_blank"
                                        class="flex min-w-0 flex-1 items-center gap-3"
                                    >
                                        <div
                                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-xl dark:bg-blue-900/20"
                                        >
                                            📄
                                        </div>

                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-medium text-gray-900 dark:text-white">
                                                {{ file.nama_file }}
                                            </p>

                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                Dokumen
                                            </p>
                                        </div>
                                    </a>

                                    <button
                                        type="button"
                                        @click="hapusFileLama(file)"
                                        class="rounded-lg p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20"
                                    >
                                        🗑️
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- PILIH FILE -->
                        <div class="grid gap-3 sm:grid-cols-2">

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
                                        PDF, JPG, PNG
                                    </p>
                                </div>

                                <input
                                    type="file"
                                    multiple
                                    accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/jpeg,image/png,image/webp"
                                    class="hidden"
                                    @change="handleNota"
                                />
                            </label>

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

                       
                        <!-- NOTA BARU -->
                        <div
                            v-if="notaBaru"
                            class="mt-6 space-y-3"
                        >
                            <p class="section-label">
                                Nota Baru
                            </p>

                            <div
                                class="flex items-center gap-4 rounded-xl border border-emerald-200 bg-emerald-50/50 p-3 dark:border-emerald-800 dark:bg-emerald-900/10"
                            >
                                <!-- PREVIEW -->
                                <div
                                    class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-700"
                                >
                                    <img
                                        v-if="notaPreview"
                                        :src="notaPreview"
                                        :alt="notaBaru.name"
                                        class="h-full w-full object-cover"
                                    />

                                    <span
                                        v-else
                                        class="text-3xl"
                                    >
                                        📄
                                    </span>
                                </div>

                                <!-- INFO -->
                                <div class="min-w-0 flex-1">
                                    <p
                                        class="truncate text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ notaBaru.name }}
                                    </p>

                                    <p
                                        class="mt-1 text-xs text-emerald-600 dark:text-emerald-400"
                                    >
                                        Nota baru akan menggantikan nota lama saat disimpan.
                                    </p>
                                </div>

                                <!-- HAPUS -->
                                <button
                                    type="button"
                                    @click="removeNotaBaru"
                                    class="rounded-lg p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20"
                                >
                                    🗑️
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <!-- BUTTON -->
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
                            : 'Simpan Perubahan'
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
                            Arahkan kamera ke objek.
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="tutupKamera"
                        class="rounded-lg px-3 py-2 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700"
                    >
                        ✕
                    </button>
                </div>

                <!-- CAMERA -->
                <div class="bg-black">
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
                        class="rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                    >
                        Batal
                    </button>

                    <button
                        type="button"
                        @click="ambilFoto"
                        class="rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700"
                    >
                        📷 Ambil Foto
                    </button>
                </div>

            </div>
        </div>
    </Teleport>
</template>



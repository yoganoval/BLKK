<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    lpj: {
        type: Object,
        required: true,
    },
})

/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const showForm = ref(false)
const editingId = ref(null)
const showBukti = ref(false)
const selectedBukti = ref(null)
const fileInput = ref(null)

const form = ref({
    tanggal: '',
    kategori: '',
    uraian: '',
    nomor_bukti: '',
    jumlah: '',
    metode_pembayaran: 'cash',
    penerima: '',
    bukti: null,
    keterangan: '',
})

const errors = ref({})

const submitting = ref(false)

/*
|--------------------------------------------------------------------------
| COMPUTED
|--------------------------------------------------------------------------
*/

const totalPemasukan = computed(() => {
    return Number(props.lpj.total_pemasukan || 0)
})

const totalPengeluaran = computed(() => {
    return Number(props.lpj.total_pengeluaran || 0)
})

const saldo = computed(() => {
    return totalPemasukan.value - totalPengeluaran.value
})

const jumlahPengeluaran = computed(() => {
    return props.lpj.pengeluaran?.length || 0
})

/*
|--------------------------------------------------------------------------
| FORMAT
|--------------------------------------------------------------------------
*/

const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(Number(value || 0))
}

const formatTanggal = (tanggal) => {
    if (!tanggal) {
        return '-'
    }

    const date = new Date(tanggal)

    if (Number.isNaN(date.getTime())) {
        return tanggal
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    }).format(date)
}

const formatStatus = (status) => {
    const statusMap = {
        draft: 'Draft',
        diajukan: 'Diajukan',
        disetujui: 'Disetujui',
        ditolak: 'Ditolak',
        selesai: 'Selesai',
    }

    return statusMap[status] || status
}

const statusClass = (status) => {
    const classes = {
        draft: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200',
        diajukan: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
        disetujui: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        ditolak: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
        selesai: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
    }

    return classes[status] || classes.draft
}

/*
|--------------------------------------------------------------------------
| FORM
|--------------------------------------------------------------------------
*/

const resetForm = () => {
    form.value = {
        tanggal: new Date().toISOString().split('T')[0],
        kategori: '',
        uraian: '',
        nomor_bukti: '',
        jumlah: '',
        metode_pembayaran: 'cash',
        penerima: '',
        bukti: null,
        keterangan: '',
    }

    errors.value = {}
    editingId.value = null

    if (fileInput.value) {
        fileInput.value.value = ''
    }
}

const openCreateForm = () => {
    resetForm()

    form.value.tanggal = new Date()
        .toISOString()
        .split('T')[0]

    showForm.value = true
}

const openEditForm = (item) => {
    errors.value = {}
    editingId.value = item.id

    form.value = {
        tanggal: item.tanggal
            ? String(item.tanggal).substring(0, 10)
            : '',
        kategori: item.kategori || '',
        uraian: item.uraian || '',
        nomor_bukti: item.nomor_bukti || '',
        jumlah: item.jumlah || '',
        metode_pembayaran: item.metode_pembayaran || 'cash',
        penerima: item.penerima || '',
        bukti: null,
        keterangan: item.keterangan || '',
    }

    showForm.value = true

    window.scrollTo({
        top: 0,
        behavior: 'smooth',
    })
}

const closeForm = () => {
    showForm.value = false
    resetForm()
}

const handleFile = (event) => {
    const file = event.target.files[0]

    if (!file) {
        form.value.bukti = null
        return
    }

    form.value.bukti = file
}

/*
|--------------------------------------------------------------------------
| SUBMIT PENGELUARAN
|--------------------------------------------------------------------------
*/

const submitPengeluaran = () => {
    errors.value = {}
    submitting.value = true

    const data = new FormData()

    data.append('tanggal', form.value.tanggal)
    data.append('kategori', form.value.kategori)
    data.append('uraian', form.value.uraian)
    data.append('nomor_bukti', form.value.nomor_bukti || '')
    data.append('jumlah', form.value.jumlah)
    data.append(
        'metode_pembayaran',
        form.value.metode_pembayaran
    )
    data.append('penerima', form.value.penerima || '')
    data.append('keterangan', form.value.keterangan || '')

    if (form.value.bukti) {
        data.append('bukti', form.value.bukti)
    }

    if (editingId.value) {
        data.append('_method', 'PUT')

        router.post(
            route(
                'lpj-keuangan.pengeluaran.update',
                editingId.value
            ),
            data,
            {
                forceFormData: true,

                onError: (error) => {
                    errors.value = error
                },

                onSuccess: () => {
                    closeForm()
                },

                onFinish: () => {
                    submitting.value = false
                },
            }
        )

        return
    }

    router.post(
        route(
            'lpj-keuangan.pengeluaran.store',
            props.lpj.id
        ),
        data,
        {
            forceFormData: true,

            onError: (error) => {
                errors.value = error
            },

            onSuccess: () => {
                closeForm()
            },

            onFinish: () => {
                submitting.value = false
            },
        }
    )
}

/*
|--------------------------------------------------------------------------
| DELETE
|--------------------------------------------------------------------------
*/

const deletePengeluaran = (item) => {
    if (
        !confirm(
            `Hapus pengeluaran "${item.uraian}" sebesar ${formatRupiah(item.jumlah)}?`
        )
    ) {
        return
    }

    router.delete(
        route(
            'lpj-keuangan.pengeluaran.destroy',
            item.id
        ),
        {
            preserveScroll: true,
        }
    )
}

/*
|--------------------------------------------------------------------------
| BUKTI
|--------------------------------------------------------------------------
*/

const getBuktiUrl = (item) => {
    if (!item.bukti_path) {
        return null
    }

    return `/storage/${item.bukti_path}`
}

const openBukti = (item) => {
    const url = getBuktiUrl(item)

    if (!url) {
        return
    }

    selectedBukti.value = {
        url,
        nama: item.bukti_nama || 'Bukti Pengeluaran',
        uraian: item.uraian,
    }

    showBukti.value = true
}

const closeBukti = () => {
    showBukti.value = false
    selectedBukti.value = null
}

/*
|--------------------------------------------------------------------------
| STATUS LPJ
|--------------------------------------------------------------------------
*/

const ajukanLpj = () => {
    if (
        !confirm(
            'Apakah LPJ ini sudah benar dan ingin diajukan?'
        )
    ) {
        return
    }

    router.post(
        route('lpj-keuangan.ajukan', props.lpj.id),
        {},
        {
            preserveScroll: true,
        }
    )
}

const setujuiLpj = () => {
    if (
        !confirm(
            'Apakah Anda yakin ingin menyetujui LPJ ini?'
        )
    ) {
        return
    }

    router.post(
        route('lpj-keuangan.setujui', props.lpj.id),
        {},
        {
            preserveScroll: true,
        }
    )
}

const tolakLpj = () => {
    if (
        !confirm(
            'Apakah Anda yakin ingin menolak LPJ ini?'
        )
    ) {
        return
    }

    router.post(
        route('lpj-keuangan.tolak', props.lpj.id),
        {},
        {
            preserveScroll: true,
        }
    )
}
</script>

<template>
    <Head :title="`LPJ ${lpj.kode_lpj}`" />

    <div class="space-y-6">

        <!-- HEADER -->
        <div
            class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
        >
            <div>
                <div class="mb-1 flex items-center gap-2">
                    <Link
                        :href="route('lpj-keuangan.index')"
                        class="text-sm text-gray-500 hover:text-blue-600 dark:text-gray-400"
                    >
                        LPJ Keuangan
                    </Link>

                    <span class="text-gray-400">/</span>

                    <span
                        class="text-sm text-gray-700 dark:text-gray-200"
                    >
                        {{ lpj.kode_lpj }}
                    </span>
                </div>

                <h1
                    class="text-2xl font-bold text-gray-900 dark:text-white"
                >
                    LPJ Keuangan
                </h1>

                <p
                    class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                >
                    Pertanggungjawaban keuangan kas BLKK
                </p>
            </div>

            <div class="flex flex-wrap gap-2">

                <Link
                    :href="route('lpj-keuangan.index')"
                    class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    ← Kembali
                </Link>

                <Link
                    :href="route('lpj-keuangan.edit', lpj.id)"
                    class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    ✏️ Edit LPJ
                </Link>

                <button
                    v-if="lpj.status === 'draft' || lpj.status === 'ditolak'"
                    type="button"
                    @click="ajukanLpj"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                >
                    📤 Ajukan LPJ
                </button>

                <button
                    v-if="lpj.status === 'diajukan'"
                    type="button"
                    @click="setujuiLpj"
                    class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-700"
                >
                    ✓ Setujui
                </button>

                <button
                    v-if="lpj.status === 'diajukan'"
                    type="button"
                    @click="tolakLpj"
                    class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700"
                >
                    ✕ Tolak
                </button>
            </div>
        </div>


        <!-- INFORMASI LPJ -->
        <div
            class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >
            <div
                class="border-b border-gray-200 px-6 py-5 dark:border-gray-700"
            >
                <div
                    class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <h2
                            class="text-lg font-semibold text-gray-900 dark:text-white"
                        >
                            {{ lpj.kode_lpj }}
                        </h2>

                        <p
                            class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                        >
                            Periode
                            {{ formatTanggal(lpj.periode_mulai) }}
                            -
                            {{ formatTanggal(lpj.periode_selesai) }}
                        </p>
                    </div>

                    <span
                        class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-semibold"
                        :class="statusClass(lpj.status)"
                    >
                        {{ formatStatus(lpj.status) }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 p-6 sm:grid-cols-3">

                <!-- PEMASUKAN -->
                <div
                    class="rounded-xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-900/50"
                >
                    <div class="flex items-center justify-between">
                        <span
                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                        >
                            Total Pemasukan
                        </span>

                        <span class="text-xl">
                            💰
                        </span>
                    </div>

                    <p
                        class="mt-2 text-2xl font-bold text-green-600 dark:text-green-400"
                    >
                        {{ formatRupiah(totalPemasukan) }}
                    </p>

                    <p
                        class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                    >
                        Dari pembayaran peserta yang lunas
                    </p>
                </div>


                <!-- PENGELUARAN -->
                <div
                    class="rounded-xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-900/50"
                >
                    <div class="flex items-center justify-between">
                        <span
                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                        >
                            Total Pengeluaran
                        </span>

                        <span class="text-xl">
                            💸
                        </span>
                    </div>

                    <p
                        class="mt-2 text-2xl font-bold text-red-600 dark:text-red-400"
                    >
                        {{ formatRupiah(totalPengeluaran) }}
                    </p>

                    <p
                        class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                    >
                        {{ jumlahPengeluaran }} transaksi
                    </p>
                </div>


                <!-- SALDO -->
                <div
                    class="rounded-xl border border-blue-200 bg-blue-50 p-5 dark:border-blue-900/50 dark:bg-blue-900/20"
                >
                    <div class="flex items-center justify-between">
                        <span
                            class="text-sm font-medium text-blue-700 dark:text-blue-300"
                        >
                            Saldo Kas
                        </span>

                        <span class="text-xl">
                            🏦
                        </span>
                    </div>

                    <p
                        class="mt-2 text-2xl font-bold"
                        :class="
                            saldo >= 0
                                ? 'text-blue-700 dark:text-blue-300'
                                : 'text-red-600 dark:text-red-400'
                        "
                    >
                        {{ formatRupiah(saldo) }}
                    </p>

                    <p
                        class="mt-1 text-xs text-blue-600/70 dark:text-blue-300/70"
                    >
                        Pemasukan - Pengeluaran
                    </p>
                </div>
            </div>
        </div>


        <!-- FORM PENGELUARAN -->
        <div
            v-if="showForm"
            class="rounded-2xl border border-blue-200 bg-white shadow-sm dark:border-blue-900/50 dark:bg-gray-800"
        >
            <div
                class="flex items-center justify-between border-b border-gray-200 px-6 py-5 dark:border-gray-700"
            >
                <div>
                    <h2
                        class="font-semibold text-gray-900 dark:text-white"
                    >
                        {{
                            editingId
                                ? 'Edit Pengeluaran'
                                : 'Tambah Pengeluaran'
                        }}
                    </h2>

                    <p
                        class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                    >
                        Masukkan transaksi pengeluaran dan bukti
                    </p>
                </div>

                <button
                    type="button"
                    @click="closeForm"
                    class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                >
                    ✕
                </button>
            </div>

            <form
                @submit.prevent="submitPengeluaran"
                enctype="multipart/form-data"
                class="p-6"
            >
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                    <!-- TANGGAL -->
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Tanggal
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            v-model="form.tanggal"
                            type="date"
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                        />

                        <p
                            v-if="errors.tanggal"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ errors.tanggal }}
                        </p>
                    </div>


                    <!-- KATEGORI -->
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Kategori
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            v-model="form.kategori"
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                        >
                            <option value="">
                                Pilih kategori
                            </option>

                            <option value="ATK">
                                ATK
                            </option>

                            <option value="Konsumsi">
                                Konsumsi
                            </option>

                            <option value="Transportasi">
                                Transportasi
                            </option>

                            <option value="Peralatan">
                                Peralatan
                            </option>

                            <option value="Honorarium">
                                Honorarium
                            </option>

                            <option value="Operasional">
                                Operasional
                            </option>

                            <option value="Pemeliharaan">
                                Pemeliharaan
                            </option>

                            <option value="Lain-lain">
                                Lain-lain
                            </option>
                        </select>

                        <p
                            v-if="errors.kategori"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ errors.kategori }}
                        </p>
                    </div>


                    <!-- URAIAN -->
                    <div class="md:col-span-2">
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Uraian Pengeluaran
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            v-model="form.uraian"
                            type="text"
                            placeholder="Contoh: Pembelian ATK untuk kegiatan pelatihan"
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                        />

                        <p
                            v-if="errors.uraian"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ errors.uraian }}
                        </p>
                    </div>


                    <!-- JUMLAH -->
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Jumlah
                            <span class="text-red-500">*</span>
                        </label>

                        <div class="relative">
                            <span
                                class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-500"
                            >
                                Rp
                            </span>

                            <input
                                v-model="form.jumlah"
                                type="number"
                                min="0"
                                placeholder="0"
                                class="w-full rounded-xl border border-gray-300 bg-white py-2.5 pl-12 pr-4 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                            />
                        </div>

                        <p
                            v-if="errors.jumlah"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ errors.jumlah }}
                        </p>
                    </div>


                    <!-- NOMOR BUKTI -->
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Nomor Bukti
                        </label>

                        <input
                            v-model="form.nomor_bukti"
                            type="text"
                            placeholder="Contoh: INV-001"
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                        />
                    </div>


                    <!-- METODE -->
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Metode Pembayaran
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            v-model="form.metode_pembayaran"
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                        >
                            <option value="cash">
                                Cash
                            </option>

                            <option value="transfer">
                                Transfer
                            </option>

                            <option value="qris">
                                QRIS
                            </option>
                        </select>
                    </div>


                    <!-- PENERIMA -->
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Penerima
                        </label>

                        <input
                            v-model="form.penerima"
                            type="text"
                            placeholder="Nama penerima"
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                        />
                    </div>


                    <!-- BUKTI -->
                    <div class="md:col-span-2">
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Bukti Pengeluaran
                        </label>

                        <div
                            class="rounded-xl border-2 border-dashed border-gray-300 p-5 text-center transition hover:border-blue-400 dark:border-gray-600"
                        >
                            <input
                                ref="fileInput"
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                capture="environment"
                                @change="handleFile"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100 dark:text-gray-400 dark:file:bg-blue-900/30 dark:file:text-blue-300"
                            />

                            <p
                                class="mt-2 text-xs text-gray-500 dark:text-gray-400"
                            >
                                Bisa upload foto nota atau langsung
                                mengambil foto menggunakan kamera HP.
                                Maksimal 5 MB.
                            </p>

                            <p
                                v-if="form.bukti"
                                class="mt-2 text-sm font-medium text-green-600"
                            >
                                ✓ {{ form.bukti.name }}
                            </p>
                        </div>

                        <p
                            v-if="errors.bukti"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ errors.bukti }}
                        </p>
                    </div>


                    <!-- KETERANGAN -->
                    <div class="md:col-span-2">
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Keterangan
                        </label>

                        <textarea
                            v-model="form.keterangan"
                            rows="3"
                            placeholder="Keterangan tambahan..."
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                        ></textarea>
                    </div>
                </div>


                <!-- FORM BUTTON -->
                <div
                    class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-5 dark:border-gray-700"
                >
                    <button
                        type="button"
                        @click="closeForm"
                        class="rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        :disabled="submitting"
                        class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {{
                            submitting
                                ? 'Menyimpan...'
                                : editingId
                                    ? 'Simpan Perubahan'
                                    : 'Simpan Pengeluaran'
                        }}
                    </button>
                </div>
            </form>
        </div>


        <!-- RINCIAN PENGELUARAN -->
        <div
            class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >
            <!-- HEADER -->
            <div
                class="flex flex-col gap-4 border-b border-gray-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-700"
            >
                <div>
                    <h2
                        class="text-lg font-semibold text-gray-900 dark:text-white"
                    >
                        Rincian Pengeluaran
                    </h2>

                    <p
                        class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                    >
                        Catatan penggunaan kas BLKK
                    </p>
                </div>

                <button
                    v-if="
                        lpj.status === 'draft' ||
                        lpj.status === 'ditolak'
                    "
                    type="button"
                    @click="openCreateForm"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                >
                    <span class="text-lg leading-none">+</span>
                    Tambah Pengeluaran
                </button>
            </div>


            <!-- TABLE -->
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr
                            class="border-b border-gray-200 bg-gray-50 text-left dark:border-gray-700 dark:bg-gray-900/50"
                        >
                            <th
                                class="whitespace-nowrap px-5 py-4 font-semibold text-gray-700 dark:text-gray-300"
                            >
                                No
                            </th>

                            <th
                                class="whitespace-nowrap px-5 py-4 font-semibold text-gray-700 dark:text-gray-300"
                            >
                                Tanggal
                            </th>

                            <th
                                class="whitespace-nowrap px-5 py-4 font-semibold text-gray-700 dark:text-gray-300"
                            >
                                Kategori
                            </th>

                            <th
                                class="min-w-[250px] px-5 py-4 font-semibold text-gray-700 dark:text-gray-300"
                            >
                                Uraian
                            </th>

                            <th
                                class="whitespace-nowrap px-5 py-4 text-right font-semibold text-gray-700 dark:text-gray-300"
                            >
                                Jumlah
                            </th>

                            <th
                                class="whitespace-nowrap px-5 py-4 text-center font-semibold text-gray-700 dark:text-gray-300"
                            >
                                Bukti
                            </th>

                            <th
                                v-if="
                                    lpj.status === 'draft' ||
                                    lpj.status === 'ditolak'
                                "
                                class="whitespace-nowrap px-5 py-4 text-center font-semibold text-gray-700 dark:text-gray-300"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody
                        class="divide-y divide-gray-200 dark:divide-gray-700"
                    >
                        <tr
                            v-for="(item, index) in lpj.pengeluaran"
                            :key="item.id"
                            class="transition hover:bg-gray-50 dark:hover:bg-gray-700/30"
                        >
                            <td
                                class="whitespace-nowrap px-5 py-4 text-gray-600 dark:text-gray-400"
                            >
                                {{ index + 1 }}
                            </td>

                            <td
                                class="whitespace-nowrap px-5 py-4 text-gray-700 dark:text-gray-300"
                            >
                                {{ formatTanggal(item.tanggal) }}
                            </td>

                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300"
                                >
                                    {{ item.kategori }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <div
                                    class="font-medium text-gray-900 dark:text-white"
                                >
                                    {{ item.uraian }}
                                </div>

                                <div
                                    v-if="item.nomor_bukti"
                                    class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                                >
                                    Bukti: {{ item.nomor_bukti }}
                                </div>

                                <div
                                    v-if="item.penerima"
                                    class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                                >
                                    Penerima: {{ item.penerima }}
                                </div>
                            </td>

                            <td
                                class="whitespace-nowrap px-5 py-4 text-right font-semibold text-red-600 dark:text-red-400"
                            >
                                {{ formatRupiah(item.jumlah) }}
                            </td>

                            <td class="px-5 py-4 text-center">
                                <button
                                    v-if="item.bukti_path"
                                    type="button"
                                    @click="openBukti(item)"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-300"
                                >
                                    📷 Lihat
                                </button>

                                <span
                                    v-else
                                    class="text-xs text-gray-400"
                                >
                                    Tidak ada
                                </span>
                            </td>

                            <td
                                v-if="
                                    lpj.status === 'draft' ||
                                    lpj.status === 'ditolak'
                                "
                                class="px-5 py-4"
                            >
                                <div
                                    class="flex justify-center gap-1"
                                >
                                    <button
                                        type="button"
                                        @click="openEditForm(item)"
                                        class="rounded-lg p-2 text-blue-600 transition hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/20"
                                        title="Edit"
                                    >
                                        ✏️
                                    </button>

                                    <button
                                        type="button"
                                        @click="deletePengeluaran(item)"
                                        class="rounded-lg p-2 text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                                        title="Hapus"
                                    >
                                        🗑️
                                    </button>
                                </div>
                            </td>
                        </tr>


                        <!-- EMPTY -->
                        <tr
                            v-if="
                                !lpj.pengeluaran ||
                                lpj.pengeluaran.length === 0
                            "
                        >
                            <td
                                :colspan="
                                    lpj.status === 'draft' ||
                                    lpj.status === 'ditolak'
                                        ? 7
                                        : 6
                                "
                                class="px-5 py-12 text-center"
                            >
                                <div
                                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-2xl dark:bg-gray-700"
                                >
                                    💰
                                </div>

                                <h3
                                    class="mt-4 font-semibold text-gray-900 dark:text-white"
                                >
                                    Belum ada pengeluaran
                                </h3>

                                <p
                                    class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                                >
                                    Tambahkan pengeluaran kas BLKK.
                                </p>

                                <button
                                    v-if="
                                        lpj.status === 'draft' ||
                                        lpj.status === 'ditolak'
                                    "
                                    type="button"
                                    @click="openCreateForm"
                                    class="mt-4 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                                >
                                    + Tambah Pengeluaran
                                </button>
                            </td>
                        </tr>
                    </tbody>

                    <!-- TOTAL -->
                    <tfoot
                        v-if="
                            lpj.pengeluaran &&
                            lpj.pengeluaran.length > 0
                        "
                    >
                        <tr
                            class="border-t-2 border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-900/50"
                        >
                            <td
                                colspan="4"
                                class="px-5 py-4 text-right font-bold text-gray-900 dark:text-white"
                            >
                                TOTAL PENGELUARAN
                            </td>

                            <td
                                class="whitespace-nowrap px-5 py-4 text-right text-base font-bold text-red-600 dark:text-red-400"
                            >
                                {{ formatRupiah(totalPengeluaran) }}
                            </td>

                            <td
                                :colspan="
                                    lpj.status === 'draft' ||
                                    lpj.status === 'ditolak'
                                        ? 2
                                        : 1
                                "
                            ></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>


        <!-- RINGKASAN AKHIR -->
        <div
            class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >
            <div
                class="border-b border-gray-200 px-6 py-5 dark:border-gray-700"
            >
                <h2
                    class="font-semibold text-gray-900 dark:text-white"
                >
                    Ringkasan Keuangan
                </h2>
            </div>

            <div class="p-6">
                <div class="space-y-3">

                    <div
                        class="flex items-center justify-between border-b border-gray-100 pb-3 dark:border-gray-700"
                    >
                        <span
                            class="text-sm text-gray-600 dark:text-gray-400"
                        >
                            Total Pemasukan
                        </span>

                        <span
                            class="font-semibold text-green-600 dark:text-green-400"
                        >
                            {{ formatRupiah(totalPemasukan) }}
                        </span>
                    </div>

                    <div
                        class="flex items-center justify-between border-b border-gray-100 pb-3 dark:border-gray-700"
                    >
                        <span
                            class="text-sm text-gray-600 dark:text-gray-400"
                        >
                            Total Pengeluaran
                        </span>

                        <span
                            class="font-semibold text-red-600 dark:text-red-400"
                        >
                            {{ formatRupiah(totalPengeluaran) }}
                        </span>
                    </div>

                    <div
                        class="flex items-center justify-between pt-1"
                    >
                        <span
                            class="text-base font-bold text-gray-900 dark:text-white"
                        >
                            Saldo Akhir
                        </span>

                        <span
                            class="text-xl font-bold"
                            :class="
                                saldo >= 0
                                    ? 'text-blue-600 dark:text-blue-400'
                                    : 'text-red-600 dark:text-red-400'
                            "
                        >
                            {{ formatRupiah(saldo) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>


        <!-- KETERANGAN -->
        <div
            v-if="lpj.keterangan"
            class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >
            <h2
                class="font-semibold text-gray-900 dark:text-white"
            >
                Keterangan
            </h2>

            <p
                class="mt-2 whitespace-pre-line text-sm leading-relaxed text-gray-600 dark:text-gray-300"
            >
                {{ lpj.keterangan }}
            </p>
        </div>


        <!-- MODAL BUKTI -->
        <Teleport to="body">
            <div
                v-if="showBukti && selectedBukti"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-black/70 p-4"
                @click.self="closeBukti"
            >
                <div
                    class="max-h-[95vh] w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-gray-800"
                >
                    <!-- MODAL HEADER -->
                    <div
                        class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-700"
                    >
                        <div>
                            <h3
                                class="font-semibold text-gray-900 dark:text-white"
                            >
                                Bukti Pengeluaran
                            </h3>

                            <p
                                class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                            >
                                {{ selectedBukti.uraian }}
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="closeBukti"
                            class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                        >
                            ✕
                        </button>
                    </div>

                    <!-- IMAGE -->
                    <div
                        class="flex max-h-[75vh] items-center justify-center overflow-auto bg-gray-100 p-4 dark:bg-gray-900"
                    >
                        <img
                            :src="selectedBukti.url"
                            :alt="selectedBukti.nama"
                            class="max-h-[70vh] max-w-full rounded-lg object-contain shadow-lg"
                        />
                    </div>

                    <!-- FOOTER -->
                    <div
                        class="flex justify-end border-t border-gray-200 px-5 py-4 dark:border-gray-700"
                    >
                        <a
                            :href="selectedBukti.url"
                            target="_blank"
                            class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                        >
                            Buka Foto
                        </a>
                    </div>
                </div>
            </div>
        </Teleport>

    </div>
</template>
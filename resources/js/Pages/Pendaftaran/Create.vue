<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

defineProps({
    programPelatihans: {
        type: Array,
        default: () => [],
    },

    kelass: {
        type: Array,
        default: () => [],
    },
})

const form = useForm({
    // PESERTA
    nama: '',
    alamat: '',
    kamar: '',
    lembaga: '',

    // PENDAFTARAN
    program_pelatihan_id: '',
    kelas_id: '',
    tanggal_daftar: new Date().toISOString().split('T')[0],
    status: 'aktif',

    // PEMBAYARAN
    jumlah: '',
    tanggal_bayar: new Date().toISOString().split('T')[0],
    metode: '',
    status_pembayaran: 'pending',
    bukti: null,
    keterangan: '',
})

const handleFile = (event) => {
    form.bukti = event.target.files[0] ?? null
}

const submit = () => {
    form.post(route('pendaftarans.store'), {
        forceFormData: true,
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Daftar Peserta" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">

            <!-- HEADER -->
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="mb-2 flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                        <Link
                            :href="route('pendaftarans.index')"
                            class="hover:text-indigo-600"
                        >
                            Pendaftaran
                        </Link>

                        <span>/</span>

                        <span class="text-slate-700 dark:text-slate-300">
                            Daftar Peserta
                        </span>
                    </div>

                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                        Daftar Peserta
                    </h1>

                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Tambahkan peserta, pendaftaran, dan pembayaran sekaligus.
                    </p>
                </div>

                <Link
                    :href="route('pendaftarans.index')"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                >
                    Kembali
                </Link>
            </div>

            <form @submit.prevent="submit">

                <!-- ===================================================== -->
                <!-- DATA PESERTA -->
                <!-- ===================================================== -->

                <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">

                    <div class="border-b border-slate-200 px-6 py-5 dark:border-slate-800">
                        <div class="flex items-center gap-3">

                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
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
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                    />
                                </svg>
                            </div>

                            <div>
                                <h2 class="font-semibold text-slate-900 dark:text-white">
                                    Data Peserta
                                </h2>

                                <p class="text-sm text-slate-500 dark:text-slate-400">
                                    Informasi peserta baru.
                                </p>
                            </div>

                        </div>
                    </div>

                    <div class="grid gap-6 p-6 md:grid-cols-2">

                        <!-- NAMA -->
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Nama Peserta
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                v-model="form.nama"
                                type="text"
                                placeholder="Masukkan nama lengkap peserta"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            />

                            <p
                                v-if="form.errors.nama"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.nama }}
                            </p>
                        </div>

                        <!-- ALAMAT -->
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Alamat
                            </label>

                            <textarea
                                v-model="form.alamat"
                                rows="3"
                                placeholder="Masukkan alamat peserta"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            ></textarea>
                        </div>

                        <!-- KAMAR -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Kamar
                            </label>

                            <input
                                v-model="form.kamar"
                                type="text"
                                placeholder="Contoh: A-12"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            />
                        </div>

                        <!-- LEMBAGA -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Lembaga
                            </label>

                            <input
                                v-model="form.lembaga"
                                type="text"
                                placeholder="Masukkan lembaga"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            />
                        </div>

                    </div>
                </div>

                <!-- ===================================================== -->
                <!-- DATA PENDAFTARAN -->
                <!-- ===================================================== -->

                <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">

                    <div class="border-b border-slate-200 px-6 py-5 dark:border-slate-800">
                        <h2 class="font-semibold text-slate-900 dark:text-white">
                            Data Pendaftaran
                        </h2>

                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Tentukan program dan kelas peserta.
                        </p>
                    </div>

                    <div class="grid gap-6 p-6 md:grid-cols-2">

                        <!-- PROGRAM -->
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Program Pelatihan
                                <span class="text-red-500">*</span>
                            </label>

                            <select
                                v-model="form.program_pelatihan_id"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                                <option value="">
                                    Pilih Program Pelatihan
                                </option>

                                <option
                                    v-for="program in programPelatihans"
                                    :key="program.id"
                                    :value="program.id"
                                >
                                    {{ program.nama }}
                                    — {{ program.pelatihan?.nama }}
                                    — Tahap {{ program.tahap?.nama }}
                                </option>
                            </select>

                            <p
                                v-if="form.errors.program_pelatihan_id"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.program_pelatihan_id }}
                            </p>
                        </div>

                        <!-- KELAS -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Kelas
                                <span class="text-red-500">*</span>
                            </label>

                            <select
                                v-model="form.kelas_id"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                                <option value="">
                                    Pilih Kelas
                                </option>

                                <option
                                    v-for="kelas in kelass"
                                    :key="kelas.id"
                                    :value="kelas.id"
                                >
                                    {{ kelas.nama }}
                                    ({{ kelas.durasi_hari }} hari)
                                </option>
                            </select>

                            <p
                                v-if="form.errors.kelas_id"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.kelas_id }}
                            </p>
                        </div>

                        <!-- TANGGAL -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Tanggal Daftar
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                v-model="form.tanggal_daftar"
                                type="date"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            />
                        </div>

                        <!-- STATUS -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Status Pendaftaran
                            </label>

                            <select
                                v-model="form.status"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                                <option value="aktif">
                                    Aktif
                                </option>

                                <option value="selesai">
                                    Selesai
                                </option>

                                <option value="batal">
                                    Batal
                                </option>
                            </select>
                        </div>

                    </div>
                </div>

                <!-- ===================================================== -->
                <!-- DATA PEMBAYARAN -->
                <!-- ===================================================== -->

                <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">

                    <div class="border-b border-slate-200 px-6 py-5 dark:border-slate-800">
                        <div class="flex items-center gap-3">

                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
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
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>

                            <div>
                                <h2 class="font-semibold text-slate-900 dark:text-white">
                                    Pembayaran
                                </h2>

                                <p class="text-sm text-slate-500 dark:text-slate-400">
                                    Masukkan pembayaran pertama peserta.
                                </p>
                            </div>

                        </div>
                    </div>

                    <div class="grid gap-6 p-6 md:grid-cols-2">

                        <!-- JUMLAH -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Jumlah Pembayaran
                                <span class="text-red-500">*</span>
                            </label>

                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-500">
                                    Rp
                                </span>

                                <input
                                    v-model="form.jumlah"
                                    type="number"
                                    min="0"
                                    step="1000"
                                    placeholder="0"
                                    class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-12 pr-4 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                />
                            </div>

                            <p
                                v-if="form.errors.jumlah"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.jumlah }}
                            </p>
                        </div>

                        <!-- TANGGAL BAYAR -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Tanggal Bayar
                            </label>

                            <input
                                v-model="form.tanggal_bayar"
                                type="date"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            />
                        </div>

                        <!-- METODE -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Metode Pembayaran
                            </label>

                            <select
                                v-model="form.metode"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                                <option value="">
                                    Pilih Metode
                                </option>

                                <option value="tunai">
                                    Tunai
                                </option>

                                <option value="transfer">
                                    Transfer
                                </option>

                                <option value="qris">
                                    QRIS
                                </option>
                            </select>
                        </div>

                        <!-- STATUS PEMBAYARAN -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Status Pembayaran
                                <span class="text-red-500">*</span>
                            </label>

                            <select
                                v-model="form.status_pembayaran"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                                <option value="pending">
                                    Pending
                                </option>

                                <option value="lunas">
                                    Lunas
                                </option>

                                <option value="ditolak">
                                    Ditolak
                                </option>
                            </select>
                        </div>

                        <!-- BUKTI -->
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Bukti Pembayaran
                            </label>

                            <input
                                type="file"
                                accept=".jpg,.jpeg,.png,.pdf"
                                @change="handleFile"
                                class="block w-full rounded-xl border border-slate-200 bg-white text-sm text-slate-600 file:mr-4 file:border-0 file:bg-indigo-50 file:px-4 file:py-3 file:font-semibold file:text-indigo-600 hover:file:bg-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:file:bg-indigo-500/10 dark:file:text-indigo-400"
                            />

                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                                Format JPG, JPEG, PNG atau PDF. Maksimal 5 MB.
                            </p>

                            <p
                                v-if="form.errors.bukti"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.bukti }}
                            </p>
                        </div>

                        <!-- KETERANGAN -->
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Keterangan
                            </label>

                            <textarea
                                v-model="form.keterangan"
                                rows="3"
                                placeholder="Catatan pembayaran..."
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            ></textarea>
                        </div>

                    </div>
                </div>

                <!-- BUTTON -->
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                    <Link
                        :href="route('pendaftarans.index')"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                    >
                        Batal
                    </Link>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <svg
                            v-if="form.processing"
                            class="h-4 w-4 animate-spin"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            />
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                            />
                        </svg>

                        {{
                            form.processing
                                ? 'Menyimpan...'
                                : 'Daftarkan Peserta'
                        }}
                    </button>

                </div>

            </form>
        </div>
    </div>
</template>
<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    pesertas: {
        type: Array,
        default: () => [],
    },

    programPelatihans: {
        type: Array,
        default: () => [],
    },

    kelass: {
        type: Array,
        default: () => [],
    },
})

const pencarianPeserta = ref('')
const dropdownPesertaTerbuka = ref(false)
const pesertaDipilih = ref(null)

const form = useForm({
    peserta_id: '',

    program_pelatihan_id: '',
    kelas_id: '',
    tanggal_daftar: new Date().toISOString().split('T')[0],
    status: 'aktif',

    jumlah: '',
    tanggal_bayar: new Date().toISOString().split('T')[0],
    metode: '',
    status_pembayaran: 'pending',
    bukti: null,
    keterangan: '',
})

const hasilPencarianPeserta = computed(() => {
    const keyword = pencarianPeserta.value.trim().toLowerCase()

    if (!keyword) {
        return []
    }

    return props.pesertas
        .filter((peserta) => {
            const nama = String(peserta.nama ?? '').toLowerCase()
            const username = String(peserta.username ?? '').toLowerCase()
            const kamar = String(peserta.kamar ?? '').toLowerCase()
            const lembaga = String(peserta.lembaga ?? '').toLowerCase()

            return (
                nama.includes(keyword) ||
                username.includes(keyword) ||
                kamar.includes(keyword) ||
                lembaga.includes(keyword)
            )
        })
        .slice(0, 8)
})

const pilihPeserta = (peserta) => {
    pesertaDipilih.value = peserta
    pencarianPeserta.value = peserta.nama
    form.peserta_id = peserta.id
    dropdownPesertaTerbuka.value = false
}

const cariPeserta = () => {
    // Jika teks diubah setelah memilih peserta,
    // batalkan pilihan lama agar tidak salah mendaftar.
    if (
        pesertaDipilih.value &&
        pencarianPeserta.value !== pesertaDipilih.value.nama
    ) {
        pesertaDipilih.value = null
        form.peserta_id = ''
    }

    dropdownPesertaTerbuka.value = true
}

const handleFile = (event) => {
    form.bukti = event.target.files?.[0] ?? null
}

const submit = () => {
    form.post(route('pendaftarans.store'), {
        forceFormData: true,
        preserveScroll: true,
    })
}

// Bersihkan pilihan peserta jika akun yang tersedia berubah.
watch(
    () => props.pesertas,
    (pesertas) => {
        if (
            pesertaDipilih.value &&
            !pesertas.some((peserta) => peserta.id === pesertaDipilih.value.id)
        ) {
            pesertaDipilih.value = null
            pencarianPeserta.value = ''
            form.peserta_id = ''
        }
    },
)
</script>


<template>
    <Head title="Tambah Pendaftaran" />

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
                            Tambah Pendaftaran
                        </span>
                    </div>

                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                        Tambah Pendaftaran
                    </h1>

                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Pilih akun peserta, tentukan program dan kelas, lalu masukkan pembayaran.
                    </p>
                </div>

                <Link
                    :href="route('pendaftarans.index')"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                >
                    Kembali
                </Link>
            </div>

            <form @submit.prevent="submit">

                <!-- DATA PESERTA -->
                <section class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
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
                                    Pilih Peserta
                                </h2>

                                <p class="text-sm text-slate-500 dark:text-slate-400">
                                    Pilih akun peserta yang sudah dibuat melalui menu Peserta.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <label
                            for="peserta_id"
                            class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300"
                        >
                            Akun Peserta
                            <span class="text-red-500">*</span>
                        </label>

                        <!-- DATA PESERTA -->
                        <section
                            class="mb-6 overflow-visible rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
                        >
                            <div class="border-b border-slate-200 px-6 py-5 dark:border-slate-800">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400"
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
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                            />
                                        </svg>
                                    </div>

                                    <div>
                                        <h2 class="font-semibold text-slate-900 dark:text-white">
                                            Pilih Peserta
                                        </h2>

                                        <p class="text-sm text-slate-500 dark:text-slate-400">
                                            Ketik nama atau username peserta yang sudah memiliki akun.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6">
                                <label
                                    for="cari_peserta"
                                    class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300"
                                >
                                    Nama Peserta
                                    <span class="text-red-500">*</span>
                                </label>

                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                        <svg
                                            class="h-5 w-5 text-slate-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"
                                            />
                                        </svg>
                                    </div>

                                    <input
                                        id="cari_peserta"
                                        :value="pencarianPeserta"
                                        type="text"
                                        autocomplete="off"
                                        placeholder="Ketik nama peserta..."
                                        @input="
                                            pencarianPeserta = $event.target.value;
                                            cariPeserta()
                                        "
                                        @focus="dropdownPesertaTerbuka = true"
                                        @keydown.esc="dropdownPesertaTerbuka = false"
                                        class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-11 pr-4 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                    />

                                    <!-- DAFTAR SARAN -->
                                    <div
                                        v-if="dropdownPesertaTerbuka && pencarianPeserta.trim()"
                                        class="absolute inset-x-0 top-full z-30 mt-2 max-h-72 overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-800"
                                    >
                                        <button
                                            v-for="peserta in hasilPencarianPeserta"
                                            :key="peserta.id"
                                            type="button"
                                            @mousedown.prevent="pilihPeserta(peserta)"
                                            class="flex w-full items-center gap-3 border-b border-slate-100 px-4 py-3 text-left last:border-b-0 hover:bg-indigo-50 dark:border-slate-700 dark:hover:bg-slate-700"
                                        >
                                            <div
                                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 font-semibold text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300"
                                            >
                                                {{ peserta.nama?.charAt(0)?.toUpperCase() ?? '?' }}
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">
                                                    {{ peserta.nama }}
                                                </p>

                                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                                    @{{ peserta.username }}

                                                    <template v-if="peserta.kamar">
                                                        · Kamar {{ peserta.kamar }}
                                                    </template>
                                                </p>
                                            </div>

                                            <svg
                                                class="h-5 w-5 shrink-0 text-slate-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="m9 18 6-6-6-6"
                                                />
                                            </svg>
                                        </button>

                                        <div
                                            v-if="hasilPencarianPeserta.length === 0"
                                            class="px-4 py-5 text-center text-sm text-slate-500 dark:text-slate-400"
                                        >
                                            Peserta tidak ditemukan.
                                        </div>
                                    </div>
                                </div>

                                <!-- PESERTA TERPILIH -->
                                <div
                                    v-if="pesertaDipilih"
                                    class="mt-4 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-900 dark:bg-emerald-950/30"
                                >
                                    <svg
                                        class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600 dark:text-emerald-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="m5 13 4 4L19 7"
                                        />
                                    </svg>

                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">
                                            Peserta dipilih
                                        </p>

                                        <p class="mt-1 text-sm text-emerald-700 dark:text-emerald-400">
                                            {{ pesertaDipilih.nama }}
                                        </p>

                                        <p class="mt-1 text-xs text-emerald-700 dark:text-emerald-400">
                                            Username: {{ pesertaDipilih.username }}

                                            <template v-if="pesertaDipilih.kamar">
                                                · Kamar {{ pesertaDipilih.kamar }}
                                            </template>

                                            <template v-if="pesertaDipilih.lembaga">
                                                · {{ pesertaDipilih.lembaga }}
                                            </template>
                                        </p>
                                    </div>

                                    <button
                                        type="button"
                                        @click="
                                            pesertaDipilih = null;
                                            pencarianPeserta = '';
                                            form.peserta_id = '';
                                        "
                                        class="text-sm font-semibold text-emerald-700 hover:text-emerald-900 dark:text-emerald-400"
                                    >
                                        Ganti
                                    </button>
                                </div>

                                <p
                                    v-if="form.errors.peserta_id"
                                    class="mt-2 text-xs text-red-500"
                                >
                                    {{ form.errors.peserta_id }}
                                </p>

                                <div
                                    v-if="props.pesertas.length === 0"
                                    class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-300"
                                >
                                    Belum ada akun peserta aktif. Buat akun terlebih dahulu melalui menu Peserta.
                                </div>
                            </div>
                        </section>

                        <p
                            v-if="form.errors.peserta_id"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.peserta_id }}
                        </p>

                        <div
                            v-if="props.pesertas.length === 0"
                            class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-300"
                        >
                            Belum ada akun peserta aktif. Buat akun terlebih dahulu melalui menu Peserta.
                        </div>

                        <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                            Data akun peserta tidak dibuat ulang atau diubah pada formulir ini.
                        </p>
                    </div>
                </section>

                <!-- DATA PENDAFTARAN -->
                <section class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="border-b border-slate-200 px-6 py-5 dark:border-slate-800">
                        <h2 class="font-semibold text-slate-900 dark:text-white">
                            Data Pendaftaran
                        </h2>

                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Tentukan program pelatihan dan kelas yang diikuti.
                        </p>
                    </div>

                    <div class="grid gap-6 p-6 md:grid-cols-2">

                        <!-- PROGRAM -->
                        <div class="md:col-span-2">
                            <label
                                for="program_pelatihan_id"
                                class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300"
                            >
                                Program Pelatihan
                                <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="program_pelatihan_id"
                                v-model="form.program_pelatihan_id"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                                <option value="">
                                    Pilih Program Pelatihan
                                </option>

                                <option
                                    v-for="program in props.programPelatihans"
                                    :key="program.id"
                                    :value="program.id"
                                >
                                    {{ program.nama }}
                                    <template v-if="program.pelatihan?.nama">
                                        — {{ program.pelatihan.nama }}
                                    </template>
                                    <template v-if="program.tahap?.nama">
                                        — Tahap {{ program.tahap.nama }}
                                    </template>
                                </option>
                            </select>

                            <p
                                v-if="form.errors.program_pelatihan_id"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.program_pelatihan_id }}
                            </p>

                            <p
                                v-if="props.programPelatihans.length === 0"
                                class="mt-2 text-xs text-amber-600 dark:text-amber-400"
                            >
                                Belum ada program pelatihan yang sedang dibuka.
                            </p>
                        </div>

                        <!-- KELAS -->
                        <div>
                            <label
                                for="kelas_id"
                                class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300"
                            >
                                Kelas
                                <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="kelas_id"
                                v-model="form.kelas_id"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                                <option value="">
                                    Pilih Kelas
                                </option>

                                <option
                                    v-for="kelas in props.kelass"
                                    :key="kelas.id"
                                    :value="kelas.id"
                                >
                                    {{ kelas.nama }}
                                    <template v-if="kelas.durasi_hari">
                                        ({{ kelas.durasi_hari }} hari)
                                    </template>
                                </option>
                            </select>

                            <p
                                v-if="form.errors.kelas_id"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.kelas_id }}
                            </p>
                        </div>

                        <!-- TANGGAL DAFTAR -->
                        <div>
                            <label
                                for="tanggal_daftar"
                                class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300"
                            >
                                Tanggal Daftar
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                id="tanggal_daftar"
                                v-model="form.tanggal_daftar"
                                type="date"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            />

                            <p
                                v-if="form.errors.tanggal_daftar"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.tanggal_daftar }}
                            </p>
                        </div>

                        <!-- STATUS -->
                        <div class="md:col-span-2">
                            <label
                                for="status"
                                class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300"
                            >
                                Status Pendaftaran
                            </label>

                            <select
                                id="status"
                                v-model="form.status"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                                <option value="aktif">Aktif</option>
                                <option value="selesai">Selesai</option>
                                <option value="batal">Batal</option>
                            </select>

                            <p
                                v-if="form.errors.status"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.status }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- DATA PEMBAYARAN -->
                <section class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
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
                                    Masukkan data pembayaran awal untuk pendaftaran ini.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-6 p-6 md:grid-cols-2">

                        <!-- JUMLAH -->
                        <div>
                            <label
                                for="jumlah"
                                class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300"
                            >
                                Jumlah Pembayaran (Rp)
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                id="jumlah"
                                v-model="form.jumlah"
                                type="number"
                                min="0"
                                step="1"
                                required
                                placeholder="Masukkan jumlah pembayaran"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            />

                            <p
                                v-if="form.errors.jumlah"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.jumlah }}
                            </p>
                        </div>

                        <!-- TANGGAL BAYAR -->
                        <div>
                            <label
                                for="tanggal_bayar"
                                class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300"
                            >
                                Tanggal Bayar
                            </label>

                            <input
                                id="tanggal_bayar"
                                v-model="form.tanggal_bayar"
                                type="date"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            />

                            <p
                                v-if="form.errors.tanggal_bayar"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.tanggal_bayar }}
                            </p>
                        </div>

                        <!-- METODE -->
                        <div>
                            <label
                                for="metode"
                                class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300"
                            >
                                Metode Pembayaran
                            </label>

                            <select
                                id="metode"
                                v-model="form.metode"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                                <option value="">Pilih Metode</option>
                                <option value="tunai">Tunai</option>
                                <option value="transfer">Transfer</option>
                                <option value="qris">QRIS</option>
                            </select>

                            <p
                                v-if="form.errors.metode"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.metode }}
                            </p>
                        </div>

                        <!-- STATUS PEMBAYARAN -->
                        <div>
                            <label
                                for="status_pembayaran"
                                class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300"
                            >
                                Status Pembayaran
                                <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="status_pembayaran"
                                v-model="form.status_pembayaran"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                                <option value="pending">Pending</option>
                                <option value="lunas">Lunas</option>
                                <option value="ditolak">Ditolak</option>
                            </select>

                            <p
                                v-if="form.errors.status_pembayaran"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.status_pembayaran }}
                            </p>
                        </div>

                        <!-- BUKTI -->
                        <div class="md:col-span-2">
                            <label
                                for="bukti"
                                class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300"
                            >
                                Bukti Pembayaran
                            </label>

                            <input
                                id="bukti"
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
                            <label
                                for="keterangan"
                                class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300"
                            >
                                Keterangan
                            </label>

                            <textarea
                                id="keterangan"
                                v-model="form.keterangan"
                                rows="3"
                                placeholder="Catatan pembayaran..."
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            ></textarea>

                            <p
                                v-if="form.errors.keterangan"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.keterangan }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- ERROR UMUM -->
                <div
                    v-if="form.errors.pembayaran"
                    class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300"
                >
                    {{ form.errors.pembayaran }}
                </div>

                <!-- TOMBOL -->
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <Link
                        :href="route('pendaftarans.index')"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                    >
                        Batal
                    </Link>

                    <button
                        type="submit"
                        :disabled="
                            form.processing ||
                            props.pesertas.length === 0 ||
                            props.programPelatihans.length === 0
                        "
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
                                : 'Simpan Pendaftaran'
                        }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
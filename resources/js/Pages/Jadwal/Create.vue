<script setup>
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
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
    program_pelatihan_id: '',
    kelas_ids: [],
    pertemuan: 1,
    tanggal: '',
    jam_mulai: '',
    jam_selesai: '',
    kegiatan: '',
    keterangan: '',
    status: 'belum_dimulai',
})

const selectedKelas = computed(() => {
    return props.kelass.find(
        item => Number(item.id) === Number(form.kelas_ids[0])
    )
})

const maxPertemuan = computed(() => {
    return selectedKelas.value?.durasi_hari ?? null
})

const submit = () => {
    form.post(route('jadwals.store'), {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Tambah Jadwal" />

    <div class="min-h-screen bg-slate-50 px-4 py-6 dark:bg-slate-950 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl">

            <!-- HEADER -->
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm"
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
                                stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                            />
                        </svg>
                    </div>

                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                            Tambah Jadwal
                        </h1>

                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            Tambahkan jadwal kegiatan pelatihan
                        </p>
                    </div>
                </div>

                <Link
                    :href="route('jadwals.index')"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
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
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"
                        />
                    </svg>

                    Kembali
                </Link>
            </div>

            <!-- FORM -->
            <form @submit.prevent="submit">

                <!-- PROGRAM -->
                <div
                    class="mb-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div class="mb-6">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">
                            Program Pelatihan
                        </h2>

                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Pilih program dan kelas yang memiliki jadwal.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Program Pelatihan
                                <span class="text-red-500">*</span>
                            </label>

                            <select
                                v-model="form.program_pelatihan_id"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                                <option value="">
                                    Pilih program pelatihan
                                </option>

                                <option
                                    v-for="program in programPelatihans"
                                    :key="program.id"
                                    :value="program.id"
                                >
                                    {{ program.nama }}
                                    — {{ program.pelatihan?.nama ?? '' }}
                                    <template v-if="program.tahap">
                                        ({{ program.tahap.nama }})
                                    </template>
                                </option>
                            </select>

                            <p
                                v-if="form.errors.program_pelatihan_id"
                                class="mt-2 text-xs font-medium text-red-600"
                            >
                                {{ form.errors.program_pelatihan_id }}
                            </p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium">
                                Kelas
                            </label>

                            <div class="space-y-2 rounded-lg border p-4">
                                <label
                                    v-for="kelas in kelass"
                                    :key="kelas.id"
                                    class="flex cursor-pointer items-center gap-3"
                                >
                                    <input
                                        type="checkbox"
                                        :value="kelas.id"
                                        v-model="form.kelas_ids"
                                        class="rounded border-gray-300"
                                    />

                                    <div>
                                        <div class="font-medium">
                                            {{ kelas.nama }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ kelas.durasi_hari }} hari
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <p
                                v-if="form.errors.kelas_ids"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.kelas_ids }}
                            </p>
                        </div>

                    </div>
                </div>

                <!-- DETAIL JADWAL -->
                <div
                    class="mb-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div class="mb-6">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">
                            Detail Jadwal
                        </h2>

                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Tentukan pertemuan, tanggal, dan waktu kegiatan.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                        <!-- PERTEMUAN -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Pertemuan
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                v-model.number="form.pertemuan"
                                type="number"
                                min="1"
                                :max="maxPertemuan || undefined"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            />

                            <p
                                v-if="maxPertemuan"
                                class="mt-2 text-xs text-slate-500 dark:text-slate-400"
                            >
                                Maksimal {{ maxPertemuan }} pertemuan untuk kelas ini.
                            </p>

                            <p
                                v-if="form.errors.pertemuan"
                                class="mt-2 text-xs font-medium text-red-600"
                            >
                                {{ form.errors.pertemuan }}
                            </p>
                        </div>

                        <!-- TANGGAL -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Tanggal
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                v-model="form.tanggal"
                                type="date"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            />

                            <p
                                v-if="form.errors.tanggal"
                                class="mt-2 text-xs font-medium text-red-600"
                            >
                                {{ form.errors.tanggal }}
                            </p>
                        </div>

                        <!-- JAM MULAI -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Jam Mulai
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                v-model="form.jam_mulai"
                                type="time"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            />

                            <p
                                v-if="form.errors.jam_mulai"
                                class="mt-2 text-xs font-medium text-red-600"
                            >
                                {{ form.errors.jam_mulai }}
                            </p>
                        </div>

                        <!-- JAM SELESAI -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Jam Selesai
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                v-model="form.jam_selesai"
                                type="time"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            />

                            <p
                                v-if="form.errors.jam_selesai"
                                class="mt-2 text-xs font-medium text-red-600"
                            >
                                {{ form.errors.jam_selesai }}
                            </p>
                        </div>

                    </div>
                </div>

                <!-- KEGIATAN -->
                <div
                    class="mb-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div class="mb-6">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">
                            Kegiatan
                        </h2>

                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Tambahkan informasi kegiatan dan status jadwal.
                        </p>
                    </div>

                    <div class="space-y-5">

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Nama Kegiatan
                            </label>

                            <input
                                v-model="form.kegiatan"
                                type="text"
                                placeholder="Contoh: Praktik Dasar Komputer"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            />

                            <p
                                v-if="form.errors.kegiatan"
                                class="mt-2 text-xs font-medium text-red-600"
                            >
                                {{ form.errors.kegiatan }}
                            </p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Keterangan
                            </label>

                            <textarea
                                v-model="form.keterangan"
                                rows="4"
                                placeholder="Tambahkan keterangan jadwal..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            ></textarea>

                            <p
                                v-if="form.errors.keterangan"
                                class="mt-2 text-xs font-medium text-red-600"
                            >
                                {{ form.errors.keterangan }}
                            </p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Status
                                <span class="text-red-500">*</span>
                            </label>

                            <select
                                v-model="form.status"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                                <option value="belum_dimulai">
                                    Belum Dimulai
                                </option>

                                <option value="berlangsung">
                                    Berlangsung
                                </option>

                                <option value="selesai">
                                    Selesai
                                </option>

                                <option value="dibatalkan">
                                    Dibatalkan
                                </option>
                            </select>

                            <p
                                v-if="form.errors.status"
                                class="mt-2 text-xs font-medium text-red-600"
                            >
                                {{ form.errors.status }}
                            </p>
                        </div>

                    </div>
                </div>

                <!-- ACTION -->
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <Link
                        :href="route('jadwals.index')"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                    >
                        Batal
                    </Link>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <svg
                            v-if="form.processing"
                            class="h-5 w-5 animate-spin"
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

                        {{ form.processing ? 'Menyimpan...' : 'Simpan Jadwal' }}
                    </button>
                </div>

            </form>
        </div>
    </div>
</template>
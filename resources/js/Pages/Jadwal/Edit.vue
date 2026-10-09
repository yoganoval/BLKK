<script setup>
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    jadwal: {
        type: Object,
        required: true,
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

/*
|--------------------------------------------------------------------------
| Form
|--------------------------------------------------------------------------
|
| jadwal.kelas sekarang berupa Collection/Array karena belongsToMany.
| Jadi kita ambil semua ID kelas yang sudah terhubung.
|
*/

const form = useForm({
    program_pelatihan_id: props.jadwal.program_pelatihan_id ?? '',

    kelas_ids: Array.isArray(props.jadwal.kelas)
        ? props.jadwal.kelas.map(kelas => Number(kelas.id))
        : [],

    pertemuan: props.jadwal.pertemuan ?? 1,

    tanggal: props.jadwal.tanggal
        ? String(props.jadwal.tanggal).substring(0, 10)
        : '',

    jam_mulai: props.jadwal.jam_mulai
        ? String(props.jadwal.jam_mulai).substring(0, 5)
        : '',

    jam_selesai: props.jadwal.jam_selesai
        ? String(props.jadwal.jam_selesai).substring(0, 5)
        : '',

    kegiatan: props.jadwal.kegiatan ?? '',
    keterangan: props.jadwal.keterangan ?? '',
    status: props.jadwal.status ?? 'belum_dimulai',
})

/*
|--------------------------------------------------------------------------
| Selected Classes
|--------------------------------------------------------------------------
*/

const selectedKelass = computed(() => {
    return props.kelass.filter(kelas =>
        form.kelas_ids.includes(Number(kelas.id))
    )
})

/*
|--------------------------------------------------------------------------
| Maksimal Pertemuan
|--------------------------------------------------------------------------
|
| Karena bisa memilih beberapa kelas, kita gunakan durasi
| terkecil dari semua kelas yang dipilih.
|
*/

const maxPertemuan = computed(() => {
    if (selectedKelass.value.length === 0) {
        return null
    }

    return Math.min(
        ...selectedKelass.value.map(kelas => Number(kelas.durasi_hari))
    )
})

/*
|--------------------------------------------------------------------------
| Toggle Kelas
|--------------------------------------------------------------------------
*/

const toggleKelas = (kelasId) => {
    const id = Number(kelasId)

    if (form.kelas_ids.includes(id)) {
        form.kelas_ids = form.kelas_ids.filter(item => item !== id)
    } else {
        form.kelas_ids.push(id)
    }
}

/*
|--------------------------------------------------------------------------
| Submit
|--------------------------------------------------------------------------
*/

const submit = () => {
    form.put(route('jadwals.update', props.jadwal.id), {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Edit Jadwal" />

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
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.5-8.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 8.5-8.5z"
                            />
                        </svg>
                    </div>

                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                            Edit Jadwal
                        </h1>

                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            Perbarui informasi jadwal kegiatan
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

            <form @submit.prevent="submit">

                <!-- PROGRAM & KELAS -->
                <div
                    class="mb-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div class="mb-6">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">
                            Program Pelatihan
                        </h2>

                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Perbarui program dan kelas yang mengikuti jadwal ini.
                        </p>
                    </div>

                    <div class="space-y-5">

                        <!-- PROGRAM -->
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

                        <!-- KELAS -->
                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                    Kelas
                                    <span class="text-red-500">*</span>
                                </label>

                                <span
                                    v-if="form.kelas_ids.length"
                                    class="text-xs font-medium text-indigo-600 dark:text-indigo-400"
                                >
                                    {{ form.kelas_ids.length }} kelas dipilih
                                </span>
                            </div>

                            <div
                                class="grid grid-cols-1 gap-3 sm:grid-cols-2"
                            >
                                <label
                                    v-for="kelas in kelass"
                                    :key="kelas.id"
                                    class="group flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4 transition hover:border-indigo-300 hover:bg-indigo-50/50 dark:border-slate-700 dark:bg-slate-800/60 dark:hover:border-indigo-500 dark:hover:bg-indigo-400/10"
                                    :class="{
                                        'border-indigo-500 bg-indigo-50 dark:border-indigo-500 dark:bg-indigo-400/10':
                                            form.kelas_ids.includes(Number(kelas.id))
                                    }"
                                >
                                    <input
                                        type="checkbox"
                                        :checked="form.kelas_ids.includes(Number(kelas.id))"
                                        @change="toggleKelas(kelas.id)"
                                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-700"
                                    />

                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white">
                                            {{ kelas.nama }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                            {{ kelas.durasi_hari }} hari
                                        </p>
                                    </div>

                                    <svg
                                        v-if="form.kelas_ids.includes(Number(kelas.id))"
                                        class="h-5 w-5 shrink-0 text-indigo-600 dark:text-indigo-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 13l4 4L19 7"
                                        />
                                    </svg>
                                </label>
                            </div>

                            <p
                                v-if="kelass.length === 0"
                                class="rounded-xl bg-slate-50 p-4 text-sm text-slate-500 dark:bg-slate-800 dark:text-slate-400"
                            >
                                Belum ada data kelas.
                            </p>

                            <p
                                v-if="form.errors.kelas_ids"
                                class="mt-2 text-xs font-medium text-red-600"
                            >
                                {{ form.errors.kelas_ids }}
                            </p>

                            <p
                                v-if="form.errors['kelas_ids.0']"
                                class="mt-2 text-xs font-medium text-red-600"
                            >
                                {{ form.errors['kelas_ids.0'] }}
                            </p>

                            <!-- KELAS TERPILIH -->
                            <div
                                v-if="selectedKelass.length"
                                class="mt-4 rounded-xl border border-indigo-100 bg-indigo-50 p-4 dark:border-indigo-400/20 dark:bg-indigo-400/10"
                            >
                                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600 dark:text-indigo-400">
                                    Kelas Terpilih
                                </p>

                                <div class="mt-2 flex flex-wrap gap-2">
                                    <span
                                        v-for="kelas in selectedKelass"
                                        :key="kelas.id"
                                        class="inline-flex items-center rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-indigo-700 shadow-sm dark:bg-slate-800 dark:text-indigo-400"
                                    >
                                        {{ kelas.nama }}
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- DETAIL -->
                <div
                    class="mb-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div class="mb-6">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">
                            Detail Jadwal
                        </h2>

                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Atur waktu pelaksanaan jadwal.
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
                                Maksimal {{ maxPertemuan }} pertemuan berdasarkan
                                kelas dengan durasi paling pendek.
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
                            Informasi kegiatan dan status jadwal.
                        </p>
                    </div>

                    <div class="space-y-5">

                        <!-- KEGIATAN -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Nama Kegiatan
                            </label>

                            <input
                                v-model="form.kegiatan"
                                type="text"
                                placeholder="Nama kegiatan"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            />

                            <p
                                v-if="form.errors.kegiatan"
                                class="mt-2 text-xs font-medium text-red-600"
                            >
                                {{ form.errors.kegiatan }}
                            </p>
                        </div>

                        <!-- KETERANGAN -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Keterangan
                            </label>

                            <textarea
                                v-model="form.keterangan"
                                rows="4"
                                placeholder="Keterangan..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            ></textarea>

                            <p
                                v-if="form.errors.keterangan"
                                class="mt-2 text-xs font-medium text-red-600"
                            >
                                {{ form.errors.keterangan }}
                            </p>
                        </div>

                        <!-- STATUS -->
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
                        :disabled="form.processing || form.kelas_ids.length === 0"
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

                        {{
                            form.processing
                                ? 'Menyimpan...'
                                : 'Simpan Perubahan'
                        }}
                    </button>

                </div>

            </form>
        </div>
    </div>
</template>
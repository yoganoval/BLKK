
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
    judul: '',
    deskripsi: '',
    isi_materi: '',
    // urutan: 1,
    status: 'draft',
})

const programTerpilih = computed(() => {
    return props.programPelatihans.find(
        program =>
            Number(program.id) === Number(form.program_pelatihan_id)
    )
})

const jumlahKelasDipilih = computed(() => form.kelas_ids.length)

const semuaKelasDipilih = computed(() => {
    return props.kelass.length > 0 &&
        props.kelass.every(kelas =>
            form.kelas_ids.includes(Number(kelas.id))
        )
})

function toggleSemuaKelas() {
    if (semuaKelasDipilih.value) {
        form.kelas_ids = []
    } else {
        form.kelas_ids = props.kelass.map(kelas => Number(kelas.id))
    }
}

function submit() {
    form.post(route('materi.store'), {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Tambah Materi" />

    <div class="min-h-screen bg-slate-50 px-4 py-6 dark:bg-slate-950 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl">

            <!-- HEADER -->
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm">
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
                                d="M12 4v16m8-8H4"
                            />
                        </svg>
                    </div>

                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                            Tambah Materi
                        </h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            Tambahkan materi untuk program dan beberapa kelas.
                        </p>
                    </div>
                </div>

                <Link
                    :href="route('materi.index')"
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

                <!-- PROGRAM PELATIHAN -->
                <section class="mb-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="mb-5">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">
                            Program Pelatihan
                        </h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Pilih program beserta tahap yang sesuai.
                        </p>
                    </div>

                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                        Program Pelatihan
                        <span class="text-red-500">*</span>
                    </label>

                    <select
                        v-model="form.program_pelatihan_id"
                        required
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
                            — {{ program.pelatihan?.nama ?? '-' }}
                            — {{ program.tahap?.nama ?? 'Tanpa tahap' }}
                        </option>
                    </select>

                    <p
                        v-if="form.errors.program_pelatihan_id"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.program_pelatihan_id }}
                    </p>

                    <div
                        v-if="programTerpilih"
                        class="mt-4 rounded-xl border border-indigo-100 bg-indigo-50 p-4 dark:border-indigo-900 dark:bg-indigo-950/40"
                    >
                        <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600 dark:text-indigo-400">
                            Program terpilih
                        </p>

                        <p class="mt-1 font-semibold text-slate-900 dark:text-white">
                            {{ programTerpilih.nama }}
                        </p>

                        <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                            Pelatihan:
                            {{ programTerpilih.pelatihan?.nama ?? '-' }}
                        </p>

                        <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                            Tahap:
                            {{ programTerpilih.tahap?.nama ?? 'Belum ditentukan' }}
                        </p>
                    </div>
                </section>

                <!-- PILIH KELAS -->
                <section class="mb-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="mb-5">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">
                            Kelas Penerima Materi
                        </h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Centang semua kelas yang boleh mengakses materi ini.
                        </p>
                    </div>

                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-xl bg-slate-50 p-4 dark:bg-slate-800">
                        <label class="flex cursor-pointer items-center gap-3 text-sm font-semibold text-slate-700 dark:text-slate-200">
                            <input
                                type="checkbox"
                                :checked="semuaKelasDipilih"
                                :disabled="kelass.length === 0"
                                class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                @change="toggleSemuaKelas"
                            />
                            Pilih semua kelas
                        </label>

                        <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300">
                            {{ jumlahKelasDipilih }} kelas dipilih
                        </span>
                    </div>

                    <div
                        v-if="kelass.length > 0"
                        class="grid grid-cols-1 gap-3 sm:grid-cols-2"
                    >
                        <label
                            v-for="kelas in kelass"
                            :key="kelas.id"
                            class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition"
                            :class="form.kelas_ids.includes(Number(kelas.id))
                                ? 'border-indigo-500 bg-indigo-50 dark:border-indigo-500 dark:bg-indigo-950/40'
                                : 'border-slate-200 hover:border-indigo-300 dark:border-slate-700 dark:hover:border-indigo-700'"
                        >
                            <input
                                v-model="form.kelas_ids"
                                type="checkbox"
                                :value="Number(kelas.id)"
                                class="mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            />

                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-slate-900 dark:text-white">
                                    {{ kelas.nama }}
                                </p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                    Durasi {{ kelas.durasi_hari }} hari
                                </p>
                            </div>
                        </label>
                    </div>

                    <div
                        v-else
                        class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500 dark:border-slate-700"
                    >
                        Belum ada data kelas.
                    </div>

                    <p
                        v-if="form.errors.kelas_ids"
                        class="mt-3 text-sm text-red-600"
                    >
                        {{ form.errors.kelas_ids }}
                    </p>

                    <p
                        v-for="(error, index) in Object.keys(form.errors)
                            .filter(key => key.startsWith('kelas_ids.'))
                            .map(key => form.errors[key])"
                        :key="index"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ error }}
                    </p>
                </section>

                <!-- DETAIL MATERI -->
                <section class="mb-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="mb-5">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">
                            Detail Materi
                        </h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Lengkapi informasi materi.
                        </p>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Judul Materi
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                v-model="form.judul"
                                type="text"
                                required
                                maxlength="255"
                                placeholder="Contoh: Pengenalan HTML"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            />

                            <p v-if="form.errors.judul" class="mt-2 text-sm text-red-600">
                                {{ form.errors.judul }}
                            </p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Deskripsi
                            </label>

                            <textarea
                                v-model="form.deskripsi"
                                rows="3"
                                placeholder="Penjelasan singkat materi..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            ></textarea>

                            <p v-if="form.errors.deskripsi" class="mt-2 text-sm text-red-600">
                                {{ form.errors.deskripsi }}
                            </p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Isi Materi
                            </label>

                            <textarea
                                v-model="form.isi_materi"
                                rows="8"
                                placeholder="Tuliskan isi materi di sini..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            ></textarea>

                            <p v-if="form.errors.isi_materi" class="mt-2 text-sm text-red-600">
                                {{ form.errors.isi_materi }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <!-- <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                    Urutan
                                    <span class="text-red-500">*</span>
                                </label>

                                <input
                                    v-model.number="form.urutan"
                                    type="number"
                                    min="1"
                                    required
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                />

                                <p v-if="form.errors.urutan" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.urutan }}
                                </p>
                            </div> -->

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                    Status
                                    <span class="text-red-500">*</span>
                                </label>

                                <select
                                    v-model="form.status"
                                    required
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                >
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                </select>

                                <p v-if="form.errors.status" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.status }}
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- TOMBOL -->
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <Link
                        :href="route('materi.index')"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                    >
                        Batal
                    </Link>

                    <button
                        type="submit"
                        :disabled="form.processing || kelass.length === 0"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <svg
                            v-if="form.processing"
                            class="h-5 w-5 animate-spin"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                        </svg>

                        {{ form.processing ? 'Menyimpan...' : 'Simpan Materi' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
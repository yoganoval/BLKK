<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    pendaftaran: {
        type: Object,
        required: true,
    },

    pesertas: {
        type: Array,
        default: () => [],
    },

    programPelatihans: {
        type: Array,
        default: () => [],
    },

    kelas: {
        type: Array,
        default: () => [],
    },
})

const form = useForm({
    peserta_id: props.pendaftaran.peserta_id,
    program_pelatihan_id: props.pendaftaran.program_pelatihan_id,
    kelas_id: props.pendaftaran.kelas_id,
    tanggal_daftar: props.pendaftaran.tanggal_daftar,
    status: props.pendaftaran.status,
})

const submit = () => {
    form.put(route('pendaftarans.update', props.pendaftaran.id))
}
</script>

<template>
    <Head title="Edit Pendaftaran" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
        <div class="mx-auto max-w-4xl px-4 py-6 sm:px-6 lg:px-8">

            <!-- HEADER -->
            <div class="mb-6">
                <div class="mb-4 flex items-center gap-2 text-sm text-slate-500">
                    <Link
                        :href="route('pendaftarans.index')"
                        class="hover:text-indigo-600"
                    >
                        Pendaftaran
                    </Link>

                    <span>/</span>

                    <span class="text-slate-700 dark:text-slate-300">
                        Edit
                    </span>
                </div>

                <h1 class="text-xl font-bold text-slate-800 dark:text-white">
                    Edit Pendaftaran
                </h1>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Perbarui data pendaftaran peserta
                </p>
            </div>

            <!-- FORM -->
            <form
                @submit.prevent="submit"
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="p-6 sm:p-8">

                    <div class="grid grid-cols-1 gap-6">

                        <!-- PESERTA -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                Peserta
                            </label>

                            <select
                                v-model="form.peserta_id"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                            >
                                <option
                                    v-for="peserta in pesertas"
                                    :key="peserta.id"
                                    :value="peserta.id"
                                >
                                    {{ peserta.nama }}
                                </option>
                            </select>

                            <p v-if="form.errors.peserta_id" class="mt-1 text-xs text-red-500">
                                {{ form.errors.peserta_id }}
                            </p>
                        </div>

                        <!-- PROGRAM -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                Program Pelatihan
                            </label>

                            <select
                                v-model="form.program_pelatihan_id"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                            >
                                <option
                                    v-for="program in programPelatihans"
                                    :key="program.id"
                                    :value="program.id"
                                >
                                    {{ program.nama }}
                                    - {{ program.pelatihan?.nama || '' }}
                                    (Tahap {{ program.tahap?.urutan || '-' }})
                                </option>
                            </select>

                            <p v-if="form.errors.program_pelatihan_id" class="mt-1 text-xs text-red-500">
                                {{ form.errors.program_pelatihan_id }}
                            </p>
                        </div>

                        <!-- KELAS -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                Kelas
                            </label>

                            <select
                                v-model="form.kelas_id"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                            >
                                <option
                                    v-for="item in kelas"
                                    :key="item.id"
                                    :value="item.id"
                                >
                                    {{ item.nama }}
                                    - {{ item.durasi_hari }} Hari
                                </option>
                            </select>

                            <p v-if="form.errors.kelas_id" class="mt-1 text-xs text-red-500">
                                {{ form.errors.kelas_id }}
                            </p>
                        </div>

                        <!-- TANGGAL -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                Tanggal Daftar
                            </label>

                            <input
                                v-model="form.tanggal_daftar"
                                type="date"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                            />

                            <p v-if="form.errors.tanggal_daftar" class="mt-1 text-xs text-red-500">
                                {{ form.errors.tanggal_daftar }}
                            </p>
                        </div>

                        <!-- STATUS -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                Status
                            </label>

                            <select
                                v-model="form.status"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                            >
                                <option value="aktif">Aktif</option>
                                <option value="selesai">Selesai</option>
                                <option value="batal">Batal</option>
                            </select>

                            <p v-if="form.errors.status" class="mt-1 text-xs text-red-500">
                                {{ form.errors.status }}
                            </p>
                        </div>

                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-800/40">
                    <Link
                        :href="route('pendaftarans.index')"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                    >
                        Batal
                    </Link>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-60"
                    >
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</template>
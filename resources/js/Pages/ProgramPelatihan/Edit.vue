<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    programPelatihan: {
        type: Object,
        required: true,
    },

    pelatihans: {
        type: Array,
        default: () => [],
    },

    tahaps: {
        type: Array,
        default: () => [],
    },
})

const form = useForm({
    pelatihan_id: props.programPelatihan.pelatihan_id,
    tahap_id: props.programPelatihan.tahap_id,
    nama: props.programPelatihan.nama,
    kuota: props.programPelatihan.kuota,
    status: props.programPelatihan.status,
})

const submit = () => {
    form.put(
        route(
            'program-pelatihans.update',
            props.programPelatihan.id
        )
    )
}
</script>

<template>
    <Head title="Edit Program Pelatihan" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">

        <div class="mx-auto max-w-4xl px-4 py-6 sm:px-6 lg:px-8">

            <!-- HEADER -->
            <div class="mb-6">

                <div class="mb-4 flex items-center gap-2 text-sm text-slate-500">

                    <Link
                        :href="route('program-pelatihans.index')"
                        class="hover:text-indigo-600"
                    >
                        Program Pelatihan
                    </Link>

                    <span>/</span>

                    <span>
                        Edit
                    </span>

                </div>

                <h1 class="text-xl font-bold text-slate-800 dark:text-white">
                    Edit Program Pelatihan
                </h1>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Perbarui data program pelatihan
                </p>

            </div>

            <!-- FORM -->
            <form
                @submit.prevent="submit"
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >

                <div class="p-6 sm:p-8">

                    <div class="grid grid-cols-1 gap-6">

                        <!-- PELATIHAN -->
                        <div>

                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                Pelatihan
                            </label>

                            <select
                                v-model="form.pelatihan_id"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                            >

                                <option
                                    v-for="pelatihan in pelatihans"
                                    :key="pelatihan.id"
                                    :value="pelatihan.id"
                                >
                                    {{ pelatihan.nama }}
                                </option>

                            </select>

                            <p
                                v-if="form.errors.pelatihan_id"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.pelatihan_id }}
                            </p>

                        </div>

                        <!-- TAHAP -->
                        <div>

                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                Tahap
                            </label>

                            <select
                                v-model="form.tahap_id"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                            >

                                <option
                                    v-for="tahap in tahaps"
                                    :key="tahap.id"
                                    :value="tahap.id"
                                >
                                    Tahap {{ tahap.urutan }} - {{ tahap.nama }}
                                </option>

                            </select>

                            <p
                                v-if="form.errors.tahap_id"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.tahap_id }}
                            </p>

                        </div>

                        <!-- NAMA -->
                        <div>

                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                Nama Program
                            </label>

                            <input
                                v-model="form.nama"
                                type="text"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                            />

                            <p
                                v-if="form.errors.nama"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.nama }}
                            </p>

                        </div>

                        <!-- KUOTA + STATUS -->
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                            <div>

                                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    Kuota
                                </label>

                                <input
                                    v-model="form.kuota"
                                    type="number"
                                    min="1"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                                />

                                <p
                                    v-if="form.errors.kuota"
                                    class="mt-1 text-xs text-red-500"
                                >
                                    {{ form.errors.kuota }}
                                </p>

                            </div>

                            <div>

                                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    Status
                                </label>

                                <select
                                    v-model="form.status"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                                >

                                    <option value="dibuka">
                                        Dibuka
                                    </option>

                                    <option value="ditutup">
                                        Ditutup
                                    </option>

                                </select>

                            </div>

                        </div>

                        <!-- INFO KUOTA -->
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800">

                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Catatan Kuota
                            </p>

                            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                                Kuota tidak boleh lebih kecil dari jumlah peserta aktif
                                yang sudah terdaftar pada program ini.
                            </p>

                        </div>

                    </div>

                </div>

                <!-- ACTION -->
                <div class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-800/40">

                    <Link
                        :href="route('program-pelatihans.index')"
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
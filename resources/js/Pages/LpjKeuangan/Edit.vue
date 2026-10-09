<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
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

const form = useForm({
    kode_lpj: props.lpj.kode_lpj || '',
    periode_mulai: props.lpj.periode_mulai
        ? String(props.lpj.periode_mulai).substring(0, 10)
        : '',
    periode_selesai: props.lpj.periode_selesai
        ? String(props.lpj.periode_selesai).substring(0, 10)
        : '',
    keterangan: props.lpj.keterangan || '',
})

const submit = () => {
    form.put(
        route('lpj-keuangan.update', props.lpj.id),
        {
            preserveScroll: true,
        }
    )
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
</script>

<template>
    <Head :title="`Edit ${lpj.kode_lpj}`" />

    <div class="mx-auto max-w-4xl space-y-6">

        <!-- HEADER -->
        <div>
            <div class="mb-2 flex items-center gap-2">
                <Link
                    :href="route('lpj-keuangan.index')"
                    class="text-sm text-gray-500 hover:text-blue-600 dark:text-gray-400"
                >
                    LPJ Keuangan
                </Link>

                <span class="text-gray-400">/</span>

                <Link
                    :href="
                        route(
                            'lpj-keuangan.show',
                            lpj.id
                        )
                    "
                    class="text-sm text-gray-500 hover:text-blue-600 dark:text-gray-400"
                >
                    {{ lpj.kode_lpj }}
                </Link>

                <span class="text-gray-400">/</span>

                <span
                    class="text-sm text-gray-700 dark:text-gray-300"
                >
                    Edit
                </span>
            </div>

            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        Edit LPJ Keuangan
                    </h1>

                    <p
                        class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                    >
                        Ubah informasi LPJ {{ lpj.kode_lpj }}.
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


        <!-- FORM -->
        <form
            @submit.prevent="submit"
            class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >

            <!-- HEADER -->
            <div
                class="border-b border-gray-200 px-6 py-5 dark:border-gray-700"
            >
                <h2
                    class="font-semibold text-gray-900 dark:text-white"
                >
                    Informasi LPJ
                </h2>

                <p
                    class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                >
                    Perubahan periode akan memengaruhi perhitungan
                    pemasukan otomatis.
                </p>
            </div>


            <div class="space-y-6 p-6">

                <!-- KODE -->
                <div>
                    <label
                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Kode LPJ
                        <span class="text-red-500">*</span>
                    </label>

                    <input
                        v-model="form.kode_lpj"
                        type="text"
                        placeholder="Contoh: LPJ-2026-001"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                        :class="{
                            'border-red-500':
                                form.errors.kode_lpj,
                        }"
                    />

                    <p
                        v-if="form.errors.kode_lpj"
                        class="mt-1 text-xs text-red-500"
                    >
                        {{ form.errors.kode_lpj }}
                    </p>
                </div>


                <!-- PERIODE -->
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                    <!-- MULAI -->
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Periode Mulai
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            v-model="form.periode_mulai"
                            type="date"
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                            :class="{
                                'border-red-500':
                                    form.errors.periode_mulai,
                            }"
                        />

                        <p
                            v-if="form.errors.periode_mulai"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.periode_mulai }}
                        </p>
                    </div>


                    <!-- SELESAI -->
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Periode Selesai
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            v-model="form.periode_selesai"
                            type="date"
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                            :class="{
                                'border-red-500':
                                    form.errors.periode_selesai,
                            }"
                        />

                        <p
                            v-if="form.errors.periode_selesai"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.periode_selesai }}
                        </p>
                    </div>
                </div>


                <!-- KETERANGAN -->
                <div>
                    <label
                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Keterangan
                    </label>

                    <textarea
                        v-model="form.keterangan"
                        rows="4"
                        placeholder="Keterangan LPJ..."
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                        :class="{
                            'border-red-500':
                                form.errors.keterangan,
                        }"
                    ></textarea>

                    <p
                        v-if="form.errors.keterangan"
                        class="mt-1 text-xs text-red-500"
                    >
                        {{ form.errors.keterangan }}
                    </p>
                </div>


                <!-- PERINGATAN -->
                <div
                    class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-900/50 dark:bg-yellow-900/20"
                >
                    <div class="flex gap-3">
                        <div class="text-xl">
                            ⚠️
                        </div>

                        <div>
                            <h3
                                class="text-sm font-semibold text-yellow-800 dark:text-yellow-300"
                            >
                                Perhatikan periode LPJ
                            </h3>

                            <p
                                class="mt-1 text-xs leading-relaxed text-yellow-700 dark:text-yellow-300"
                            >
                                Total pemasukan dihitung berdasarkan
                                pembayaran peserta yang berstatus
                                <strong>lunas</strong> dalam periode
                                LPJ. Jika periode diubah, total
                                pemasukan juga akan berubah.
                            </p>
                        </div>
                    </div>
                </div>
            </div>


            <!-- FOOTER -->
            <div
                class="flex flex-col-reverse gap-3 border-t border-gray-200 px-6 py-5 sm:flex-row sm:justify-end dark:border-gray-700"
            >
                <Link
                    :href="
                        route(
                            'lpj-keuangan.show',
                            lpj.id
                        )
                    "
                    class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    Batal
                </Link>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
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
</template>

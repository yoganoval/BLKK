<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    pembayaran: {
        type: Object,
        required: true,
    },

    pendaftarans: {
        type: Array,
        default: () => [],
    },
})

const form = useForm({
    pendaftaran_id: props.pembayaran.pendaftaran_id,
    jumlah: props.pembayaran.jumlah,
    tanggal_bayar: props.pembayaran.tanggal_bayar,
    metode: props.pembayaran.metode || '',
    status: props.pembayaran.status,
    bukti: null,
    keterangan: props.pembayaran.keterangan || '',
})

const submit = () => {
    form
        .transform((data) => ({
            ...data,
            _method: 'PUT',
        }))
        .post(route('pembayarans.update', props.pembayaran.id), {
            forceFormData: true,
        })
}
</script>

<template>
    <Head title="Edit Pembayaran" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
        <div class="mx-auto max-w-4xl px-4 py-6 sm:px-6 lg:px-8">

            <!-- HEADER -->
            <div class="mb-6">

                <div class="mb-4 flex items-center gap-2 text-sm text-slate-500">

                    <Link
                        :href="route('pembayarans.index')"
                        class="hover:text-indigo-600"
                    >
                        Pembayaran
                    </Link>

                    <span>/</span>

                    <span>
                        Edit
                    </span>

                </div>

                <h1 class="text-xl font-bold text-slate-800 dark:text-white">
                    Edit Pembayaran
                </h1>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Perbarui data transaksi pembayaran
                </p>

            </div>

            <!-- FORM -->
            <form
                @submit.prevent="submit"
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >

                <div class="p-6 sm:p-8">

                    <div class="grid grid-cols-1 gap-6">

                        <!-- PENDAFTARAN -->
                        <div>

                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                Pendaftaran
                            </label>

                            <select
                                v-model="form.pendaftaran_id"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                            >
                                <option
                                    v-for="pendaftaran in pendaftarans"
                                    :key="pendaftaran.id"
                                    :value="pendaftaran.id"
                                >
                                    {{ pendaftaran.peserta?.nama || '-' }}
                                    -
                                    {{ pendaftaran.program_pelatihan?.nama || '-' }}
                                    -
                                    {{ pendaftaran.kelas?.nama || '-' }}
                                </option>
                            </select>

                            <p
                                v-if="form.errors.pendaftaran_id"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.pendaftaran_id }}
                            </p>

                        </div>

                        <!-- JUMLAH + TANGGAL -->
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                            <div>

                                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    Jumlah Pembayaran
                                </label>

                                <div class="relative">

                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-500">
                                        Rp
                                    </span>

                                    <input
                                        v-model="form.jumlah"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-12 pr-4 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                                    />

                                </div>

                                <p
                                    v-if="form.errors.jumlah"
                                    class="mt-1 text-xs text-red-500"
                                >
                                    {{ form.errors.jumlah }}
                                </p>

                            </div>

                            <div>

                                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    Tanggal Bayar
                                </label>

                                <input
                                    v-model="form.tanggal_bayar"
                                    type="date"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                                />

                                <p
                                    v-if="form.errors.tanggal_bayar"
                                    class="mt-1 text-xs text-red-500"
                                >
                                    {{ form.errors.tanggal_bayar }}
                                </p>

                            </div>

                        </div>

                        <!-- METODE + STATUS -->
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                            <div>

                                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    Metode Pembayaran
                                </label>

                                <select
                                    v-model="form.metode"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                                >
                                    <option value="">
                                        -- Pilih Metode --
                                    </option>

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

                            <div>

                                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    Status
                                </label>

                                <select
                                    v-model="form.status"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
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

                        </div>

                        <!-- BUKTI LAMA -->
                        <div
                            v-if="pembayaran.bukti"
                            class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800"
                        >

                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Bukti Saat Ini
                            </p>

                            <a
                                :href="`/storage/${pembayaran.bukti}`"
                                target="_blank"
                                class="mt-2 inline-flex text-sm font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400"
                            >
                                Lihat Bukti Pembayaran
                            </a>

                        </div>

                        <!-- BUKTI BARU -->
                        <div>

                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                Ganti Bukti Pembayaran
                            </label>

                            <input
                                type="file"
                                accept="image/*,.pdf"
                                @change="form.bukti = $event.target.files[0]"
                                class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-2 file:font-semibold file:text-indigo-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                            />

                            <p class="mt-1 text-xs text-slate-500">
                                Kosongkan jika tidak ingin mengganti bukti.
                            </p>

                        </div>

                        <!-- KETERANGAN -->
                        <div>

                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                Keterangan
                            </label>

                            <textarea
                                v-model="form.keterangan"
                                rows="4"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                            ></textarea>

                        </div>

                    </div>

                </div>

                <!-- ACTION -->
                <div class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-800/40">

                    <Link
                        :href="route('pembayarans.index')"
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
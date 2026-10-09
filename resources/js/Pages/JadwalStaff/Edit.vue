<script setup>
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
})

const form = useForm({
    nama: props.jadwal.nama ?? '',
    hari: props.jadwal.hari ?? '',
    jam_masuk: props.jadwal.jam_masuk
        ? props.jadwal.jam_masuk.substring(0, 5)
        : '',
    jam_pulang: props.jadwal.jam_pulang
        ? props.jadwal.jam_pulang.substring(0, 5)
        : '',
    toleransi_menit: props.jadwal.toleransi_menit ?? 0,
    status: Boolean(props.jadwal.status),
    keterangan: props.jadwal.keterangan ?? '',
})

const hari = [
    { value: 'senin', label: 'Senin' },
    { value: 'selasa', label: 'Selasa' },
    { value: 'rabu', label: 'Rabu' },
    { value: 'kamis', label: 'Kamis' },
    { value: 'jumat', label: 'Jumat' },
    { value: 'sabtu', label: 'Sabtu' },
    { value: 'minggu', label: 'Minggu' },
]

const submit = () => {
    form.put(route('jadwal-staff.update', props.jadwal.id))
}
</script>

<template>
    <Head title="Edit Jadwal Staff" />

    <div class="mx-auto max-w-3xl space-y-6">
        <!-- HEADER -->
        <div>
            <Link
                :href="route('jadwal-staff.index')"
                class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400"
            >
                ← Kembali ke Jadwal Staff
            </Link>

            <h1
                class="mt-3 text-2xl font-bold text-slate-800 dark:text-white"
            >
                Edit Jadwal Staff
            </h1>

            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Perbarui pengaturan jadwal kerja staff.
            </p>
        </div>

        <!-- FORM -->
        <form
            @submit.prevent="submit"
            class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900"
        >
            <div class="space-y-6">
                <!-- NAMA -->
                <div>
                    <label
                        class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300"
                    >
                        Nama Jadwal
                    </label>

                    <input
                        v-model="form.nama"
                        type="text"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    />

                    <p
                        v-if="form.errors.nama"
                        class="mt-1 text-xs text-red-500"
                    >
                        {{ form.errors.nama }}
                    </p>
                </div>

                <!-- HARI -->
                <div>
                    <label
                        class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300"
                    >
                        Hari
                    </label>

                    <select
                        v-model="form.hari"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    >
                        <option
                            v-for="item in hari"
                            :key="item.value"
                            :value="item.value"
                        >
                            {{ item.label }}
                        </option>
                    </select>

                    <p
                        v-if="form.errors.hari"
                        class="mt-1 text-xs text-red-500"
                    >
                        {{ form.errors.hari }}
                    </p>
                </div>

                <!-- JAM -->
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label
                            class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300"
                        >
                            Jam Masuk
                        </label>

                        <input
                            v-model="form.jam_masuk"
                            type="time"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        />

                        <p
                            v-if="form.errors.jam_masuk"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.jam_masuk }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300"
                        >
                            Jam Pulang
                        </label>

                        <input
                            v-model="form.jam_pulang"
                            type="time"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        />

                        <p
                            v-if="form.errors.jam_pulang"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.jam_pulang }}
                        </p>
                    </div>
                </div>

                <!-- TOLERANSI -->
                <div>
                    <label
                        class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300"
                    >
                        Toleransi Keterlambatan
                    </label>

                    <div class="relative">
                        <input
                            v-model.number="form.toleransi_menit"
                            type="number"
                            min="0"
                            max="1440"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 pr-20 text-sm text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        />

                        <span
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-sm text-slate-400"
                        >
                            menit
                        </span>
                    </div>

                    <p
                        v-if="form.errors.toleransi_menit"
                        class="mt-1 text-xs text-red-500"
                    >
                        {{ form.errors.toleransi_menit }}
                    </p>
                </div>

                <!-- STATUS -->
                <div
                    class="rounded-xl border border-slate-200 p-4 dark:border-slate-700"
                >
                    <label
                        class="flex cursor-pointer items-center justify-between"
                    >
                        <div>
                            <div
                                class="text-sm font-semibold text-slate-700 dark:text-slate-300"
                            >
                                Jadwal Aktif
                            </div>

                            <div
                                class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                            >
                                Jadwal dapat digunakan untuk absensi.
                            </div>
                        </div>

                        <input
                            v-model="form.status"
                            type="checkbox"
                            class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-800"
                        />
                    </label>
                </div>

                <!-- KETERANGAN -->
                <div>
                    <label
                        class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300"
                    >
                        Keterangan
                    </label>

                    <textarea
                        v-model="form.keterangan"
                        rows="4"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    ></textarea>

                    <p
                        v-if="form.errors.keterangan"
                        class="mt-1 text-xs text-red-500"
                    >
                        {{ form.errors.keterangan }}
                    </p>
                </div>
            </div>

            <!-- BUTTON -->
            <div
                class="mt-8 flex justify-end gap-3 border-t border-slate-200 pt-6 dark:border-slate-700"
            >
                <Link
                    :href="route('jadwal-staff.index')"
                    class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                >
                    Batal
                </Link>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50"
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
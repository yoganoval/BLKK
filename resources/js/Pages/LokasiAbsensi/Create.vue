<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import MapPicker from './MapPicker.vue'

defineOptions({
    layout: AdminLayout,
})

const mapPicker = ref(null)

const form = useForm({
    nama: '',
    latitude: '',
    longitude: '',
    radius: 100,
    status: 'aktif',
    keterangan: '',
})

const submit = () => {
    form.post(route('lokasi-absensi.store'), {
        preserveScroll: true,
    })
}

const useCurrentLocation = () => {
    mapPicker.value?.useCurrentLocation()
}
</script>

<template>
    <Head title="Tambah Lokasi Absensi" />

    <div class="space-y-6">

        <!-- HEADER -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1
                    class="text-2xl font-bold text-slate-800 dark:text-white"
                >
                    Tambah Lokasi Absensi
                </h1>

                <p
                    class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                >
                    Tentukan lokasi dan radius yang diperbolehkan
                    untuk absensi staff.
                </p>
            </div>

            <Link
                :href="route('lokasi-absensi.index')"
                class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
            >
                ← Kembali
            </Link>
        </div>

        <!-- FORM -->
        <div
            class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900"
        >
            <div class="p-6">

                <!-- INFORMASI DASAR -->
                <div class="mb-6">
                    <h2
                        class="text-base font-bold text-slate-800 dark:text-white"
                    >
                        Informasi Lokasi
                    </h2>

                    <p
                        class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                    >
                        Isi nama lokasi kemudian tentukan titik pada
                        peta.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                    <!-- NAMA -->
                    <div class="md:col-span-2">
                        <label
                            class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                        >
                            Nama Lokasi
                        </label>

                        <input
                            v-model="form.nama"
                            type="text"
                            placeholder="Contoh: Kantor BLKK"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                        />

                        <p
                            v-if="form.errors.nama"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.nama }}
                        </p>
                    </div>

                    <!-- LATITUDE -->
                    <div>
                        <label
                            class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                        >
                            Latitude
                        </label>

                        <input
                            v-model="form.latitude"
                            type="number"
                            step="0.0000001"
                            placeholder="-7.0000000"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                        />

                        <p
                            v-if="form.errors.latitude"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.latitude }}
                        </p>
                    </div>

                    <!-- LONGITUDE -->
                    <div>
                        <label
                            class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                        >
                            Longitude
                        </label>

                        <input
                            v-model="form.longitude"
                            type="number"
                            step="0.0000001"
                            placeholder="113.0000000"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                        />

                        <p
                            v-if="form.errors.longitude"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.longitude }}
                        </p>
                    </div>

                    <!-- RADIUS -->
                    <div>
                        <label
                            class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                        >
                            Radius Absensi
                        </label>

                        <div class="relative">
                            <input
                                v-model.number="form.radius"
                                type="number"
                                min="1"
                                max="10000"
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 pr-20 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                            />

                            <span
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-sm text-slate-400"
                            >
                                meter
                            </span>
                        </div>

                        <p
                            v-if="form.errors.radius"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.radius }}
                        </p>

                        <p
                            class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                        >
                            Contoh: 100 meter berarti staff harus
                            berada maksimal 100 meter dari titik lokasi.
                        </p>
                    </div>

                    <!-- STATUS -->
                    <div>
                        <label
                            class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                        >
                            Status
                        </label>

                        <select
                            v-model="form.status"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                        >
                            <option value="aktif">
                                Aktif
                            </option>

                            <option value="nonaktif">
                                Nonaktif
                            </option>
                        </select>

                        <p
                            v-if="form.errors.status"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.status }}
                        </p>
                    </div>

                    <!-- KETERANGAN -->
                    <div class="md:col-span-2">
                        <label
                            class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                        >
                            Keterangan
                        </label>

                        <textarea
                            v-model="form.keterangan"
                            rows="3"
                            placeholder="Keterangan lokasi (opsional)"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                        ></textarea>

                        <p
                            v-if="form.errors.keterangan"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.keterangan }}
                        </p>
                    </div>
                </div>

                <!-- PETA -->
                <div class="mt-8">

                    <div
                        class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <h2
                                class="text-base font-bold text-slate-800 dark:text-white"
                            >
                                Tentukan Titik Lokasi
                            </h2>

                            <p
                                class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                            >
                                Klik peta, geser marker, atau gunakan
                                lokasi GPS perangkat.
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="useCurrentLocation"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
                        >
                            📍 Gunakan Lokasi Saya
                        </button>
                    </div>

                    <MapPicker
                        ref="mapPicker"
                        v-model:latitude="form.latitude"
                        v-model:longitude="form.longitude"
                        :radius="form.radius"
                    />

                    <p
                        v-if="form.errors.latitude || form.errors.longitude"
                        class="mt-2 text-xs text-red-500"
                    >
                        Pastikan titik koordinat lokasi sudah ditentukan.
                    </p>
                </div>

                <!-- ACTION -->
                <div
                    class="mt-8 flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:justify-end dark:border-slate-700"
                >
                    <Link
                        :href="route('lokasi-absensi.index')"
                        class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                    >
                        Batal
                    </Link>

                    <button
                        type="button"
                        @click="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        {{
                            form.processing
                                ? 'Menyimpan...'
                                : 'Simpan Lokasi'
                        }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
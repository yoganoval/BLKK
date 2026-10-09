<script setup lang="ts">
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import {
    ArrowLeft,
    Save,
    Percent,
    Award,
    ListOrdered,
    FileText,
    GraduationCap,
} from 'lucide-vue-next'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

interface Kelas {
    id: number
    nama: string
    durasi_hari: number
}

const props = defineProps<{
    kelas: Kelas[]
}>()

const form = useForm({
    kelas_id: '',
    nama: '',
    bobot: '',
    nilai_maksimal: '100',
    keterangan: '',
    urutan: 1,
})

const selectedKelas = computed(() => {
    return props.kelas.find(
        (item) => item.id === Number(form.kelas_id)
    )
})

const submit = () => {
    form.post(route('komponen-nilai.store'), {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Tambah Komponen Nilai" />

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-4">
            <Link
                :href="route('komponen-nilai.index')"
                class="rounded-lg border border-slate-200 p-2 text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
            >
                <ArrowLeft class="h-5 w-5" />
            </Link>

            <div>
                <h1
                    class="text-2xl font-bold text-slate-900 dark:text-white"
                >
                    Tambah Komponen Nilai
                </h1>

                <p
                    class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                >
                    Tambahkan komponen penilaian untuk suatu kelas.
                </p>
            </div>
        </div>

        <!-- Form -->
        <form
            @submit.prevent="submit"
            class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
        >
            <div class="border-b border-slate-200 px-6 py-5 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div
                        class="rounded-lg bg-primary/10 p-2.5 text-primary"
                    >
                        <Award class="h-5 w-5" />
                    </div>

                    <div>
                        <h2
                            class="font-semibold text-slate-900 dark:text-white"
                        >
                            Informasi Komponen
                        </h2>

                        <p
                            class="text-sm text-slate-500 dark:text-slate-400"
                        >
                            Tentukan kelas dan aturan penilaiannya.
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-6 p-6">
                <!-- Kelas -->
                <div>
                    <label
                        for="kelas_id"
                        class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                    >
                        Kelas
                        <span class="text-red-500">*</span>
                    </label>

                    <div class="relative">
                        <GraduationCap
                            class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                        />

                        <select
                            id="kelas_id"
                            v-model="form.kelas_id"
                            class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-11 pr-3 text-sm text-slate-900 outline-none transition focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                            :class="{
                                'border-red-500':
                                    form.errors.kelas_id,
                            }"
                        >
                            <option value="" disabled>
                                Pilih kelas
                            </option>

                            <option
                                v-for="item in kelas"
                                :key="item.id"
                                :value="item.id"
                            >
                                {{ item.nama }}
                                — {{ item.durasi_hari }} hari
                            </option>
                        </select>
                    </div>

                    <p
                        v-if="selectedKelas"
                        class="mt-2 text-xs text-slate-500 dark:text-slate-400"
                    >
                        Komponen ini akan digunakan untuk kelas
                        <strong>
                            {{ selectedKelas.nama }}
                        </strong>
                        dengan durasi
                        {{ selectedKelas.durasi_hari }} hari.
                    </p>

                    <p
                        v-if="form.errors.kelas_id"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ form.errors.kelas_id }}
                    </p>
                </div>

                <!-- Nama Komponen -->
                <div>
                    <label
                        for="nama"
                        class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                    >
                        Nama Komponen
                        <span class="text-red-500">*</span>
                    </label>

                    <div class="relative">
                        <FileText
                            class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                        />

                        <input
                            id="nama"
                            v-model="form.nama"
                            type="text"
                            maxlength="100"
                            placeholder="Contoh: Tugas, Praktik, Ujian Akhir"
                            class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-11 pr-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                            :class="{
                                'border-red-500':
                                    form.errors.nama,
                            }"
                        />
                    </div>

                    <p
                        v-if="form.errors.nama"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ form.errors.nama }}
                    </p>
                </div>

                <!-- Bobot + Nilai Maksimal + Urutan -->
                <div class="grid gap-6 md:grid-cols-3">
                    <!-- Bobot -->
                    <div>
                        <label
                            for="bobot"
                            class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                        >
                            Bobot (%)
                            <span class="text-red-500">*</span>
                        </label>

                        <div class="relative">
                            <Percent
                                class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                            />

                            <input
                                id="bobot"
                                v-model="form.bobot"
                                type="number"
                                min="0"
                                max="100"
                                step="0.01"
                                placeholder="20"
                                class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-11 pr-3 text-sm text-slate-900 outline-none transition focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                                :class="{
                                    'border-red-500':
                                        form.errors.bobot,
                                }"
                            />
                        </div>

                        <p
                            class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                        >
                            Contoh: 20 untuk bobot 20%.
                        </p>

                        <p
                            v-if="form.errors.bobot"
                            class="mt-1 text-sm text-red-500"
                        >
                            {{ form.errors.bobot }}
                        </p>
                    </div>

                    <!-- Nilai Maksimal -->
                    <div>
                        <label
                            for="nilai_maksimal"
                            class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                        >
                            Nilai Maksimal
                            <span class="text-red-500">*</span>
                        </label>

                        <div class="relative">
                            <Award
                                class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                            />

                            <input
                                id="nilai_maksimal"
                                v-model="form.nilai_maksimal"
                                type="number"
                                min="1"
                                step="0.01"
                                placeholder="100"
                                class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-11 pr-3 text-sm text-slate-900 outline-none transition focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                                :class="{
                                    'border-red-500':
                                        form.errors.nilai_maksimal,
                                }"
                            />
                        </div>

                        <p
                            class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                        >
                            Biasanya menggunakan 100.
                        </p>

                        <p
                            v-if="form.errors.nilai_maksimal"
                            class="mt-1 text-sm text-red-500"
                        >
                            {{ form.errors.nilai_maksimal }}
                        </p>
                    </div>

                    <!-- Urutan -->
                    <div>
                        <label
                            for="urutan"
                            class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                        >
                            Urutan
                            <span class="text-red-500">*</span>
                        </label>

                        <div class="relative">
                            <ListOrdered
                                class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                            />

                            <input
                                id="urutan"
                                v-model="form.urutan"
                                type="number"
                                min="1"
                                placeholder="1"
                                class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-11 pr-3 text-sm text-slate-900 outline-none transition focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                                :class="{
                                    'border-red-500':
                                        form.errors.urutan,
                                }"
                            />
                        </div>

                        <p
                            class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                        >
                            Menentukan urutan komponen penilaian.
                        </p>

                        <p
                            v-if="form.errors.urutan"
                            class="mt-1 text-sm text-red-500"
                        >
                            {{ form.errors.urutan }}
                        </p>
                    </div>
                </div>

                <!-- Keterangan -->
                <div>
                    <label
                        for="keterangan"
                        class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                    >
                        Keterangan
                    </label>

                    <textarea
                        id="keterangan"
                        v-model="form.keterangan"
                        rows="4"
                        maxlength="1000"
                        placeholder="Contoh: Penilaian berdasarkan hasil praktik peserta selama pelatihan."
                        class="w-full resize-none rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        :class="{
                            'border-red-500':
                                form.errors.keterangan,
                        }"
                    />

                    <p
                        class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                    >
                        Opsional, maksimal 1000 karakter.
                    </p>

                    <p
                        v-if="form.errors.keterangan"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ form.errors.keterangan }}
                    </p>
                </div>

                <!-- Informasi -->
                <div
                    class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-900/50 dark:bg-blue-950/30"
                >
                    <div class="flex gap-3">
                        <Percent
                            class="mt-0.5 h-5 w-5 shrink-0 text-blue-600 dark:text-blue-400"
                        />

                        <div>
                            <p
                                class="text-sm font-semibold text-blue-800 dark:text-blue-300"
                            >
                                Perhatian Bobot Penilaian
                            </p>

                            <p
                                class="mt-1 text-sm leading-6 text-blue-700 dark:text-blue-400"
                            >
                                Total bobot seluruh komponen dalam satu
                                kelas tidak boleh melebihi
                                <strong>100%</strong>.
                            </p>

                            <p
                                class="mt-1 text-sm text-blue-700 dark:text-blue-400"
                            >
                                Contoh: Tugas 20% + Praktik 40% +
                                Kehadiran 10% + Ujian 30% = 100%.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div
                class="flex flex-col-reverse gap-3 border-t border-slate-200 px-6 py-4 sm:flex-row sm:justify-end dark:border-slate-800"
            >
                <Link
                    :href="route('komponen-nilai.index')"
                    class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                >
                    Batal
                </Link>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-medium text-white transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <Save class="h-4 w-4" />

                    {{
                        form.processing
                            ? 'Menyimpan...'
                            : 'Simpan Komponen'
                    }}
                </button>
            </div>
        </form>
    </div>
</template>
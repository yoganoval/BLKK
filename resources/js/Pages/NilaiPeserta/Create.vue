<script setup lang="ts">
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import {
    ArrowLeft,
    Save,
    Award,
    GraduationCap,
    User,
} from 'lucide-vue-next'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

interface Peserta {
    id: number
    nama: string
}

interface Kelas {
    id: number
    nama: string
    durasi_hari: number
}

interface Komponen {
    id: number
    kelas_id: number
    nama: string
    bobot: number
    nilai_maksimal: number
    kelas?: Kelas
}

const props = defineProps<{
    peserta: Peserta[]
    komponen: Komponen[]
}>()

const form = useForm({
    peserta_id: '',
    komponen_nilai_id: '',
    nilai: '',
    catatan: '',
})

const selectedKomponen = computed(() => {
    return props.komponen.find(
        item =>
            item.id === Number(form.komponen_nilai_id)
    )
})

const submit = () => {
    form.post(route('nilai-peserta.store'), {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Tambah Nilai Peserta" />

    <div class="mx-auto max-w-3xl space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-4">
            <Link
                :href="route('nilai-peserta.index')"
                class="rounded-lg border border-slate-200 p-2 text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
            >
                <ArrowLeft class="h-5 w-5" />
            </Link>

            <div>
                <h1
                    class="text-2xl font-bold text-slate-900 dark:text-white"
                >
                    Tambah Nilai Peserta
                </h1>

                <p
                    class="mt-1 text-sm text-slate-500"
                >
                    Masukkan nilai peserta berdasarkan komponen penilaian.
                </p>
            </div>
        </div>

        <form
            @submit.prevent="submit"
            class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
        >
            <div
                class="border-b border-slate-200 px-6 py-5 dark:border-slate-800"
            >
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
                            Input Nilai
                        </h2>

                        <p class="text-sm text-slate-500">
                            Isi data penilaian peserta.
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-6 p-6">
                <!-- Peserta -->
                <div>
                    <label
                        class="mb-2 block text-sm font-medium"
                    >
                        Peserta
                        <span class="text-red-500">*</span>
                    </label>

                    <div class="relative">
                        <User
                            class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                        />

                        <select
                            v-model="form.peserta_id"
                            class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-11 pr-3 text-sm dark:border-slate-700 dark:bg-slate-950"
                        >
                            <option value="" disabled>
                                Pilih peserta
                            </option>

                            <option
                                v-for="item in peserta"
                                :key="item.id"
                                :value="item.id"
                            >
                                {{ item.nama }}
                            </option>
                        </select>
                    </div>

                    <p
                        v-if="form.errors.peserta_id"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ form.errors.peserta_id }}
                    </p>
                </div>

                <!-- Komponen -->
                <div>
                    <label
                        class="mb-2 block text-sm font-medium"
                    >
                        Komponen Nilai
                        <span class="text-red-500">*</span>
                    </label>

                    <select
                        v-model="form.komponen_nilai_id"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950"
                    >
                        <option value="" disabled>
                            Pilih komponen
                        </option>

                        <option
                            v-for="item in komponen"
                            :key="item.id"
                            :value="item.id"
                        >
                            {{ item.kelas?.nama }}
                            —
                            {{ item.nama }}
                            ({{ item.bobot }}%)
                        </option>
                    </select>

                    <div
                        v-if="selectedKomponen"
                        class="mt-2 rounded-lg bg-slate-50 p-3 text-sm dark:bg-slate-800"
                    >
                        Kelas:
                        <strong>
                            {{ selectedKomponen.kelas?.nama }}
                        </strong>
                        <br />

                        Bobot:
                        <strong>
                            {{ selectedKomponen.bobot }}%
                        </strong>
                        <br />

                        Nilai maksimal:
                        <strong>
                            {{ selectedKomponen.nilai_maksimal }}
                        </strong>
                    </div>

                    <p
                        v-if="form.errors.komponen_nilai_id"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ form.errors.komponen_nilai_id }}
                    </p>
                </div>

                <!-- Nilai -->
                <div>
                    <label
                        class="mb-2 block text-sm font-medium"
                    >
                        Nilai
                        <span class="text-red-500">*</span>
                    </label>

                    <input
                        v-model="form.nilai"
                        type="number"
                        min="0"
                        :max="
                            selectedKomponen?.nilai_maksimal ??
                            100
                        "
                        step="0.01"
                        placeholder="Masukkan nilai"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950"
                    />

                    <p
                        v-if="selectedKomponen"
                        class="mt-1 text-xs text-slate-500"
                    >
                        Maksimal:
                        {{ selectedKomponen.nilai_maksimal }}
                    </p>

                    <p
                        v-if="form.errors.nilai"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ form.errors.nilai }}
                    </p>
                </div>

                <!-- Catatan -->
                <div>
                    <label
                        class="mb-2 block text-sm font-medium"
                    >
                        Catatan
                    </label>

                    <textarea
                        v-model="form.catatan"
                        rows="4"
                        maxlength="1000"
                        placeholder="Catatan penilaian..."
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950"
                    />

                    <p
                        v-if="form.errors.catatan"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ form.errors.catatan }}
                    </p>
                </div>
            </div>

            <div
                class="flex justify-end gap-3 border-t border-slate-200 px-6 py-4 dark:border-slate-800"
            >
                <Link
                    :href="route('nilai-peserta.index')"
                    class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm dark:border-slate-700"
                >
                    Batal
                </Link>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-medium text-white disabled:opacity-50"
                >
                    <Save class="h-4 w-4" />
                    {{
                        form.processing
                            ? 'Menyimpan...'
                            : 'Simpan Nilai'
                    }}
                </button>
            </div>
        </form>
    </div>
</template>
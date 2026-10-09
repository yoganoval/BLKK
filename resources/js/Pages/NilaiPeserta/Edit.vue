<script setup lang="ts">
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import {
    ArrowLeft,
    Save,
    Award,
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

interface Nilai {
    id: number
    peserta_id: number
    komponen_nilai_id: number
    nilai: number
    catatan?: string | null
    peserta?: Peserta
    komponen_nilai?: Komponen
}

const props = defineProps<{
    nilai: Nilai
    peserta: Peserta[]
    komponen: Komponen[]
}>()

const form = useForm({
    nilai: String(props.nilai.nilai),
    catatan: props.nilai.catatan ?? '',
})

const selectedKomponen = computed(() => {
    return props.komponen.find(
        item => item.id === props.nilai.komponen_nilai_id
    )
})

const submit = () => {
    form.put(
        route(
            'nilai-peserta.update',
            props.nilai.id
        ),
        {
            preserveScroll: true,
        }
    )
}
</script>

<template>
    <Head title="Edit Nilai Peserta" />

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
                    Edit Nilai Peserta
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Perbarui hasil penilaian peserta.
                </p>
            </div>
        </div>

        <form
            @submit.prevent="submit"
            class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
        >
            <div class="p-6">
                <!-- Informasi -->
                <div
                    class="mb-6 rounded-lg bg-slate-50 p-4 dark:bg-slate-800"
                >
                    <div class="flex items-center gap-3">
                        <Award class="h-5 w-5 text-primary" />

                        <div>
                            <p
                                class="font-semibold text-slate-900 dark:text-white"
                            >
                                {{ nilai.peserta?.nama }}
                            </p>

                            <p
                                class="text-sm text-slate-500"
                            >
                                {{ nilai.komponen_nilai?.kelas?.nama }}
                                —
                                {{ nilai.komponen_nilai?.nama }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="mt-3 grid grid-cols-2 gap-3 text-sm sm:grid-cols-3"
                    >
                        <div>
                            <span class="text-slate-500">
                                Bobot
                            </span>

                            <p class="font-semibold">
                                {{ nilai.komponen_nilai?.bobot }}%
                            </p>
                        </div>

                        <div>
                            <span class="text-slate-500">
                                Nilai Maksimal
                            </span>

                            <p class="font-semibold">
                                {{
                                    nilai.komponen_nilai
                                        ?.nilai_maksimal
                                }}
                            </p>
                        </div>
                    </div>
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
                            nilai.komponen_nilai?.nilai_maksimal ??
                            100
                        "
                        step="0.01"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950"
                    />

                    <p
                        v-if="form.errors.nilai"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ form.errors.nilai }}
                    </p>
                </div>

                <!-- Catatan -->
                <div class="mt-6">
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
                            : 'Simpan Perubahan'
                    }}
                </button>
            </div>
        </form>
    </div>
</template>
<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

interface OpsiJawaban {
    id: number
    opsi: string
    teks: string
    is_correct?: boolean
}

interface Soal {
    id: number
    kuis_id: number
    pertanyaan: string
    tipe: string
    bobot: number
    opsi_jawaban?: OpsiJawaban[]
}

interface Kuis {
    id: number
    judul: string
}

const props = defineProps<{
    kuis: Kuis
    soal: Soal[]
}>()

function hapusSoal(item: Soal) {
    if (!props.kuis?.id || !item.id) {
        alert('ID kuis atau soal tidak ditemukan.')
        return
    }

    if (!confirm('Yakin ingin menghapus soal ini?')) {
        return
    }

    router.delete(
        route('kuis.soal.destroy', {
            kui: props.kuis.id,
            soal: item.id,
        }),
    )
}
</script>

<template>
    <div class="min-h-screen bg-gray-50 p-4 dark:bg-gray-950 md:p-8">
        <div class="mx-auto max-w-6xl space-y-6">
            <Link
                v-if="kuis?.id"
                :href="route('kuis.show', { kui: kuis.id })"
                class="text-sm text-blue-600 hover:underline"
            >
                ← Kembali ke detail kuis
            </Link>

            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Soal Kuis
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ kuis?.judul ?? 'Kuis' }}
                    </p>
                </div>

                <Link
                    v-if="kuis?.id"
                    :href="route('kuis.soal.create', { kui: kuis.id })"
                    class="rounded-lg bg-blue-600 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-blue-700"
                >
                    + Tambah Soal
                </Link>
            </div>

            <div v-if="soal?.length" class="space-y-4">
                <div
                    v-for="(item, index) in soal"
                    :key="item.id"
                    class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900"
                >
                    <div class="flex flex-col justify-between gap-4 sm:flex-row">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-blue-600">
                                SOAL {{ index + 1 }}
                                ·
                                {{
                                    item.tipe === 'benar_salah'
                                        ? 'Benar / Salah'
                                        : item.tipe === 'esai'
                                        ? 'Esai'
                                        : 'Pilihan Ganda'

                                }}
                            </p>

                            <p class="mt-2 whitespace-pre-line font-medium text-gray-900 dark:text-white">
                                {{ item.pertanyaan }}
                            </p>

                            <p class="mt-2 text-xs text-gray-500">
                                Bobot: {{ item.bobot }}
                                ·
                                {{ item.opsi_jawaban?.length ?? 0 }} opsi
                            </p>

                            <div
                                v-if="item.opsi_jawaban?.length"
                                class="mt-4 space-y-2"
                            >
                                <div
                                    v-for="opsi in item.opsi_jawaban"
                                    :key="opsi.id"
                                    class="rounded-lg bg-gray-50 px-3 py-2 text-sm dark:bg-gray-800"
                                >
                                    <span class="font-semibold">{{ opsi.opsi }}.</span>
                                    {{ opsi.teks }}
                                </div>
                            </div>
                        </div>

                        <div class="flex h-fit flex-wrap gap-3">
                            <Link
                                :href="route('kuis.soal.edit', {
                                    kui: kuis.id,
                                    soal: item.id,
                                })"
                                class="text-sm font-medium text-amber-600 hover:underline"
                            >
                                Edit
                            </Link>

                            <Link
                                :href="route('soal.opsi-jawaban.index', item.id)"
                                class="text-sm font-medium text-blue-600 hover:underline"
                            >
                                Opsi Jawaban
                            </Link>

                            <button
                                type="button"
                                @click="hapusSoal(item)"
                                class="text-sm font-medium text-red-600 hover:underline"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div
                v-else
                class="rounded-xl border border-dashed border-gray-300 p-12 text-center dark:border-gray-700"
            >
                <h2 class="font-semibold text-gray-900 dark:text-white">
                    Belum ada soal
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Tambahkan soal untuk mulai menyusun kuis.
                </p>
            </div>
        </div>
    </div>
</template>

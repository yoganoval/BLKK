<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

interface JawabanEsai {
    id: number
    soal_id: number
    pertanyaan: string
    bobot: number
    jawaban_text: string | null
    nilai: number | null
    catatan_koreksi: string | null
    dikoreksi_at: string | null
}

interface Percobaan {
    id: number
    percobaan_ke: number
    status: string
    nilai: number | null
    lulus: boolean | null
    peserta: { id: number; nama: string }
    kuis: { id: number; judul: string; nilai_lulus: number }
}

const props = defineProps<{
    percobaanKuis: Percobaan
    jawabanEsai: JawabanEsai[]
}>()

const form = useForm({
    jawaban: Object.fromEntries(
        props.jawabanEsai.map((item) => [
            String(item.id),
            {
                nilai: item.nilai ?? 0,
                catatan_koreksi: item.catatan_koreksi ?? '',
            },
        ]),
    ) as Record<string, { nilai: number; catatan_koreksi: string }>,
})

const sudahDikoreksi = (item: JawabanEsai) =>
    item.dikoreksi_at !== null

const jumlahBelumDikoreksi = () =>
    props.jawabanEsai.filter((item) => !sudahDikoreksi(item)).length

const simpanKoreksi = () => {
    form.put(
        route('koreksi-esai.update', {
            percobaanKuis: props.percobaanKuis.id,
        }),
        {
            preserveScroll: true,
        },
    )
}

const hapusNilai = (id: number) => {
    form.jawaban[String(id)].nilai = 0
}
</script>

<template>
    <Head title="Periksa Jawaban Esai" />

    <div class="mx-auto max-w-5xl space-y-6 p-4 md:p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    Koreksi Jawaban Esai
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Berikan nilai berdasarkan kualitas jawaban peserta.
                </p>
            </div>

            <Link
                :href="route('koreksi-esai.index')"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm dark:border-gray-600 dark:text-white"
            >
                Kembali
            </Link>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-sm text-gray-500">Nama peserta</p>
                    <p class="mt-1 font-semibold text-gray-800 dark:text-white">
                        {{ percobaanKuis.peserta.nama }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Judul kuis</p>
                    <p class="mt-1 font-semibold text-gray-800 dark:text-white">
                        {{ percobaanKuis.kuis.judul }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Percobaan</p>
                    <p class="mt-1 font-medium dark:text-white">
                        Ke-{{ percobaanKuis.percobaan_ke }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Nilai akhir</p>
                    <p class="mt-1 font-semibold dark:text-white">
                        {{
                            percobaanKuis.nilai === null
                                ? 'Menunggu koreksi'
                                : percobaanKuis.nilai
                        }}
                    </p>
                </div>
            </div>

            <div class="mt-4 rounded-lg bg-blue-50 p-3 text-sm text-blue-800 dark:bg-blue-950 dark:text-blue-200">
                Nilai maksimal setiap esai mengikuti bobot soal.
                Nilai kelulusan kuis adalah
                {{ percobaanKuis.kuis.nilai_lulus }}.
                Esai yang belum dikoreksi:
                <strong>{{ jumlahBelumDikoreksi() }}</strong>.
            </div>
        </div>

        <form @submit.prevent="simpanKoreksi" class="space-y-5">
            <div
                v-for="(item, index) in jawabanEsai"
                :key="item.id"
                class="space-y-4 rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900"
            >
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Soal esai {{ index + 1 }}
                        </p>
                        <h2 class="mt-2 font-semibold text-gray-800 dark:text-white">
                            {{ item.pertanyaan }}
                        </h2>
                    </div>

                    <span
                        class="rounded-full px-3 py-1 text-xs font-medium"
                        :class="sudahDikoreksi(item)
                            ? 'bg-green-100 text-green-700'
                            : 'bg-orange-100 text-orange-700'"
                    >
                        {{ sudahDikoreksi(item) ? 'Sudah dikoreksi' : 'Belum dikoreksi' }}
                    </span>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Jawaban peserta
                    </label>

                    <div class="min-h-24 whitespace-pre-wrap rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">{{ item.jawaban_text?.trim() || 'Peserta tidak mengisi jawaban.' }}</div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label
                            :for="`nilai-${item.id}`"
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Nilai (maksimal {{ item.bobot }})
                        </label>

                        <input
                            :id="`nilai-${item.id}`"
                            v-model.number="form.jawaban[String(item.id)].nilai"
                            type="number"
                            min="0"
                            :max="item.bobot"
                            step="0.01"
                            required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                        />

                        <p
                            v-if="form.errors[`jawaban.${item.id}.nilai`]"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors[`jawaban.${item.id}.nilai`] }}
                        </p>
                    </div>

                    <div>
                        <label
                            :for="`catatan-${item.id}`"
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Catatan koreksi
                        </label>

                        <textarea
                            :id="`catatan-${item.id}`"
                            v-model="form.jawaban[String(item.id)].catatan_koreksi"
                            rows="3"
                            maxlength="5000"
                            placeholder="Contoh: Jawaban sudah benar, tetapi penjelasan kurang lengkap."
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                        />
                    </div>
                </div>

                <p
                    v-if="form.errors[`jawaban.${item.id}.catatan_koreksi`]"
                    class="text-sm text-red-600"
                >
                    {{ form.errors[`jawaban.${item.id}.catatan_koreksi`] }}
                </p>

                <button
                    type="button"
                    @click="hapusNilai(item.id)"
                    class="text-sm text-gray-500 underline hover:text-gray-800 dark:hover:text-white"
                >
                    Atur nilai menjadi 0
                </button>
            </div>

            <div
                v-if="jawabanEsai.length === 0"
                class="rounded-xl border border-gray-200 bg-white p-8 text-center text-gray-500 dark:border-gray-700 dark:bg-gray-900"
            >
                Tidak ada soal esai pada percobaan ini.
            </div>

            <div
                v-if="form.hasErrors"
                class="rounded-lg bg-red-50 p-4 text-sm text-red-700 dark:bg-red-950 dark:text-red-200"
            >
                Periksa kembali nilai dan catatan koreksi.
            </div>

            <div
                v-if="jawabanEsai.length"
                class="flex justify-end"
            >
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-lg bg-blue-600 px-5 py-3 font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Koreksi dan Hitung Nilai' }}
                </button>
            </div>
        </form>
    </div>
</template>
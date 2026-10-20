<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

interface OpsiJawaban {
    id: number
    soal_id: number
    opsi: string
    teks: string
}

interface Soal {
    id: number
    pertanyaan: string
    tipe: string
    bobot: number
    opsi_jawaban: OpsiJawaban[]
}

interface Kuis {
    id: number
    judul: string
    waktu_menit: number
    soal: Soal[]
}

interface PercobaanKuis {
    id: number
    percobaan_ke: number
    status: string
}

interface JawabanTersimpan {
    opsi_jawaban_id: number | null
    jawaban_text: string | null
}

const props = defineProps<{
    kuis: Kuis
    percobaanKuis: PercobaanKuis
    jawabanTersimpan: Record<string, JawabanTersimpan>
    sisaDetik: number
}>()

const soalAktif = ref(0)
const detik = ref(props.sisaDetik)
const sedangMenyimpan = ref(false)
const sedangSubmit = ref(false)
const pesan = ref('')
let timer: ReturnType<typeof setInterval> | undefined

const jawaban = ref<Record<number, JawabanTersimpan>>({})

for (const soal of props.kuis.soal) {
    const tersimpan = props.jawabanTersimpan[String(soal.id)]

    jawaban.value[soal.id] = {
        opsi_jawaban_id: tersimpan?.opsi_jawaban_id ?? null,
        jawaban_text: tersimpan?.jawaban_text ?? '',
    }
}

const soalSekarang = computed(
    () => props.kuis.soal[soalAktif.value],
)

const jumlahSoal = computed(() => props.kuis.soal.length)

const jumlahTerjawab = computed(() =>
    props.kuis.soal.filter((soal) => {
        const item = jawaban.value[soal.id]

        if (!item) return false

        if (soal.tipe === 'esai') {
            return Boolean(item.jawaban_text?.trim())
        }

        return item.opsi_jawaban_id !== null
    }).length,
)

const waktuTampilan = computed(() => {
    const menit = Math.floor(detik.value / 60)
    const sisa = detik.value % 60

    return `${String(menit).padStart(2, '0')}:${String(sisa).padStart(2, '0')}`
})

const form = useForm({
    soal_id: 0 as number,
    opsi_jawaban_id: null as number | null,
    jawaban_text: null as string | null,
})

function simpanJawaban(soal: Soal) {
    if (detik.value <= 0 || sedangSubmit.value) return

    const item = jawaban.value[soal.id]

    if (!item) return

    sedangMenyimpan.value = true
    pesan.value = ''

    form.soal_id = soal.id
    form.opsi_jawaban_id = item.opsi_jawaban_id
    form.jawaban_text = soal.tipe === 'esai'
        ? (item.jawaban_text?.trim() || null)
        : null

    form.post(
        route('peserta.kuis.saveAnswer', {
            percobaanKuis: props.percobaanKuis.id,
        }),
        {
            preserveScroll: true,
            onSuccess: () => {
                pesan.value = 'Jawaban berhasil disimpan.'
            },
            onError: (errors) => {
                pesan.value = Object.values(errors).join(' ')
            },
            onFinish: () => {
                sedangMenyimpan.value = false
            },
        },
    )
}

function pilihOpsi(soal: Soal, opsiId: number) {
    if (detik.value <= 0 || sedangSubmit.value) return

    jawaban.value[soal.id].opsi_jawaban_id = opsiId
    jawaban.value[soal.id].jawaban_text = null

    simpanJawaban(soal)
}

function ubahJawabanEsai(soal: Soal, teks: string) {
    jawaban.value[soal.id].jawaban_text = teks
    jawaban.value[soal.id].opsi_jawaban_id = null
}

function simpanDanLanjut() {
    if (!soalSekarang.value) return

    const soal = soalSekarang.value
    const item = jawaban.value[soal.id]

    if (
        soal.tipe !== 'esai'
        && item?.opsi_jawaban_id === null
    ) {
        pesan.value = 'Pilih jawaban terlebih dahulu.'
        return
    }

    simpanJawaban(soal)
}

function keSoal(index: number) {
    if (index >= 0 && index < jumlahSoal.value) {
        soalAktif.value = index
        pesan.value = ''
    }
}

function selesaikanKuis(otomatis = false) {
    if (sedangSubmit.value) return

    const konfirmasi = otomatis
        || window.confirm(
            'Apakah kamu yakin ingin menyelesaikan kuis? Jawaban yang belum tersimpan mungkin tidak ikut dinilai.',
        )

    if (!konfirmasi) return

    sedangSubmit.value = true

    if (timer) clearInterval(timer)

    // Simpan jawaban soal esai terakhir sebelum submit.
    const soalEsai = props.kuis.soal.filter(
        (soal) => soal.tipe === 'esai',
    )

    const simpanEsaiTerakhir = (index: number) => {
        if (index >= soalEsai.length) {
            router.post(
                route('peserta.kuis.submit', {
                    percobaanKuis: props.percobaanKuis.id,
                }),
            )
            return
        }

        const soal = soalEsai[index]
        const item = jawaban.value[soal.id]

        form.soal_id = soal.id
        form.opsi_jawaban_id = null
        form.jawaban_text = item?.jawaban_text?.trim() || null

        form.post(
            route('peserta.kuis.saveAnswer', {
                percobaanKuis: props.percobaanKuis.id,
            }),
            {
                preserveScroll: true,
                onSuccess: () => simpanEsaiTerakhir(index + 1),
                onError: () => {
                    sedangSubmit.value = false
                    pesan.value = 'Ada jawaban yang gagal disimpan. Periksa koneksi lalu coba lagi.'
                },
            },
        )
    }

    simpanEsaiTerakhir(0)
}

onMounted(() => {
    timer = setInterval(() => {
        if (detik.value > 0) {
            detik.value--
        }

        if (detik.value <= 0) {
            selesaikanKuis(true)
        }
    }, 1000)
})

onBeforeUnmount(() => {
    if (timer) clearInterval(timer)
})
</script>

<template>
    <Head :title="`Mengerjakan - ${kuis.judul}`" />

    <div class="mx-auto max-w-7xl space-y-5 p-4 sm:p-6">
        <div class="flex flex-col justify-between gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900 sm:flex-row sm:items-center">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Percobaan ke-{{ percobaanKuis.percobaan_ke }}
                </p>
                <h1 class="mt-1 text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">
                    {{ kuis.judul }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ jumlahTerjawab }} dari {{ jumlahSoal }} soal terjawab
                </p>
            </div>

            <div
                class="rounded-xl px-5 py-3 text-center"
                :class="detik <= 60
                    ? 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300'
                    : 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300'"
            >
                <p class="text-xs font-semibold uppercase tracking-wider">
                    Sisa waktu
                </p>
                <p class="mt-1 font-mono text-2xl font-bold">
                    {{ waktuTampilan }}
                </p>
            </div>
        </div>

        <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_280px]">
            <section
                v-if="soalSekarang"
                class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900 sm:p-7"
            >
                <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                    <span class="rounded-lg bg-indigo-50 px-3 py-1.5 text-sm font-semibold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                        Soal {{ soalAktif + 1 }} dari {{ jumlahSoal }}
                    </span>

                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        Bobot: {{ soalSekarang.bobot }}
                    </span>
                </div>

                <div class="mb-7 whitespace-pre-line text-base font-semibold leading-8 text-gray-900 dark:text-white sm:text-lg">
                    {{ soalSekarang.pertanyaan }}
                </div>

                <div
                    v-if="soalSekarang.tipe === 'esai'"
                    class="space-y-2"
                >
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                        Jawaban kamu
                    </label>

                    <textarea
                        :value="jawaban[soalSekarang.id]?.jawaban_text ?? ''"
                        rows="7"
                        maxlength="50000"
                        :disabled="detik <= 0 || sedangSubmit"
                        placeholder="Tuliskan jawabanmu di sini..."
                        class="w-full rounded-xl border border-gray-300 bg-white p-4 text-sm leading-6 text-gray-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 disabled:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:disabled:bg-gray-950"
                        @input="ubahJawabanEsai(soalSekarang, ($event.target as HTMLTextAreaElement).value)"
                    />

                    <p class="text-xs text-gray-500">
                        Jawaban esai akan dikoreksi oleh instruktur.
                    </p>
                </div>

                <div
                    v-else
                    class="space-y-3"
                >
                    <button
                        v-for="opsi in soalSekarang.opsi_jawaban"
                        :key="opsi.id"
                        type="button"
                        :disabled="detik <= 0 || sedangSubmit"
                        class="flex w-full items-start gap-4 rounded-xl border p-4 text-left transition disabled:cursor-not-allowed disabled:opacity-60"
                        :class="jawaban[soalSekarang.id]?.opsi_jawaban_id === opsi.id
                            ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-100 dark:border-indigo-400 dark:bg-indigo-950/50 dark:ring-indigo-900'
                            : 'border-gray-200 hover:border-indigo-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800'"
                        @click="pilihOpsi(soalSekarang, opsi.id)"
                    >
                        <span
                            class="flex size-9 shrink-0 items-center justify-center rounded-lg border text-sm font-bold"
                            :class="jawaban[soalSekarang.id]?.opsi_jawaban_id === opsi.id
                                ? 'border-indigo-600 bg-indigo-600 text-white'
                                : 'border-gray-300 text-gray-600 dark:border-gray-600 dark:text-gray-300'"
                        >
                            {{ opsi.opsi }}
                        </span>

                        <span class="pt-1 text-sm leading-6 text-gray-800 dark:text-gray-200">
                            {{ opsi.teks }}
                        </span>
                    </button>
                </div>

                <p
                    v-if="pesan"
                    class="mt-4 rounded-lg bg-blue-50 p-3 text-sm text-blue-700 dark:bg-blue-950/40 dark:text-blue-300"
                >
                    {{ pesan }}
                </p>

                <div class="mt-8 flex flex-wrap items-center justify-between gap-3 border-t border-gray-200 pt-5 dark:border-gray-700">
                    <button
                        type="button"
                        :disabled="soalAktif === 0"
                        class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800"
                        @click="keSoal(soalAktif - 1)"
                    >
                        ← Sebelumnya
                    </button>

                    <button
                        v-if="soalAktif < jumlahSoal - 1"
                        type="button"
                        class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-50"
                        :disabled="sedangMenyimpan || sedangSubmit || detik <= 0"
                        @click="simpanDanLanjut"
                    >
                        {{ sedangMenyimpan ? 'Menyimpan...' : 'Simpan & Lanjut →' }}
                    </button>

                    <button
                        v-else
                        type="button"
                        class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 disabled:opacity-50"
                        :disabled="sedangSubmit || detik <= 0"
                        @click="selesaikanKuis()"
                    >
                        {{ sedangSubmit ? 'Menyelesaikan...' : 'Selesaikan Kuis' }}
                    </button>
                </div>
            </section>

            <aside class="h-fit rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <h2 class="font-bold text-gray-900 dark:text-white">
                    Navigasi soal
                </h2>

                <div class="mt-4 grid grid-cols-5 gap-2">
                    <button
                        v-for="(soal, index) in kuis.soal"
                        :key="soal.id"
                        type="button"
                        class="flex aspect-square items-center justify-center rounded-lg border text-sm font-semibold transition"
                        :class="[
                            soalAktif === index
                                ? 'border-indigo-600 bg-indigo-600 text-white'
                                : jawaban[soal.id]?.opsi_jawaban_id !== null && jawaban[soal.id]?.opsi_jawaban_id !== undefined
                                    || Boolean(jawaban[soal.id]?.jawaban_text?.trim())
                                    ? 'border-emerald-300 bg-emerald-50 text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-300'
                                    : 'border-gray-200 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800',
                        ]"
                        @click="keSoal(index)"
                    >
                        {{ index + 1 }}
                    </button>
                </div>

                <div class="mt-5 space-y-2 text-xs text-gray-600 dark:text-gray-300">
                    <div class="flex items-center gap-2">
                        <span class="size-3 rounded bg-indigo-600"></span>
                        Soal aktif
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="size-3 rounded border border-emerald-400 bg-emerald-50 dark:bg-emerald-950"></span>
                        Sudah dijawab
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="size-3 rounded border border-gray-300 dark:border-gray-600"></span>
                        Belum dijawab
                    </div>
                </div>

                <div class="mt-5 rounded-xl bg-gray-50 p-4 dark:bg-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Progres pengerjaan
                    </p>

                    <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">
                        {{ jumlahTerjawab }}/{{ jumlahSoal }}
                    </p>

                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                        <div
                            class="h-full rounded-full bg-indigo-600 transition-all"
                            :style="{ width: `${jumlahSoal ? (jumlahTerjawab / jumlahSoal) * 100 : 0}%` }"
                        ></div>
                    </div>
                </div>

                <button
                    type="button"
                    class="mt-5 w-full rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-700 disabled:opacity-50"
                    :disabled="sedangSubmit || detik <= 0"
                    @click="selesaikanKuis()"
                >
                    Selesaikan Kuis
                </button>
            </aside>
        </div>
    </div>
</template>

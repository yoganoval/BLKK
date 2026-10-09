<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3'
import {
    Save,
    Users,
    ClipboardList,
    ArrowLeft,
    CheckCircle2,
} from 'lucide-vue-next'
import { computed, ref, watch } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

interface Kelas {
    id: number
    nama: string
}

interface Komponen {
    id: number
    kelas_id: number
    nama: string
    bobot: number
    nilai_maksimal: number
    keterangan?: string
    urutan: number
}

interface Peserta {
    id: number
    nama: string
}

interface Nilai {
    id: number
    peserta_id: number
    komponen_nilai_id: number
    nilai: number
    catatan?: string
}

const props = defineProps<{
    kelas: Kelas[]
    komponen: Komponen[]
    peserta: Peserta[]
    nilai: Nilai[]
    filters: {
        kelas_id?: number | string | null
        komponen_nilai_id?: number | string | null
    }
}>()

const kelasId = ref(
    props.filters?.kelas_id
        ? Number(props.filters.kelas_id)
        : null
)

const komponenId = ref(
    props.filters?.komponen_nilai_id
        ? Number(props.filters.komponen_nilai_id)
        : null
)

const selectedKelas = computed(() => {
    return props.kelas.find(
        item => item.id === kelasId.value
    )
})

const selectedKomponen = computed(() => {
    return props.komponen.find(
        item => item.id === komponenId.value
    )
})

/*
|--------------------------------------------------------------------------
| Komponen berdasarkan kelas
|--------------------------------------------------------------------------
*/

const komponenKelas = computed(() => {
    if (!kelasId.value) {
        return []
    }

    return props.komponen.filter(
        item => item.kelas_id === kelasId.value
    )
})

/*
|--------------------------------------------------------------------------
| Peserta
|--------------------------------------------------------------------------
|
| Controller sebaiknya sudah mengirim peserta berdasarkan
| kelas yang dipilih.
|
*/

const pesertaList = computed(() => {
    return props.peserta
})

/*
|--------------------------------------------------------------------------
| Form nilai
|--------------------------------------------------------------------------
*/

const nilaiForm = ref<
    Record<number, number | string>
>({})

const catatanForm = ref<
    Record<number, string>
>({})

/*
|--------------------------------------------------------------------------
| Ambil nilai lama
|--------------------------------------------------------------------------
*/

const loadNilai = () => {
    const values: Record<
        number,
        number | string
    > = {}

    const notes: Record<
        number,
        string
    > = {}

    props.nilai.forEach(item => {
        if (
            !komponenId.value ||
            item.komponen_nilai_id !==
                komponenId.value
        ) {
            return
        }

        values[item.peserta_id] = item.nilai

        notes[item.peserta_id] =
            item.catatan ?? ''
    })

    pesertaList.value.forEach(peserta => {
        if (
            values[peserta.id] === undefined
        ) {
            values[peserta.id] = ''
        }

        if (
            notes[peserta.id] === undefined
        ) {
            notes[peserta.id] = ''
        }
    })

    nilaiForm.value = values
    catatanForm.value = notes
}

/*
|--------------------------------------------------------------------------
| Ganti kelas
|--------------------------------------------------------------------------
*/

const changeKelas = () => {
    komponenId.value = null

    nilaiForm.value = {}
    catatanForm.value = {}
}

/*
|--------------------------------------------------------------------------
| Pilih komponen
|--------------------------------------------------------------------------
*/

const changeKomponen = () => {
    loadNilai()
}

/*
|--------------------------------------------------------------------------
| Watch
|--------------------------------------------------------------------------
*/

watch(
    () => props.nilai,
    () => {
        loadNilai()
    },
    {
        deep: true,
    }
)

watch(
    () => props.peserta,
    () => {
        loadNilai()
    },
    {
        deep: true,
    }
)

/*
|--------------------------------------------------------------------------
| Submit nilai massal
|--------------------------------------------------------------------------
*/

const form = useForm<{
    kelas_id: number | null
    komponen_nilai_id: number | null
    nilai: Record<number, number | string>
    catatan: Record<number, string>
}>({
    kelas_id: kelasId.value,
    komponen_nilai_id: komponenId.value,
    nilai: {},
    catatan: {},
})

const submit = () => {
    form.kelas_id = kelasId.value
    form.komponen_nilai_id = komponenId.value
    form.nilai = nilaiForm.value
    form.catatan = catatanForm.value

    form.post(
        route('penilaian.store'),
        {
            preserveScroll: true,
            onSuccess: () => {
                loadNilai()
            },
        }
    )
}

/*
|--------------------------------------------------------------------------
| Statistik
|--------------------------------------------------------------------------
*/

const jumlahPeserta = computed(
    () => pesertaList.value.length
)

const jumlahSudahDinilai = computed(() => {
    return pesertaList.value.filter(
        peserta => {
            const value =
                nilaiForm.value[peserta.id]

            return (
                value !== undefined &&
                value !== '' &&
                value !== null
            )
        }
    ).length
})

const progressPersen = computed(() => {
    if (!jumlahPeserta.value) {
        return 0
    }

    return Math.round(
        (jumlahSudahDinilai.value /
            jumlahPeserta.value) *
            100
    )
})

/*
|--------------------------------------------------------------------------
| Validasi nilai
|--------------------------------------------------------------------------
*/

const nilaiError = (
    pesertaId: number
) => {
    const value =
        nilaiForm.value[pesertaId]

    if (
        value === '' ||
        value === null ||
        value === undefined
    ) {
        return ''
    }

    const number = Number(value)

    if (Number.isNaN(number)) {
        return 'Nilai harus berupa angka.'
    }

    if (
        selectedKomponen.value &&
        number >
            Number(
                selectedKomponen.value
                    .nilai_maksimal
            )
    ) {
        return `Maksimal ${selectedKomponen.value.nilai_maksimal}.`
    }

    if (number < 0) {
        return 'Nilai tidak boleh kurang dari 0.'
    }

    return ''
}
</script>

<template>
    <Head title="Penilaian Peserta" />

    <div
        class="min-h-screen bg-slate-50 p-6
               dark:bg-slate-950"
    >
        <div class="mx-auto max-w-7xl">

            <!-- HEADER -->
            <div class="mb-6">
                <div
                    class="flex flex-wrap items-center
                           justify-between gap-4"
                >
                    <div>
                        <h1
                            class="text-2xl font-bold
                                   text-slate-900
                                   dark:text-white"
                        >
                            Penilaian Peserta
                        </h1>

                        <p
                            class="mt-1 text-sm
                                   text-slate-500
                                   dark:text-slate-400"
                        >
                            Input nilai seluruh peserta
                            dalam satu kelas.
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="
                            router.get(
                                route(
                                    'nilai-peserta.index'
                                )
                            )
                        "
                        class="inline-flex
                               items-center gap-2
                               rounded-lg border
                               border-slate-300
                               bg-white px-4 py-2
                               text-sm font-medium
                               text-slate-700
                               hover:bg-slate-50
                               dark:border-slate-700
                               dark:bg-slate-900
                               dark:text-slate-300"
                    >
                        <ArrowLeft
                            class="h-4 w-4"
                        />

                        Daftar Nilai
                    </button>
                </div>
            </div>

            <!-- FILTER -->
            <div
                class="rounded-xl border
                       border-slate-200
                       bg-white p-5 shadow-sm
                       dark:border-slate-800
                       dark:bg-slate-900"
            >
                <div
                    class="grid gap-5
                           md:grid-cols-2"
                >

                    <!-- KELAS -->
                    <div>
                        <label
                            class="mb-2 block
                                   text-sm font-medium
                                   text-slate-700
                                   dark:text-slate-300"
                        >
                            Kelas
                        </label>

                        <select
                            v-model="kelasId"
                            @change="changeKelas"
                            class="w-full rounded-lg
                                   border-slate-300
                                   bg-white
                                   dark:border-slate-700
                                   dark:bg-slate-800"
                        >
                            <option
                                :value="null"
                            >
                                Pilih Kelas
                            </option>

                            <option
                                v-for="item in kelas"
                                :key="item.id"
                                :value="item.id"
                            >
                                {{ item.nama }}
                            </option>
                        </select>
                    </div>

                    <!-- KOMPONEN -->
                    <div>
                        <label
                            class="mb-2 block
                                   text-sm font-medium
                                   text-slate-700
                                   dark:text-slate-300"
                        >
                            Komponen Nilai
                        </label>

                        <select
                            v-model="komponenId"
                            @change="
                                changeKomponen
                            "
                            :disabled="!kelasId"
                            class="w-full rounded-lg
                                   border-slate-300
                                   bg-white
                                   disabled:cursor-not-allowed
                                   disabled:bg-slate-100
                                   dark:border-slate-700
                                   dark:bg-slate-800
                                   dark:disabled:bg-slate-950"
                        >
                            <option
                                :value="null"
                            >
                                Pilih Komponen
                            </option>

                            <option
                                v-for="
                                    item in
                                    komponenKelas
                                "
                                :key="item.id"
                                :value="item.id"
                            >
                                {{ item.nama }}
                                —
                                {{ item.bobot }}%
                            </option>
                        </select>
                    </div>

                </div>

                <!-- INFO KOMPONEN -->
                <div
                    v-if="selectedKomponen"
                    class="mt-5 rounded-lg
                           bg-blue-50 p-4
                           dark:bg-blue-950/30"
                >
                    <div
                        class="flex flex-wrap
                               items-center gap-6"
                    >
                        <div>
                            <div
                                class="text-xs
                                       text-slate-500"
                            >
                                Komponen
                            </div>

                            <div
                                class="font-semibold
                                       text-slate-900
                                       dark:text-white"
                            >
                                {{ selectedKomponen.nama }}
                            </div>
                        </div>

                        <div>
                            <div
                                class="text-xs
                                       text-slate-500"
                            >
                                Bobot
                            </div>

                            <div
                                class="font-semibold
                                       text-blue-600"
                            >
                                {{ selectedKomponen.bobot }}%
                            </div>
                        </div>

                        <div>
                            <div
                                class="text-xs
                                       text-slate-500"
                            >
                                Nilai Maksimal
                            </div>

                            <div
                                class="font-semibold
                                       text-slate-900
                                       dark:text-white"
                            >
                                {{
                                    selectedKomponen
                                        .nilai_maksimal
                                }}
                            </div>
                        </div>

                        <div
                            v-if="
                                selectedKomponen
                                    .keterangan
                            "
                        >
                            <div
                                class="text-xs
                                       text-slate-500"
                            >
                                Keterangan
                            </div>

                            <div
                                class="text-sm
                                       text-slate-700
                                       dark:text-slate-300"
                            >
                                {{
                                    selectedKomponen
                                        .keterangan
                                }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STATISTIK -->
            <div
                v-if="
                    kelasId &&
                    komponenId
                "
                class="mt-5 grid gap-4
                       sm:grid-cols-3"
            >
                <div
                    class="rounded-xl border
                           border-slate-200
                           bg-white p-5
                           dark:border-slate-800
                           dark:bg-slate-900"
                >
                    <div
                        class="flex items-center
                               gap-3"
                    >
                        <div
                            class="rounded-lg
                                   bg-blue-100 p-2
                                   dark:bg-blue-900/30"
                        >
                            <Users
                                class="h-5 w-5
                                       text-blue-600"
                            />
                        </div>

                        <div>
                            <div
                                class="text-xs
                                       text-slate-500"
                            >
                                Jumlah Peserta
                            </div>

                            <div
                                class="text-xl
                                       font-bold"
                            >
                                {{ jumlahPeserta }}
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-xl border
                           border-slate-200
                           bg-white p-5
                           dark:border-slate-800
                           dark:bg-slate-900"
                >
                    <div
                        class="flex items-center
                               gap-3"
                    >
                        <div
                            class="rounded-lg
                                   bg-green-100 p-2
                                   dark:bg-green-900/30"
                        >
                            <CheckCircle2
                                class="h-5 w-5
                                       text-green-600"
                            />
                        </div>

                        <div>
                            <div
                                class="text-xs
                                       text-slate-500"
                            >
                                Sudah Dinilai
                            </div>

                            <div
                                class="text-xl
                                       font-bold"
                            >
                                {{ jumlahSudahDinilai }}
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-xl border
                           border-slate-200
                           bg-white p-5
                           dark:border-slate-800
                           dark:bg-slate-900"
                >
                    <div
                        class="flex items-center
                               gap-3"
                    >
                        <div
                            class="rounded-lg
                                   bg-purple-100 p-2
                                   dark:bg-purple-900/30"
                        >
                            <ClipboardList
                                class="h-5 w-5
                                       text-purple-600"
                            />
                        </div>

                        <div class="flex-1">
                            <div
                                class="flex
                                       justify-between
                                       text-xs
                                       text-slate-500"
                            >
                                <span>
                                    Progress
                                </span>

                                <span>
                                    {{ progressPersen }}%
                                </span>
                            </div>

                            <div
                                class="mt-2 h-2
                                       overflow-hidden
                                       rounded-full
                                       bg-slate-200
                                       dark:bg-slate-700"
                            >
                                <div
                                    class="h-full
                                           rounded-full
                                           bg-purple-600
                                           transition-all"
                                    :style="{
                                        width:
                                            progressPersen +
                                            '%'
                                    }"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABEL -->
            <div
                v-if="
                    kelasId &&
                    komponenId
                "
                class="mt-5 overflow-hidden
                       rounded-xl border
                       border-slate-200
                       bg-white shadow-sm
                       dark:border-slate-800
                       dark:bg-slate-900"
            >
                <div
                    class="flex flex-wrap
                           items-center
                           justify-between gap-4
                           border-b
                           border-slate-200
                           px-5 py-4
                           dark:border-slate-800"
                >
                    <div>
                        <h2
                            class="font-semibold
                                   text-slate-900
                                   dark:text-white"
                        >
                            Daftar Nilai
                        </h2>

                        <p
                            class="text-sm
                                   text-slate-500"
                        >
                            {{
                                selectedKelas?.nama
                            }}
                            —
                            {{
                                selectedKomponen?.nama
                            }}
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="submit"
                        :disabled="
                            form.processing ||
                            pesertaList.length === 0
                        "
                        class="inline-flex
                               items-center gap-2
                               rounded-lg
                               bg-blue-600 px-5 py-2.5
                               text-sm font-medium
                               text-white
                               hover:bg-blue-700
                               disabled:cursor-not-allowed
                               disabled:opacity-50"
                    >
                        <Save
                            class="h-4 w-4"
                        />

                        {{
                            form.processing
                                ? 'Menyimpan...'
                                : 'Simpan Semua Nilai'
                        }}
                    </button>
                </div>

                <div
                    v-if="
                        pesertaList.length === 0
                    "
                    class="px-5 py-12
                           text-center
                           text-slate-500"
                >
                    Tidak ada peserta pada
                    kelas ini.
                </div>

                <div
                    v-else
                    class="overflow-x-auto"
                >
                    <table
                        class="w-full text-sm"
                    >
                        <thead
                            class="bg-slate-50
                                   dark:bg-slate-800"
                        >
                            <tr>
                                <th
                                    class="w-16 px-5 py-3
                                           text-center"
                                >
                                    No
                                </th>

                                <th
                                    class="px-5 py-3
                                           text-left"
                                >
                                    Peserta
                                </th>

                                <th
                                    class="w-48 px-5 py-3
                                           text-left"
                                >
                                    Nilai
                                </th>

                                <th
                                    class="w-72 px-5 py-3
                                           text-left"
                                >
                                    Catatan
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr
                                v-for="
                                    (
                                        item,
                                        index
                                    ) in pesertaList
                                "
                                :key="item.id"
                                class="border-t
                                       border-slate-200
                                       dark:border-slate-800"
                            >
                                <td
                                    class="px-5 py-4
                                           text-center
                                           text-slate-500"
                                >
                                    {{ index + 1 }}
                                </td>

                                <td
                                    class="px-5 py-4"
                                >
                                    <div
                                        class="font-medium
                                               text-slate-900
                                               dark:text-white"
                                    >
                                        {{ item.nama }}
                                    </div>

                                    <div
                                        class="text-xs
                                               text-slate-500"
                                    >
                                        ID:
                                        {{ item.id }}
                                    </div>
                                </td>

                                <td
                                    class="px-5 py-4"
                                >
                                    <input
                                        v-model="
                                            nilaiForm[
                                                item.id
                                            ]
                                        "
                                        type="number"
                                        min="0"
                                        :max="
                                            selectedKomponen
                                                ?.nilai_maksimal
                                        "
                                        step="0.01"
                                        class="w-full rounded-lg
                                               border-slate-300
                                               text-center
                                               font-semibold
                                               dark:border-slate-700
                                               dark:bg-slate-800"
                                        placeholder="0"
                                    />

                                    <p
                                        v-if="
                                            nilaiError(
                                                item.id
                                            )
                                        "
                                        class="mt-1
                                               text-xs
                                               text-red-500"
                                    >
                                        {{
                                            nilaiError(
                                                item.id
                                            )
                                        }}
                                    </p>
                                </td>

                                <td
                                    class="px-5 py-4"
                                >
                                    <input
                                        v-model="
                                            catatanForm[
                                                item.id
                                            ]
                                        "
                                        type="text"
                                        class="w-full rounded-lg
                                               border-slate-300
                                               dark:border-slate-700
                                               dark:bg-slate-800"
                                        placeholder="Catatan..."
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-if="
                        pesertaList.length > 0
                    "
                    class="flex items-center
                           justify-between
                           border-t
                           border-slate-200
                           bg-slate-50 px-5 py-4
                           dark:border-slate-800
                           dark:bg-slate-800/50"
                >
                    <div
                        class="text-sm
                               text-slate-500"
                    >
                        {{ jumlahSudahDinilai }}
                        dari
                        {{ jumlahPeserta }}
                        peserta sudah memiliki
                        nilai.
                    </div>

                    <button
                        type="button"
                        @click="submit"
                        :disabled="
                            form.processing
                        "
                        class="inline-flex
                               items-center gap-2
                               rounded-lg
                               bg-blue-600 px-5 py-2.5
                               text-sm font-medium
                               text-white
                               hover:bg-blue-700
                               disabled:opacity-50"
                    >
                        <Save
                            class="h-4 w-4"
                        />

                        {{
                            form.processing
                                ? 'Menyimpan...'
                                : 'Simpan Semua Nilai'
                        }}
                    </button>
                </div>
            </div>

            <!-- BELUM MEMILIH -->
            <div
                v-else
                class="mt-5 rounded-xl border
                       border-dashed
                       border-slate-300
                       bg-white p-12 text-center
                       dark:border-slate-700
                       dark:bg-slate-900"
            >
                <ClipboardList
                    class="mx-auto h-12 w-12
                           text-slate-400"
                />

                <h3
                    class="mt-4 font-semibold
                           text-slate-900
                           dark:text-white"
                >
                    Pilih kelas dan komponen
                </h3>

                <p
                    class="mt-1 text-sm
                           text-slate-500"
                >
                    Pilih kelas dan komponen nilai
                    untuk mulai memasukkan nilai
                    peserta.
                </p>
            </div>

        </div>
    </div>
</template>
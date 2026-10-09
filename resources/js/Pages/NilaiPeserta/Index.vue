<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import {
    Plus,
    Pencil,
    Trash2,
    Eye,
    Search,
    Filter,
    Award,
    GraduationCap,
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
    penilai?: {
        id: number
        name: string
    }
}

const props = defineProps<{
    nilai: {
        data: Nilai[]
        links: any[]
        current_page: number
        total: number
    }
    peserta: Peserta[]
    komponen: Komponen[]
    kelas: Kelas[]
    filters: {
        peserta_id?: number | string | null
        komponen_nilai_id?: number | string | null
        kelas_id?: number | string | null
    }
}>()

const kelasId = ref(
    props.filters?.kelas_id
        ? Number(props.filters.kelas_id)
        : null
)

const pesertaId = ref(
    props.filters?.peserta_id
        ? Number(props.filters.peserta_id)
        : null
)

const komponenId = ref(
    props.filters?.komponen_nilai_id
        ? Number(props.filters.komponen_nilai_id)
        : null
)

const submitFilter = () => {
    router.get(
        route('nilai-peserta.index'),
        {
            kelas_id: kelasId.value || undefined,
            peserta_id: pesertaId.value || undefined,
            komponen_nilai_id:
                komponenId.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    )
}

const resetFilter = () => {
    kelasId.value = null
    pesertaId.value = null
    komponenId.value = null

    router.get(
        route('nilai-peserta.index'),
        {},
        {
            preserveState: true,
            replace: true,
        }
    )
}

const confirmDelete = (item: Nilai) => {
    if (
        confirm(
            `Yakin ingin menghapus nilai ${item.peserta?.nama ?? 'peserta'}?`
        )
    ) {
        router.delete(
            route('nilai-peserta.destroy', item.id),
            {
                preserveScroll: true,
            }
        )
    }
}

const nilaiClass = (nilai: number) => {
    const value = Number(nilai)

    if (value >= 80) {
        return 'text-emerald-600 dark:text-emerald-400'
    }

    if (value >= 60) {
        return 'text-blue-600 dark:text-blue-400'
    }

    if (value >= 50) {
        return 'text-amber-600 dark:text-amber-400'
    }

    return 'text-red-600 dark:text-red-400'
}
</script>

<template>
    <Head title="Nilai Peserta" />

    <div class="space-y-6">
        <!-- Header -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1
                    class="text-2xl font-bold text-slate-900 dark:text-white"
                >
                    Nilai Peserta
                </h1>

                <p
                    class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                >
                    Kelola nilai peserta berdasarkan komponen penilaian.
                </p>
            </div>

            <Link
                :href="route('nilai-peserta.create')"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white hover:opacity-90"
            >
                <Plus class="h-4 w-4" />
                Tambah Nilai
            </Link>
        </div>

        <!-- Filter -->
        <div
            class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900"
        >
            <div class="mb-4 flex items-center gap-2">
                <Filter class="h-4 w-4 text-slate-500" />

                <h2
                    class="font-semibold text-slate-900 dark:text-white"
                >
                    Filter Nilai
                </h2>
            </div>

            <div
                class="grid gap-3 md:grid-cols-2 lg:grid-cols-4"
            >
                <!-- Kelas -->
                <select
                    v-model="kelasId"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-slate-950"
                >
                    <option :value="null">
                        Semua Kelas
                    </option>

                    <option
                        v-for="item in kelas"
                        :key="item.id"
                        :value="item.id"
                    >
                        {{ item.nama }}
                    </option>
                </select>

                <!-- Peserta -->
                <select
                    v-model="pesertaId"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-slate-950"
                >
                    <option :value="null">
                        Semua Peserta
                    </option>

                    <option
                        v-for="item in peserta"
                        :key="item.id"
                        :value="item.id"
                    >
                        {{ item.nama }}
                    </option>
                </select>

                <!-- Komponen -->
                <select
                    v-model="komponenId"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-slate-950"
                >
                    <option :value="null">
                        Semua Komponen
                    </option>

                    <option
                        v-for="item in komponen"
                        :key="item.id"
                        :value="item.id"
                    >
                        {{ item.nama }}
                        -
                        {{ item.kelas?.nama }}
                    </option>
                </select>

                <div class="flex gap-2">
                    <button
                        type="button"
                        class="flex-1 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white hover:opacity-90"
                        @click="submitFilter"
                    >
                        <Search
                            class="mr-1 inline-block h-4 w-4"
                        />
                        Filter
                    </button>

                    <button
                        type="button"
                        class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                        @click="resetFilter"
                    >
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div
            class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
        >
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-sm">
                    <thead
                        class="border-b border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-950"
                    >
                        <tr>
                            <th class="px-5 py-4 text-left">
                                #
                            </th>

                            <th class="px-5 py-4 text-left">
                                Peserta
                            </th>

                            <th class="px-5 py-4 text-left">
                                Komponen
                            </th>

                            <th class="px-5 py-4 text-center">
                                Bobot
                            </th>

                            <th class="px-5 py-4 text-center">
                                Nilai
                            </th>

                            <th class="px-5 py-4 text-left">
                                Penilai
                            </th>

                            <th class="px-5 py-4 text-right">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody
                        v-if="nilai.data.length"
                        class="divide-y divide-slate-200 dark:divide-slate-800"
                    >
                        <tr
                            v-for="(item, index) in nilai.data"
                            :key="item.id"
                            class="hover:bg-slate-50 dark:hover:bg-slate-800/50"
                        >
                            <td class="px-5 py-4 text-slate-500">
                                {{
                                    (nilai.current_page - 1) *
                                        15 +
                                    index +
                                    1
                                }}
                            </td>

                            <td class="px-5 py-4">
                                <div
                                    class="font-semibold text-slate-900 dark:text-white"
                                >
                                    {{ item.peserta?.nama ?? '-' }}
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <div
                                    class="font-medium text-slate-800 dark:text-slate-200"
                                >
                                    {{
                                        item.komponen_nilai?.nama ??
                                        '-'
                                    }}
                                </div>

                                <div
                                    class="text-xs text-slate-500"
                                >
                                    {{
                                        item.komponen_nilai?.kelas
                                            ?.nama ?? '-'
                                    }}
                                </div>
                            </td>

                            <td
                                class="px-5 py-4 text-center"
                            >
                                {{
                                    item.komponen_nilai?.bobot ??
                                    0
                                }}%
                            </td>

                            <td
                                class="px-5 py-4 text-center"
                            >
                                <span
                                    class="text-lg font-bold"
                                    :class="
                                        nilaiClass(
                                            Number(item.nilai)
                                        )
                                    "
                                >
                                    {{ item.nilai }}
                                </span>

                                <span
                                    class="text-xs text-slate-400"
                                >
                                    /
                                    {{
                                        item.komponen_nilai
                                            ?.nilai_maksimal ?? 100
                                    }}
                                </span>
                            </td>

                            <td
                                class="px-5 py-4 text-slate-600 dark:text-slate-400"
                            >
                                {{
                                    item.penilai?.name ?? '-'
                                }}
                            </td>

                            <td class="px-5 py-4">
                                <div
                                    class="flex justify-end gap-1"
                                >
                                    <Link
                                        :href="
                                            route(
                                                'nilai-peserta.show',
                                                item.id
                                            )
                                        "
                                        class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800"
                                        title="Detail"
                                    >
                                        <Eye class="h-4 w-4" />
                                    </Link>

                                    <Link
                                        :href="
                                            route(
                                                'nilai-peserta.edit',
                                                item.id
                                            )
                                        "
                                        class="rounded-lg p-2 text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/20"
                                        title="Edit"
                                    >
                                        <Pencil
                                            class="h-4 w-4"
                                        />
                                    </Link>

                                    <button
                                        type="button"
                                        class="rounded-lg p-2 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                                        title="Hapus"
                                        @click="
                                            confirmDelete(item)
                                        "
                                    >
                                        <Trash2
                                            class="h-4 w-4"
                                        />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>

                    <tbody v-else>
                        <tr>
                            <td
                                colspan="7"
                                class="px-5 py-12 text-center"
                            >
                                <Award
                                    class="mx-auto mb-3 h-10 w-10 text-slate-400"
                                />

                                <p
                                    class="font-semibold text-slate-900 dark:text-white"
                                >
                                    Belum ada data nilai
                                </p>

                                <p
                                    class="mt-1 text-sm text-slate-500"
                                >
                                    Silakan tambahkan nilai peserta.
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="nilai.links?.length > 3"
                class="flex flex-wrap justify-end gap-1 border-t border-slate-200 px-5 py-4 dark:border-slate-800"
            >
                <template
                    v-for="(link, index) in nilai.links"
                    :key="index"
                >
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        v-html="link.label"
                        class="rounded-lg px-3 py-1.5 text-sm"
                        :class="
                            link.active
                                ? 'bg-primary text-white'
                                : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800'
                        "
                    />

                    <span
                        v-else
                        v-html="link.label"
                        class="px-3 py-1.5 text-sm text-slate-400"
                    />
                </template>
            </div>
        </div>
    </div>
</template>
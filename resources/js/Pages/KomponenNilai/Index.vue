<script setup lang="ts">
import { computed, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import {
    Plus,
    Pencil,
    Trash2,
    Search,
    ListChecks,
    GraduationCap,
    Percent,
    Award,
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

interface KomponenNilai {
    id: number
    kelas_id: number
    nama: string
    bobot: number
    nilai_maksimal: number
    keterangan?: string | null
    urutan: number
    kelas?: Kelas
}

const props = defineProps<{
    komponenNilai: {
        data: KomponenNilai[]
        links: any[]
        current_page: number
        last_page: number
        total: number
    }
    kelas: Kelas[]
    filters: {
        search?: string
        kelas_id?: number | string | null
    }
}>()

const search = ref(props.filters?.search ?? '')
const kelasId = ref(
    props.filters?.kelas_id
        ? Number(props.filters.kelas_id)
        : null
)

const submitFilter = () => {
    router.get(
        route('komponen-nilai.index'),
        {
            search: search.value || undefined,
            kelas_id: kelasId.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    )
}

const resetFilter = () => {
    search.value = ''
    kelasId.value = null

    router.get(
        route('komponen-nilai.index'),
        {},
        {
            preserveState: true,
            replace: true,
        }
    )
}

const totalBobot = computed(() => {
    return props.komponenNilai.data.reduce(
        (total, item) => total + Number(item.bobot),
        0
    )
})

const bobotClass = computed(() => {
    if (totalBobot.value === 100) {
        return 'text-emerald-600 dark:text-emerald-400'
    }

    if (totalBobot.value > 100) {
        return 'text-red-600 dark:text-red-400'
    }

    return 'text-amber-600 dark:text-amber-400'
})

const confirmDelete = (item: KomponenNilai) => {
    if (
        confirm(
            `Yakin ingin menghapus komponen "${item.nama}"?`
        )
    ) {
        router.delete(
            route('komponen-nilai.destroy', item.id),
            {
                preserveScroll: true,
            }
        )
    }
}
</script>

<template>
    <Head title="Komponen Nilai" />

    <div class="space-y-6">
        <!-- Header -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1
                    class="text-2xl font-bold text-slate-900 dark:text-white"
                >
                    Komponen Nilai
                </h1>

                <p
                    class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                >
                    Kelola komponen penilaian berdasarkan kelas.
                </p>
            </div>

            <Link
                :href="route('komponen-nilai.create')"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white transition hover:opacity-90"
            >
                <Plus class="h-4 w-4" />
                Tambah Komponen
            </Link>
        </div>

        <!-- Statistik -->
        <div class="grid gap-4 sm:grid-cols-3">
            <div
                class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="rounded-lg bg-blue-100 p-2.5 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400"
                    >
                        <ListChecks class="h-5 w-5" />
                    </div>

                    <div>
                        <p
                            class="text-sm text-slate-500 dark:text-slate-400"
                        >
                            Total Komponen
                        </p>

                        <p
                            class="text-xl font-bold text-slate-900 dark:text-white"
                        >
                            {{ komponenNilai.total }}
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="rounded-lg bg-purple-100 p-2.5 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400"
                    >
                        <GraduationCap class="h-5 w-5" />
                    </div>

                    <div>
                        <p
                            class="text-sm text-slate-500 dark:text-slate-400"
                        >
                            Total Kelas
                        </p>

                        <p
                            class="text-xl font-bold text-slate-900 dark:text-white"
                        >
                            {{ kelas.length }}
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="rounded-lg bg-emerald-100 p-2.5 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400"
                    >
                        <Percent class="h-5 w-5" />
                    </div>

                    <div>
                        <p
                            class="text-sm text-slate-500 dark:text-slate-400"
                        >
                            Total Bobot
                        </p>

                        <p
                            class="text-xl font-bold"
                            :class="bobotClass"
                        >
                            {{ totalBobot }}%
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div
            class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900"
        >
            <div
                class="grid gap-3 md:grid-cols-[1fr_250px_auto_auto]"
            >
                <!-- Search -->
                <div class="relative">
                    <Search
                        class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                    />

                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari komponen..."
                        class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-slate-950"
                        @keyup.enter="submitFilter"
                    />
                </div>

                <!-- Kelas -->
                <select
                    v-model="kelasId"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-slate-950"
                    @change="submitFilter"
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
                        ({{ item.durasi_hari }} hari)
                    </option>
                </select>

                <button
                    type="button"
                    class="rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white hover:opacity-90"
                    @click="submitFilter"
                >
                    Cari
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
                            <th
                                class="px-5 py-4 text-left font-semibold text-slate-700 dark:text-slate-300"
                            >
                                #
                            </th>

                            <th
                                class="px-5 py-4 text-left font-semibold text-slate-700 dark:text-slate-300"
                            >
                                Komponen
                            </th>

                            <th
                                class="px-5 py-4 text-left font-semibold text-slate-700 dark:text-slate-300"
                            >
                                Kelas
                            </th>

                            <th
                                class="px-5 py-4 text-center font-semibold text-slate-700 dark:text-slate-300"
                            >
                                Bobot
                            </th>

                            <th
                                class="px-5 py-4 text-center font-semibold text-slate-700 dark:text-slate-300"
                            >
                                Nilai Maks.
                            </th>

                            <th
                                class="px-5 py-4 text-center font-semibold text-slate-700 dark:text-slate-300"
                            >
                                Urutan
                            </th>

                            <th
                                class="px-5 py-4 text-left font-semibold text-slate-700 dark:text-slate-300"
                            >
                                Keterangan
                            </th>

                            <th
                                class="px-5 py-4 text-right font-semibold text-slate-700 dark:text-slate-300"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody
                        v-if="komponenNilai.data.length"
                        class="divide-y divide-slate-200 dark:divide-slate-800"
                    >
                        <tr
                            v-for="(item, index) in komponenNilai.data"
                            :key="item.id"
                            class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50"
                        >
                            <td
                                class="px-5 py-4 text-slate-500 dark:text-slate-400"
                            >
                                {{
                                    (komponenNilai.current_page - 1) * 10 +
                                    index +
                                    1
                                }}
                            </td>

                            <td class="px-5 py-4">
                                <div
                                    class="font-semibold text-slate-900 dark:text-white"
                                >
                                    {{ item.nama }}
                                </div>

                                <div
                                    class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                                >
                                    Komponen #{{ item.id }}
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <div
                                    class="font-medium text-slate-700 dark:text-slate-300"
                                >
                                    {{ item.kelas?.nama ?? '-' }}
                                </div>

                                <div
                                    v-if="item.kelas"
                                    class="text-xs text-slate-500"
                                >
                                    {{ item.kelas.durasi_hari }} hari
                                </div>
                            </td>

                            <td class="px-5 py-4 text-center">
                                <span
                                    class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400"
                                >
                                    {{ item.bobot }}%
                                </span>
                            </td>

                            <td
                                class="px-5 py-4 text-center font-medium text-slate-700 dark:text-slate-300"
                            >
                                {{ item.nilai_maksimal }}
                            </td>

                            <td
                                class="px-5 py-4 text-center text-slate-600 dark:text-slate-400"
                            >
                                {{ item.urutan }}
                            </td>

                            <td
                                class="max-w-[250px] px-5 py-4 text-slate-600 dark:text-slate-400"
                            >
                                <span class="line-clamp-2">
                                    {{ item.keterangan || '-' }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <div
                                    class="flex justify-end gap-2"
                                >
                                    <Link
                                        :href="
                                            route(
                                                'komponen-nilai.edit',
                                                item.id
                                            )
                                        "
                                        class="rounded-lg p-2 text-blue-600 transition hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/20"
                                        title="Edit"
                                    >
                                        <Pencil class="h-4 w-4" />
                                    </Link>

                                    <button
                                        type="button"
                                        class="rounded-lg p-2 text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                                        title="Hapus"
                                        @click="confirmDelete(item)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>

                    <!-- Empty -->
                    <tbody v-else>
                        <tr>
                            <td
                                colspan="8"
                                class="px-5 py-12 text-center"
                            >
                                <div
                                    class="mx-auto flex max-w-sm flex-col items-center"
                                >
                                    <Award
                                        class="mb-3 h-10 w-10 text-slate-400"
                                    />

                                    <h3
                                        class="font-semibold text-slate-900 dark:text-white"
                                    >
                                        Belum ada komponen nilai
                                    </h3>

                                    <p
                                        class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                                    >
                                        Tambahkan komponen penilaian untuk
                                        kelas.
                                    </p>

                                    <Link
                                        :href="
                                            route(
                                                'komponen-nilai.create'
                                            )
                                        "
                                        class="mt-4 inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:opacity-90"
                                    >
                                        <Plus class="h-4 w-4" />
                                        Tambah Komponen
                                    </Link>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="komponenNilai.links?.length > 3"
                class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-5 py-4 dark:border-slate-800"
            >
                <p
                    class="text-sm text-slate-500 dark:text-slate-400"
                >
                    Total {{ komponenNilai.total }} komponen
                </p>

                <div class="flex flex-wrap gap-1">
                    <template
                        v-for="(link, index) in komponenNilai.links"
                        :key="index"
                    >
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            v-html="link.label"
                            class="rounded-lg px-3 py-1.5 text-sm transition"
                            :class="
                                link.active
                                    ? 'bg-primary text-white'
                                    : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800'
                            "
                        />

                        <span
                            v-else
                            v-html="link.label"
                            class="rounded-lg px-3 py-1.5 text-sm text-slate-400"
                        />
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
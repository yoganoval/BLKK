
<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

interface ProgramPelatihan {
    id: number;
    nama?: string;
    pelatihan?: { nama: string };
    tahap?: { nama: string };
}

interface Kelas {
    id: number;
    nama: string;
    durasi_hari?: number;
}

const props = defineProps<{
    programPelatihans: ProgramPelatihan[];
    kelass: Kelas[];
}>();

const form = useForm({
    program_pelatihan_id: '',
    kelas_id: '',
    judul: '',
    instruksi: '',
    deadline: '',
    nilai_maksimal: 100,
    status: 'draft',
});

function submit() {
    form.post(route('tugas.store'));
}
</script>

<template>
    <Head title="Tambah Tugas" />

    <div class="mx-auto max-w-4xl space-y-6 p-4 sm:p-6">
        <div>
            <Link
                :href="route('tugas.index')"
                class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400"
            >
                ← Kembali ke daftar tugas
            </Link>

            <h1 class="mt-3 text-2xl font-bold text-slate-800 dark:text-white">
                Tambah Tugas
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Buat tugas baru untuk program pelatihan dan kelas yang dipilih.
            </p>
        </div>

        <form
            @submit.prevent="submit"
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800"
        >
            <div class="border-b border-slate-200 px-6 py-5 dark:border-slate-700">
                <h2 class="font-semibold text-slate-800 dark:text-white">
                    Informasi Tugas
                </h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Isi informasi tugas dengan lengkap.
                </p>
            </div>

            <div class="space-y-5 p-6">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Program Pelatihan <span class="text-red-500">*</span>
                    </label>
                    <select
                        v-model="form.program_pelatihan_id"
                        required
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                        :class="{ 'border-red-500': form.errors.program_pelatihan_id }"
                    >
                        <option value="">Pilih program pelatihan</option>
                        <option
                            v-for="p in props.programPelatihans"
                            :key="p.id"
                            :value="String(p.id)"
                        >
                            {{ p.pelatihan?.nama ?? p.nama ?? `Program #${p.id}` }}
                            {{ p.tahap?.nama ? ` - ${p.tahap.nama}` : '' }}
                        </option>
                    </select>
                    <p v-if="form.errors.program_pelatihan_id" class="mt-1 text-sm text-red-500">
                        {{ form.errors.program_pelatihan_id }}
                    </p>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Kelas <span class="text-red-500">*</span>
                    </label>
                    <select
                        v-model="form.kelas_id"
                        required
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                        :class="{ 'border-red-500': form.errors.kelas_id }"
                    >
                        <option value="">Pilih kelas</option>
                        <option
                            v-for="k in props.kelass"
                            :key="k.id"
                            :value="String(k.id)"
                        >
                            {{ k.nama }}
                            {{ k.durasi_hari ? ` (${k.durasi_hari} hari)` : '' }}
                        </option>
                    </select>
                    <p v-if="form.errors.kelas_id" class="mt-1 text-sm text-red-500">
                        {{ form.errors.kelas_id }}
                    </p>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Judul Tugas <span class="text-red-500">*</span>
                    </label>
                    <input
                        v-model="form.judul"
                        type="text"
                        maxlength="255"
                        required
                        placeholder="Contoh: Praktik Microsoft Excel"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                        :class="{ 'border-red-500': form.errors.judul }"
                    />
                    <p v-if="form.errors.judul" class="mt-1 text-sm text-red-500">
                        {{ form.errors.judul }}
                    </p>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Instruksi Tugas
                    </label>
                    <textarea
                        v-model="form.instruksi"
                        rows="6"
                        placeholder="Tuliskan petunjuk, ketentuan pengerjaan, dan format pengumpulan tugas..."
                        class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                        :class="{ 'border-red-500': form.errors.instruksi }"
                    />
                    <p v-if="form.errors.instruksi" class="mt-1 text-sm text-red-500">
                        {{ form.errors.instruksi }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                            Deadline
                        </label>
                        <input
                            v-model="form.deadline"
                            type="datetime-local"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                            :class="{ 'border-red-500': form.errors.deadline }"
                        />
                        <p v-if="form.errors.deadline" class="mt-1 text-sm text-red-500">
                            {{ form.errors.deadline }}
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            Kosongkan jika tidak ada batas waktu.
                        </p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                            Nilai Maksimal <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model.number="form.nilai_maksimal"
                            type="number"
                            min="0"
                            step="0.01"
                            required
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                            :class="{ 'border-red-500': form.errors.nilai_maksimal }"
                        />
                        <p v-if="form.errors.nilai_maksimal" class="mt-1 text-sm text-red-500">
                            {{ form.errors.nilai_maksimal }}
                        </p>
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Status Tugas <span class="text-red-500">*</span>
                    </label>
                    <select
                        v-model="form.status"
                        required
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                        :class="{ 'border-red-500': form.errors.status }"
                    >
                        <option value="draft">Draft — belum dipublikasikan</option>
                        <option value="published">Dipublikasikan</option>
                        <option value="closed">Ditutup</option>
                    </select>
                    <p v-if="form.errors.status" class="mt-1 text-sm text-red-500">
                        {{ form.errors.status }}
                    </p>
                </div>
            </div>

            <div class="flex flex-col-reverse justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4 sm:flex-row dark:border-slate-700 dark:bg-slate-900/40">
                <Link
                    :href="route('tugas.index')"
                    class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-semibold text-slate-600 hover:bg-white dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800"
                >
                    Batal
                </Link>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Tugas' }}
                </button>
            </div>
        </form>
    </div>
</template>

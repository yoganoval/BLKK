
<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue'
import { router, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

defineOptions({ layout: AdminLayout })

type Kelas = {
  id: number
  nama: string
}

type Pengaturan = {
  metode_rekap: 'rata_rata'
  bobot_tugas: string | number
  bobot_kuis: string | number
  otomatis: boolean
  hitung_belum_dinilai: boolean
  esai_harus_dinilai: boolean
} | null

const props = defineProps<{
  kelasList: Kelas[]
  kelasTerpilih: number | null
  pengaturan: Pengaturan
}>()

const form = useForm({
  metode_rekap: props.pengaturan?.metode_rekap ?? 'rata_rata',
  bobot_tugas: Number(props.pengaturan?.bobot_tugas ?? 60),
  bobot_kuis: Number(props.pengaturan?.bobot_kuis ?? 40),
  otomatis: props.pengaturan?.otomatis ?? true,
  hitung_belum_dinilai:
    props.pengaturan?.hitung_belum_dinilai ?? false,
  esai_harus_dinilai:
    props.pengaturan?.esai_harus_dinilai ?? true,
})

const totalBobot = computed(
  () => Number(form.bobot_tugas) + Number(form.bobot_kuis),
)

function pilihKelas(event: Event) {
  const target = event.target as HTMLSelectElement
  router.get(
    '/pengaturan-rekap-nilai',
    { kelas_id: target.value },
    { preserveState: false, preserveScroll: true },
  )
}

function simpan() {
  if (!props.kelasTerpilih) return

  form.put(
    `/pengaturan-rekap-nilai/${props.kelasTerpilih}`,
    { preserveScroll: true },
  )
}
</script>

<template>
  <div class="space-y-6 p-6">
    <div>
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
        Pengaturan Rekap Nilai
      </h1>
      <p class="mt-1 text-sm text-gray-500">
        Atur perhitungan nilai tugas dan kuis untuk setiap kelas.
      </p>
    </div>

    <div
      v-if="kelasList.length"
      class="max-w-3xl space-y-6 rounded-xl border border-gray-200
             bg-white p-6 dark:border-gray-700 dark:bg-gray-900"
    >
      <div>
        <label class="mb-2 block text-sm font-medium">
          Kelas
        </label>
        <select
          :value="kelasTerpilih ?? ''"
          class="w-full rounded-lg border-gray-300 dark:border-gray-700
                 dark:bg-gray-800"
          @change="pilihKelas"
        >
          <option
            v-for="kelas in kelasList"
            :key="kelas.id"
            :value="kelas.id"
          >
            {{ kelas.nama }}
          </option>
        </select>
      </div>

      <div>
        <label class="mb-2 block text-sm font-medium">
          Metode rekap
        </label>
        <select
          v-model="form.metode_rekap"
          class="w-full rounded-lg border-gray-300 dark:border-gray-700
                 dark:bg-gray-800"
        >
          <option value="rata_rata">Rata-rata semua nilai</option>
        </select>
      </div>

      <div class="grid gap-4 sm:grid-cols-2">
        <div>
          <label class="mb-2 block text-sm font-medium">
            Bobot tugas (%)
          </label>
          <input
            v-model.number="form.bobot_tugas"
            type="number"
            min="0"
            max="100"
            step="1"
            class="w-full rounded-lg border-gray-300 dark:border-gray-700
                   dark:bg-gray-800"
          />
          <p v-if="form.errors.bobot_tugas" class="mt-1 text-sm text-red-600">
            {{ form.errors.bobot_tugas }}
          </p>
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium">
            Bobot kuis (%)
          </label>
          <input
            v-model.number="form.bobot_kuis"
            type="number"
            min="0"
            max="100"
            step="1"
            class="w-full rounded-lg border-gray-300 dark:border-gray-700
                   dark:bg-gray-800"
          />
          <p v-if="form.errors.bobot_kuis" class="mt-1 text-sm text-red-600">
            {{ form.errors.bobot_kuis }}
          </p>
        </div>
      </div>

      <p
        class="text-sm"
        :class="totalBobot === 100 ? 'text-green-600' : 'text-red-600'"
      >
        Total bobot: {{ totalBobot }}%
        <span v-if="totalBobot !== 100">
          — total harus 100%.
        </span>
      </p>

      <div class="space-y-4 border-t border-gray-200 pt-4 dark:border-gray-700">
        <label class="flex items-start gap-3">
          <input v-model="form.otomatis" type="checkbox" class="mt-1 rounded" />
          <span>
            <span class="block font-medium">Rekap otomatis</span>
            <span class="text-sm text-gray-500">
              Perbarui rekap ketika tugas dinilai atau kuis selesai.
            </span>
          </span>
        </label>

        <label class="flex items-start gap-3">
          <input
            v-model="form.hitung_belum_dinilai"
            type="checkbox"
            class="mt-1 rounded"
          />
          <span>
            <span class="block font-medium">
              Tugas yang belum dinilai dihitung nol
            </span>
            <span class="text-sm text-gray-500">
              Nonaktifkan agar nilai yang belum dinilai tidak masuk rata-rata.
            </span>
          </span>
        </label>

        <label class="flex items-start gap-3">
          <input
            v-model="form.esai_harus_dinilai"
            type="checkbox"
            class="mt-1 rounded"
          />
          <span>
            <span class="block font-medium">
              Esai wajib dinilai sebelum nilai kuis masuk rekap
            </span>
            <span class="text-sm text-gray-500">
              Kuis dengan esai menunggu sampai penilaian esai selesai.
            </span>
          </span>
        </label>
      </div>

      <div class="flex justify-end border-t border-gray-200 pt-4 dark:border-gray-700">
        <button
          type="button"
          :disabled="form.processing || totalBobot !== 100"
          class="rounded-lg bg-blue-600 px-5 py-2.5 font-medium text-white
                 hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
          @click="simpan"
        >
          {{ form.processing ? 'Menyimpan...' : 'Simpan Pengaturan' }}
        </button>
      </div>
    </div>

    <div v-else class="rounded-xl border p-6 text-gray-500">
      Belum ada kelas. Tambahkan kelas terlebih dahulu sebelum mengatur rekap.
    </div>
  </div>
</template>

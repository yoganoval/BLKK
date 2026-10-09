<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    role: {
        type: Object,
        required: true,
    },
})

const form = useForm({
    name: props.role.name ?? '',
})

const submit = () => {
    form.put(`/roles/${props.role.id}`)
}
</script>

<template>
    <Head title="Edit Role" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                    Edit Role
                </h2>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Ubah informasi role.
                </p>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">

                <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">

                    <form @submit.prevent="submit">

                        <!-- NAMA ROLE -->
                        <div>
                            <label
                                for="name"
                                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Nama Role
                            </label>

                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Masukkan nama role"
                                class="w-full rounded-lg border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                :class="{
                                    'border-red-500': form.errors.name
                                }"
                            />

                            <p
                                v-if="form.errors.name"
                                class="mt-2 text-sm text-red-600"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- BUTTON -->
                        <div class="mt-6 flex justify-end gap-3">

                            <!-- BATAL -->
                            <Link
                                href="/roles"
                                class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                            >
                                Batal
                            </Link>

                            <!-- UPDATE -->
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {{
                                    form.processing
                                        ? 'Memperbarui...'
                                        : 'Update'
                                }}
                            </button>

                        </div>

                    </form>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
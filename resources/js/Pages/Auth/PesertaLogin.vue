<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    username: '',
    password: '',
});

const submit = () => {
    form.post(route('peserta.login.store'));
};
</script>

<template>
    <Head title="Login Peserta" />

    <div
        class="flex min-h-screen items-center justify-center
               bg-gray-100 px-4 dark:bg-gray-950"
    >
        <div
            class="w-full max-w-md rounded-2xl bg-white p-8 shadow-lg
                   dark:bg-gray-900"
        >
            <div class="mb-8 text-center">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Login Peserta BLKK
                </h1>

                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Masuk menggunakan akun peserta yang telah dibuat admin.
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label
                        for="username"
                        class="mb-2 block text-sm font-medium
                               text-gray-700 dark:text-gray-300"
                    >
                        Username
                    </label>

                    <input
                        id="username"
                        v-model="form.username"
                        type="text"
                        autocomplete="username"
                        required
                        autofocus
                        class="w-full rounded-lg border border-gray-300
                               px-4 py-3 dark:border-gray-700
                               dark:bg-gray-800 dark:text-white"
                        placeholder="Masukkan username"
                    />

                    <p
                        v-if="form.errors.username"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ form.errors.username }}
                    </p>
                </div>

                <div>
                    <label
                        for="password"
                        class="mb-2 block text-sm font-medium
                               text-gray-700 dark:text-gray-300"
                    >
                        Password
                    </label>

                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        autocomplete="current-password"
                        required
                        class="w-full rounded-lg border border-gray-300
                               px-4 py-3 dark:border-gray-700
                               dark:bg-gray-800 dark:text-white"
                        placeholder="Masukkan password"
                    />

                    <p
                        v-if="form.errors.password"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ form.errors.password }}
                    </p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-lg bg-blue-600 px-4 py-3
                           font-semibold text-white hover:bg-blue-700
                           disabled:cursor-not-allowed disabled:opacity-50"
                >
                    {{
                        form.processing
                            ? 'Memproses...'
                            : 'Login'
                    }}
                </button>
            </form>
        </div>
    </div>
</template>
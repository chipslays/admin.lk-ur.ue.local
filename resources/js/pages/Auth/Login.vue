<template>
    <Head title="Вход" />

    <GuestLayout>
        <div class="min-h-dvh flex flex-col items-center justify-center gap-4">
            <div class="p-6 rounded-brand bg-white border shadow-xs max-w-md w-full mx-auto flex flex-col gap-4">
                <div class="mb-4 text-lg font-semibold">
                    Вход
                </div>

                <form @submit.prevent="submit">
                    <div>
                        <input
                            v-model="form.email"
                            class="px-3 py-2 rounded-brand border bg-gray-50 focus:bg-white w-full"
                            :class="{ 'border-red-400': form.errors.email }"
                            type="email"
                            placeholder="Почта"
                            autocomplete="email"
                        >
                        <div v-if="form.errors.email" class="mt-1 text-danger-500 text-xs">
                            {{ form.errors.email }}
                        </div>
                    </div>

                    <div class="mt-4">
                        <input
                            v-model="form.password"
                            class="px-3 py-2 rounded-brand border bg-gray-50 focus:bg-white w-full"
                            :class="{ 'border-red-400': form.errors.password }"
                            type="password"
                            placeholder="Пароль"
                            autocomplete="current-password"
                        >
                        <div v-if="form.errors.password" class="mt-1 text-danger-500 text-xs">
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="block mt-8 px-4 py-3 rounded-brand bg-black text-white font-medium w-full disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        Войти
                    </button>
                </form>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import GuestLayout from '@/layouts/GuestLayout.vue';

const form = useForm({
    email: '',
    password: '',
    remember: true,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

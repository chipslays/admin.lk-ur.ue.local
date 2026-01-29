<template>
    <Head title="Обращения ЛК ЮЛ" />

    <AppLayout>
        <div class="min-h-dvh p-4">
            <Header />
            <div class="max-w-4xl w-full mx-auto">
                <div class="">
                    <div class="flex flex-col gap-4">
                        <div class="pb-4 pt-8 font-medium text-xl">
                            Обращения ЛК ЮЛ
                        </div>
                        <form @submit.prevent="handleSearch" class="flex items-center gap-2">
                            <input
                                v-model="searchQuery"
                                class="px-3 py-2 rounded-brand border bg-white w-full focus:ring-2 focus:ring-gray-200"
                                type="search"
                                placeholder="Поиск по номеру обращения, тексту, имени пользователя или его почты"
                            >
                            <button
                                v-if="searchQuery.length > 0"
                                type="submit"
                                class="px-8 py-2 rounded-brand border bg-black text-white font-medium shrink-0 flex items-center gap-2"
                            >
                                <SearchIcon class="shrink-0 size-4" />
                                <span>
                                    Найти
                                </span>
                            </button>
                        </form>
                        <div class="flex items-center gap-2">
                            <Link
                                :href="route('tickets.list', { status: 'all' })"
                                class="px-3 py-1.5 w-full text-center rounded-brand bg-white border"
                            >
                                Все: {{ counts.all }}
                            </Link>
                            <Link
                                :href="route('tickets.list', { status: 'open' })"
                                class="px-3 py-1.5 w-full text-center rounded-brand border border-transparent bg-green-500 text-white"
                            >
                                Открыто: {{ counts.open }}
                            </Link>
                            <Link
                                :href="route('tickets.list', { status: 'in_progress' })"
                                class="px-3 py-1.5 w-full text-center rounded-brand border border-transparent bg-yellow-500 text-white"
                            >
                                В работе: {{ counts.in_progress }}
                            </Link>
                            <Link
                                :href="route('tickets.list', { status: 'closed' })"
                                class="px-3 py-1.5 w-full text-center rounded-brand border border-transparent bg-danger-500 text-white"
                            >
                                Закрыто: {{ counts.closed }}
                            </Link>
                            <Link
                                :href="route('tickets.list', { status: 'cancelled' })"
                                class="px-3 py-1.5 w-full text-center rounded-brand border border-transparent bg-gray-200 text-"
                            >
                                Отменено: {{ counts.cancelled }}
                            </Link>
                        </div>
                        <template v-if="tickets.data.length > 0">
                            <template v-for="item in tickets.data" :key="`ticket_${item.id}`">
                                <div class="p-6 rounded-brand bg-gray-100 border">
                                    <Link :href="route('tickets.show', { id: item.id })" class="flex flex-col gap-4">
                                        <div class="text-gray-500 text-xs flex items-center gap-2">
                                            <div class="flex">
                                                <div :class="{
                                                    'text-green-50 bg-green-500': item.status === 'open',
                                                    'text-yellow-50 bg-yellow-500': item.status === 'in_progress',
                                                    'text-danger-50 bg-red-500': item.status === 'closed',
                                                    'text-gray-600 bg-gray-200': item.status === 'cancelled',
                                                }" class="font-medium px-1.5 py-0.5 rounded-brand text-xs">
                                                    <template v-if="item.status === 'open'">
                                                        Открыто
                                                    </template>
                                                    <template v-else-if="item.status === 'in_progress'">
                                                        В работе
                                                    </template>
                                                    <template v-else-if="item.status === 'closed'">
                                                        Закрыто
                                                    </template>
                                                    <template v-else-if="item.status === 'cancelled'">
                                                        Отменено
                                                    </template>
                                                </div>
                                            </div>
                                            <div class="">
                                                Обращение №{{ item.id }} от {{ new Date(item.created_at).toLocaleString() }}
                                            </div>
                                        </div>
                                        <div class="text-base font-medium">
                                            {{ item.title }}
                                        </div>
                                        <div v-if="item.first_message" class="text-gray-700 line-clamp-2 leading-relaxed hyphens-auto">
                                            {{ item.first_message.message }}
                                        </div>
                                        <div class="text-xs">
                                            <div class="flex items-center gap-4">
                                                <div class="">
                                                    {{ item.user?.name ?? 'Пользователь' }}
                                                </div>
                                                <div class="text-gray-500">
                                                    {{ item.email ?? '-' }}
                                                </div>
                                                <div class="text-gray-500">
                                                    {{ item.phone ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </Link>
                                </div>
                            </template>
                            <Pagination :links="tickets.links" />
                        </template>
                        <template v-if="tickets.data.length === 0">
                            <div class="p-12 flex items-center justify-center rounded-brand bg-gray-100 border-2 border-dashed text-gray-500">
                                Ничего нет
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { SearchIcon } from 'lucide-vue-next';
import Header from '@/components/Header.vue';
import Pagination from '@/components/Pagination.vue';

const props = defineProps({
    tickets: Object,
    counts: Object,
    filters: Object,
});

const searchQuery = ref(props.filters.search || '');

const handleSearch = () => {
    router.get(route('tickets.list'), {
        search: searchQuery.value,
        status: props.filters.status
    }, {
        preserveState: true,
        replace: true
    });
};
</script>

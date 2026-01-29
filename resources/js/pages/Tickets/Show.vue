<template>
    <Head :title="`Обращение №${ticket.id}`" />

    <AppLayout>
        <div class="min-h-dvh p-4">
            <Header />
            <div class="max-w-4xl w-full mx-auto">
                <div class="">
                    <div class="flex flex-col gap-4">
                        <div class="flex pb-4 pt-8">
                            <button @click="window.history.back()" class="flex items-center gap-2 font-medium">
                                <ArrowLeftIcon class="shrink-0 size-4" />
                                <div>
                                    Вернуться назад
                                </div>
                            </button>
                        </div>
                        <div class="p-4 rounded-brand bg-gray-100 border">
                            <div class="text-xl font-semibold">
                                {{ ticket.title }}
                            </div>
                            <div class="mt-4 flex items-center justify-between gap-4">
                                <div x-data="{ open: false }" x-on:click.outside="open = false" class="flex">
                                    <button x-ref="button" x-on:click="open = !open" class="px-3 py-1.5 rounded-brand border bg-white flex items-center gap-1">
                                        <div :class="{
                                            'text-green-600': ticket.status === 'open',
                                            'text-yellow-500': ticket.status === 'in_progress',
                                            'text-danger-500': ticket.status === 'closed',
                                            'text-gray-500': ticket.status === 'cancelled',
                                        }" class="font-medium">
                                            <template v-if="ticket.status === 'open'">
                                                Открыто
                                            </template>
                                            <template v-else-if="ticket.status === 'in_progress'">
                                                В работе
                                            </template>
                                            <template v-else-if="ticket.status === 'closed'">
                                                Закрыто
                                            </template>
                                            <template v-else-if="ticket.status === 'cancelled'">
                                                Отменено
                                            </template>
                                        </div>
                                        <ChevronsUpDownIcon class="shrink-0 size-4 text-gray-400" />
                                    </button>
                                    <div x-show="open" x-cloak x-anchor.bottom-start.offset.8="$refs.button" class="w-48 bg-white rounded-brand border shadow z-10">
                                        <div class="p-1 flex flex-col gap-1">
                                            <Link :href="route('tickets.set-status', { id: ticket.id, status: 'open' })" x-on:click="open = false" method="post" as="button" class="px-3 py-2 rounded-brand text-left hover:bg-gray-100 text-green-600">
                                                Открыто
                                            </Link>
                                            <Link :href="route('tickets.set-status', { id: ticket.id, status: 'in_progress' })" x-on:click="open = false" method="post" as="button" class="px-3 py-2 rounded-brand text-left hover:bg-gray-100 text-yellow-600">
                                                В работе
                                            </Link>
                                            <Link :href="route('tickets.set-status', { id: ticket.id, status: 'closed' })" x-on:click="open = false" method="post" as="button" class="px-3 py-2 rounded-brand text-left hover:bg-gray-100 text-danger-500">
                                                Закрыто
                                            </Link>
                                            <Link :href="route('tickets.set-status', { id: ticket.id, status: 'cancelled' })" x-on:click="open = false" method="post" as="button" class="px-3 py-2 rounded-brand text-left hover:bg-gray-100 text-gray-500">
                                                Отменено
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 text-xs">
                                    <div class="text-gray-500">
                                        Создано {{ new Date(ticket.created_at).toLocaleString() }}
                                    </div>
                                    <div v-if="ticket.status === 'closed' && ticket.closed_at" class="text-red-500">
                                        Закрыто {{ new Date(ticket.closed_at).toLocaleString() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 rounded-brand bg-gray-100 border grid grid-cols-3 gap-4">
                            <div class="font-medium">
                                Потребитель
                            </div>
                            <div class="col-span-full">
                                <div class="text-xs text-gray-500">
                                    Пользователь
                                </div>
                                <div class="truncate">
                                    {{ ticket.user.name }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">
                                    Договор
                                </div>
                                <div class="truncate">
                                    {{ ticket.dog_number ? `№${ticket.dog_number}` : '-' }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">
                                    Почта
                                </div>
                                <div class="truncate">
                                    {{ ticket.email }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">
                                    Телефон
                                </div>
                                <div class="truncate">
                                    {{ ticket.phone }}
                                </div>
                            </div>
                        </div>
                        <div class="p-4 rounded-brand bg-gray-100 border flex flex-col gap-4">
                            <div class="flex items-center justify-between gap-4">
                                <div class="font-medium">
                                    Заметка
                                </div>
                                <div v-if="notesForm.processing" class="text-gray-500">
                                    Сохранение
                                </div>
                                <div v-else-if="notesSaved" class="text-green-600">
                                    Сохранено
                                </div>
                            </div>
                            <textarea
                                v-model="notesForm.notes"
                                @input="handleNotesChange"
                                class="p-3 w-full border rounded-brand min-h-16 max-h-96 bg-white"
                                rows="1"
                                placeholder="Текст заметки"
                            ></textarea>
                        </div>
                        <div class="p-4 rounded-brand bg-gray-100 border flex flex-col gap-4">
                            <div class="font-medium">
                                Сообщения: {{ ticket.messages.length }}
                            </div>
                            <template v-for="message in ticket.messages" :key="`message_${message.id}`">
                                <div :class="{ 'bg-white': message.user_id, 'bg-gray-200': !message.user_id }" class="p-4 rounded-brand border flex flex-col gap-3">
                                    <div class="text-xs text-gray-500 flex items-center gap-2">
                                        <template v-if="message.user?.name">
                                            {{ message.user?.name }} (Потребитель)
                                        </template>
                                        <template v-else>
                                            <img class="size-4 shrink-0" src="/images/logo.svg" alt="logo">
                                            <span>
                                                {{ message.manager_name }} (Ульяновскэнерго)
                                            </span>
                                        </template>
                                    </div>
                                    <div class="leading-relaxed whitespace-pre-wrap">
                                        {{ message.message }}
                                    </div>
                                    <div v-if="message.attachments?.length > 0" class="flex flex-wrap items-center gap-2">
                                        <template v-for="(filePath, fileIndex) in message.attachments">
                                            <a :href="`${$page.props.lk_url}/tickets/attachment/download?message_id=${message.id}&index=${fileIndex}&from=admin`" target="_blank" class="px-2.5 py-1.5 rounded-brand bg-gray-50 border flex items-center gap-2 font-medium text-xs border-dashed border-gray-300">
                                                <DownloadIcon class="shrink-0 size-4 text-gray-500" />
                                                <span>
                                                    {{ basename(filePath) }}
                                                </span>
                                            </a>
                                        </template>
                                    </div>
                                    <div class="text-xs flex items-center justify-between gap-4">
                                        <div class="text-gray-500">
                                            {{ new Date(message.created_at).toLocaleString() }}
                                        </div>
                                        <button v-if="!message.user_id" @click="deleteMessage(message)" class="text-danger-500">
                                            Удалить
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <form @submit.prevent="submitMessage" class="p-4 rounded-brand bg-gray-100 border flex flex-col gap-4">
                            <div class="font-medium">
                                Написать ответ
                            </div>
                            <div class="flex flex-col gap-4">
                                <div>
                                    <textarea
                                        v-model="messageForm.message"
                                        class="p-3 w-full border rounded-brand min-h-16 max-h-96 bg-white"
                                        rows="3"
                                        placeholder="Текст ответа"
                                        :class="{ 'border-red-500': messageForm.errors.message }"
                                    ></textarea>
                                    <div v-if="messageForm.errors.message" class="mt-1 text-red-500 text-xs">
                                        {{ messageForm.errors.message }}
                                    </div>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="p-4 rounded-xl border-2 border-gray-200 border-dashed cursor-pointer bg-white">
                                        <div>Выберите файлы</div>
                                        <div class="mt-2 text-gray-500 text-xs">
                                            До 5 файлов формата JPG, PNG, PDF, ZIP и каждый не более 10 МБ
                                        </div>
                                        <div v-if="selectedFiles.length > 0" class="mt-2 text-gray-700 text-xs">
                                            Прикреплено: {{ selectedFiles.length }}
                                        </div>
                                        <input
                                            type="file"
                                            multiple
                                            accept=".jpg,.jpeg,.png,.pdf,.zip"
                                            class="hidden"
                                            @input="handleFiles"
                                        >
                                    </label>
                                    <div v-if="selectedFiles.length > 0" class="flex flex-wrap items-center gap-2">
                                        <div v-for="(file, index) in selectedFiles" :key="index" class="px-2.5 py-1.5 rounded-brand bg-gray-50 border flex items-center gap-2 text-xs">
                                            <span>{{ file.name }}</span>
                                            <button type="button" @click="removeFile(index)" class="text-red-500 hover:text-red-700">
                                                ✕
                                            </button>
                                        </div>
                                    </div>
                                    <div v-if="messageForm.errors.attachments" class="text-red-500 text-xs">
                                        {{ messageForm.errors.attachments }}
                                    </div>
                                </div>
                                <div>
                                    <button
                                        type="submit"
                                        :disabled="messageForm.processing"
                                        :class="{ 'opacity-50 cursor-not-allowed': messageForm.processing }"
                                        class="px-4 py-3 bg-black text-white rounded-brand font-medium"
                                    >
                                        {{ messageForm.processing ? 'Отправка...' : 'Отправить ответ' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { ArrowLeftIcon, ChevronsUpDownIcon, DownloadIcon } from 'lucide-vue-next';
import Header from '@/components/Header.vue';

const props = defineProps({
    ticket: Object,
});

const selectedFiles = ref([]);
const notesSaved = ref(false);
let notesTimeout = null;

const messageForm = useForm({
    message: '',
    attachments: [],
});

const notesForm = useForm({
    notes: props.ticket.notes || '',
});

const handleFiles = (event) => {
    const files = Array.from(event.target.files);

    if (files.length + selectedFiles.value.length > 5) {
        alert('Максимум 5 файлов');
        return;
    }

    selectedFiles.value = [...selectedFiles.value, ...files];
    messageForm.attachments = selectedFiles.value;
};

const removeFile = (index) => {
    selectedFiles.value.splice(index, 1);
    messageForm.attachments = selectedFiles.value;
};

const submitMessage = () => {
    messageForm.post(route('tickets.message.store', props.ticket.id), {
        preserveScroll: true,
        onSuccess: () => {
            messageForm.reset();
            selectedFiles.value = [];
        },
    });
};

const handleNotesChange = () => {
    notesSaved.value = false;

    if (notesTimeout) {
        clearTimeout(notesTimeout);
    }

    notesTimeout = setTimeout(() => {
        saveNotes();
    }, 1000);
};

const saveNotes = () => {
    notesForm.post(route('tickets.notes.update', props.ticket.id), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            notesSaved.value = true;
            setTimeout(() => {
                notesSaved.value = false;
            }, 2000);
        },
    });
};

const basename = (path) => {
    return path.substring(path.lastIndexOf('/') + 1);
};

const deleteMessage = (message) => {
    if (!confirm('Вы уверены, что хотите удалить это сообщение?')) {
        return;
    }

    router.post(route('tickets.message.delete', { ticketId: message.ticket_id, messageId: message.id }), {}, {
        preserveScroll: true,
    });
};
</script>

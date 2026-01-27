<template>
    <header class="p-4 rounded-brand bg-gray-100 border flex items-center justify-between gap-4 max-w-4xl mx-auto">
        <div class="flex items-center gap-4">
            <Link href="/" class="block">
                <img class="size-8 shrink-0" src="/images/logo.svg" alt="logo">
            </Link>
        </div>
        <div class="flex items-center gap-4">
            <div x-data="{ open: false }" x-on:click.outside="open = false" class="flex">
                <button x-ref="button" x-on:click="open = !open" class="flex items-center gap-1 text-gray-500 hover:text-black">
                    <span>
                        {{ user.name }}
                    </span>
                    <ChevronDownIcon class="shrink-0 size-3.5 text-gray-500" />
                </button>
                <div x-show="open" x-cloak x-anchor.bottom-end.offset.8="$refs.button" class="w-52 shrink-0 bg-white rounded-brand border shadow z-10">
                    <div class="p-3">
                        <div class="truncate">
                            {{ user.name }}
                        </div>
                        <div class="text-gray-500 text-xs truncate">
                            {{ user.email }}
                        </div>
                    </div>
                    <hr>
                    <div class="p-1 flex flex-col gap-1">
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="px-3 py-2 rounded-brand text-left hover:bg-gray-100 flex items-center gap-2 text-danger-500"
                        >
                            <LogOutIcon class="shrink-0 size-4" />
                            <span>Выйти</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { useUser } from '@/composables/useUser';
import { Link } from '@inertiajs/vue3';
import { ChevronDown, ChevronDownIcon, LogOutIcon } from 'lucide-vue-next';

const { user } = useUser();
</script>
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import Label from '@/components/ui/label/Label.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

interface User {
    id: number;
    name: string;
    email: string;
}

interface Props {
    users?: User[];
}

const props = defineProps<Props>();

const goToChat = (id: number) => {
  router.visit(route('chat', { id }))
}

</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div v-for="user in users" :key="user.id"
                    class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                          @click="() => goToChat(user.id)" role="button" tabindex="0">
                    <div class="m-5">
                        <Label>Name : {{ user.name }} </Label>
                        <Label class="mt-3">Email : {{ user.email }} </Label>
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import Chat from '@/components/Chat.vue'
import { type BreadcrumbItem } from '@/types'

const props = defineProps<{
  messages: Array<any>
  auth: { user: any }
}>()

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
]
</script>

<template>
  <Head title="User Chat" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <Chat :messages="messages" :current-user="auth.user" :receiver="receiver_id" />
  </AppLayout>
</template>

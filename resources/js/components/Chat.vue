<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { Paperclip, Send } from 'lucide-vue-next';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { getInitials } from '@/composables/useInitials';
import type { ChatMessage } from '@/types';
import {useEcho} from '@laravel/echo-vue';
const page = usePage();
const auth = page.props.auth;
const receiver = page.props.receiver_id;

const messages = ref<ChatMessage[]>([]);
const newMessage = ref('');
const fileInput = ref<HTMLInputElement | null>(null);
const chatContainer = ref<HTMLElement | null>(null);

const scrollToBottom = () => {
    setTimeout(() => {
        if (chatContainer.value) {
            chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
        }
    }, 100);
};

// Fetch initial messages from the server
onMounted(() => {
    messages.value = page.props.messages || [];
    scrollToBottom();


    window.useEcho.private(`chat.${auth.user.id}`)
    .listen('MessageSent', (e: { message: ChatMessage }) => {
        messages.value.push(e.message);
        scrollToBottom();
    });

});


// Send message to backend
const sendMessage = () => {
    if (!newMessage.value.trim()) return;

    const payload = {
        content: newMessage.value,
        type: 'text'
    };

    router.post(route('chat.send', { receiver: receiver }), payload, {
        preserveScroll: true,
        onSuccess: () => {
            newMessage.value = '';
            scrollToBottom();
        }
    });
};

// Handle file upload
const handleFileUpload = (event: Event) => {
    const files = (event.target as HTMLInputElement).files;
    if (!files || files.length === 0) return;

    const formData = new FormData();
    formData.append('file', files[0]);
    formData.append('type', 'file');

    router.post('/chat/send', formData, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => scrollToBottom()
    });
};
</script>

<template>
        <!-- Chat Area -->
        <div class="flex flex-1 flex-col  dark:bg-neutral-900">
            <!-- Messages -->
            <div ref="chatContainer" class="flex-1 overflow-y-auto p-4 space-y-4">
                <div v-for="message in messages" :key="message.id" :class="[
                    'flex',
                    message.sender.id === auth.user.id ? 'justify-end' : 'justify-start'
                ]">
                    <div class="flex items-start gap-2 max-w-[70%]">
                        <!-- Show avatar only for others -->
                        <Avatar v-if="message.sender.id !== auth.user.id" class="h-8 w-8">
                            <AvatarImage v-if="message.sender.avatar" :src="message.sender.avatar"
                                :alt="message.sender.name" />
                            <AvatarFallback>{{ getInitials(message.sender.name) }}</AvatarFallback>
                        </Avatar>

                        <!-- Message Bubble -->
                        <div :class="[
                            'rounded-lg px-4 py-3 shadow',
                            message.sender.id === auth.user.id
                                ? 'bg-slate-400 text-black'
                                : 'bg-white text-black dark:bg-neutral-800 dark:text-white'
                        ]">
                            <span class="text-sm font-semibold block mb-1">
                                {{ message.sender.name }}
                            </span>

                            <div v-if="message.type === 'text'" class="whitespace-pre-line">
                                {{ message.content }}
                            </div>

                            <div v-else-if="message.type === 'file'" class="flex items-center gap-2">
                                <Paperclip class="h-4 w-4" />
                                <a :href="`/storage/chat_files/${message.content}`" target="_blank" class="underline">
                                    {{ message.content }}
                                </a>
                            </div>

                            <span class="mt-1 block text-xs opacity-60 text-end">
                                {{ new Date(message.created_at).toLocaleTimeString() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Input Area -->
            <div class="border-t border-gray-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 p-4">
                <div class="flex items-center gap-4">
                    <!-- File Upload -->
                    <Button variant="ghost" size="icon" class="h-10 w-10" @click="() => fileInput?.click()">
                        <Paperclip class="h-5 w-5" />
                        <input ref="fileInput" type="file" class="hidden" @change="handleFileUpload" />
                    </Button>

                    <!-- Text Input -->
                    <input v-model="newMessage" type="text" placeholder="Type a message..."
                        class="flex-1 rounded-lg border border-gray-300 dark:border-neutral-700 bg-gray-100 dark:bg-neutral-800 text-black dark:text-white px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary"
                        @keyup.enter="sendMessage" />

                    <!-- Send Button -->
                    <Button variant="default" size="icon" class="h-10 w-10" @click="sendMessage">
                        <Send class="h-5 w-5" />
                    </Button>
                </div>
            </div>
        </div>
</template>

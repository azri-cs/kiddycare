<script setup lang="ts">
import { ref, onMounted } from 'vue';

const props = defineProps({
    message: {
        type: String,
        required: true
    },
    type: {
        type: String,
        default: 'success',
        validator: (value) => ['success', 'error', 'info', 'warning'].includes(value)
    },
    duration: {
        type: Number,
        default: 5000
    }
});

const visible = ref(true);
const emit = defineEmits(['close']);

onMounted(() => {
    if (props.duration > 0) {
        setTimeout(() => {
            visible.value = false;
            emit('close');
        }, props.duration);
    }
});

const getIcon = () => {
    switch (props.type) {
        case 'success':
            return `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>`;
        case 'error':
            return `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
              </svg>`;
        case 'info':
            return `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
              </svg>`;
        case 'warning':
            return `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
              </svg>`;
        default:
            return '';
    }
};

const getBgColor = () => {
    switch (props.type) {
        case 'success': return 'bg-green-50 dark:bg-green-950 border-green-200 dark:border-green-900';
        case 'error': return 'bg-red-50 dark:bg-red-950 border-red-200 dark:border-red-900';
        case 'info': return 'bg-blue-50 dark:bg-blue-950 border-blue-200 dark:border-blue-900';
        case 'warning': return 'bg-yellow-50 dark:bg-yellow-950 border-yellow-200 dark:border-yellow-900';
        default: return 'bg-card';
    }
};

const getTextColor = () => {
    switch (props.type) {
        case 'success': return 'text-green-800 dark:text-green-300';
        case 'error': return 'text-red-800 dark:text-red-300';
        case 'info': return 'text-blue-800 dark:text-blue-300';
        case 'warning': return 'text-yellow-800 dark:text-yellow-300';
        default: return 'text-foreground';
    }
};

const getIconColor = () => {
    switch (props.type) {
        case 'success': return 'text-green-500 dark:text-green-400';
        case 'error': return 'text-red-500 dark:text-red-400';
        case 'info': return 'text-blue-500 dark:text-blue-400';
        case 'warning': return 'text-yellow-500 dark:text-yellow-400';
        default: return 'text-foreground';
    }
};

function close() {
    visible.value = false;
    emit('close');
}
</script>

<template>
    <transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="visible" :class="[getBgColor(), 'pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg border shadow-lg']">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0" v-html="getIcon()" :class="getIconColor()"></div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p :class="[getTextColor(), 'text-sm font-medium']">{{ message }}</p>
                    </div>
                    <div class="ml-4 flex flex-shrink-0">
                        <button @click="close" type="button" class="inline-flex rounded-md text-muted-foreground hover:text-foreground">
                            <span class="sr-only">Close</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

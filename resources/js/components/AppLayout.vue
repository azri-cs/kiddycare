<script setup lang="ts">
import { defineProps } from 'vue';
import ThemeToggle from './ThemeToggle.vue';

defineProps({
    title: {
        type: String,
        required: false,
        default: null
    }
});
</script>

<template>
    <div class="min-h-screen bg-background text-foreground">
        <!-- Header -->
        <header class="border-b border-border sticky top-0 bg-card/80 backdrop-blur-md z-10">
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <img src="/favicon.ico" alt="Logo" class="w-8 h-8" />
                    <h1 class="text-xl font-semibold">BabySitter Booking</h1>
                </div>
                <ThemeToggle />
            </div>
        </header>

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-8">
            <h1 v-if="title" class="text-2xl md:text-3xl font-bold mb-6">{{ title }}</h1>
            <slot></slot>
        </main>

        <!-- Footer -->
        <footer class="bg-card border-t border-border py-6 mt-auto">
            <div class="container mx-auto px-4 text-center text-sm text-muted-foreground">
                <p>© {{ new Date().getFullYear() }} BabySitter Booking Service. All rights reserved.</p>
            </div>
        </footer>
    </div>
</template>

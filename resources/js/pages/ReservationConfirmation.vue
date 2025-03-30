<script setup lang="ts">
import { defineProps } from 'vue';
import AppLayout from '../components/AppLayout.vue';

const props = defineProps({
    bookingNumber: {
        type: String,
        required: true
    }
});
</script>

<template>
    <AppLayout title="Reservation Confirmation">
        <div class="max-w-2xl mx-auto">
            <div class="bg-card rounded-lg shadow-md border border-border p-6 text-center">
                <div class="mb-6 flex justify-center">
                    <div class="w-16 h-16 bg-accent/50 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-primary">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <h2 class="text-xl font-semibold mb-4">Thank you for your reservation!</h2>
                <p class="text-muted-foreground mb-6">Your booking has been successfully placed. Please save your booking number for reference.</p>

                <div class="bg-accent/30 rounded-lg p-6 mb-6 inline-block">
                    <p class="text-sm text-muted-foreground mb-1">Your Booking Number</p>
                    <p class="text-xl font-mono font-bold tracking-wider">{{ bookingNumber }}</p>
                </div>

                <p class="text-sm mb-6">We will contact you shortly to confirm the details of your reservation.</p>

                <div class="border-t border-border pt-6">
                    <button
                        @click="router.visit('/')"
                        class="px-6 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-ring transition-colors"
                    >
                        Book Another Reservation
                    </button>
                </div>
            </div>

            <div class="mt-8 bg-card rounded-lg shadow-md border border-border p-6">
                <h3 class="text-lg font-semibold mb-4">What's Next?</h3>
                <ol class="space-y-4">
                    <li class="flex">
                        <div class="flex-shrink-0 w-8 h-8 bg-accent rounded-full flex items-center justify-center mr-3 mt-0.5">
                            <span class="font-semibold text-sm">1</span>
                        </div>
                        <div>
                            <p class="font-medium">Confirmation Email</p>
                            <p class="text-sm text-muted-foreground">You will receive a confirmation email with all the details of your reservation.</p>
                        </div>
                    </li>
                    <li class="flex">
                        <div class="flex-shrink-0 w-8 h-8 bg-accent rounded-full flex items-center justify-center mr-3 mt-0.5">
                            <span class="font-semibold text-sm">2</span>
                        </div>
                        <div>
                            <p class="font-medium">Babysitter Assignment</p>
                            <p class="text-sm text-muted-foreground">We will match you with an experienced babysitter based on your needs.</p>
                        </div>
                    </li>
                    <li class="flex">
                        <div class="flex-shrink-0 w-8 h-8 bg-accent rounded-full flex items-center justify-center mr-3 mt-0.5">
                            <span class="font-semibold text-sm">3</span>
                        </div>
                        <div>
                            <p class="font-medium">Pre-service Call</p>
                            <p class="text-sm text-muted-foreground">Your assigned babysitter will call you 24 hours before the service to introduce themselves.</p>
                        </div>
                    </li>
                </ol>
            </div>
        </div>
    </AppLayout>
</template>

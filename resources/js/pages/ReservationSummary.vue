<script setup lang="ts">
import axios from 'axios';
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '../components/AppLayout.vue';

const props = defineProps({
    formData: {
        type: Object,
        required: true
    },
});

const bookingSuccessful = ref(false);
const bookingNumber = ref(null);

const form = useForm(props.formData);

const submitReservation = () => {
    axios.post('/api/v1/babysitting-requests', form, {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        }
    })
        .then(response => {
            bookingSuccessful.value = true;
            bookingNumber.value = response.data.data.booking_number;
            router.visit(route('reservation.confirmation', { bookingNumber: bookingNumber.value }));
        })
        .catch(error => {
            console.log("API errors:", error.response.data);
            if (error.response && error.response.data && error.response.data.errors) {
                form.clearErrors();
                Object.keys(error.response.data.errors).forEach(field => {
                    form.setError(field, error.response.data.errors[field][0]);
                });
            }
        });
};



function formatDateTime(dateTimeString) {
    if (!dateTimeString) return 'N/A';
    const date = new Date(dateTimeString);
    return date.toLocaleString('en-US', {
        weekday: 'short',
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        hour12: true
    });
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function calculateAge(birthDateString) {
    if (!birthDateString) return 'N/A';

    const birthDate = new Date(birthDateString);
    const today = new Date();

    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDifference = today.getMonth() - birthDate.getMonth();

    if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }

    return age + (age === 1 ? ' year' : ' years');
}
</script>

<template>
    <AppLayout title="Reservation Summary">
        <div class="max-w-4xl mx-auto">
            <div class="bg-card rounded-lg shadow-md border border-border p-6">
                <p class="text-muted-foreground mb-6">Please review your reservation details before confirming.</p>

                <div class="space-y-8">
                    <!-- Contact Information Section -->
                    <div>
                        <h2 class="text-lg font-semibold border-b border-border pb-2 mb-4">Contact Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <p class="text-muted-foreground">Name</p>
                                <p class="font-medium">{{ form.name }}</p>
                            </div>
                            <div>
                                <p class="text-muted-foreground">Email</p>
                                <p class="font-medium">{{ form.email }}</p>
                            </div>
                            <div>
                                <p class="text-muted-foreground">Phone</p>
                                <p class="font-medium">{{ form.phone }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Address Section -->
                    <div>
                        <h2 class="text-lg font-semibold border-b border-border pb-2 mb-4">Address</h2>
                        <div class="text-sm">
                            <p class="font-medium">{{ form.address }}</p>
                            <p class="font-medium">{{ form.city }}, {{ form.state }} {{ form.zip_code }}</p>
                        </div>
                    </div>

                    <!-- Reservation Details Section -->
                    <div>
                        <h2 class="text-lg font-semibold border-b border-border pb-2 mb-4">Reservation Details</h2>
                        <div class="bg-accent/30 rounded-lg p-4 mb-4 text-sm">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-muted-foreground">Start Time</p>
                                    <p class="font-medium">{{ formatDateTime(form.start_datetime) }}</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground">End Time</p>
                                    <p class="font-medium">{{ formatDateTime(form.end_datetime) }}</p>
                                </div>
                            </div>

                            <div class="mt-4" v-if="form.message">
                                <p class="text-muted-foreground">Additional Instructions</p>
                                <p class="whitespace-pre-line">{{ form.message }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Children Details Section -->
                    <div>
                        <h2 class="text-lg font-semibold border-b border-border pb-2 mb-4">Children Details</h2>
                        <div class="space-y-4">
                            <div v-for="(child, index) in form.care_recipients" :key="index" class="bg-accent/30 rounded-lg p-4 border border-border">
                                <h3 class="font-medium mb-3">Child #{{ index + 1 }}</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <p class="text-muted-foreground">Name</p>
                                        <p class="font-medium">{{ child.name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-muted-foreground">Date of Birth</p>
                                        <p class="font-medium">{{ formatDate(child.date_of_birth) }} ({{ calculateAge(child.date_of_birth) }})</p>
                                    </div>
                                </div>
                                <div class="mt-4 text-sm" v-if="child.remarks">
                                    <p class="text-muted-foreground">Special Notes</p>
                                    <p class="whitespace-pre-line">{{ child.remarks }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="form.errors && Object.keys(form.errors).length > 0" class="mt-6 bg-destructive/10 text-destructive rounded-md p-4">
                    <h3 class="font-medium mb-2">Please correct the following errors:</h3>
                    <ul class="list-disc list-inside space-y-1 text-sm">
                        <li v-for="(error, field) in form.errors" :key="field">{{ error }}</li>
                    </ul>
                </div>

                <div class="mt-8 pt-6 border-t border-border flex justify-between">
                    <button
                        @click="router.visit('/')"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-secondary text-secondary-foreground rounded-md hover:bg-secondary/80 focus:outline-none focus:ring-2 focus:ring-ring transition-colors disabled:opacity-50 disabled:pointer-events-none"
                    >
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75L3 12m0 0l3.75-3.75M3 12h18" />
                            </svg>
                            Back to Edit
                        </div>
                    </button>

                    <button
                        @click="submitReservation"
                        :disabled="form.processing"
                        class="px-6 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-ring transition-colors disabled:opacity-50 disabled:pointer-events-none"
                    >
                        <div class="flex items-center">
                            <span>Confirm Reservation</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ml-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

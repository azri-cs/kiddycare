<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import AppLayout from '../components/AppLayout.vue';
import { ref, computed } from 'vue';

const now = new Date();
const minDateTime = new Date(now);
minDateTime.setHours(minDateTime.getHours() + 6);
const maxDateTime = new Date(now);
maxDateTime.setDate(maxDateTime.getDate() + 30);

const minDate = minDateTime.toISOString().slice(0, 16);
const maxDate = maxDateTime.toISOString().slice(0, 16);
const todayDate = now.toISOString().slice(0,10);

const form = useForm({
    name: '',
    email: '',
    phone: '',
    address: '',
    city: '',
    state: '',
    zip_code: '',
    start_datetime: '',
    end_datetime: '',
    message: '',
    care_recipients: [
        { name: '', date_of_birth: '', remarks: '' }
    ],
});

// Frontend validation
const errors = ref({});
const isValidEmail = (email) => {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
};

const validateChildAge = (dateOfBirth) => {
    if (!dateOfBirth) return "Date of birth is required";

    const dob = new Date(dateOfBirth);
    const now = new Date();

    if (isNaN(dob.getTime())) return "Invalid date";
    if (dob >= now) return "Date of birth must be in the past";

    // Calculate age in months
    const yearDiff = now.getFullYear() - dob.getFullYear();
    const monthDiff = now.getMonth() - dob.getMonth();
    const ageInMonths = yearDiff * 12 + monthDiff;

    if (ageInMonths < 1) return "The child must be at least 1 month old.";
    if (ageInMonths > 155) return "The child must be at most 12 years and 11 months old.";

    return null;
};

const validateForm = () => {
    const newErrors = {};

    // Basic field validation
    if (!form.name) newErrors.name = "Name is required";
    else if (form.name.length > 255) newErrors.name = "Name cannot be longer than 255 characters";

    if (!form.email) newErrors.email = "Email is required";
    else if (!isValidEmail(form.email)) newErrors.email = "Please enter a valid email address";
    else if (form.email.length > 255) newErrors.email = "Email cannot be longer than 255 characters";

    if (!form.phone) newErrors.phone = "Phone number is required";
    else if (form.phone.length > 20) newErrors.phone = "Phone number cannot be longer than 20 characters";

    if (!form.address) newErrors.address = "Address is required";
    else if (form.address.length > 255) newErrors.address = "Address cannot be longer than 255 characters";

    if (!form.city) newErrors.city = "City is required";
    else if (form.city.length > 100) newErrors.city = "City cannot be longer than 100 characters";

    if (!form.state) newErrors.state = "State is required";
    else if (form.state.length > 100) newErrors.state = "State cannot be longer than 100 characters";

    if (!form.zip_code) newErrors.zip_code = "Zip code is required";
    else if (form.zip_code.length > 20) newErrors.zip_code = "Zip code cannot be longer than 20 characters";

    // Date/time validations
    if (!form.start_datetime) {
        newErrors.start_datetime = "Start time is required";
    } else {
        const startDate = new Date(form.start_datetime);

        if (startDate < minDateTime) {
            newErrors.start_datetime = "Booking must be at least 6 hours in advance.";
        } else if (startDate > maxDateTime) {
            newErrors.start_datetime = "Booking must be within the next 30 days.";
        }
    }

    if (!form.end_datetime) {
        newErrors.end_datetime = "End time is required";
    } else if (form.start_datetime && form.end_datetime) {
        const startDate = new Date(form.start_datetime);
        const endDate = new Date(form.end_datetime);

        if (endDate <= startDate) {
            newErrors.end_datetime = "End time must be after start time";
        }
    }

    if (form.message && form.message.length > 1000) {
        newErrors.message = "Additional instructions cannot be longer than 1000 characters";
    }

    // Care recipients validation
    if (!form.care_recipients || form.care_recipients.length === 0) {
        newErrors.care_recipients = "At least one child is required for babysitting.";
    } else if (form.care_recipients.length > 4) {
        newErrors.care_recipients = "Maximum 4 children allowed per booking.";
    } else {
        form.care_recipients.forEach((child, index) => {
            const baseKey = `care_recipients.${index}`;

            if (!child.name) {
                newErrors[`${baseKey}.name`] = "Child name is required";
            } else if (child.name.length > 255) {
                newErrors[`${baseKey}.name`] = "Child name cannot be longer than 255 characters";
            }

            const ageError = validateChildAge(child.date_of_birth);
            if (ageError) {
                newErrors[`${baseKey}.date_of_birth`] = ageError;
            }

            if (child.remarks && child.remarks.length > 1000) {
                newErrors[`${baseKey}.remarks`] = "Remarks cannot be longer than 1000 characters";
            }
        });
    }

    errors.value = newErrors;
    return Object.keys(newErrors).length === 0;
};

const addChild = () => {
    if (form.care_recipients.length < 4) {
        form.care_recipients.push({ name: '', date_of_birth: '', remarks: '' });
    } else {
        alert('Maximum 4 children allowed.');
    }
};

const removeChild = (index) => {
    if (form.care_recipients.length > 1) {
        form.care_recipients.splice(index, 1);
    } else {
        alert('At least one child is required.');
    }
};

const submitForm = () => {
    if (validateForm()) {
        form.post('/reservation-summary');
    }
};

const getChildError = (index, field) => {
    const key = `care_recipients.${index}.${field}`;
    return errors.value[key] || form.errors[key];
};
</script>

<template>
    <AppLayout title="Babysitter Reservation Form">
        <div class="max-w-4xl mx-auto">
            <div class="bg-card rounded-lg shadow-md border border-border p-6 mb-8">
                <p class="text-muted-foreground mb-6">Please fill out the form below to book a babysitter. All fields marked with * are required.</p>

                <form @submit.prevent="submitForm" class="space-y-8">
                    <!-- Contact Information Section -->
                    <div class="space-y-6">
                        <h2 class="text-xl font-semibold border-b border-border pb-2">Contact Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium mb-1">Name <span class="text-red-600">*</span></label>
                                <input
                                    type="text"
                                    id="name"
                                    v-model="form.name"
                                    maxlength="255"
                                    required
                                    class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring"
                                    :class="{'border-destructive': errors.name || form.errors.name}"
                                >
                                <div class="text-destructive text-xs mt-1" v-if="errors.name || form.errors.name">
                                    {{ errors.name || form.errors.name }}
                                </div>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium mb-1">Email <span class="text-red-600">*</span></label>
                                <input
                                    type="email"
                                    id="email"
                                    v-model="form.email"
                                    maxlength="255"
                                    required
                                    class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring"
                                    :class="{'border-destructive': errors.email || form.errors.email}"
                                >
                                <div class="text-destructive text-xs mt-1" v-if="errors.email || form.errors.email">
                                    {{ errors.email || form.errors.email }}
                                </div>
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium mb-1">Phone <span class="text-red-600">*</span></label>
                                <input
                                    type="tel"
                                    id="phone"
                                    v-model="form.phone"
                                    maxlength="20"
                                    required
                                    class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring"
                                    :class="{'border-destructive': errors.phone || form.errors.phone}"
                                >
                                <div class="text-destructive text-xs mt-1" v-if="errors.phone || form.errors.phone">
                                    {{ errors.phone || form.errors.phone }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address Section -->
                    <div class="space-y-6">
                        <h2 class="text-xl font-semibold border-b border-border pb-2">Address</h2>
                        <div>
                            <label for="address" class="block text-sm font-medium mb-1">Street Address <span class="text-red-600">*</span></label>
                            <input
                                type="text"
                                id="address"
                                v-model="form.address"
                                maxlength="255"
                                required
                                class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring"
                                :class="{'border-destructive': errors.address || form.errors.address}"
                            >
                            <div class="text-destructive text-xs mt-1" v-if="errors.address || form.errors.address">
                                {{ errors.address || form.errors.address }}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="city" class="block text-sm font-medium mb-1">City <span class="text-red-600">*</span></label>
                                <input
                                    type="text"
                                    id="city"
                                    v-model="form.city"
                                    maxlength="100"
                                    required
                                    class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring"
                                    :class="{'border-destructive': errors.city || form.errors.city}"
                                >
                                <div class="text-destructive text-xs mt-1" v-if="errors.city || form.errors.city">
                                    {{ errors.city || form.errors.city }}
                                </div>
                            </div>

                            <div>
                                <label for="state" class="block text-sm font-medium mb-1">State <span class="text-red-600">*</span></label>
                                <input
                                    type="text"
                                    id="state"
                                    v-model="form.state"
                                    maxlength="100"
                                    required
                                    class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring"
                                    :class="{'border-destructive': errors.state || form.errors.state}"
                                >
                                <div class="text-destructive text-xs mt-1" v-if="errors.state || form.errors.state">
                                    {{ errors.state || form.errors.state }}
                                </div>
                            </div>

                            <div>
                                <label for="zip_code" class="block text-sm font-medium mb-1">Zip Code <span class="text-red-600">*</span></label>
                                <input
                                    type="text"
                                    id="zip_code"
                                    v-model="form.zip_code"
                                    maxlength="20"
                                    required
                                    class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring"
                                    :class="{'border-destructive': errors.zip_code || form.errors.zip_code}"
                                >
                                <div class="text-destructive text-xs mt-1" v-if="errors.zip_code || form.errors.zip_code">
                                    {{ errors.zip_code || form.errors.zip_code }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reservation Details Section -->
                    <div class="space-y-6">
                        <h2 class="text-xl font-semibold border-b border-border pb-2">Reservation Details</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="start_datetime" class="block text-sm font-medium mb-1">Start Time <span class="text-red-600">*</span></label>
                                <div class="text-xs text-muted-foreground mb-2">Must be at least 6 hours in advance, within 30 days</div>
                                <input
                                    type="datetime-local"
                                    id="start_datetime"
                                    v-model="form.start_datetime"
                                    :min="minDate"
                                    :max="maxDate"
                                    required
                                    class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring"
                                    :class="{'border-destructive': errors.start_datetime || form.errors.start_datetime}"
                                >
                                <div class="text-destructive text-xs mt-1" v-if="errors.start_datetime || form.errors.start_datetime">
                                    {{ errors.start_datetime || form.errors.start_datetime }}
                                </div>
                            </div>

                            <div>
                                <label for="end_datetime" class="block text-sm font-medium mb-1">End Time <span class="text-red-600">*</span></label>
                                <div class="text-xs text-muted-foreground mb-2">Must be after start time</div>
                                <input
                                    type="datetime-local"
                                    id="end_datetime"
                                    v-model="form.end_datetime"
                                    :min="form.start_datetime || minDate"
                                    :max="maxDate"
                                    required
                                    class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring"
                                    :class="{'border-destructive': errors.end_datetime || form.errors.end_datetime}"
                                >
                                <div class="text-destructive text-xs mt-1" v-if="errors.end_datetime || form.errors.end_datetime">
                                    {{ errors.end_datetime || form.errors.end_datetime }}
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium mb-1">Additional Instructions</label>
                            <textarea
                                id="message"
                                v-model="form.message"
                                rows="3"
                                maxlength="1000"
                                class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring"
                                placeholder="Special requests, dietary needs, allergies, etc."
                                :class="{'border-destructive': errors.message || form.errors.message}"
                            ></textarea>
                            <div class="text-right text-xs text-muted-foreground">{{ form.message.length }}/1000</div>
                            <div class="text-destructive text-xs mt-1" v-if="errors.message || form.errors.message">
                                {{ errors.message || form.errors.message }}
                            </div>
                        </div>
                    </div>

                    <!-- Children Details Section -->
                    <div class="space-y-6">
                        <div class="flex justify-between items-center border-b border-border pb-2">
                            <h2 class="text-xl font-semibold">Children Details</h2>
                            <button
                                type="button"
                                @click.prevent="addChild"
                                v-if="form.care_recipients.length < As4"
                                class="text-sm px-2 py-1 rounded-md bg-secondary text-secondary-foreground hover:bg-secondary/80 flex items-center"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Add Child
                            </button>
                        </div>

                        <div class="text-destructive text-xs mt-1" v-if="errors.care_recipients || form.errors.care_recipients">
                            {{ errors.care_recipients || form.errors.care_recipients }}
                        </div>

                        <div v-for="(child, index) in form.care_recipients" :key="index" class="bg-accent/30 rounded-lg p-4 mb-4 border border-border">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="font-medium">Child #{{ index + 1 }}</h3>
                                <button
                                    type="button"
                                    @click.prevent="removeChild(index)"
                                    v-if="form.care_recipients.length > 1"
                                    class="text-xs px-2 py-1 rounded-md text-destructive hover:bg-destructive/10 flex items-center"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                    Remove
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label :for="`child_name_${index}`" class="block text-sm font-medium mb-1">Name <span class="text-red-600">*</span></label>
                                    <div class="text-xs text-muted-foreground mb-2">&nbsp;</div>
                                    <input
                                        :id="`child_name_${index}`"
                                        v-model="child.name"
                                        type="text"
                                        maxlength="255"
                                        required
                                        class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring"
                                        :class="{'border-destructive': getChildError(index, 'name')}"
                                    >
                                    <div class="text-destructive text-xs mt-1" v-if="getChildError(index, 'name')">
                                        {{ getChildError(index, 'name') }}
                                    </div>
                                </div>

                                <div>
                                    <label :for="`child_dob_${index}`" class="block text-sm font-medium mb-1">Date of Birth <span class="text-red-600">*</span></label>
                                    <div class="text-xs text-muted-foreground mb-2">Child must be 1 month to 12 years and 11 months old</div>
                                    <input
                                        :id="`child_dob_${index}`"
                                        v-model="child.date_of_birth"
                                        type="date"
                                        :max="todayDate"
                                        required
                                        class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring"
                                        :class="{'border-destructive': getChildError(index, 'date_of_birth')}"
                                    >
                                    <div class="text-destructive text-xs mt-1" v-if="getChildError(index, 'date_of_birth')">
                                        {{ getChildError(index, 'date_of_birth') }}
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <label :for="`child_remarks_${index}`" class="block text-sm font-medium mb-1">Special Notes</label>
                                    <textarea
                                        :id="`child_remarks_${index}`"
                                        v-model="child.remarks"
                                        rows="2"
                                        maxlength="1000"
                                        class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring"
                                        placeholder="Allergies, medical conditions, special needs, etc."
                                        :class="{'border-destructive': getChildError(index, 'remarks')}"
                                    ></textarea>
                                    <div class="text-right text-xs text-muted-foreground">{{ child.remarks.length }}/1000</div>
                                    <div class="text-destructive text-xs mt-1" v-if="getChildError(index, 'remarks')">
                                        {{ getChildError(index, 'remarks') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-ring transition-colors disabled:opacity-50 disabled:pointer-events-none"
                        >
                            <div class="flex items-center">
                                <span>Review Reservation</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ml-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.error {
    color: red;
    font-size: 0.8em;
}
.child-info {
    border: 1px solid #ccc;
    padding: 10px;
    margin-top: 10px;
}
</style>

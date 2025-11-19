<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    },
    label: {
        type: String,
        required: true
    },
    id: {
        type: String,
        default: null
    },
    type: {
        type: String,
        default: 'text'
    },
    required: {
        type: Boolean,
        default: false
    },
    error: {
        type: String,
        default: ''
    },
    placeholder: {
        type: String,
        default: null
    },
    disabled: {
        type: Boolean,
        default: false
    },
    help: {
        type: String,
        default: null
    }
})

const emit = defineEmits(['update:modelValue'])
const showPassword = ref(false)

const inputPlaceholder = computed(() => props.placeholder || props.label)
const inputId = computed(() => props.id || props.label.toLowerCase().replace(/\s+/g, '-'))

function updateValue(event) {
    emit('update:modelValue', event.target.value)
}

function togglePasswordVisibility() {
    showPassword.value = !showPassword.value
}
</script>

<template>
    <div class="mb-4">
        <!-- Label -->
        <label :for="inputId" class="block text-sm font-semibold text-black mb-1">
            {{ label }}
            <span v-if="required" class="text-red-600">*</span>
        </label>

        <div class="relative">
            <input
                :id="inputId"
                :type="showPassword ? 'text' : type"
                :value="modelValue"
                :required="required"
                :disabled="disabled"
                :placeholder="inputPlaceholder"
                @input="updateValue"
                
                class="
                    w-full rounded-lg px-4 py-2 text-sm outline-none transition-all
                    bg-[#F2F2F2] text-[#000000]
                    placeholder:text-[#6B7280]
                    border border-[#D1D5DC]
                    focus:border-[#EF874B] focus:ring-2 focus:ring-[#EF874B]/40
                    disabled:opacity-60 disabled:cursor-not-allowed
                "
            />

            <!-- Password Toggle -->
            <button
                v-if="type === 'password'"
                type="button"
                class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-700"
                @click="togglePasswordVisibility"
            >
                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg"
                     fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 
                             12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 
                             0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 
                             0-8.573-3.007-9.963-7.178z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>

                <svg v-else xmlns="http://www.w3.org/2000/svg"
                     fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 
                             16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 
                             2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 
                             0 8.773 3.162 10.065 7.498a10.523 10.523 0 
                             01-4.293 5.774" />
                </svg>
            </button>
        </div>

        <!-- Error -->
        <p v-if="error" class="mt-1 text-xs text-red-600">
            {{ error }}
        </p>

        <!-- Helper Text -->
        <p v-if="help && !error" class="mt-1 text-xs text-gray-600">
            {{ help }}
        </p>
    </div>
</template>


<style scoped>
</style>

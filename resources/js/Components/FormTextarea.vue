<script setup>
import { computed } from 'vue'

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
    },
    rows: {
        type: Number,
        default: 3
    }
})

const emit = defineEmits(['update:modelValue'])

const inputPlaceholder = computed(() => props.placeholder || props.label)
const inputId = computed(() => props.id || props.label.toLowerCase().replace(/\s+/g, '-'))

const textareaClass = computed(() => {
    const base =
        "w-full rounded-lg px-4 py-2 text-sm transition-all outline-none " +
        "bg-[#F2F2F2] text-black placeholder:text-gray-500 border border-[#D1D5DC]"

    const focus = "focus:border-[#EF874B] focus:ring-2 focus:ring-[#EF874B]/40"
    const error = props.error ? "border-red-500" : ""
    const disabled = props.disabled ? "opacity-60 cursor-not-allowed" : ""

    return `${base} ${focus} ${error} ${disabled}`
})

function updateValue(event) {
    emit('update:modelValue', event.target.value)
}
</script>

<template>
    <div class="mb-3">
        <!-- Label -->
        <label :for="inputId" class="block text-sm font-semibold text-black mb-1">
            {{ label }} <span v-if="required" class="text-red-600">*</span>
        </label>

        <!-- Textarea -->
        <textarea
            :id="inputId"
            :value="modelValue"
            :rows="rows"
            :required="required"
            :disabled="disabled"
            :placeholder="inputPlaceholder"
            :class="textareaClass"
            :aria-invalid="!!error"
            :aria-describedby="error ? `${inputId}-error` : undefined"
            @input="updateValue"
        ></textarea>

        <!-- Error -->
        <p v-if="error" :id="`${inputId}-error`" class="mt-1 text-xs text-red-600">
            {{ error }}
        </p>

        <!-- Help text -->
        <p v-if="help && !error" class="mt-1 text-xs text-gray-500">
            {{ help }}
        </p>
    </div>
</template>

<style scoped>
</style>

<script setup>
const props = defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: 'Konfirmasi' },
    message: { type: String, default: '' },
    confirmText: { type: String, default: 'Ya' },
    cancelText: { type: String, default: 'Batal' },
    variant: {
        type: String,
        default: 'primary',
        validator: value => ['primary', 'danger', 'success'].includes(value)
    }
})

const emit = defineEmits(['confirm', 'cancel'])

const variantClasses = {
    primary: {
        accent: 'text-[#1E88E5]',
        button: 'bg-[#1E88E5] hover:bg-[#1565C0]',
        border: 'border-[#1E88E5]'
    },
    danger: {
        accent: 'text-[#D32F2F]',
        button: 'bg-[#D32F2F] hover:bg-[#B71C1C]',
        border: 'border-[#D32F2F]'
    },
    success: {
        accent: 'text-[#2E7D32]',
        button: 'bg-[#2E7D32] hover:bg-[#1B5E20]',
        border: 'border-[#2E7D32]'
    }
}
</script>

<template>
    <Transition name="fade">
        <div
            v-if="show"
            class="fixed inset-0 z-[1000] flex items-center justify-center px-4">
            <div
                class="absolute inset-0 bg-black/40"
                @click="emit('cancel')"></div>

            <div
                class="relative w-full max-w-md rounded-3xl bg-white shadow-2xl p-8 text-center border"
                :class="variantClasses[variant].border">
                <div
                    class="w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4"
                    :class="variantClasses[variant].border">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        class="w-8 h-8"
                        :class="variantClasses[variant].accent">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 9v3.75m0 3.75h.007v.008H12v-.008zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <h3 class="text-2xl font-semibold text-[#1E1E1E] mb-3">
                    {{ title }}
                </h3>
                <p class="text-gray-600 mb-8 leading-relaxed">
                    {{ message }}
                </p>

                <div class="flex gap-4 justify-center">
                    <button
                        type="button"
                        class="px-6 py-2 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 transition"
                        @click="emit('cancel')">
                        {{ cancelText }}
                    </button>
                    <button
                        type="button"
                        class="px-6 py-2 rounded-full text-white transition"
                        :class="variantClasses[variant].button"
                        @click="emit('confirm')">
                        {{ confirmText }}
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>


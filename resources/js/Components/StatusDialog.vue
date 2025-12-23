<script setup>
const props = defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: '' },
    message: { type: String, default: '' },
    buttonText: { type: String, default: 'OK' },
    variant: {
        type: String,
        default: 'success',
        validator: value => ['success', 'danger', 'info'].includes(value)
    }
})

const emit = defineEmits(['close'])

const variantClasses = {
    success: {
        accent: 'text-[#2E7D32]',
        border: 'border-[#2E7D32]'
    },
    danger: {
        accent: 'text-[#D32F2F]',
        border: 'border-[#D32F2F]'
    },
    info: {
        accent: 'text-[#1E88E5]',
        border: 'border-[#1E88E5]'
    }
}

const buttonClasses = {
    success: 'bg-[#29B765] hover:bg-[#1E8E4D]',
    danger: 'bg-[#D32F2F] hover:bg-[#B71C1C]',
    info: 'bg-[#1E88E5] hover:bg-[#1565C0]'
}
</script>

<template>
    <Transition name="fade">
        <div
            v-if="show"
            class="fixed inset-0 z-[1000] flex items-center justify-center px-4">
            <div
                class="absolute inset-0 bg-black/30"
                @click="emit('close')"></div>

            <div
                class="relative w-full max-w-sm rounded-3xl bg-white shadow-2xl p-8 text-center border"
                :class="variantClasses[variant].border">
                <div
                    class="w-20 h-20 mx-auto rounded-full flex items-center justify-center border mb-6"
                    :class="[variantClasses[variant].border, variantClasses[variant].accent]">
                    <svg
                        v-if="variant === 'success'"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                        class="w-10 h-10"
                        :class="variantClasses[variant].accent">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M5 13l4 4L19 7" />
                    </svg>
                    <svg
                        v-else
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                        class="w-10 h-10"
                        :class="variantClasses[variant].accent">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <h3 class="text-2xl font-semibold text-[#1E1E1E] mb-2">
                    {{ title }}
                </h3>
                <p class="text-gray-600 mb-8 leading-relaxed">
                    {{ message }}
                </p>

                <button
                    type="button"
                    class="px-8 py-2 rounded-full text-white font-semibold transition"
                    :class="buttonClasses[variant]"
                    @click="emit('close')">
                    {{ buttonText }}
                </button>
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


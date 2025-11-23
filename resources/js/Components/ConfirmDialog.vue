<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    title: {
        type: String,
        default: 'Konfirmasi'
    },
    message: {
        type: String,
        required: true
    },
    confirmText: {
        type: String,
        default: 'Konfirmasi'
    },
    cancelText: {
        type: String,
        default: 'Cancel'
    },
    confirmColor: {
        type: String,
        default: 'red' // red, blue, green
    },
    type: {
        type: String,
        default: 'confirm' // confirm, delete, publish
    }
})

const emit = defineEmits(['confirm', 'cancel', 'close'])

const isVisible = ref(props.show)

watch(() => props.show, (newVal) => {
    isVisible.value = newVal
})

const handleConfirm = () => {
    emit('confirm')
    isVisible.value = false
}

const handleCancel = () => {
    emit('cancel')
    emit('close')
    isVisible.value = false
}

const getButtonColor = () => {
    const colors = {
        red: 'bg-[#EF4444] hover:bg-[#DC2626]',
        blue: 'bg-[#3B82F6] hover:bg-[#2563EB]',
        green: 'bg-[#10B981] hover:bg-[#059669]',
        orange: 'bg-[#7A2509] hover:bg-[#5e1d07]'
    }
    return colors[props.confirmColor] || colors.red
}
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isVisible"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 px-4 backdrop-blur-sm"
                @click.self="handleCancel"
            >
                <Transition
                    enter-active-class="transition-all ease-out duration-500"
                    enter-from-class="opacity-0 -translate-y-20 scale-95"
                    enter-to-class="opacity-100 translate-y-0 scale-100"
                    leave-active-class="transition-all ease-in duration-300"
                    leave-from-class="opacity-100 translate-y-0 scale-100"
                    leave-to-class="opacity-0 translate-y-10 scale-95"
                >
                    <div
                        v-if="isVisible"
                        class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden animate-shake"
                        :class="{
                            'border-t-8 border-red-500': confirmColor === 'red',
                            'border-t-8 border-blue-500': confirmColor === 'blue',
                            'border-t-8 border-green-500': confirmColor === 'green',
                            'border-t-8 border-orange-500': confirmColor === 'orange'
                        }"
                    >
                        <!-- Animated top decoration -->
                        <div class="h-2 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-shimmer"></div>
                        
                        <div class="p-8">
                            <div class="text-center">
                                <h3 class="text-2xl font-bold text-gray-900 mb-4 animate-slide-down">
                                    {{ message }}
                                </h3>
                            </div>

                            <div class="flex gap-3 mt-6 animate-slide-up animation-delay-150">
                                <button
                                    @click="handleConfirm"
                                    :class="getButtonColor()"
                                    class="flex-1 px-6 py-3 text-white font-semibold rounded-xl transition-all duration-200 hover:scale-105 hover:shadow-lg active:scale-95 transform"
                                >
                                    {{ confirmText }}
                                </button>
                                <button
                                    @click="handleCancel"
                                    class="flex-1 px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-xl transition-all duration-200 hover:scale-105 active:scale-95 transform"
                                >
                                    {{ cancelText }}
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
@keyframes slide-down {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes slide-up {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes shake {
    0%, 100% {
        transform: translateX(0) rotate(0deg);
    }
    25% {
        transform: translateX(-3px) rotate(-1deg);
    }
    75% {
        transform: translateX(3px) rotate(1deg);
    }
}

@keyframes shimmer {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
}

.animate-slide-down {
    animation: slide-down 0.4s ease-out;
}

.animate-slide-up {
    animation: slide-up 0.4s ease-out;
}

.animate-shake {
    animation: shake 0.5s ease-in-out;
}

.animate-shimmer {
    animation: shimmer 2s ease-in-out infinite;
}

.animation-delay-150 {
    animation-delay: 0.15s;
    opacity: 0;
    animation-fill-mode: forwards;
}
</style>

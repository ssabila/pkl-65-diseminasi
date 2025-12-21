<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    type: {
        type: String,
        default: 'success' // success, error, warning, info
    },
    title: {
        type: String,
        required: true
    },
    message: {
        type: String,
        default: ''
    },
    buttonText: {
        type: String,
        default: 'OK'
    }
})

const emit = defineEmits(['close'])

const isVisible = ref(props.show)

watch(() => props.show, (newVal) => {
    isVisible.value = newVal
})

const handleClose = () => {
    emit('close')
    isVisible.value = false
}

const getIcon = () => {
    const icons = {
        success: `
            <svg class="w-20 h-20 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke-width="2" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
            </svg>
        `,
        error: `
            <svg class="w-20 h-20 text-red-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke-width="2" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9l-6 6m0-6l6 6" />
            </svg>
        `,
        warning: `
            <svg class="w-20 h-20 text-yellow-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        `,
        info: `
            <svg class="w-20 h-20 text-blue-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke-width="2" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16v-4m0-4h.01" />
            </svg>
        `
    }
    return icons[props.type] || icons.success
}

const getButtonColor = () => {
    const colors = {
        success: 'bg-green-500 hover:bg-green-600',
        error: 'bg-red-500 hover:bg-red-600',
        warning: 'bg-yellow-500 hover:bg-yellow-600',
        info: 'bg-blue-500 hover:bg-blue-600'
    }
    return colors[props.type] || colors.success
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
                @click.self="handleClose"
            >
                <Transition
                    enter-active-class="transition-all ease-out duration-500"
                    enter-from-class="opacity-0 scale-50 rotate-12"
                    enter-to-class="opacity-100 scale-100 rotate-0"
                    leave-active-class="transition-all ease-in duration-300"
                    leave-from-class="opacity-100 scale-100 rotate-0"
                    leave-to-class="opacity-0 scale-75 -rotate-6"
                >
                    <div
                        v-if="isVisible"
                        class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8 transform animate-bounce-in"
                    >
                        <div class="text-center">
                            <!-- Icon with pulse animation -->
                            <div class="mb-6 relative inline-block animate-scale-in" v-html="getIcon()"></div>

                            <!-- Title with slide animation -->
                            <h3 class="text-2xl font-bold text-gray-900 mb-3 animate-slide-up">
                                {{ title }}
                            </h3>

                            <!-- Message with delayed slide animation -->
                            <p v-if="message" class="text-gray-600 mb-6 animate-slide-up animation-delay-100">
                                {{ message }}
                            </p>

                            <!-- Button with delayed fade animation -->
                            <button
                                @click="handleClose"
                                :class="getButtonColor()"
                                class="px-8 py-3 text-white font-semibold rounded-xl transition-all duration-150 min-w-[120px] hover:scale-105 hover:shadow-lg active:scale-95 animate-fade-in animation-delay-200"
                            >
                                {{ buttonText }}
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
@keyframes scale-in {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
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

@keyframes fade-in {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes bounce-in {
    0% {
        transform: scale(0.3);
        opacity: 0;
    }
    50% {
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.animate-scale-in {
    animation: scale-in 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.animate-slide-up {
    animation: slide-up 0.4s ease-out;
}

.animate-fade-in {
    animation: fade-in 0.4s ease-out;
}

.animate-bounce-in {
    animation: bounce-in 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.animation-delay-100 {
    animation-delay: 0.1s;
    opacity: 0;
    animation-fill-mode: forwards;
}

.animation-delay-200 {
    animation-delay: 0.2s;
    opacity: 0;
    animation-fill-mode: forwards;
}

/* Icon pulse effect */
:deep(svg) {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.9;
        transform: scale(1.05);
    }
}
</style>

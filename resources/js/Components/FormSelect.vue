<script setup>
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    },
    label: {
        type: String,
        required: true
    },
    placeholder: {
        type: String,
        default: 'Select option'
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
    disabled: {
        type: Boolean,
        default: false
    },
    options: {
        type: Array,
        default: () => []
    },
    optionLabel: {
        type: String,
        default: 'label'
    },
    optionValue: {
        type: String,
        default: 'value'
    },
    loading: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:modelValue'])

const isOpen = ref(false)
const searchQuery = ref('')
const dropdownPosition = ref('bottom')
const selectRef = ref(null)
const highlightedIndex = ref(-1)
const inputId = computed(() => props.id || (props.label ? props.label.toLowerCase().replace(/\s+/g, '-') : 'select-' + Math.random().toString(36).substr(2, 9)))

const filteredOptions = computed(() => {
    const query = searchQuery.value.toLowerCase()
    return props.options.filter(option => {
        const label = option[props.optionLabel]
        if (typeof label !== 'string') return false
        return label.toLowerCase().includes(query)
    })
})

const displayValue = computed(() => {
    const selected = props.options.find(
        option => String(option[props.optionValue]) === String(props.modelValue)
    )
    return selected ? selected[props.optionLabel] : props.placeholder
})

const selectClass = computed(() => {
    const base =
        "w-full rounded-lg px-4 py-2 text-sm appearance-none transition-all outline-none " +
        "bg-[#F2F2F2] text-black placeholder:text-gray-500 " +
        "border border-[#D1D5DC] focus:border-[#EF874B] focus:ring-2 focus:ring-[#EF874B]/40 "

    const open = isOpen.value ? "border-[#EF874B] ring-2 ring-[#EF874B]/40 " : ""
    const error = props.error ? "border-red-500" : ""
    const disabled = props.disabled ? "opacity-50 cursor-not-allowed" : ""

    return base + open + error + disabled
})

function toggleDropdown() {
    if (props.disabled) return

    isOpen.value = !isOpen.value
    if (isOpen.value) {
        nextTick(() => {
            const input = document.querySelector(`#${inputId.value}`)
            if (input) {
                input.focus()
                updateDropdownPosition(input)
            }
        })
    }
}

function updateDropdownPosition(inputEl) {
    const rect = inputEl.getBoundingClientRect()
    const spaceBelow = window.innerHeight - rect.bottom
    const spaceAbove = rect.top
    const dropdownHeight = 250

    dropdownPosition.value =
        spaceBelow < dropdownHeight && spaceAbove > spaceBelow ? 'top' : 'bottom'
}

function selectOption(option) {
    emit('update:modelValue', option[props.optionValue])
    searchQuery.value = ''
    isOpen.value = false
    highlightedIndex.value = -1
}

function isOptionSelected(option) {
    return String(option[props.optionValue]) === String(props.modelValue)
}

function handleClickOutside(e) {
    if (selectRef.value && !selectRef.value.contains(e.target)) {
        isOpen.value = false
        searchQuery.value = ''
        highlightedIndex.value = -1
    }
}

function clearSelection() {
    emit('update:modelValue', '')
    searchQuery.value = ''
}

function handleKeydown(e) {
    if (!isOpen.value) {
        if (['ArrowDown', 'Enter', ' '].includes(e.key)) {
            e.preventDefault()
            isOpen.value = true
        }
        return
    }

    switch (e.key) {
        case 'Escape':
            e.preventDefault()
            isOpen.value = false
            searchQuery.value = ''
            highlightedIndex.value = -1
            break
        case 'ArrowDown':
            e.preventDefault()
            if (highlightedIndex.value < filteredOptions.value.length - 1) {
                highlightedIndex.value++
                scrollToHighlighted()
            }
            break
        case 'ArrowUp':
            e.preventDefault()
            if (highlightedIndex.value > 0) {
                highlightedIndex.value--
                scrollToHighlighted()
            }
            break
        case 'Enter':
            e.preventDefault()
            if (highlightedIndex.value >= 0) {
                selectOption(filteredOptions.value[highlightedIndex.value])
            }
            break
        case 'Home':
            highlightedIndex.value = 0
            scrollToHighlighted()
            break
        case 'End':
            highlightedIndex.value = filteredOptions.value.length - 1
            scrollToHighlighted()
            break
    }
}

function scrollToHighlighted() {
    nextTick(() => {
        const item = document.querySelector(
            `#${inputId.value}-option-${highlightedIndex.value}`
        )
        if (item) {
            item.scrollIntoView({ block: 'nearest', behavior: 'smooth' })
        }
    })
}

const scrollHandler = () => {
    if (isOpen.value) {
        const input = document.querySelector(`#${inputId.value}`)
        if (input) updateDropdownPosition(input)
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside, true)
    window.addEventListener('scroll', scrollHandler, true)
    window.addEventListener('resize', scrollHandler)
})

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside, true)
    window.removeEventListener('scroll', scrollHandler, true)
    window.removeEventListener('resize', scrollHandler)
})
</script>

<template>
    <fieldset ref="selectRef" class="mb-4 relative">
        <!-- Label -->
        <label :for="inputId" class="block text-sm font-semibold text-black mb-1">
            {{ label }} <span v-if="required" class="text-red-600">*</span>
        </label>

        <!-- Select Box -->
        <div class="relative" @click.stop="toggleDropdown">
            <input
                :id="inputId"
                type="text"
                readonly
                :value="displayValue"
                :disabled="disabled"
                :class="selectClass"
                role="combobox"
                :aria-expanded="isOpen"
                @keydown="handleKeydown"
            />

            <!-- Clear button -->
            <button
                v-if="modelValue && !disabled"
                type="button"
                @click.stop="clearSelection"
                class="absolute right-8 top-1/2 -translate-y-1/2 text-gray-500 hover:text-black"
            >
                ×
            </button>

            <!-- Arrow -->
            <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-700"
                 :class="{ 'rotate-180': isOpen }"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 9l-7 7-7-7" />
            </svg>
        </div>

        <!-- Dropdown -->
        <section
            v-show="isOpen"
            class="absolute z-50 w-full mt-1 bg-white border border-[#D1D5DC] rounded-xl shadow-lg overflow-hidden max-h-[250px] flex flex-col"
            :class="dropdownPosition === 'top' ? 'bottom-full mb-2' : 'top-full mt-2'"
        >
            <!-- Search Box -->
            <div class="p-2 border-b border-[#D1D5DC] bg-[#F8F8F8]">
                <input
                    v-model="searchQuery"
                    type="search"
                    placeholder="Search..."
                    class="w-full px-3 py-1.5 text-sm rounded-lg border border-[#D1D5DC] bg-white outline-none focus:border-[#EF874B]"
                    @click.stop
                />
            </div>

            <!-- Options -->
            <ul class="overflow-y-auto text-sm">
                <li
                    v-if="loading"
                    class="py-4 text-center text-gray-500"
                >
                    Loading...
                </li>

                <li
                    v-for="(option, index) in filteredOptions"
                    :key="option[optionValue]"
                    :id="`${inputId}-option-${index}`"
                    @mouseenter="highlightedIndex = index"
                    @click="selectOption(option)"
                    class="px-4 py-2 cursor-pointer"
                    :class="{
                        'bg-[#FCDA7B]/40': isOptionSelected(option),
                        'bg-[#F2F2F2]': highlightedIndex === index && !isOptionSelected(option)
                    }"
                >
                    {{ option[optionLabel] }}
                </li>

                <li
                    v-if="!loading && filteredOptions.length === 0"
                    class="py-4 text-center text-gray-500"
                >
                    No matches found
                </li>
            </ul>
        </section>

        <!-- Error -->
        <p v-if="error" class="mt-1 text-xs text-red-600">
            {{ error }}
        </p>
    </fieldset>
</template>

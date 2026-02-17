<script setup lang="ts">
import { ref, computed, useAttrs } from 'vue'

const onInput = (event: Event) => {
  emit('update:modelValue', (event.target as HTMLInputElement).value)
}


const props = defineProps<{
  modelValue: string
  label: string
  type?: string
  placeholder?: string
  required?: boolean
  autofocus?: boolean
  error?: string
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>()

const attrs = useAttrs()
const inputType = ref(props.type || 'text')
const isFocused = ref(false)

const togglePasswordVisibility = () => {
  if (props.type === 'password') {
    inputType.value = inputType.value === 'password' ? 'text' : 'password'
  }
}

const labelClasses = computed(() => [
  'absolute left-3 transition-all duration-200 pointer-events-none',
  (isFocused.value || props.modelValue)
    ? '-top-2 text-xs bg-white px-1 text-primary-600'
    : 'top-3 text-neutral-500'
])

const inputClasses = computed(() => [
  'block w-full px-3 py-2.5 border rounded-lg focus:outline-none focus:ring-2 transition-all duration-200',
  props.error
    ? 'border-red-500 focus:border-red-500 focus:ring-red-200'
    : 'border-neutral-300 focus:border-primary-500 focus:ring-primary-200'
])
</script>

<template>
  <div class="relative mb-2">
    <label :for="label" :class="labelClasses">
      {{ label }}<span v-if="required">*</span>
    </label>

    <input
      :id="label"
      :type="inputType"
      :value="modelValue"
      :placeholder="placeholder"
      :required="required"
      :autofocus="autofocus"
      v-bind="attrs"
      :class="inputClasses"
      @input="onInput"
      @focus="isFocused = true"
      @blur="isFocused = false"
    />

    <button
      v-if="props.type === 'password'"
      type="button"
      class="absolute right-3 top-3 text-neutral-500 hover:text-neutral-700"
      @click="togglePasswordVisibility"
    >
      <span class="material-symbols-outlined">
        {{ inputType === 'password' ? 'visibility' : 'visibility_off' }}
      </span>
    </button>

    <p v-if="error" class="mt-1 text-sm text-red-600 animate-fade-in">
      {{ error }}
    </p>
  </div>
</template>


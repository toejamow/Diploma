<script setup lang="ts">
const props = withDefaults(defineProps<{
  type?: 'button' | 'submit' | 'reset'
  variant?: 'primary' | 'secondary' | 'outline' | 'text'
  size?: 'sm' | 'md' | 'lg'
  disabled?: boolean
  loading?: boolean
  full?: boolean
}>(), {
  type: 'button',
  variant: 'primary',
  size: 'md',
  disabled: false,
  loading: false,
  full: false
})

</script>


<template>
  <button 
    :type="type"
:disabled="!!props.disabled || !!props.loading"
    class="relative inline-flex items-center justify-center rounded-lg font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2"
    :class="[
      {
        'bg-primary-600 hover:bg-primary-700 text-white focus:ring-primary-500': variant === 'primary',
        'bg-neutral-100 text-neutral-800 focus:ring-neutral-300': variant === 'secondary',
        'border border-neutral-300 hover:bg-neutral-50 text-neutral-800 focus:ring-neutral-300': variant === 'outline',
        'hover:bg-neutral-100 text-primary-600 hover:text-primary-700': variant === 'text'
      },
      {
        'text-sm h-9 px-3': size === 'sm',
        'text-base h-11 px-5': size === 'md',
        'text-lg h-14 px-8': size === 'lg'
      },
      { 'w-full': full },
      { 'opacity-70 cursor-not-allowed': props.disabled || props.loading }
    ]"
  >
    <span 
      v-if="props.loading" 
      class="absolute inset-0 flex items-center justify-center"
    >
      <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
      </svg>
    </span>
    <span :class="{ 'opacity-0': props.loading }">
      <slot></slot>
    </span>
  </button>
</template>

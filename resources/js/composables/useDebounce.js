// composables/useDebounce.js
import { ref, watch } from 'vue'

export function useDebounce(value, delay = 500) {
    const debouncedValue = ref(value.value)
    let timeout = null

    watch(value, (newValue) => {
        if (timeout) {
            clearTimeout(timeout)
        }
        
        timeout = setTimeout(() => {
            debouncedValue.value = newValue
        }, delay)
    })

    return debouncedValue
}
import { useState } from '#app'

export const useAlert = () => {
  const alert = useState<string | null>('alert', () => null)
  const alertType = useState<'success' | 'error' | 'info'>('alertType', () => 'info')

  const showAlert = (message: string, type: 'success' | 'error' | 'info' = 'info', duration = 3000) => {
    alert.value = message
    alertType.value = type

    // Animation de fade-out après 3 secondes
    setTimeout(() => {
      alert.value = null
    }, duration);
  }

  const getAlertClasses = () => {
    const baseClasses = 'fixed top-4 right-4 left-4 lg:left-auto px-4 py-3 rounded-lg shadow-lg z-70'
    const animationClasses = 'animate-fade-in-down'
    const typeClasses = {
      success: 'bg-green-100 border border-green-400 text-green-700',
      error: 'bg-red-100 border border-red-400 text-red-700',
      info: 'bg-blue-100 border border-blue-400 text-blue-700'
    }
    return `${baseClasses} ${animationClasses} ${typeClasses[alertType.value]}`
  }

  const getIconName = () => {
    return {
      success: 'heroicons:check-circle',
      error: 'heroicons:exclamation-circle',
      info: 'heroicons:information-circle'
    }[alertType.value]
  }

  return {
    alert,
    alertType,
    showAlert,
    getAlertClasses,
    getIconName
  }
}

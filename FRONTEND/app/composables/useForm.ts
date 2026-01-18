import { ref, computed } from "vue";

export function useForm<T extends Record<string, any>>(initialValues: T) {
	const values = ref<T>({ ...initialValues });
	const errors = ref<Record<string, string>>({});
	const isSubmitting = ref(false);

	const setFieldValue = (field: keyof T, value: any) => {
		values.value[field] = value;
		delete errors.value[field as string];
	};

	const setFieldError = (field: keyof T, error: string) => {
		errors.value[field as string] = error;
	};

	const setErrors = (newErrors: Record<string, string[]>) => {
		errors.value = {};
		Object.keys(newErrors).forEach((key) => {
			errors.value[key] = newErrors[key][0];
		});
	};

	const reset = () => {
		values.value = { ...initialValues };
		errors.value = {};
		isSubmitting.value = false;
	};

	const isValid = computed(() => {
		return Object.keys(errors.value).length === 0;
	});

	return {
		values,
		errors,
		isSubmitting,
		setFieldValue,
		setFieldError,
		setErrors,
		reset,
		isValid,
	};
}

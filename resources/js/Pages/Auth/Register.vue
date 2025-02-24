<script setup>
import { computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head } from '@inertiajs/vue3';

const page = usePage();
const formFields = computed(() => page.props.formFields ?? []);

const formData = {};
formFields.value.forEach(field => {
    formData[field.name] = '';
});

const form = useForm(formData);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            formFields.value.forEach(field => {
                form.reset(field.name);
            });
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />
        <form @submit.prevent="submit">
            <div v-for="field in formFields" :key="field.name">
                <InputLabel :for="field.name" :value="field.label ?? field.name" />
                <TextInput
                    :id="field.name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form[field.name]"
                    required
                    :autocomplete="field.name"
                />
                <InputError class="mt-2" :message="form.errors[field.name]" />
            </div>
            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

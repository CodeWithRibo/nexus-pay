<script setup>
import LoginForm from "@/Pages/components/auth/LoginForm.vue";
import { Head, useForm } from "@inertiajs/vue3";
import KioskLayout from "@/Pages/components/layout/KioskLayout.vue";

const form = useForm({
    login: null,
    password: null,
});

const submitForm = () => {
    form.post(route("login.store"), {
        onError: () => {},
    });
};
</script>

<template>
    <KioskLayout>
        <Head title="Login" />
        <LoginForm :form="form" @submit="submitForm" />
    </KioskLayout>
</template>

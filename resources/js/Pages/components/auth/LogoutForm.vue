<script setup>
import { useForm } from "@inertiajs/vue3";

const form = useForm();

const submitForm = () => {
    form.post(route("login.destroy"));
};
</script>

<template>
    <form @submit.prevent="submitForm">
        <slot/>
    </form>
</template>

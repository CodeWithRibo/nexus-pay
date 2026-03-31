<script setup>
import { Button } from "@/components/ui/button/index.js";
import { useForm } from "@inertiajs/vue3";

const form = useForm();

const submitForm = () => {
    form.post(route("login.destroy"));
};
</script>

<template>
    <form @submit.prevent="submitForm">
        <Button type="submit">Logout</Button>
    </form>
</template>

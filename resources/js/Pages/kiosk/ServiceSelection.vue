<script setup>
import KioskLayout from "@/Pages/components/layout/KioskLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import ServiceCard from "@/Pages/components/kiosk/ServiceCard.vue";
import { computed } from "vue";

const page = usePage();
const user = computed(() => page.props.auth.user);

const isAuth = computed(() => Boolean(!user.value));
</script>

<template>
    <KioskLayout
        class="w-full flex items-center justify-center relative min-h-screen"
    >
        <Head title="Service" />
        <ServiceCard />
    </KioskLayout>
</template>

<style scoped></style>

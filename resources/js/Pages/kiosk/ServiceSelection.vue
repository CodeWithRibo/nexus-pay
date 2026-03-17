<script setup>
import KioskLayout from "@/Pages/components/layout/KioskLayout.vue";
import { Head } from "@inertiajs/vue3";
import ServiceCard from "@/Pages/components/kiosk/ServiceCard.vue";
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

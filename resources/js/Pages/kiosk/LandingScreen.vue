<script setup>
import { Head } from "@inertiajs/vue3";
import HeroSection from "@/Pages/components/kiosk/HeroSection.vue";
import KioskLayout from "@/Pages/components/layout/KioskLayout.vue";
</script>

<template>
    <KioskLayout>
        <Head title="Landing" />
        <HeroSection />
    </KioskLayout>
</template>

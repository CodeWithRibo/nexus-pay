<script setup>
import { Head } from "@inertiajs/vue3";
import HeroSection from "@/Pages/components/kiosk/HeroSection.vue";
import KioskLayout from "@/Pages/components/layout/KioskLayout.vue";
</script>

<template>
    <KioskLayout
        class="w-full flex items-center justify-center relative min-h-screen"
    >
        <Head title="Landing" />
        <HeroSection />
    </KioskLayout>
</template>

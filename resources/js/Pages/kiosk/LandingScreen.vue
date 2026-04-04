<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import HeroSection from "@/Pages/components/kiosk/HeroSection.vue";
import KioskLayout from "@/Pages/components/layout/KioskLayout.vue";
import { computed } from "vue";
import IdleScanner from "@/components/IdleScanner.vue";

const page = usePage();
const user = computed(() => page.props.auth.user === null);

const isAuth = computed(() => Boolean(!user.value));
</script>

<template>
    <IdleScanner v-if="isAuth" />
    <KioskLayout
        class="w-full flex items-center justify-center relative min-h-screen"
    >
        <Head title="Home" />
        <HeroSection />
    </KioskLayout>
</template>

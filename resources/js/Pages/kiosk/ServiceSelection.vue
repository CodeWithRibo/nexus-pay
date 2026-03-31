<script setup>
import KioskLayout from "@/Pages/components/layout/KioskLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import ServiceCard from "@/Pages/components/kiosk/ServiceCard.vue";
import { computed } from "vue";
import HeaderSection from "@/Pages/components/layout/kiosk/HeaderSection.vue";
import Footer from "@/Pages/components/layout/kiosk/Footer.vue";

const page = usePage();
const user = computed(() => page.props.auth.user);

const isAuth = computed(() => Boolean(!user.value));
</script>

<template>
    <KioskLayout>
        <Head title="Service" />
        <div class="min-h-screen flex flex-col">
            <HeaderSection/>
            <main class="mt-10">
                <ServiceCard />
            </main>
            <Footer/>
        </div>

    </KioskLayout>
</template>

<style scoped></style>

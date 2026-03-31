<script setup>
import KioskLayout from "@/Pages/components/layout/KioskLayout.vue";
import { Head } from "@inertiajs/vue3";
import BillSummary from "@/Pages/components/kiosk/BillSummary.vue";
import HeaderSection from "@/Pages/components/layout/kiosk/HeaderSection.vue";
import Footer from "@/Pages/components/layout/kiosk/Footer.vue";

const props = defineProps({
    student: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <KioskLayout>
        <Head title="Payment Method" />
        <div class="min-h-screen flex flex-col">
            <HeaderSection />
            <main class="flex-1 flex overflow-hidden">
                <BillSummary :student="student" />
            </main>
            <Footer />
        </div>
    </KioskLayout>
</template>

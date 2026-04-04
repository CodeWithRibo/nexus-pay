<script setup>
import KioskLayout from "@/Pages/components/layout/KioskLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import BillSummary from "@/Pages/components/kiosk/BillSummary.vue";
import HeaderSection from "@/Pages/components/layout/kiosk/HeaderSection.vue";
import Footer from "@/Pages/components/layout/kiosk/Footer.vue";
import { computed } from "vue";
import IdleScanner from "@/components/IdleScanner.vue";

const page = usePage();
const user = computed(() => page.props.auth.user === null);
const isAuth = computed(() => Boolean(!user.value));

const props = defineProps({
    student: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <IdleScanner v-if="isAuth" />
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

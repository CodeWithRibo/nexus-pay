<script setup>
import KioskLayout from "@/Pages/components/layout/KioskLayout.vue";
import { Head } from "@inertiajs/vue3";
import OutstandingBalances from "@/Pages/components/kiosk/OutstandingBalances.vue";
import HeaderSection from "@/Pages/components/layout/kiosk/HeaderSection.vue";
import Footer from "@/Pages/components/layout/kiosk/Footer.vue";
</script>

<template>
    <KioskLayout>
        <!--Show list of outstanding balance-->
        <Head title="Balances" />
        <div class="min-h-screen flex flex-col">
            <HeaderSection />
            <main class="flex-1 flex overflow-hidden">
                <OutstandingBalances />
            </main>
            <Footer />
        </div>
    </KioskLayout>
</template>

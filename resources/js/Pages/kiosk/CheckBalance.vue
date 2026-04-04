<script setup>
import KioskLayout from "@/Pages/components/layout/KioskLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import OutstandingBalances from "@/Pages/components/kiosk/OutstandingBalances.vue";
import HeaderSection from "@/Pages/components/layout/kiosk/HeaderSection.vue";
import Footer from "@/Pages/components/layout/kiosk/Footer.vue";
import IdleScanner from "@/components/IdleScanner.vue";
import { computed } from "vue";

const page = usePage();
const user = computed(() => page.props.auth.user === null);
const isAuth = computed(() => Boolean(!user.value));
</script>

<template>
    <IdleScanner v-if="isAuth" />
    <KioskLayout>
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

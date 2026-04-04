<script setup>
import { Head, router, usePage } from "@inertiajs/vue3";
import KioskLayout from "@/Pages/components/layout/KioskLayout.vue";
import Footer from "@/Pages/components/layout/kiosk/Footer.vue";
import {
    CheckCircle2,
    Printer,
    Download,
    Home,
    Calendar,
} from "lucide-vue-next";
import { Button } from "@/components/ui/button/index.js";
import HeaderSection from "@/Pages/components/layout/kiosk/HeaderSection.vue";
import LeavingModal from "@/components/LeavingModal.vue";
import { computed, ref } from "vue";
import IdleScanner from "@/components/IdleScanner.vue";

const page = usePage();
const user = computed(() => page.props.auth.user === null);
const isAuth = computed(() => Boolean(!user.value));
const props = defineProps({
    student_name: String,
    student_id: String,
    reference_number: String,
    fee_category: String,
    amount_paid: String,
    total_paid_to_date: Number,
    outstanding_balance: Number,
    transaction_date: String,
});

const leavingModal = ref(false);

const formatCurrency = (amount) => {
    return new Intl.NumberFormat("en-PH", {
        style: "currency",
        currency: "PHP",
    }).format(amount);
};

const handleLogout = () => {
    router.post(route("login.destroy"));
};

const handleLeavingModal = () => {
    leavingModal.value = true;
};
</script>

<template>
    <IdleScanner v-if="isAuth" />
    <KioskLayout>
        <Head title="Payment Receipt" />
        <div class="flex flex-col bg-[#0a0a0a] min-h-screen">
            <HeaderSection />
            <main
                class="flex-1 flex items-center justify-center w-full px-3 sm:px-4 md:px-6 lg:px-8 py-6"
            >
                <div
                    class="mx-auto max-w-7xl bg-[#1a1a1a] border border-white/10 rounded-3xl p-7 space-y-5"
                >
                    <div class="flex justify-center">
                        <div
                            class="w-20 h-20 bg-[#2a2a2a] border border-white/20 rounded-2xl flex items-center justify-center"
                        >
                            <CheckCircle2 class="size-10 text-white" />
                        </div>
                    </div>

                    <div class="text-center space-y-2 sm:space-y-3">
                        <h1
                            class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white"
                        >
                            Payment Successful!
                        </h1>
                        <p
                            class="text-gray-400 text-xs sm:text-sm leading-relaxed max-w-lg mx-auto"
                        >
                            Your transaction has been processed successfully.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                        <div
                            class="bg-[#0f0f0f] border border-white/10 rounded-xl sm:rounded-2xl p-4 sm:p-6 space-y-3 sm:space-y-4"
                        >
                            <p
                                class="text-gray-500 text-sm sm:text-base lg:text-lg uppercase tracking-wider font-semibold"
                            >
                                Student Details
                            </p>
                            <div class="space-y-2 sm:space-y-3">
                                <div>
                                    <p
                                        class="text-gray-500 text-xs sm:text-sm mb-1"
                                    >
                                        Student Name
                                    </p>
                                    <p
                                        class="text-white text-base sm:text-lg font-medium"
                                    >
                                        {{ student_name }}
                                    </p>
                                </div>
                                <div>
                                    <p
                                        class="text-gray-500 text-xs sm:text-sm mb-1"
                                    >
                                        Student ID
                                    </p>
                                    <p
                                        class="text-white text-base sm:text-lg font-medium"
                                    >
                                        {{ student_id }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-[#0f0f0f] border border-white/10 rounded-xl sm:rounded-2xl p-4 sm:p-6 space-y-3 sm:space-y-4"
                        >
                            <p
                                class="text-gray-500 text-xs uppercase tracking-wider font-semibold"
                            >
                                Payment Summary
                            </p>
                            <div class="space-y-2 sm:space-y-3">
                                <div>
                                    <p
                                        class="text-gray-500 text-xs sm:text-sm mb-1"
                                    >
                                        Reference Number
                                    </p>
                                    <p
                                        class="text-white text-base sm:text-lg font-medium break-all"
                                    >
                                        {{ reference_number }}
                                    </p>
                                </div>
                                <div>
                                    <p
                                        class="text-gray-500 text-xs sm:text-sm mb-1"
                                    >
                                        Fee Category
                                    </p>
                                    <p
                                        class="text-white text-base sm:text-lg font-medium"
                                    >
                                        {{ fee_category }}
                                    </p>
                                </div>
                                <div>
                                    <p
                                        class="text-gray-500 text-xs sm:text-sm mb-1"
                                    >
                                        Amount Paid Today
                                    </p>
                                    <p
                                        class="text-white text-xl sm:text-2xl font-bold"
                                    >
                                        {{ formatCurrency(amount_paid) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6"
                    >
                        <div
                            class="bg-[#0f0f0f] border border-white/10 rounded-xl sm:rounded-2xl p-4 sm:p-6 space-y-3 sm:space-y-4"
                        >
                            <p
                                class="text-gray-500 text-xs uppercase tracking-wider font-semibold"
                            >
                                Total Paid to Date
                            </p>
                            <p
                                class="text-white text-2xl sm:text-3xl font-bold"
                            >
                                {{ formatCurrency(total_paid_to_date) }}
                            </p>
                            <p class="text-gray-400 text-xs sm:text-sm">
                                Cumulative amount paid for this fee
                            </p>
                        </div>

                        <div
                            class="bg-[#0f0f0f] border border-white/10 rounded-xl sm:rounded-2xl p-4 sm:p-6 space-y-3 sm:space-y-4"
                        >
                            <p
                                class="text-gray-500 text-xs uppercase tracking-wider font-semibold"
                            >
                                Outstanding Balance
                            </p>
                            <p
                                class="text-white text-2xl sm:text-3xl font-bold"
                            >
                                {{ formatCurrency(outstanding_balance) }}
                            </p>
                            <p class="text-gray-400 text-xs sm:text-sm">
                                Remaining balance to pay {{ fee_category }}
                            </p>
                        </div>

                        <div
                            class="bg-[#0f0f0f] border border-white/10 rounded-xl sm:rounded-2xl p-4 sm:p-6 space-y-3 sm:space-y-4"
                        >
                            <p
                                class="text-gray-500 text-xs uppercase tracking-wider font-semibold flex items-center gap-2"
                            >
                                <Calendar class="size-3 sm:size-4" />
                                Transaction Date
                            </p>
                            <p
                                class="text-white text-base sm:text-lg font-medium"
                            >
                                {{ transaction_date }}
                            </p>
                            <p class="text-gray-400 text-xs sm:text-sm">
                                Date and time of payment
                            </p>
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 pt-4"
                    >
                        <Button
                            class="w-full h-12 sm:h-14 lg:h-18 text-sm sm:text-base lg:text-lg bg-white text-black hover:bg-gray-200 font-semibold rounded-lg sm:rounded-xl flex items-center justify-center gap-2"
                        >
                            <Printer class="size-4 sm:size-5" />
                            Print Receipt
                        </Button>
                        <Button
                            @click="handleLeavingModal"
                            class="w-full h-12 sm:h-14 lg:h-18 text-sm sm:text-base lg:text-lg bg-[#0f0f0f] border border-white/20 text-white hover:bg-white/10 font-semibold rounded-lg sm:rounded-xl flex items-center justify-center gap-2"
                        >
                            <Home class="size-4 sm:size-5" />
                            Return Home
                        </Button>
                    </div>
                </div>
            </main>
            <Footer />
        </div>
    </KioskLayout>
    <LeavingModal v-model:open="leavingModal" @logout="handleLogout" />
</template>

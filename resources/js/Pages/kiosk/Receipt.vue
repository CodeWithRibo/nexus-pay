<script setup>
import { Head, router } from "@inertiajs/vue3";
import KioskLayout from "@/Pages/components/layout/KioskLayout.vue";
import HeaderSection from "@/Pages/components/kiosk/HeaderSection.vue";
import Footer from "@/Pages/components/layout/kiosk/Footer.vue";
import { CheckCircle2, Printer, Download, Home } from "lucide-vue-next";
import { Button } from "@/components/ui/button/index.js";

const student = {
    name: "Jonathan Alexander Doe",
    id: "STU-2023-8942",
    level: "Grade 11 - Science A",
};

const payment = {
    reference: "#REF-982341459",
    category: "Tuition Fees - Semester 2",
    total: "₱2,500.00",
};
</script>

<template>
    <KioskLayout>
        <Head title="Payment Receipt" />
        <div class="min-h-screen flex flex-col bg-[#0a0a0a]">
            <!--            <HeaderSection />-->
            <main class="flex-1 flex items-center justify-center px-8 py-10">
                <div
                    class="w-full max-w-3xl bg-[#1a1a1a] border border-white/10 rounded-3xl p-12 space-y-8"
                >
                    <div class="flex justify-center">
                        <div
                            class="w-24 h-24 bg-[#2a2a2a] border border-white/20 rounded-2xl flex items-center justify-center"
                        >
                            <CheckCircle2 class="size-14 text-white" />
                        </div>
                    </div>

                    <div class="text-center space-y-3">
                        <h1 class="text-4xl font-bold text-white">
                            Payment Successful!
                        </h1>
                        <p
                            class="text-gray-400 text-sm leading-relaxed max-w-lg mx-auto"
                        >
                            Your transaction has been processed successfully. A
                            digital copy of your receipt has been sent to your
                            registered email.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div
                            class="bg-[#0f0f0f] border border-white/10 rounded-2xl p-6 space-y-4"
                        >
                            <p
                                class="text-gray-500 text-lg uppercase tracking-wider font-semibold"
                            >
                                Student Details
                            </p>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-gray-500 text-base mb-1">
                                        Student Name
                                    </p>
                                    <p class="text-white text-lg font-medium">
                                        {{ student.name }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-base mb-1">
                                        Student ID
                                    </p>
                                    <p class="text-white text-lg font-medium">
                                        {{ student.id }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-base mb-1">
                                        Grade / Level
                                    </p>
                                    <p class="text-white text-lg font-medium">
                                        {{ student.level }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-[#0f0f0f] border border-white/10 rounded-2xl p-6 space-y-4"
                        >
                            <p
                                class="text-gray-500 text-xs uppercase tracking-wider font-semibold"
                            >
                                Payment Summary
                            </p>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-gray-500 text-base mb-1">
                                        Reference Number
                                    </p>
                                    <p class="text-white text-lg font-medium">
                                        {{ payment.reference }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-base mb-1">
                                        Fee Category
                                    </p>
                                    <p class="text-white text-lg font-medium">
                                        {{ payment.category }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-base mb-1">
                                        Total Amount Paid
                                    </p>
                                    <p class="text-white text-4xl font-bold">
                                        {{ payment.total }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4">
                        <Button
                            class="w-full h-18 text-lg bg-white text-black hover:bg-gray-200 font-semibold rounded-xl flex items-center justify-center gap-2"
                        >
                            <Printer class="size-5" />
                            Print Receipt
                        </Button>
                        <Button
                            class="w-full h-18 text-lg bg-[#0f0f0f] border border-white/20 text-white hover:bg-white/10 font-semibold rounded-xl flex items-center justify-center gap-2"
                        >
                            <Download class="size-5" />
                            Download PDF
                        </Button>
                        <Button
                            @click="router.visit(route('kiosk.landing'))"
                            class="w-full h-18 text-lg bg-[#0f0f0f] border border-white/20 text-white hover:bg-white/10 font-semibold rounded-xl flex items-center justify-center gap-2"
                        >
                            <Home class="size-5" />
                            Return Home
                        </Button>
                    </div>
                </div>
            </main>
            <Footer />
        </div>
    </KioskLayout>
</template>

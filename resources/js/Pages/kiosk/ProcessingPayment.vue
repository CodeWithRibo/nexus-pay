<script setup>
import { Head, router } from "@inertiajs/vue3";
import { onMounted, onUnmounted, ref } from "vue";
import KioskLayout from "@/Pages/components/layout/KioskLayout.vue";
import HeaderSection from "@/Pages/components/kiosk/HeaderSection.vue";
import Footer from "@/Pages/components/layout/kiosk/Footer.vue";
import { Shield, Lock, RefreshCw } from "lucide-vue-next";

const progress = ref(1);
const statusMessage = ref("CONNECTING TO CENTRAL BANK GATEWAY...");
let timer = null;

onMounted(() => {
    timer = setInterval(() => {
        if (progress.value >= 100) {
            clearInterval(timer);
            router.visit(route("kiosk.tuition-fee.receipt"));
            return;
        }
        progress.value += 1;

        if (progress.value < 30) {
            statusMessage.value = "CONNECTING TO CENTRAL BANK GATEWAY...";
        } else if (progress.value < 60) {
            statusMessage.value = "VERIFYING TRANSACTION DETAILS...";
        } else if (progress.value < 90) {
            statusMessage.value = "PROCESSING SECURE PAYMENT...";
        } else {
            statusMessage.value = "FINALIZING TRANSACTION...";
        }
    }, 50);
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
});
</script>

<template>
    <KioskLayout>
        <Head title="Processing Payment" />
        <div class="min-h-screen flex flex-col bg-[#0a0a0a]">
            <main class="flex-1 flex items-center justify-center px-10 py-8">
                <div class="w-full rounded-3xl p-16 space-y-10">
                    <div class="flex justify-center">
                        <div
                            class="w-24 h-24 bg-[#2a2a2a] border border-white/20 rounded-xl flex items-center justify-center shadow-lg"
                        >
                            <span class="text-5xl font-black italic text-white"
                                >N</span
                            >
                        </div>
                    </div>

                    <div class="text-center space-y-3">
                        <h1
                            class="text-5xl font-bold tracking-tight text-white"
                        >
                            Processing Payment...
                        </h1>
                        <p class="text-gray-400 text-lg leading-relaxed">
                            Please wait. Do not remove your card or close the
                            screen.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span
                                class="text-lg font-bold uppercase tracking-[0.15em] text-white"
                            >
                                Secure Handshake
                            </span>
                            <span class="text-lg font-bold text-white">
                                {{ progress }}%
                            </span>
                        </div>
                        <div
                            class="w-full h-1.5 bg-[#2a2a2a] rounded-full overflow-hidden"
                        >
                            <div
                                class="h-full bg-white transition-all duration-150 ease-linear rounded-full"
                                :style="{ width: `${progress}%` }"
                            ></div>
                        </div>
                    </div>

                    <div class="text-center pt-2">
                        <p
                            class="text-base text-gray-600 uppercase tracking-[0.2em] font-medium"
                        >
                            {{ statusMessage }}
                        </p>
                    </div>

                    <div class="flex justify-center items-center gap-8 pt-8">
                        <div
                            class="w-13 h-13 rounded-full bg-[#1a1a1a] border border-white/10 flex items-center justify-center"
                        >
                            <Shield class="size-5 text-gray-500" />
                        </div>
                        <div
                            class="w-13 h-13 rounded-full bg-[#1a1a1a] border border-white/10 flex items-center justify-center"
                        >
                            <Lock class="size-5 text-gray-500" />
                        </div>
                        <div
                            class="w-13 h-13 rounded-full bg-[#1a1a1a] border border-white/10 flex items-center justify-center"
                        >
                            <RefreshCw class="size-5 text-gray-500" />
                        </div>
                    </div>

                    <div class="text-center">
                        <p
                            class="text-base text-gray-700 uppercase tracking-[0.25em] font-medium"
                        >
                            Encrypted Transaction Hub
                        </p>
                    </div>
                </div>
            </main>
        </div>
    </KioskLayout>
</template>

<script setup>
import { Head, router } from "@inertiajs/vue3";
import { onMounted, onUnmounted, ref } from "vue";
import KioskLayout from "@/Pages/components/layout/KioskLayout.vue";
import { Shield, Lock, RefreshCw } from "lucide-vue-next";

const props = defineProps({
    transaction_id: {
        type: String,
        required: true,
    },
    amount_paid: {
        type: Number,
        required: true,
    },
    credit_balance: {
        type: Number,
        required: true,
    },
});

const progress = ref(0);
const statusMessage = ref("CONNECTING TO CENTRAL BANK GATEWAY...");
let timer = null;

const stages = [
    { target: 25, message: "CONNECTING TO CENTRAL BANK GATEWAY...", duration: 2000 },
    { target: 50, message: "VERIFYING TRANSACTION DETAILS...", duration: 2500 },
    { target: 75, message: "PROCESSING SECURE PAYMENT...", duration: 2500 },
    { target: 85, message: "FINALIZING TRANSACTION..", duration: 1000 },
    { target: 100, message: "TRANSACTION COMPLETED...", duration: 2000 },
];

const runProgressStages = (stageIndex = 0) => {
    if (stageIndex >= stages.length) {
        processPayment();
        return;
    }

    const stage = stages[stageIndex];
    statusMessage.value = stage.message;

    const startProgress = progress.value;
    const progressDiff = stage.target - startProgress;
    const stepTime = 100;
    const steps = stage.duration / stepTime;
    const increment = progressDiff / steps;
    let stepCount = 0;

    timer = setInterval(() => {
        stepCount++;
        progress.value = Math.min(
            Math.round(startProgress + increment * stepCount),
            stage.target
        );

        if (stepCount >= steps) {
            clearInterval(timer);
            runProgressStages(stageIndex + 1);
        }
    }, stepTime);
};

const processPayment = () => {
    router.post(
        route("kiosk.tuition-fee.processing.process", {
            transaction_id: props.transaction_id,
        }),
        {
            amount_paid: props.amount_paid,
            credit_balance: props.credit_balance,
        },
        {
            preserveScroll: true,
            onError: (errors) => {
                console.error("Payment failed:", errors);
                statusMessage.value = "TRANSACTION FAILED. PLEASE TRY AGAIN.";
            },
        }
    );
};

onMounted(() => {
    runProgressStages(0);
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
                            class="w-62 h-62 flex items-center justify-center shadow-lg animate-bounce transition-all duration-75"
                        >
                            <img
                                class="rounded-lg"
                                :src="'/storage/nexus_logo_v2.png'"
                                alt="nexus_logo"
                            />
                        </div>
                    </div>

                    <div class="text-center space-y-3">
                        <h1
                            class="text-5xl font-bold tracking-tight text-white"
                        >
                            Processing Payment...
                        </h1>
                        <p class="text-gray-400 text-lg leading-relaxed">
                            Please wait. Do not close the screen.
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

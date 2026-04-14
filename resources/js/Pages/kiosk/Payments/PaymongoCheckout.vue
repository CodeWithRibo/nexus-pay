<script setup>
import { Head, router, usePage } from "@inertiajs/vue3";
import { computed, onMounted, onUnmounted, ref } from "vue";
import axios from "axios";
import { toast } from "vue-sonner";
import { Button } from "@/components/ui/button/index.js";
import KioskLayout from "@/Pages/components/layout/KioskLayout.vue";
import HeaderSection from "@/Pages/components/layout/kiosk/HeaderSection.vue";
import Footer from "@/Pages/components/layout/kiosk/Footer.vue";
import IdleScanner from "@/components/IdleScanner.vue";
import { RefreshCw, ExternalLink } from "lucide-vue-next";

const page = usePage();
const user = computed(() => page.props.auth.user === null);
const isAuth = computed(() => Boolean(!user.value));

const props = defineProps({
    transaction_id: {
        type: String,
        required: true,
    },
    amount_due: {
        type: Number,
        required: true,
    },
    payment_method: {
        type: String,
        default: "qrph",
    },
    status: {
        type: String,
        default: "awaiting_payment",
    },
    failed_reason: {
        type: String,
        default: "",
    },
    qr_code: {
        type: String,
        default: "",
    },
    checkout_url: {
        type: String,
        default: "",
    },
    expires_at: {
        type: String,
        default: null,
    },
});

const status = ref(props.status);
const failureMessage = ref(props.failed_reason);
const isChecking = ref(false);
let poller = null;

const formattedAmount = computed(() => {
    return new Intl.NumberFormat("en-PH", {
        style: "currency",
        currency: "PHP",
    }).format(Number(props.amount_due || 0));
});

const formattedMethod = computed(() => {
    const method = String(props.payment_method).toLowerCase();
    if (method === "qrph") return "QR Ph (GCash / Maya)";
    if (method === "gcash") return "GCash";
    if (method === "paymaya") return "Maya";

    return props.payment_method?.toUpperCase() || "PayMongo";
});

const expiresAtText = computed(() => {
    if (!props.expires_at) return "Valid for approximately 30 minutes.";
    return `Expires at ${new Date(props.expires_at).toLocaleString("en-PH")}`;
});

const checkStatus = async () => {
    if (isChecking.value || status.value === "failed") return;

    isChecking.value = true;
    try {
        const { data } = await axios.get(
            route("kiosk.paymongo.status", {
                transaction_id: props.transaction_id,
            }),
        );

        status.value = data.status;

        if (data.status === "completed" && data.receipt_url) {
            router.visit(data.receipt_url);
            return;
        }

        if (data.status === "failed") {
            failureMessage.value = data.message || "Payment failed.";
            toast.error("PayMongo payment failed", {
                description: failureMessage.value,
            });
        }
    } catch (error) {
        toast.error("Unable to verify payment status", {
            description: "Please try checking again in a few seconds.",
        });
    } finally {
        isChecking.value = false;
    }
};

const openCheckout = () => {
    if (!props.checkout_url) return;
    window.open(props.checkout_url, "_self", "noopener,noreferrer");
};

onMounted(() => {
    if (status.value !== "failed") {
        checkStatus();
        poller = setInterval(checkStatus, 5000);
    }
});

onUnmounted(() => {
    if (poller) clearInterval(poller);
});
</script>

<template>
    <IdleScanner v-if="isAuth" />
    <KioskLayout>
        <Head title="Pay with PayMongo" />
        <div class="min-h-screen flex flex-col">
            <HeaderSection />
            <main class="flex-1 flex items-center justify-center px-8 py-10">
                <div
                    class="w-full max-w-4xl bg-[#1a1a1a] border border-white/10 rounded-3xl p-8 space-y-8"
                >
                    <div class="text-center space-y-3">
                        <h1
                            class="text-4xl font-bold tracking-tight text-white"
                        >
                            Pay via PayMongo
                        </h1>
                        <p class="text-gray-400 text-lg">
                            Scan the QR with GCash/Maya or continue in your
                            wallet app.
                        </p>
                    </div>

                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center"
                    >
                        <div
                            class="bg-[#0f0f0f] border border-white/10 rounded-2xl p-6 space-y-4"
                        >
                            <div class="space-y-1">
                                <p
                                    class="text-gray-500 text-xs uppercase tracking-wider font-semibold"
                                >
                                    Payment Method
                                </p>
                                <p class="text-xl text-white font-semibold">
                                    {{ formattedMethod }}
                                </p>
                            </div>
                            <div class="space-y-1">
                                <p
                                    class="text-gray-500 text-xs uppercase tracking-wider font-semibold"
                                >
                                    Amount
                                </p>
                                <p class="text-3xl text-white font-bold">
                                    {{ formattedAmount }}
                                </p>
                            </div>
                            <div class="space-y-1">
                                <p
                                    class="text-gray-500 text-xs uppercase tracking-wider font-semibold"
                                >
                                    Payment Validity
                                </p>
                                <p class="text-sm text-gray-300">
                                    {{ expiresAtText }}
                                </p>
                            </div>
                            <div
                                v-if="status === 'failed'"
                                class="rounded-xl border border-red-500/40 bg-red-500/10 p-4 text-red-300 text-sm"
                            >
                                {{ failureMessage }}
                            </div>
                            <div class="flex flex-col gap-3 pt-2">
                                <Button
                                    @click="checkStatus"
                                    :disabled="
                                        isChecking || status === 'failed'
                                    "
                                    class="h-12 text-base bg-white text-black hover:bg-zinc-200"
                                >
                                    <RefreshCw class="size-5 mr-2" />
                                    Check Payment Status
                                </Button>
                                <Button
                                    v-if="checkout_url"
                                    @click="openCheckout"
                                    class="h-12 text-base border border-white/30 bg-transparent text-white hover:bg-white/10"
                                >
                                    <ExternalLink class="size-5 mr-2" />
                                    Open Wallet Checkout
                                </Button>
                            </div>
                        </div>

                        <div
                            class="bg-[#0f0f0f] border border-white/10 rounded-2xl p-6 flex items-center justify-center min-h-[380px]"
                        >
                            <div
                                v-if="qr_code"
                                class="bg-white rounded-2xl p-5 w-full max-w-sm"
                            >
                                <img
                                    :src="qr_code"
                                    alt="PayMongo QR code"
                                    class="w-full h-auto rounded-lg"
                                />
                            </div>
                            <div
                                v-else
                                class="text-center space-y-3 text-gray-300 max-w-sm"
                            >
                                <ExternalLink class="size-10 mx-auto" />
                                <p>
                                    This payment method uses secure wallet
                                    redirect.
                                </p>
                                <p class="text-sm text-gray-400">
                                    Tap <b>Open Wallet Checkout</b> to continue
                                    in the selected app.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <Footer />
        </div>
    </KioskLayout>
</template>
<style scoped>
div {
    animation: fade-in 0.6s ease-out;
}

@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script setup>
import { Head, router, usePage } from "@inertiajs/vue3";
import { ref, computed, watch, watchEffect } from "vue";
import KioskLayout from "@/Pages/components/layout/KioskLayout.vue";
import HeaderSection from "@/Pages/components/layout/kiosk/HeaderSection.vue";
import Footer from "@/Pages/components/layout/kiosk/Footer.vue";
import { Button } from "@/components/ui/button/index.js";
import { Wallet, Banknote, CircleCheck, Plus } from "lucide-vue-next";
import { toast } from "vue-sonner";
import IdleScanner from "@/components/IdleScanner.vue";

const insertedAmount = ref(Number(localStorage.getItem("insertedAmount")) || 0);
const isSubmitting = ref(false);
const manualAmount = ref("");
const page = usePage();
const user = computed(() => page.props.auth.user === null);
const isAuth = computed(() => Boolean(!user.value));

watch(insertedAmount, (newVal) => {
    localStorage.setItem("insertedAmount", newVal);
});

const props = defineProps({
    studAmountDue: {
        type: String,
        require: true,
    },
    transaction_id: {
        type: String,
        require: true,
    },
});

const formattedCurrency = new Intl.NumberFormat("en-PH", {
    style: "currency",
    currency: "PHP",
}).format(props.studAmountDue);

const remainingAmount = computed(() =>
    Math.max(props.studAmountDue - insertedAmount.value, 0),
);

if (insertedAmount.value >= props.studAmountDue) {
    isSubmitting.value = true;
}

const handleConfirmPayment = () => {
    const creditBalance = Math.max(
        insertedAmount.value - props.studAmountDue,
        0,
    );

    if (insertedAmount.value <= 0) return false;

    localStorage.removeItem("insertedAmount");

    router.post(
        route("kiosk.tuition-fee.processing.start", {
            transaction_id: props.transaction_id,
        }),
        {
            amount_paid: insertedAmount.value,
            credit_balance: creditBalance,
        },
    );
};

const handleManualInsert = () => {
    const amount = parseFloat(manualAmount.value);
    if (isNaN(amount) || amount <= 0) return;

    const acceptedBills = [20, 50, 100, 200, 500, 1000];

    if (!acceptedBills.includes(amount)) {
        toast.error("Invalid Bill Detected", {
            description:
                "Please use genuine ₱20, ₱50, ₱100, ₱200, ₱500, or ₱1,000 bills.",
            duration: 2500,
            position: "top-center",
        });
        manualAmount.value = "";
        return;
    }

    insertedAmount.value = Math.min(
        insertedAmount.value + amount,
        props.studAmountDue,
    );
    manualAmount.value = "";

    toast.success(`₱${amount}.00 Accepted`, {
        description: `Total inserted is now ₱${insertedAmount.value}.00`,
        duration: 2000,
        position: "top-center",
    });

    if (insertedAmount.value >= props.studAmountDue) {
        isSubmitting.value = true;
    }
};

const addPresetAmount = (amount) => {
    if (isSubmitting.value) return;

    insertedAmount.value = Math.min(
        insertedAmount.value + amount,
        props.studAmountDue,
    );
    manualAmount.value = "";

    toast.success(`₱${amount}.00 Accepted`, {
        description: `Total inserted is now ₱${insertedAmount.value}.00`,
        duration: 2000,
        position: "top-center",
    });

    if (insertedAmount.value >= props.studAmountDue) {
        isSubmitting.value = true;
    }
};
</script>

<template>
    <IdleScanner v-if="isAuth" />
    <KioskLayout>
        <Head title="Cash Insertion (Interactive Mockup)" />
        <div class="min-h-screen flex flex-col">
            <HeaderSection
                :insertedAmount="insertedAmount"
                :isLocked="insertedAmount > 0"
            />
            <main
                class="flex-1 flex flex-col items-center justify-center px-10 py-8"
            >
                <div class="text-center space-y-2 mb-8">
                    <h1 class="text-6xl font-bold tracking-tight uppercase">
                        Insert Cash
                    </h1>
                    <p class="text-gray-400 text-base">
                        Please insert bills into the lighted slot below the
                        screen.
                    </p>
                </div>

                <div
                    class="relative w-full max-w-2xl bg-[#1a1a1a] border-2 border-white/10 rounded-3xl p-12 mb-10"
                >
                    <div class="flex flex-col items-center space-y-6">
                        <div
                            class="relative w-64 h-40 border-4 border-white/30 rounded-lg bg-black/40 flex items-center justify-center"
                        >
                            <div
                                class="w-56 h-3 bg-linear-to-b from-white/60 to-white/20 rounded-full shadow-[0_0_20px_rgba(255,255,255,0.5)]"
                            ></div>

                            <div
                                class="absolute bottom-4 left-0 right-0 text-center"
                            >
                                <p
                                    class="text-[10px] text-gray-500 uppercase tracking-widest mb-1"
                                >
                                    Enter Amount to Insert
                                </p>
                                <div
                                    class="flex items-center justify-center gap-2"
                                >
                                    <span class="text-3xl font-bold text-white"
                                        >₱</span
                                    >
                                    <span
                                        class="text-3xl font-bold text-white"
                                        >{{ insertedAmount.toFixed(2) }}</span
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="w-full max-w-md space-y-3">
                            <div class="flex gap-2">
                                <input
                                    v-model="manualAmount"
                                    type="number"
                                    placeholder="Enter amount"
                                    :disabled="isSubmitting"
                                    @keyup.enter="handleManualInsert"
                                    class="flex-1 px-4 py-3 text-lg bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-white/50 disabled:opacity-50"
                                />
                                <Button
                                    :disabled="isSubmitting || !manualAmount"
                                    @click="handleManualInsert"
                                    class="px-6 py-7 text-xl flex items-center bg-white text-black hover:bg-gray-200 disabled:opacity-50 rounded-lg font-semibold"
                                >
                                    <Plus class="size-5" />
                                    <p>Add</p>
                                </Button>
                            </div>

                            <div class="flex gap-2 justify-center">
                                <Button
                                    v-for="amount in [20, 50, 100, 500, 1000]"
                                    :key="amount"
                                    :disabled="isSubmitting"
                                    @click="addPresetAmount(amount)"
                                    class="px-4 py-2 bg-white/5 border border-white/20 text-white hover:bg-white/10 disabled:opacity-50 rounded-lg text-2xl font-semibold"
                                >
                                    ₱{{ amount }}
                                </Button>
                            </div>
                        </div>

                        <Button
                            @click.prevent="handleConfirmPayment"
                            class="flex w-full items-center justify-center gap-2 text-black bg-white hover:bg-white rounded-xl px-5 py-7 hover:shadow-[0_0_20px_rgba(255,255,255,0.4)] transition-shadow duration-300"
                        >
                            <CircleCheck class="size-7" />
                            <span class="text-2xl font-bold">
                                Confirm Payment
                            </span>
                        </Button>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-6 w-full max-w-4xl">
                    <div
                        class="bg-[#1a1a1a] border border-white/10 rounded-xl p-6 text-center space-y-2"
                    >
                        <div
                            class="flex items-center justify-center gap-2 mb-2"
                        >
                            <Wallet class="size-5 text-gray-400" />
                            <p
                                class="text-xs text-gray-400 uppercase tracking-wider font-semibold"
                            >
                                Amount Due
                            </p>
                        </div>
                        <p class="text-4xl font-bold">
                            {{ formattedCurrency }}
                        </p>
                    </div>
                    <div
                        class="bg-white text-black rounded-xl p-6 text-center space-y-2"
                    >
                        <div
                            class="flex items-center justify-center gap-2 mb-2"
                        >
                            <Banknote class="size-5 text-gray-700" />
                            <p
                                class="text-xs text-gray-700 uppercase tracking-wider font-semibold"
                            >
                                Inserted
                            </p>
                        </div>
                        <p class="text-4xl font-bold">
                            ₱{{ insertedAmount.toLocaleString() }}.00
                        </p>
                    </div>
                    <div
                        class="bg-[#1a1a1a] border border-white/10 rounded-xl p-6 text-center space-y-2"
                    >
                        <div
                            class="flex items-center justify-center gap-2 mb-2"
                        >
                            <Wallet class="size-5 text-gray-400" />
                            <p
                                class="text-xs text-gray-400 uppercase tracking-wider font-semibold"
                            >
                                Remaining
                            </p>
                        </div>
                        <p class="text-4xl font-bold">
                            ₱{{ remainingAmount.toLocaleString() }}.00
                        </p>
                    </div>
                </div>
            </main>
            <Footer />
        </div>
    </KioskLayout>
</template>

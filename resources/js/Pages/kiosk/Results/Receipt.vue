<script setup>
import { Head, router, usePage } from "@inertiajs/vue3";
import KioskLayout from "@/Pages/components/layout/KioskLayout.vue";
import Footer from "@/Pages/components/layout/kiosk/Footer.vue";
import { Printer, Home, User, Info, CheckCircle } from "lucide-vue-next";
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
    student_email: String,
    student_id: String,
    reference_number: String,
    fee_category: String,
    amount_paid: String,
    payment_channel: String,
    payment_provider: String,
    payment_method: String,
    total_paid_to_date: Number,
    outstanding_balance: Number,
    current_overpayment: Number,
    overpayment_used: Number,
    total_overpayment: Number,
    transaction_date: String,
    status: String,
});

const formattedPaymentMethod = computed(() => {
    if (!props.payment_method) return "N/A";

    const method = String(props.payment_method).toLowerCase();
    if (method === "qrph") return "QR Ph";
    if (method === "paymaya") return "Maya";
    if (method === "gcash") return "GCash";
    if (method === "cash") return "Cash";

    return props.payment_method.toUpperCase();
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
        <div
            class="flex flex-col bg-[#0F0F0F] min-h-screen text-white font-sans"
        >
            <HeaderSection />
            <main
                class="flex-1 flex flex-col items-center justify-center w-full py-12 px-8"
            >
                <div class="text-center mb-10 space-y-4">
                    <div class="flex justify-center mb-4">
                        <div
                            class="w-16 h-16 bg-emerald-500/20 rounded-full flex items-center justify-center"
                        >
                            <CheckCircle class="size-10 text-emerald-400" />
                        </div>
                    </div>
                    <h1 class="text-5xl font-bold tracking-tight">
                        Payment Successful!
                    </h1>
                    <p class="text-zinc-500 text-lg">
                        Transaction completed on {{ transaction_date }}
                    </p>
                </div>

                <div class="grid grid-cols-12 gap-6 w-full max-w-6xl">
                    <div class="col-span-7 space-y-6">
                        <div
                            class="bg-[#1E1E1E] rounded-3xl p-8 relative overflow-hidden flex flex-col justify-between min-h-[380px]"
                        >
                            <div class="grid grid-cols-2 gap-8 mb-12">
                                <div>
                                    <p
                                        class="text-[11px] font-bold text-zinc-500 uppercase tracking-[0.15em] mb-2"
                                    >
                                        Transaction Ref
                                    </p>
                                    <p class="text-2xl font-bold break-all">
                                        {{ reference_number }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p
                                        class="text-[11px] font-bold text-zinc-500 uppercase tracking-[0.15em] mb-2"
                                    >
                                        Amount Paid
                                    </p>
                                    <p
                                        class="text-4xl font-extrabold text-emerald-400"
                                    >
                                        {{ formatCurrency(amount_paid) }}
                                    </p>
                                    <p
                                        v-if="overpayment_used > 0"
                                        class="text-emerald-400 text-sm mt-1"
                                    >
                                        +{{ formatCurrency(overpayment_used) }}
                                        from overpayment
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-8 mt-auto">
                                <div class="space-y-8">
                                    <div>
                                        <p
                                            class="text-[11px] font-bold text-zinc-500 uppercase tracking-[0.15em] mb-1"
                                        >
                                            Fee Category
                                        </p>
                                        <p class="text-lg font-medium">
                                            {{ fee_category }}
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-[11px] font-bold text-zinc-500 uppercase tracking-[0.15em] mb-1"
                                        >
                                            Status
                                        </p>
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="size-2 bg-emerald-500 rounded-full animate-pulse"
                                            ></div>
                                            <p
                                                class="text-emerald-500 font-bold text-sm tracking-widest uppercase"
                                            >
                                                {{ status }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-8">
                                    <div>
                                        <p
                                            class="text-[11px] font-bold text-zinc-500 uppercase tracking-[0.15em] mb-1"
                                        >
                                            Payment Channel
                                        </p>
                                        <p class="text-lg font-medium">
                                            Kiosk - {{ formattedPaymentMethod }}
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-[11px] font-bold text-zinc-500 uppercase tracking-[0.15em] mb-1"
                                        >
                                            Payment Provider
                                        </p>
                                        <p class="text-lg font-medium">
                                            {{ payment_provider }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="absolute -bottom-4 -right-4 opacity-[0.03]"
                            >
                                <Printer class="size-48" />
                            </div>
                        </div>

                        <div
                            class="bg-[#1E1E1E] rounded-3xl p-6 flex items-center gap-6"
                        >
                            <div
                                class="size-16 bg-zinc-800 rounded-2xl flex items-center justify-center overflow-hidden border border-white/5 shadow-inner"
                            >
                                <User class="size-10 text-zinc-600" />
                            </div>
                            <div class="grid grid-cols-3 flex-1 gap-4">
                                <div>
                                    <p
                                        class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-1"
                                    >
                                        Student Name
                                    </p>
                                    <p class="text-base font-bold truncate">
                                        {{ student_name }}
                                    </p>
                                </div>
                                <div>
                                    <p
                                        class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-1"
                                    >
                                        Student ID
                                    </p>
                                    <p class="text-base font-bold">
                                        {{ student_id }}
                                    </p>
                                </div>
                                <div>
                                    <p
                                        class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-1"
                                    >
                                        Email Address
                                    </p>
                                    <p class="text-base font-bold truncate">
                                        {{ student_email }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <Button
                            class="w-full h-16 rounded-full bg-zinc-400/10 hover:bg-zinc-400/20 text-white font-bold text-lg border-none shadow-none transition-all active:scale-[0.98] cursor-pointer"
                        >
                            <Printer class="size-6 mr-3 opacity-60" />
                            Print Receipt
                        </Button>
                    </div>

                    <div class="col-span-5 flex flex-col gap-6">
                        <div
                            class="bg-[#1E1E1E] rounded-3xl p-8 flex flex-col flex-1"
                        >
                            <div class="flex items-center gap-3 mb-10">
                                <div class="p-2 bg-zinc-800 rounded-lg">
                                    <Printer class="size-5 text-zinc-400" />
                                </div>
                                <h2 class="text-xl font-bold tracking-tight">
                                    Financial Overview
                                </h2>
                            </div>

                            <div class="space-y-8 flex-1">
                                <div class="flex justify-between items-center">
                                    <p
                                        class="text-zinc-400 text-lg font-medium"
                                    >
                                        Total Paid to Date
                                    </p>
                                    <p
                                        class="text-2xl font-bold tracking-tight"
                                    >
                                        {{ formatCurrency(total_paid_to_date) }}
                                    </p>
                                </div>
                                <div class="flex justify-between items-center">
                                    <p
                                        class="text-zinc-400 text-lg font-medium"
                                    >
                                        Outstanding Balance
                                    </p>
                                    <p
                                        class="text-2xl font-bold tracking-tight text-red-500"
                                    >
                                        {{
                                            formatCurrency(outstanding_balance)
                                        }}
                                    </p>
                                </div>
                                <div class="flex justify-between items-center">
                                    <p
                                        class="text-zinc-400 text-lg font-medium"
                                    >
                                        Current Overpayment
                                    </p>
                                    <p
                                        class="text-2xl font-bold tracking-tight text-zinc-500"
                                    >
                                        {{
                                            formatCurrency(current_overpayment)
                                        }}
                                    </p>
                                </div>
                                <div class="flex justify-between items-center">
                                    <p
                                        class="text-zinc-400 text-lg font-medium"
                                    >
                                        Total Account Credit
                                    </p>
                                    <p
                                        class="text-2xl font-bold tracking-tight text-emerald-400"
                                    >
                                        {{ formatCurrency(total_overpayment) }}
                                    </p>
                                </div>
                            </div>

                            <div
                                class="mt-8 bg-zinc-800/40 rounded-2xl p-5 flex gap-4 border border-white/5"
                            >
                                <Info class="size-6 text-zinc-500 shrink-0" />
                                <p
                                    v-if="current_overpayment > 0"
                                    class="text-sm text-zinc-400 leading-relaxed"
                                >
                                    Good news! Your overpayment is saved as a
                                    credit and will be used to lower your next
                                    payment.
                                </p>
                                <p
                                    v-else
                                    class="text-sm text-zinc-400 leading-relaxed"
                                >
                                    Please keep this receipt for your records.
                                    You can view your full transaction history
                                    anytime in your student portal.
                                </p>
                            </div>
                        </div>

                        <Button
                            @click="handleLeavingModal"
                            class="w-full h-16 rounded-full bg-zinc-800 hover:bg-zinc-700 text-white font-bold text-lg border-none shadow-xl shadow-black/40 transition-all active:scale-[0.98] cursor-pointer"
                        >
                            <Home class="size-6 mr-3 opacity-60" />
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

<style scoped>
main {
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

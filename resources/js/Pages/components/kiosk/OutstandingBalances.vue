<script setup>
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button/index.js";
import { ArrowRight, CheckCircle } from "lucide-vue-next";

const props = defineProps({
    studentBalances: {
        type: Object,
        required: true,
    },
    totalAssessment: {
        type: Number,
        required: true,
    },
    amountSettled: {
        type: Number,
        required: true,
    },
    amountDue: {
        type: Number,
        required: true,
    },
});

const netBalance = computed(() => Math.max(0, props.amountDue));
const isAllPaid = computed(() => props.amountDue <= 0);

const studDataBalances = computed(() => [
    {
        title: "Total Assessment",
        amount: props.totalAssessment,
        description: "Academic year 2026-2027",
    },
    {
        title: "Amount Settled",
        amount: props.amountSettled,
        description: "Processed Payments",
    },
    {
        title: "Net Balances",
        amount: netBalance.value,
        description: isAllPaid.value ? "Fully Settled" : "Outstanding Due",
    },
]);

const formatCurrency = (value) =>
    new Intl.NumberFormat("en-PH", {
        style: "currency",
        currency: "PHP",
        minimumFractionDigits: 2,
    }).format(Math.max(0, value));

const getItemBalance = (item) => {
    return Math.max(0, item.total_amount - item.paid_amount);
};

const proceedToPayAll = () => {
    router.visit(route("kiosk.payment-method", { pay_all: true }));
};

const proceedToPayItem = (balanceId) => {
    router.visit(route("kiosk.payment-method", { balance_id: balanceId }));
};
</script>

<template>
    <section class="flex-1 overflow-y-auto px-6 py-8 md:px-10 md:py-10">
        <div class="mx-auto w-full max-w-7xl space-y-10">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <article
                    v-for="item in studDataBalances"
                    class="border border-gray-500 rounded-lg bg-[#FFFFFF0D] px-6 py-7 transition-all duration-500 ease-in-out hover:border-white hover:shadow-[0_0_20px_rgba(255,255,255,0.3)]"
                >
                    <p
                        class="text-[14px] font-semibold uppercase tracking-[0.35em] text-gray-500"
                    >
                        {{ item.title }}
                    </p>
                    <h2
                        class="mt-5 text-5xl font-bold tracking-tight text-white"
                    >
                        {{ formatCurrency(item.amount) }}
                    </h2>
                    <p
                        class="mt-1 text-[13px] uppercase tracking-[0.3em] text-gray-600"
                    >
                        {{ item.description }}
                    </p>
                </article>
            </div>

            <section
                v-if="!isAllPaid"
                class="relative overflow-hidden border rounded-xl border-white/10 bg-[#f4f4f4] px-8 py-10 text-black md:px-12 md:py-12"
            >
                <div
                    class="pointer-events-none absolute -right-4 top-0 h-full w-24 -skew-x-14 bg-black/5"
                ></div>

                <div
                    class="relative flex flex-col gap-8 md:flex-row md:items-center md:justify-between"
                >
                    <div>
                        <p
                            class="text-[14px] font-semibold uppercase tracking-[0.35em] text-gray-500"
                        >
                            Immediate Action
                        </p>
                        <h3
                            class="mt-3 max-w-135 text-5xl font-black leading-none tracking-tight"
                        >
                            Settle all outstanding account balances
                        </h3>
                    </div>

                    <div class="flex items-center gap-6">
                        <div class="text-right">
                            <p
                                class="text-[14px] font-semibold uppercase tracking-[0.35em] text-gray-500"
                            >
                                Total Payable
                            </p>
                            <p class="mt-2 text-6xl font-black tracking-tight">
                                {{ formatCurrency(netBalance) }}
                            </p>
                        </div>

                        <Button
                            @click="proceedToPayAll"
                            class="h-20 w-20 rounded-xl bg-black text-white hover:bg-black"
                        >
                            <ArrowRight class="size-7" />
                        </Button>
                    </div>
                </div>
            </section>

            <section
                v-else
                class="relative overflow-hidden border rounded-xl border-emerald-500/30 bg-emerald-500/10 px-8 py-10 md:px-12 md:py-12"
            >
                <div
                    class="relative flex flex-col gap-8 md:flex-row md:items-center md:justify-between"
                >
                    <div class="flex items-center gap-6">
                        <div
                            class="w-20 h-20 rounded-xl bg-emerald-500/20 flex items-center justify-center"
                        >
                            <CheckCircle class="size-10 text-emerald-400" />
                        </div>
                        <div>
                            <p
                                class="text-[14px] font-semibold uppercase tracking-[0.35em] text-emerald-400"
                            >
                                Congratulations
                            </p>
                            <h3
                                class="mt-3 max-w-135 text-5xl font-black leading-none tracking-tight text-white"
                            >
                                All balances have been fully settled
                            </h3>
                        </div>
                    </div>

                    <div class="text-right">
                        <p
                            class="text-[14px] font-semibold uppercase tracking-[0.35em] text-emerald-400"
                        >
                            Total Payable
                        </p>
                        <p
                            class="mt-2 text-6xl font-black tracking-tight text-emerald-400"
                        >
                            ₱0.00
                        </p>
                    </div>
                </div>
            </section>

            <section class="space-y-4">
                <div class="flex items-center gap-4">
                    <p
                        class="text-[14px] font-semibold uppercase tracking-[0.35em] text-gray-500"
                    >
                        Itemized Breakdown
                    </p>
                    <div class="h-px flex-1 bg-white/10"></div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <article
                        v-for="item in studentBalances"
                        :key="item.id"
                        :class="[
                            'border rounded-lg p-6 transition-all duration-500 ease-in-out',
                            item.status === 'completed'
                                ? 'border-emerald-500/30 bg-emerald-500/5 opacity-75'
                                : 'border-gray-500 bg-[#FFFFFF0D] hover:border-white hover:shadow-[0_0_20px_rgba(255,255,255,0.3)]',
                        ]"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h4
                                    class="text-3xl font-extrabold uppercase tracking-tight text-white"
                                >
                                    {{ item.fee_name }}
                                </h4>
                                <p
                                    v-if="item.status === 'completed'"
                                    class="mt-2 text-emerald-400 text-sm font-semibold uppercase tracking-wider"
                                >
                                    ✓ Fully Paid
                                </p>
                            </div>
                            <p class="text-4xl font-bold text-white">
                                {{ formatCurrency(getItemBalance(item)) }}
                            </p>
                        </div>

                        <Button
                            v-if="item.status !== 'completed'"
                            @click="proceedToPayItem(item.id)"
                            class="mt-8 w-full justify-between rounded-none border border-white/10 bg-transparent px-6 py-6 uppercase tracking-[0.25em] text-white hover:bg-white/5"
                        >
                            Pay Assessment
                            <ArrowRight class="size-4" />
                        </Button>
                        <div
                            v-else
                            class="mt-8 w-full flex items-center justify-center rounded-none border border-emerald-500/20 bg-emerald-500/10 px-6 py-6 text-emerald-400 uppercase tracking-[0.25em]"
                        >
                            Payment Complete
                        </div>
                    </article>
                </div>
            </section>
        </div>
    </section>
</template>

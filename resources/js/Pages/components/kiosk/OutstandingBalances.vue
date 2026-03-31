<script setup>
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button/index.js";
import { ArrowRight } from "lucide-vue-next";

const assessments = [
    {
        id: 1,
        title: "Late Registration",
        description: "Academic year 2023-2024",
        amountDue: 1200,
        amountPaid: 0,
    },
    {
        id: 2,
        title: "Laboratory Fee",
        description: "Science department",
        amountDue: 250,
        amountPaid: 0,
    },
];

const studDataBalances = [
    {
        title: "Total Assessment",
        amount: 1450,
        description: "Academic year 2023-2024",
    },
    {
        title: "Amount Settled",
        amount: 0,
        description: " Processed Payments ",
    },
    {
        title: "Net Balances",
        amount: 1450,
        description: "Outstanding Due ",
    },
];

const totalAssessment = computed(() =>
    assessments.reduce((sum, item) => sum + item.amountDue, 0),
);

const amountSettled = computed(() =>
    assessments.reduce((sum, item) => sum + item.amountPaid, 0),
);

const netBalance = computed(() => totalAssessment.value - amountSettled.value);

const formatCurrency = (value) =>
    new Intl.NumberFormat("en-PH", {
        style: "currency",
        currency: "PHP",
        minimumFractionDigits: 2,
    }).format(value);

const proceedToPayment = () => {
    // Reuse existing flow for now; swap route when dedicated outstanding route is ready.
    router.visit(route("kiosk.tuition-fee.payment-method"));
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
                        class="text-[10px] font-semibold uppercase tracking-[0.35em] text-gray-500"
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

            <!-- Immediate action -->
            <section
                class="relative overflow-hidden border border-white/10 bg-[#f4f4f4] px-8 py-10 text-black md:px-12 md:py-12"
            >
                <div
                    class="pointer-events-none absolute -right-4 top-0 h-full w-24 -skew-x-14 bg-black/5"
                ></div>

                <div
                    class="relative flex flex-col gap-8 md:flex-row md:items-center md:justify-between"
                >
                    <div>
                        <p
                            class="text-[10px] font-semibold uppercase tracking-[0.35em] text-gray-500"
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
                                class="text-[10px] font-semibold uppercase tracking-[0.35em] text-gray-500"
                            >
                                Total Payable
                            </p>
                            <p class="mt-2 text-6xl font-black tracking-tight">
                                {{ formatCurrency(netBalance) }}
                            </p>
                        </div>

                        <Button
                            @click="proceedToPayment"
                            class="h-20 w-20 rounded-none bg-black text-white hover:bg-black"
                        >
                            <ArrowRight class="size-7" />
                        </Button>
                    </div>
                </div>
            </section>

            <section class="space-y-4">
                <div class="flex items-center gap-4">
                    <p
                        class="text-[10px] font-semibold uppercase tracking-[0.35em] text-gray-500"
                    >
                        Itemized Breakdown
                    </p>
                    <div class="h-px flex-1 bg-white/10"></div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <article
                        v-for="item in assessments"
                        :key="item.id"
                        class="border border-gray-500 rounded-lg bg-[#FFFFFF0D] p-6 transition-all duration-500 ease-in-out hover:border-white hover:shadow-[0_0_20px_rgba(255,255,255,0.3)]"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h4
                                    class="text-3xl font-extrabold uppercase tracking-tight text-white"
                                >
                                    {{ item.title }}
                                </h4>
                                <p
                                    class="mt-1 text-[10px] uppercase tracking-[0.25em] text-gray-500"
                                >
                                    {{ item.description }}
                                </p>
                            </div>
                            <p class="text-4xl font-bold text-white">
                                {{
                                    formatCurrency(
                                        item.amountDue - item.amountPaid,
                                    )
                                }}
                            </p>
                        </div>

                        <Button
                            @click="proceedToPayment"
                            class="mt-8 w-full justify-between rounded-none border border-white/10 bg-transparent px-6 py-6 uppercase tracking-[0.25em] text-white hover:bg-white/5"
                        >
                            Pay Assessment
                            <ArrowRight class="size-4" />
                        </Button>
                    </article>
                </div>
            </section>
        </div>
    </section>
</template>

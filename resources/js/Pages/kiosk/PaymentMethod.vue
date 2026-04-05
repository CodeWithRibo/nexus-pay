<script setup>
import { Head, router, usePage } from "@inertiajs/vue3";
import { Label } from "@/components/ui/label/index.js";
import { Button } from "@/components/ui/button/index.js";
import { BanknoteArrowUp, QrCode, CheckCircle } from "lucide-vue-next";
import { ref, computed } from "vue";
import KioskLayout from "@/Pages/components/layout/KioskLayout.vue";
import HeaderSection from "@/Pages/components/layout/kiosk/HeaderSection.vue";
import Footer from "@/Pages/components/layout/kiosk/Footer.vue";
import IdleScanner from "@/components/IdleScanner.vue";

const page = usePage();
const user = computed(() => page.props.auth.user === null);
const isAuth = computed(() => Boolean(!user.value));

const isDisabled = ref(false);
const props = defineProps({
    student: {
        type: Object,
        required: true,
    },
    balance_id: {
        type: [Number, String, null],
        default: null,
    },
    pay_all: {
        type: Boolean,
        default: false,
    },
});

if (Number(props.student.current_balance) === 0) {
    isDisabled.value = true;
}

const formattedCurrency = new Intl.NumberFormat("en-PH", {
    style: "currency",
    currency: "PHP",
}).format(Number(props.student.current_balance));

const initiatePayment = () => {
    router.post(route("kiosk.payment-method.initiate"), {
        balance_id: props.balance_id,
        pay_all: props.pay_all,
    });
};
</script>

<template>
    <IdleScanner v-if="isAuth" />
    <KioskLayout>
        <Head title="Payment Method" />
        <div class="min-h-screen flex flex-col">
            <HeaderSection />
            <main class="flex flex-1 w-full min-h-0 overflow-hidden">
                <!--Aside-->
                <div class="hidden md:flex flex-col w-120 border border-white/20">
                    <div class="flex flex-col flex-1 overflow-y-auto">
                        <nav class="flex-1 pt-15 pl-7 bg-[#FFFFFF0D] space-y-10">
                            <div class="space-y-5">
                                <h1
                                    class="text-[45px] font-bold tracking-tight text-white"
                                >
                                    Bill Summary
                                </h1>
                                <p
                                    class="text-gray-400 font-semibold text-[18px] w-[70%] tracking-wider"
                                >
                                    Please verify your details before proceeding to
                                    payment
                                </p>
                            </div>
                            <div>
                                <div>
                                    <Label
                                        for="student_name"
                                        class="uppercase font-bold text-base text-gray-400 tracking-wider mb-2"
                                    >
                                        Student name
                                    </Label>
                                    <h2
                                        class="text-white font-semibold text-3xl tracking-tighter"
                                    >
                                        {{ student.student_name }}
                                    </h2>
                                    <div
                                        class="w-[85%] border-b-[0.01rem] border-b-gray-500 mt-8 mb-4"
                                    ></div>
                                </div>
                                <div>
                                    <Label
                                        for="student_name"
                                        class="uppercase font-bold text-base text-gray-400 tracking-wider mb-2"
                                    >
                                        Student ID
                                    </Label>
                                    <h2
                                        class="text-white font-semibold text-3xl tracking-tighter"
                                    >
                                        {{ student.student_id }}
                                    </h2>
                                    <div
                                        class="w-[85%] border-b-[0.01rem] border-b-gray-500 mt-8 mb-4"
                                    ></div>
                                </div>
                                <div>
                                    <Label
                                        for="student_name"
                                        class="uppercase font-bold text-base text-gray-400 tracking-wider mb-2"
                                    >
                                        Description
                                    </Label>
                                    <h2
                                        class="text-white font-semibold text-3xl tracking-tighter"
                                    >
                                        {{ student.description }}
                                    </h2>
                                    <div
                                        class="w-[85%] border-b-[0.01rem] border-b-gray-500 mt-8 mb-4"
                                    ></div>
                                </div>
                            </div>
                            <div
                                class="bg-[#FFFFFF14] rounded-lg p-5 w-92 text-white space-y-5"
                            >
                                <span class="text-gray-400 text-lg"
                                    >Current Balance</span
                                >
                                <h1 class="text-5xl font-bold">
                                    {{ formattedCurrency }}
                                </h1>
                            </div>
                        </nav>
                    </div>
                </div>
                <!--Hero-->
                <div class="flex flex-1 flex-col mt-20 space-y-10">
                    <div class="space-y-5">
                        <h1 class="text-center text-5xl font-bold tracking-tighter">
                            Select Payment Method
                        </h1>
                        <p class="text-center text-xl tracking-tight text-gray-400">
                            Choose your preferred way to pay
                        </p>
                    </div>
                    <div class="flex gap-5 mx-10">
                        <Button
                            :disabled="isDisabled"
                            @click="initiatePayment"
                            :class="{
                                'cursor-not-allowed': isDisabled,
                                'hover:border-white/60 hover:shadow-[0_0_20px_rgba(255,255,255,0.3)]':
                                    !isDisabled,
                            }"
                            class="group flex-1 h-42 p-15 rounded-2xl border-2 border-white/20 transition-all duration-300 flex items-center justify-start gap-6 text-left"
                        >
                            <div class="flex items-center gap-6">
                                <div
                                    :class="{
                                        'group-hover:bg-white/15 group-hover:border-white/60':
                                            !isDisabled,
                                    }"
                                    class="rounded-full p-4 border border-white/30 text-white transition-colors duration-300"
                                >
                                    <BanknoteArrowUp class="size-10" />
                                </div>
                                <div class="space-y-3">
                                    <span class="text-3xl leading-tight"
                                        >Pay via Kiosk</span
                                    >
                                    <br />
                                    <span class="text-gray-400 text-lg tracking-wide"
                                        >Insert cash bills into the machine</span
                                    >
                                </div>
                            </div>
                        </Button>
                        <Button
                            class="group flex-1 h-42 p-10 rounded-2xl border-2 border-white/20 transition-all duration-300 flex items-center justify-start gap-6 text-left hover:border-white/60 hover:shadow-[0_0_20px_rgba(255,255,255,0.3)]"
                        >
                            <div
                                class="rounded-full p-4 border border-white/30 text-white transition-colors duration-300 group-hover:bg-white/15 group-hover:border-white/60"
                            >
                                <QrCode class="size-10" />
                            </div>
                            <div class="space-y-3">
                                <span class="text-3xl leading-tight"
                                    >Pay via Paymongo
                                </span>
                                <br />
                                <span class="text-gray-400 text-lg tracking-wide"
                                    >Scan qr code
                                </span>
                            </div>
                        </Button>
                    </div>
                    <div
                        v-if="student.current_balance > 0"
                        class="flex flex-col items-center justify-center mt-20"
                    >
                        <p class="text-gray-300 text-lg font-sembold tracking-wider">
                            This kiosk is monitored for security. Please do not leave
                            during a transaction
                        </p>
                    </div>

                    <div
                        v-else
                        class="flex flex-col items-center justify-center p-10 mx-10 bg-zinc-900/50 rounded-2xl border border-dashed border-zinc-700"
                    >
                        <CheckCircle class="size-13 text-emerald-500 mb-4" />
                        <h3 class="text-2xl font-bold text-white">
                            {{ student.description }} Settled
                        </h3>
                        <p class="text-zinc-400 text-lg text-center">
                            There are no pending charges for your
                            <b>{{ student.description }}</b
                            >.
                        </p>
                    </div>
                </div>
            </main>
            <Footer />
        </div>
    </KioskLayout>
</template>

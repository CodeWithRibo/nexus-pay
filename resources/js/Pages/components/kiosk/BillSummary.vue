<script setup>
import { Label } from "@/components/ui/label/index.js";
import { Button } from "@/components/ui/button/index.js";
import { Switch } from "@/components/ui/switch/index.js";
import { BanknoteArrowUp, QrCode, CheckCircle } from "lucide-vue-next";
import { router } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const isDisabled = ref(false);
const useOverpayment = ref(false);
const paymongoMethod = ref("qrph");

const props = defineProps({
    student: {
        type: Object,
        required: true,
    },
});

if (Number(props.student.current_balance) === 0) {
    isDisabled.value = true;
}

const currentBalance = computed(() => {
    const balance = Number(props.student.current_balance);
    const overpayment = Number(props.student.over_payment || 0);

    if (useOverpayment.value && overpayment > 0) {
        return Math.max(balance - overpayment, 0);
    }

    return balance;
});

const appliedOverpayment = computed(() => {
    const balance = Number(props.student.current_balance);
    const overpayment = Number(props.student.over_payment || 0);

    if (useOverpayment.value && overpayment > 0) {
        return Math.min(balance, overpayment);
    }

    return 0;
});

const formattedCurrency = computed(() => {
    return new Intl.NumberFormat("en-PH", {
        style: "currency",
        currency: "PHP",
    }).format(currentBalance.value);
});

const formattedOriginalBalance = new Intl.NumberFormat("en-PH", {
    style: "currency",
    currency: "PHP",
}).format(Number(props.student.current_balance));

const formattedOverpayment = new Intl.NumberFormat("en-PH", {
    style: "currency",
    currency: "PHP",
}).format(Number(props.student.over_payment || 0));

const initiatePayment = () => {
    router.post(route("kiosk.tuition-fee.initiate-payment"), {
        use_overpayment: useOverpayment.value,
    });
};

const initiatePaymongoPayment = () => {
    router.post(route("kiosk.paymongo.initiate"), {
        context: "tuition",
        use_overpayment: useOverpayment.value,
        paymongo_method: paymongoMethod.value,
    });
};
</script>

<template>
    <div class="flex flex-1 w-full min-h-0 overflow-hidden">
        <!--Aside-->
        <div class="hidden md:flex flex-col w-120 border border-white/20">
            <div class="flex flex-col flex-1 overflow-y-auto">
                <nav
                    class="flex-1 pl-7 bg-[#FFFFFF0D] space-y-10"
                    :class="student.over_payment > 0 ? 'pt-7' : 'pt-15'"
                >
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
                    <div class="flex flex-col gap-3 mb-2">
                        <div
                            v-if="Number(student.over_payment) > 0"
                            class="bg-[#FFFFFF0D] rounded-lg p-5 w-92 text-white space-y-4 border border-white/10"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <span
                                        class="text-gray-400 text-sm uppercase tracking-wider"
                                        >Available Overpayment</span
                                    >
                                    <h3
                                        class="text-2xl font-bold text-emerald-400"
                                    >
                                        {{ formattedOverpayment }}
                                    </h3>
                                </div>
                            </div>
                            <div
                                class="flex items-center justify-between pt-2 border-t border-white/10"
                            >
                                <Label
                                    for="use-overpayment"
                                    class="text-base text-white cursor-pointer"
                                >
                                    Use Overpayment
                                </Label>
                                <Switch
                                    id="use-overpayment"
                                    v-model:checked="useOverpayment"
                                    :disabled="isDisabled"
                                />
                            </div>
                        </div>
                        <div
                            class="rounded-lg p-5 w-92 text-white space-y-5"
                            :class="
                                useOverpayment && appliedOverpayment > 0
                                    ? 'bg-linear-to-br from-emerald-600/20 to-emerald-800/20 border-2 border-emerald-500/50 shadow-lg shadow-emerald-500/20'
                                    : 'bg-[#FFFFFF14]'
                            "
                        >
                            <span class="text-gray-400 text-lg">
                                {{
                                    useOverpayment && appliedOverpayment > 0
                                        ? "New Balance"
                                        : "Current Balance"
                                }}
                            </span>
                            <div>
                                <h1
                                    class="text-5xl font-bold transition-all duration-300"
                                    :class="
                                        useOverpayment && appliedOverpayment > 0
                                            ? 'text-emerald-300'
                                            : ''
                                    "
                                >
                                    {{ formattedCurrency }}
                                </h1>
                                <p
                                    v-if="
                                        useOverpayment && appliedOverpayment > 0
                                    "
                                    class="text-gray-400 text-sm mt-2 line-through"
                                >
                                    Original:
                                    {{ formattedOriginalBalance }}
                                </p>
                            </div>
                        </div>
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
                    :disabled="isDisabled"
                    @click="initiatePaymongoPayment"
                    :class="{
                        'cursor-not-allowed': isDisabled,
                        'hover:border-white/60 hover:shadow-[0_0_20px_rgba(255,255,255,0.3)]':
                            !isDisabled,
                    }"
                    class="group flex-1 h-42 p-10 rounded-2xl border-2 border-white/20 transition-all duration-300 flex items-center justify-start gap-6 text-left"
                >
                    <div
                        :class="{
                            'group-hover:bg-white/15 group-hover:border-white/60':
                                !isDisabled,
                        }"
                        class="rounded-full p-4 border border-white/30 text-white transition-colors duration-300"
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
                        <div class="pt-2">
                            <select
                                v-model="paymongoMethod"
                                @click.stop
                                class="bg-[#0f0f0f] border border-white/20 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-white/40"
                            >
                                <option value="qrph">
                                    QR Ph (GCash / Maya)
                                </option>
                                <option value="gcash">GCash</option>
                                <option value="paymaya">Maya</option>
                            </select>
                        </div>
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
    </div>
</template>

<script setup>
import { Button } from "@/components/ui/button/index.js";
import { LogIn, LogOut, ArrowLeft } from "lucide-vue-next";
import { ref } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import LeavingModal from "@/components/LeavingModal.vue";
import LogoutModal from "@/components/LogoutModal.vue";
import CancelTransactionModal from "@/components/CancelTransactionModal.vue";
import { toast } from "vue-sonner";

const props = defineProps({
    isLocked: {
        type: Boolean,
        required: false,
    },
    insertedAmount: {
        type: Number,
        required: false,
        default: 0,
    },
});

const page = usePage();

const isAuth = page.props.auth.user !== null;

const toLogin = () => {
    router.visit(route("login"));
};

const showBackBtn = ref(false);
const showLogoutBtn = ref(false);
const showCancelTransactionBtn = ref(false);
const showLeavingModal = ref(false);
const showLogoutModal = ref(false);
const showCancelTransactionModal = ref(false);

const currentRouteName = route().current();

if (
    currentRouteName === "kiosk.service-selection" ||
    currentRouteName === "kiosk.tuition-fee.receipt" ||
    currentRouteName === "kiosk.receipt"
) {
    showLogoutBtn.value = true;
}
if (currentRouteName === "kiosk.outstanding-balance") {
    showLogoutBtn.value = true;
    showBackBtn.value = true;
}
if (
    currentRouteName === "kiosk.tuition-fee.payment-method" ||
    currentRouteName === "kiosk.tuition-fee.cash-insertion" ||
    currentRouteName === "kiosk.payment-method" ||
    currentRouteName === "kiosk.cash-insertion" ||
    currentRouteName === "kiosk.paymongo.checkout"
) {
    showCancelTransactionBtn.value = true;
}

const backPage = () => {
    showLeavingModal.value = true;
};

const logoutModal = () => {
    showLogoutModal.value = true;
};

const cancelTransactionModal = () => {
    showCancelTransactionModal.value = true;
};

const handleLogout = () => {
    if (currentRouteName === "kiosk.tuition-fee.cash-insertion") {
        return router.post(route("login.destroy.with.transaction"));
    }

    router.post(route("login.destroy"));
};

const handleGoBack = () => {
    router.visit(route("kiosk.service-selection"));
};

const handleBillSummary = () => {
    if (
        currentRouteName === "kiosk.tuition-fee.cash-insertion" ||
        currentRouteName === "kiosk.cash-insertion" ||
        currentRouteName === "kiosk.paymongo.checkout"
    ) {
        toast.success("Cancelled Successfully", {
            description: "Transaction cancelled successfully.",
            duration: 2000,
            position: "top-center",
        });

        setTimeout(() => {
            router.post(route("remove-transaction"));
        }, 800);
        return;
    }
    router.visit(route("kiosk.outstanding-balance"));
};
</script>

<template>
    <header class="p-4 dark:bg-gray-100 border border-white/20">
        <div class="px-10 flex justify-between items-center h-16 mx-auto">
            <Button
                @click="backPage"
                v-if="showBackBtn"
                class="text-2xl bg-transparent hover:bg-transparent py-6 w-25 font-bold tracking-wider"
            >
                <ArrowLeft class="size-8" />
                Back Page
            </Button>
            <div aria-label="Logo" class="flex items-center p-2">
                <img
                    :src="'/storage/nexus_logo.png'"
                    class="w-18 h-15"
                    alt="nexus_logo"
                />
                <p class="tracking-widest uppercase font-bold">Nexus Pay</p>
            </div>

            <div class="items-center shrink-0 hidden lg:flex gap-5">
                <Button
                    @click="toLogin"
                    v-if="!isAuth"
                    class="text-xl bg-white/20 hover:bg-white/20 py-6 w-full font-bold tracking-wider"
                >
                    <LogIn class="size-5 text-white" />
                    Login
                </Button>

                <Button
                    v-if="showLogoutBtn && isAuth"
                    @click="logoutModal"
                    class="text-2xl bg-transparent hover:bg-transparent py-6 w-25 font-bold tracking-wider"
                >
                    <LogOut class="size-8" />
                    Logout
                </Button>
                <Button
                    v-if="showCancelTransactionBtn"
                    @click="cancelTransactionModal"
                    class="text-xl bg-white/20 hover:bg-white/20 py-6 w-full font-bold tracking-wider"
                >
                    <LogOut class="size-5 text-white" />
                    Cancel Transaction
                </Button>
            </div>
        </div>
    </header>

    <LeavingModal
        v-model:open="showLeavingModal"
        @logout="handleLogout"
        @goBack="handleGoBack"
    />

    <LogoutModal v-model:open="showLogoutModal" @logout="handleLogout" />

    <CancelTransactionModal
        v-model:open="showCancelTransactionModal"
        @billSummary="handleBillSummary"
        @logout="handleLogout"
        :isLocked="isLocked"
        :insertedAmount="insertedAmount"
    />
</template>

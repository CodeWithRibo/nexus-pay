<script setup>
import { X, CircleAlert } from "lucide-vue-next";
import {
    AlertDialog,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from "@/components/ui/alert-dialog";
import { Button } from "@/components/ui/button/index.js";
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

const currentRouteName = route().current();
const handleReceiptRoute = ref(false);
const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:open", "logout", "goBack"]);

if (
    currentRouteName === "kiosk.tuition-fee.receipt" ||
    currentRouteName === "kiosk.receipt"
) {
    handleReceiptRoute.value = true;
}

const handleLogout = () => {
    emit("logout");
    emit("update:open", false);
};

const handleGoBack = () => {
    emit("goBack");
    emit("update:open", false);
};

const handleClose = () => {
    emit("update:open", false);
};

const toHome = () => {
    router.visit(route("kiosk.landing-screen"));
};
</script>

<template>
    <AlertDialog :open="open" @update:open="(val) => emit('update:open', val)">
        <AlertDialogContent
            v-if="open"
            forceMount
            class="bg-[#1a1a1a] border-white/10 rounded-3xl p-8 max-w-lg"
            @interactOutside="handleClose"
        >
            <button
                @click="handleClose"
                class="absolute top-6 right-6 rounded-full p-2 opacity-50 transition-all hover:opacity-100 hover:bg-white/10 text-white focus:outline-none z-10"
            >
                <X class="size-6" />
                <span class="sr-only">Close</span>
            </button>

            <AlertDialogHeader class="space-y-4">
                <div
                    class="mx-auto bg-amber-500/10 p-4 rounded-full w-fit mb-2"
                >
                    <CircleAlert class="size-10 text-amber-500" />
                </div>
                <AlertDialogTitle
                    class="text-3xl font-bold text-white text-center"
                >
                    Leaving so soon?
                </AlertDialogTitle>
                <AlertDialogDescription
                    class="text-xl text-gray-400 text-center leading-relaxed"
                >
                    Your session is still active. To protect your data, please
                    logout completely if you are finished.
                </AlertDialogDescription>
            </AlertDialogHeader>

            <AlertDialogFooter class="flex flex-col gap-3 mt-6 justify-center">
                <template v-if="handleReceiptRoute">
                    <AlertDialogCancel
                        @click="toHome"
                        class="py-7 text-xl border-white/20 bg-white/5 text-white hover:bg-white/10 hover:text-white rounded-2xl flex-1 cursor-pointer transition-all active:scale-[0.98]"
                    >
                        Back to Home
                    </AlertDialogCancel>
                </template>
                <template v-else>
                    <AlertDialogCancel
                        @click="handleGoBack"
                        class="py-7 text-xl border-white/20 bg-white/5 text-white hover:bg-white/10 hover:text-white rounded-2xl flex-1 cursor-pointer transition-all active:scale-[0.98]"
                    >
                        Go Back
                    </AlertDialogCancel>
                </template>
                <Button
                    variant="destructive"
                    @click="handleLogout"
                    class="py-7 text-xl font-bold rounded-2xl flex-1 hover:opacity-90 cursor-pointer shadow-lg shadow-red-900/20 transition-all active:scale-[0.98]"
                >
                    Logout Now
                </Button>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
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

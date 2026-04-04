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

if (currentRouteName === "kiosk.tuition-fee.receipt") {
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
        <AlertDialogContent @interactOutside="handleClose">
            <button
                @click="handleClose"
                class="absolute top-4 right-4 rounded-sm opacity-70 ring-offset-background transition-opacity hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:pointer-events-none z-10"
            >
                <X class="size-5" />
                <span class="sr-only">Close</span>
            </button>

            <AlertDialogHeader>
                <AlertDialogTitle class="text-2xl flex items-center gap-3">
                    <CircleAlert class="size-7" />
                    <p>Leaving so soon?</p>
                </AlertDialogTitle>
                <AlertDialogDescription class="text-lg">
                    Your session is still active. To protect your data, please
                    logout completely if you are finished.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <template v-if="handleReceiptRoute">
                    <AlertDialogCancel
                        @click="toHome"
                        class="py-5 text-lg cursor-pointer"
                    >
                        Back to Home
                    </AlertDialogCancel>
                </template>
                <template v-else>
                    <AlertDialogCancel
                        @click="handleGoBack"
                        class="py-5 text-lg cursor-pointer"
                    >
                        Go Back
                    </AlertDialogCancel>
                </template>
                <Button
                    variant="destructive"
                    @click="handleLogout"
                    class="py-5 text-lg hover:opacity-75 cursor-pointer"
                >
                    Logout Now
                </Button>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>

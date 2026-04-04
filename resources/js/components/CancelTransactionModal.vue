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

const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:open", "logout", "billSummary"]);

const handleLogout = () => {
    emit("logout");
    emit("update:open", false);
};

const handleClose = () => {
    emit("update:open", false);
};

const handleBillSummary = () => {
    emit("billSummary");
}
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
                    <p>Cancel Transaction?</p>
                </AlertDialogTitle>
                <AlertDialogDescription class="text-lg">
                    Do you want to go back to your bill summary or log out to keep your account safe?
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel
                    @click="handleBillSummary"
                    class="py-5 text-lg cursor-pointer"
                >
                    Back to Bill Summary
                </AlertDialogCancel>
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

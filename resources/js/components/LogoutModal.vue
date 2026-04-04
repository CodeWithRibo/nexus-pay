<script setup>
import { X, ShieldCheck } from "lucide-vue-next";
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

const emit = defineEmits(["update:open", "logout", "goBack"]);

const handleLogout = () => {
    emit("logout");
    emit("update:open", false);
};

const handleClose = () => {
    emit("update:open", false);
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
                    <ShieldCheck class="size-7" />
                    <p>Secure Logout</p>
                </AlertDialogTitle>
                <AlertDialogDescription class="text-lg">
                    To prevent unauthorized access to your student account,
                    please confirm you are finished using this kiosk
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel
                    @click="handleClose"
                    class="py-5 text-lg cursor-pointer"
                >
                    Cancel
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

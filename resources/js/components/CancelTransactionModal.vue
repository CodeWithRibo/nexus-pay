<script setup>
import { X, CircleAlert, ShieldCheck, PlayCircle } from "lucide-vue-next";
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
    isLocked: {
        type: Boolean,
        required: true,
    },
    insertedAmount: {
        type: String,
        required: true,
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
                    <ShieldCheck
                        v-if="isLocked"
                        class="size-7 text-amber-500"
                    />
                    <CircleAlert v-else class="size-7" />

                    <p>
                        {{
                            isLocked
                                ? "Transaction Locked"
                                : "Cancel Transaction?"
                        }}
                    </p>
                </AlertDialogTitle>
                <AlertDialogDescription class="text-lg">
                    <template v-if="isLocked">
                        Cash has been detected. To secure your
                        <span class="font-bold">₱{{ insertedAmount }}</span
                        >, please complete the insertion process. You cannot
                        exit until a receipt is generated.
                    </template>

                    <template v-else>
                        Do you want to go back to your bill summary or log out
                        to keep your account safe?
                    </template>
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <template v-if="isLocked">
                    <AlertDialogAction
                        class="w-full py-6 text-lg font-bold tracking-wide transition-all duration-200 bg-amber-500 hover:bg-amber-600 text-white rounded-xl shadow-lg shadow-emerald-900/20 active:scale-[0.98] cursor-pointer"
                    >
                        <div class="flex items-center justify-center gap-2">
                            <PlayCircle class="size-6" />
                            <span @click="handleClose" class="text-xl">
                                Continue Inserting Cash
                            </span>
                        </div>
                    </AlertDialogAction>
                </template>

                <template v-else>
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
                </template>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>

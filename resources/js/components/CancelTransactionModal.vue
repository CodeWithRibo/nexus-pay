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
                        v-if="isLocked"
                        class="mx-auto bg-amber-500/10 p-4 rounded-full w-fit mb-2"
                    >
                        <ShieldCheck class="size-10 text-amber-500" />
                    </div>
                    <div
                        v-else
                        class="mx-auto bg-red-500/10 p-4 rounded-full w-fit mb-2"
                    >
                        <CircleAlert class="size-10 text-red-500" />
                    </div>

                    <AlertDialogTitle
                        class="text-3xl font-bold text-white text-center"
                    >
                        {{
                            isLocked
                                ? "Transaction Locked"
                                : "Cancel Transaction?"
                        }}
                    </AlertDialogTitle>
                    <AlertDialogDescription
                        class="text-xl text-gray-400 text-center leading-relaxed"
                    >
                        <template v-if="isLocked">
                            Cash has been detected. To secure your
                            <span class="font-bold text-white"
                                >₱{{ insertedAmount }}</span
                            >, please complete the insertion process. You
                            cannot exit until a receipt is generated.
                        </template>

                        <template v-else>
                            Do you want to go back to your bill summary or log
                            out to keep your account safe?
                        </template>
                    </AlertDialogDescription>
                </AlertDialogHeader>

                <AlertDialogFooter
                    class="flex flex-col gap-3 mt-6 justify-center"
                >
                    <template v-if="isLocked">
                        <Button
                            @click="handleClose"
                            class="w-full py-8 text-xl font-bold tracking-wide transition-all duration-200 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl shadow-lg shadow-amber-900/20 active:scale-[0.98] cursor-pointer"
                        >
                            <div class="flex items-center justify-center gap-3">
                                <PlayCircle class="size-7" />
                                <span> Continue Inserting Cash </span>
                            </div>
                        </Button>
                    </template>

                    <template v-else>
                        <AlertDialogCancel
                            @click="handleBillSummary"
                            class="py-7 text-xl border-white/20 bg-white/5 text-white hover:bg-white/10 hover:text-white rounded-2xl flex-1 cursor-pointer transition-all active:scale-[0.98]"
                        >
                            Back to Bill Summary
                        </AlertDialogCancel>

                        <Button
                            variant="destructive"
                            @click="handleLogout"
                            class="py-7 text-xl font-bold rounded-2xl flex-1 hover:opacity-90 cursor-pointer shadow-lg shadow-red-900/20 transition-all active:scale-[0.98]"
                        >
                            Logout Now
                        </Button>
                    </template>
                </AlertDialogFooter>
            </AlertDialogContent>
    </AlertDialog>
</template>

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
                        <ShieldCheck class="size-10 text-amber-500" />
                    </div>
                    <AlertDialogTitle
                        class="text-3xl font-bold text-white text-center"
                    >
                        Secure Logout
                    </AlertDialogTitle>
                    <AlertDialogDescription
                        class="text-xl text-gray-400 text-center leading-relaxed"
                    >
                        To prevent unauthorized access to your student account,
                        please confirm you are finished using this kiosk
                    </AlertDialogDescription>
                </AlertDialogHeader>

                <AlertDialogFooter
                    class="flex flex-col gap-3 mt-6 justify-center"
                >
                    <AlertDialogCancel
                        @click="handleClose"
                        class="py-7 text-xl border-white/20 bg-white/5 text-white hover:bg-white/10 hover:text-white rounded-2xl flex-1 cursor-pointer transition-all active:scale-[0.98]"
                    >
                        Cancel
                    </AlertDialogCancel>
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

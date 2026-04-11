<script setup>
import { router, usePage } from "@inertiajs/vue3";
import { Clock } from "lucide-vue-next";
import { computed, onMounted, onUnmounted, ref } from "vue";
import {
    AlertDialog,
    AlertDialogHeader,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogTitle,
} from "@/components/ui/alert-dialog/index.js";
import { Button } from "@/components/ui/button/index.js";

const page = usePage();
const isIdleModalOpen = ref(false);
const countdown = ref(10);
let idleTimer = null;
let countdownInterval = null;

const IDLE_LIMIT = 1500000000;

const resetTimers = () => {
    if (isIdleModalOpen.value) return;

    clearInterval(idleTimer);
    clearInterval(countdownInterval);

    idleTimer = setTimeout(() => {
        showIdleWarning();
    }, IDLE_LIMIT);
};

const showIdleWarning = () => {
    isIdleModalOpen.value = true;
    countdown.value = 15;

    countdownInterval = setInterval(() => {
        countdown.value--;

        if (countdown.value <= 0) {
            forceLogout();
        }
    }, 1000);
};

const forceLogout = () => {
    clearInterval(countdownInterval);
    isIdleModalOpen.value = false;

    router.post(route("login.destroy"), {}, { replace: true });
};

const iAmStillHere = () => {
    isIdleModalOpen.value = false;
    clearInterval(countdownInterval);
    resetTimers();
};

onMounted(() => {
    const events = ["mousemove", "keydown", "touchstart", "click"];
    events.forEach((event) => window.addEventListener(event, resetTimers));
    resetTimers();
});

onUnmounted(() => {
    const events = ["mousemove", "keydown", "touchstart", "click"];

    events.forEach((event) => window.removeEventListener(event, resetTimers));
    clearTimeout(idleTimer);
    clearInterval(countdownInterval);
});

const emit = defineEmits(["update:open"]);

const handleClose = () => {
    emit("update:open", false);
};
</script>

<template>
    <AlertDialog
        :open="isIdleModalOpen"
        @update:open="(val) => emit('update:open', val)"
    >
        <AlertDialogContent
            @interactOutside="handleClose"
            class="bg-zinc-950 border-white-500/40 max-w-md rounded-3xl shadow-[0_0_20px_rgba(255,255,255,0.3)]"
        >
            <AlertDialogHeader class="flex flex-col items-center text-center">
                <div class="p-4 bg-white-500/10 rounded-full mb-4">
                    <Clock class="size-15 text-white animate-pulse" />
                </div>
                <AlertDialogTitle
                    class="text-3xl font-black tracking-tight text-white"
                >
                    Still Working?
                </AlertDialogTitle>
                <AlertDialogDescription class="text-zinc-400 text-lg mt-2">
                    For your security, your session will auto-logout

                    <div
                        class="mt-6 text-6xl text-center font-mono font-black text-gray-200 tracking-tighter"
                    >
                        00:{{ countdown < 10 ? "0" + countdown : countdown }}
                    </div>
                </AlertDialogDescription>
            </AlertDialogHeader>

            <AlertDialogFooter class="mt-8 flex-col sm:flex-col gap-3">
                <Button
                    @click="iAmStillHere"
                    class="w-full py-7 text-xl font-bold bg-white text-black hover:bg-zinc-200 rounded-2xl transition-all active:scale-95"
                >
                    Yes, I'm still here!
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

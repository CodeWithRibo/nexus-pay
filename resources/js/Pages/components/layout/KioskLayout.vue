<script setup>
import { ref, watch, onMounted, nextTick } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import "vue-sonner/style.css";
import { Toaster } from "@/components/ui/sonner";
import { toast } from "vue-sonner";

const isLoading = ref(false);
const page = usePage();

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) {
            nextTick(() => {
                setTimeout(() => {
                    toast.success(flash.success, {
                        duration: 3000,
                    });
                }, 100);
            });
        }
        if (flash?.error) {
            nextTick(() => {
                setTimeout(() => {
                    toast.error(flash.error, {
                        duration: 4000,
                    });
                }, 100);
            });
        }
    },
    { deep: true, immediate: true },
);

router.on("start", (event) => {
    isLoading.value = true;

    isLoading.value = event.detail.visit.url.pathname !== "/login";
});
router.on("finish", () => {
    isLoading.value = false;
});
</script>

<template>
    <div class="background">
        <div
            v-if="isLoading"
            class="fixed inset-0 bg-black flex flex-col items-center justify-center z-40"
        >
            <div
                class="w-16 h-16 border-4 border-white border-t-transparent rounded-full animate-spin"
            ></div>
            <p class="mt-4 text-white uppercase tracking-widest font-bold">
                Loading please wait...
            </p>
        </div>
        <slot />
        <Toaster
            richColors
            class="pointer-events-auto"
            position="top-center"
            :toast-options="{
                classes: {
                    style: { zIndex: 9999 },
                    toast: 'bg-slate-900 text-white border-slate-800 rounded-xl shadow-lg',
                    title: 'text-lg font-bold',
                    description: 'text-slate-400',
                },
            }"
        />
    </div>
</template>

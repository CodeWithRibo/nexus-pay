<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

const isLoading = ref(false);

router.on("start", () => {
    isLoading.value = true;
});
router.on("finish", () => {
    isLoading.value = false;
});
</script>

<template>
    <div class="background">
        <div
            v-if="isLoading"
            class="fixed inset-0 bg-black flex flex-col items-center justify-center z-50"
        >
            <div
                class="w-16 h-16 border-4 border-white border-t-transparent rounded-full animate-spin"
            ></div>
            <p class="mt-4 text-white uppercase tracking-widest font-bold">
                Loading please wait...
            </p>
        </div>
        <slot />
    </div>
</template>

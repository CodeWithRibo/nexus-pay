<script setup>
import { Button } from "@/components/ui/button/index.js";
import { ArrowRight, Shield, Banknote, BadgeCheck } from "lucide-vue-next";
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

const features = ref([
    { name: "Encrypted", icon: Shield },
    { name: "Multiple Method", icon: Banknote },
    { name: "Instant Receipt", icon: BadgeCheck },
]);
const toServiceSelection = () => {
    router.visit(route("kiosk.service-selection"));
};
</script>

<template>
    <div
        class="flex flex-col items-center space-y-32 justify-center mx-auto max-w-4xl"
    >
        <div class="flex flex-col items-center">
            <img
                class="h-24 w-24 rounded-full opacity-90 drop-shadow-[0_0_32px_rgba(255,255,255,0.12)] md:h-52 md:w-52"
                :src="'/storage/logo.png'"
                alt=""
            />
            <div class="text-center mt-20 space-y-5">
                <h1 class="italic text-6xl font-bold">
                    <span>NEXUS</span>
                    <span class="text-[#ADB5BD]">PAY</span>
                </h1>
                <span class="tracking-[0.7em]">MODERN SCHOOL FEE SOLUTION</span>
            </div>
        </div>
        <div class="flex items-center flex-col relative">
            <div
                class="absolute inset-0 bg-white/10 animate-ping opacity-25 z-0"
            ></div>
            <div class="z-1">
                <Button
                    @click="toServiceSelection"
                    class="bg-white fill-white text-black font-bold text-3xl hover:text-4xl py-12 px-32 hover:py-12 hover:px-33.75 transition-all duration-300ms hover:bg-white hover:drop-shadow-lg hover:drop-shadow-white"
                >
                    TAP TO START
                    <span> <ArrowRight class="size-7" /></span>
                </Button>
            </div>
            <div
                class="flex flex-row items-center justify-center space-x-5 mt-10"
            >
                <div
                    class="flex items-center gap-2 text-gray-400"
                    v-for="(feature, index) in features"
                    :key="index"
                >
                    <component :is="feature.icon" class="size-5" />
                    <p class="text-sm uppercase tracking-[0.3em]">
                        {{ feature.name }}
                    </p>
                </div>
            </div>
            <div class="mt-5 text-center">
                <span class="text-gray-500">Nexus Pay Kiosk v1.0</span>
            </div>
        </div>
    </div>
</template>

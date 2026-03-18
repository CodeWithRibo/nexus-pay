<script setup>
import { Button } from "@/components/ui/button/index.js";
import { LogIn, CircleQuestionMarkIcon } from "lucide-vue-next";
</script>

<template>
    <header class="p-4 dark:bg-gray-100 border border-white/20">
        <div class="px-5 flex justify-between h-16 mx-auto">
            <a
                href="#"
                aria-label="Back to homepage"
                class="flex items-center p-2"
            >
                <img
                    :src="'/storage/nexus_logo.png'"
                    class="w-18 h-15"
                    alt="nexus_logo"
                />
                <p class="tracking-widest uppercase font-bold">Nexus Pay</p>
            </a>
            <div class="items-center shrink-0 hidden lg:flex gap-5">
                <Button
                    class="text-xl bg-white/20 hover:bg-white/20 py-6 w-48 font-semibold"
                >
                    <CircleQuestionMarkIcon class="size-5 text-white" />
                    Need help?
                </Button>
                <Button
                    class="text-xl bg-white/20 hover:bg-white/20 py-6 w-35 font-bold"
                >
                    <LogIn class="size-5 text-white" />
                    Cancel
                </Button>
            </div>
        </div>
    </header>
</template>

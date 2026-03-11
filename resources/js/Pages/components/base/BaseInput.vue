<script setup>
import { Input } from "@/components/ui/input/index.js";

const model = defineModel();

defineProps({
    label: String,
    type: {
        type: String,
        default: "text",
    },
    error: String,
    placeholder: String,
});
</script>

<template>
    <Label>{{ label }}</Label>
    <Input
        :class="{ 'border-red-500': error }"
        :type="type"
        v-model="model"
        :placeholder="placeholder"
    />
    <p v-if="error" class="text-sm font-medium text-red-500 italic">
        {{ error }}
    </p>
</template>

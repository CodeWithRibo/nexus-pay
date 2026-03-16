<script setup>
import { Button } from "@/components/ui/button/index.js";
import { LogIn } from "lucide-vue-next";
import {
    Field,
    FieldError,
    FieldGroup,
    FieldLabel,
    FieldSet,
} from "@/components/ui/field/index.js";
import { Input } from "@/components/ui/input/index.js";
import "kioskboard/dist/kioskboard-2.3.0.min.css";
import { computed } from "vue";

const emit = defineEmits(["submit"]);

const props = defineProps({ form: Object });
const form = props.form;

const handleSubmit = () => {
    emit("submit");
};

const isDisabled = computed(() => {
    return !form.login || !form.password;
});
</script>

<template>
    <div class="fixed inset-0 flex items-center justify-center bg-[#121212]">
        <FieldSet
            class="w-full mx-auto max-w-xl px-4 py-6 sm:px-0 overflow-y-auto"
        >
            <h1 class="text-center text-5xl font-bold mb-10">Student Login</h1>
            <FieldGroup>
                <form @submit.prevent="handleSubmit">
                    <Field
                        class="rounded-xl bg-[#FFFFFF0D] border-white/20 border-2 p-10 transition-all duration-300"
                    >
                        <FieldLabel
                            for="stud_id_or_email"
                            class="text-lg font-bold"
                        >
                            Student ID / Email</FieldLabel
                        >
                        <Input
                            type="text"
                            class="h-16 md:text-xl bg-white/10 border-white/20 border focus-visible:border-ring focus-visible:ring-white focus-visible:ring-[1px]"
                            name="login"
                            v-model="form.login"
                            placeholder="Enter your Student ID"
                        />
                        <FieldError class="text-lg">
                            {{ form.errors.login }}</FieldError
                        >

                        <FieldLabel for="password" class="text-lg font-bold">
                            Password
                        </FieldLabel>
                        <Input
                            type="password"
                            name="password"
                            class="h-16 md:text-xl bg-white/10 border-white/20 border focus-visible:border-ring focus-visible:ring-white focus-visible:ring-[1px]"
                            v-model="form.password"
                            placeholder="******"
                        />
                        <FieldError class="text-lg">
                            {{ form.errors.password }}</FieldError
                        >
                        <div class="text-center">
                            <Button
                                type="submit"
                                class="h-20 w-full text-2xl font-bold space-x-1 fill-white bg-white hover:bg-white text-[#121212] transition-all duration-300"
                                :class="{
                                    'cursor-not-allowed': isDisabled,
                                    'hover:opacity-90 hover:drop-shadow-[0_0_32px_rgba(255,255,255,0.12)]':
                                        !isDisabled,
                                }"
                                :disabled="isDisabled"
                            >
                                <LogIn class="size-7" />
                                <p>Login</p>
                            </Button>
                        </div>
                    </Field>
                </form>
            </FieldGroup>
        </FieldSet>
    </div>
</template>

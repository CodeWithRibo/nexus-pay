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

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    userType: {
        type: String,
        default: "student",
    },
});
const form = props.form;

const isAdminLogin = computed(() => props.userType === "admin");
const loginTitle = computed(() =>
    isAdminLogin.value ? "Admin Login" : "Student Login",
);
const loginLabel = computed(() =>
    isAdminLogin.value ? "Email" : "Student ID / Email",
);
const loginPlaceholder = computed(() =>
    isAdminLogin.value ? "Enter your Email" : "Enter your Student ID or Email",
);
const containerClass = computed(() =>
    isAdminLogin.value
        ? "fixed inset-0 flex items-center justify-center bg-white"
        : "fixed inset-0 flex items-center justify-center bg-[#121212]",
);
const titleClass = computed(() =>
    isAdminLogin.value
        ? "text-center text-5xl font-bold mb-10 text-[#121212]"
        : "text-center text-5xl font-bold mb-10",
);
const cardClass = computed(() =>
    isAdminLogin.value
        ? "rounded-xl bg-white border border-gray-200 p-10 transition-all duration-300 shadow-sm"
        : "rounded-xl bg-[#FFFFFF0D] border-white/20 border-2 p-10 transition-all duration-300",
);
const labelClass = computed(() =>
    isAdminLogin.value
        ? "text-lg font-bold text-[#121212]"
        : "text-lg font-bold",
);
const inputClass = computed(() =>
    isAdminLogin.value
        ? "h-16 md:text-xl bg-white text-[#121212] border-gray-300 border focus-visible:border-[#121212] focus-visible:ring-gray-300 focus-visible:ring-[1px] placeholder:text-gray-400"
        : "h-16 md:text-xl bg-white/10 border-white/20 border focus-visible:border-ring focus-visible:ring-white focus-visible:ring-[1px]",
);
const buttonClass = computed(() =>
    isAdminLogin.value
        ? "h-20 w-full text-2xl font-bold space-x-1 fill-white bg-[#121212] hover:bg-black text-white transition-all duration-300"
        : "h-20 w-full text-2xl font-bold space-x-1 fill-white bg-white hover:bg-white text-[#121212] transition-all duration-300",
);

const handleSubmit = () => {
    emit("submit");
};

const isDisabled = computed(() => {
    return !form.login || !form.password;
});
</script>

<template>
    <div :class="containerClass">
        <FieldSet
            class="w-full mx-auto max-w-xl px-4 py-6 sm:px-0 overflow-y-auto"
        >
            <h1 :class="titleClass">{{ loginTitle }}</h1>
            <FieldGroup>
                <form @submit.prevent="handleSubmit">
                    <Field :class="cardClass">
                        <FieldLabel for="stud_id_or_email" :class="labelClass">
                            {{ loginLabel }}</FieldLabel
                        >
                        <Input
                            type="text"
                            :class="inputClass"
                            name="login"
                            v-model="form.login"
                            :placeholder="loginPlaceholder"
                        />
                        <FieldError class="text-lg">
                            {{ form.errors.login }}</FieldError
                        >

                        <FieldLabel for="password" :class="labelClass">
                            Password
                        </FieldLabel>
                        <Input
                            type="password"
                            name="password"
                            :class="inputClass"
                            v-model="form.password"
                            placeholder="******"
                        />
                        <FieldError class="text-lg">
                            {{ form.errors.password }}</FieldError
                        >
                        <div class="text-center">
                            <Button
                                type="submit"
                                :class="[
                                    buttonClass,
                                    {
                                        'cursor-not-allowed': isDisabled,
                                    },
                                ]"
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

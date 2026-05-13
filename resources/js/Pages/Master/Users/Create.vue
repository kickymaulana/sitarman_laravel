<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardDescription,
    CardFooter,
} from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
    IconArrowLeft,
    IconDeviceFloppy,
    IconLoader2,
    IconUserPlus,
    IconMail,
    IconBrandWhatsapp,
    IconLock,
    IconShieldCheck,
    IconId,
} from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    roles: Array<{ id: number; name: string }>;
}>();

const form = useForm({
    name: '',
    username: '',
    whatsapp: '',
    email: '',
    role: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('users.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Tambah Pengguna Baru" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-6 bg-slate-50/50 min-h-screen">
        <!-- Header & Back Button -->
        <div class="flex items-center justify-between max-w-3xl mx-auto w-full">
            <Button
                variant="ghost"
                size="sm"
                as-child
                class="-ml-2 text-emerald-700 hover:text-emerald-800 hover:bg-emerald-50"
            >
                <Link :href="route('users.index')">
                    <IconArrowLeft class="mr-2 size-4" />
                    Kembali ke Daftar
                </Link>
            </Button>
        </div>

        <div class="max-w-3xl mx-auto w-full">
            <Card class="border-none shadow-xl ring-1 ring-emerald-100 bg-white/90 backdrop-blur-sm overflow-hidden">
                <!-- Decorative Top Bar -->
                <div class="h-1.5 w-full bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600"></div>

                <CardHeader class="pb-8">
                    <CardTitle class="text-2xl font-black text-slate-800 flex items-center gap-2">
                        <IconUserPlus class="size-6 text-emerald-600" />
                        Registrasi Pengguna
                    </CardTitle>
                    <CardDescription class="text-sm font-medium">
                        Daftarkan akun personil baru untuk akses sistem
                        <span class="text-emerald-700 font-black italic underline decoration-emerald-200">
                            SITARMAN
                        </span>
                    </CardDescription>
                </CardHeader>

                <form @submit.prevent="submit">
                    <CardContent class="grid gap-8">

                        <!-- Row 1: Nama Lengkap -->
                        <div class="grid gap-2.5">
                            <Label for="name" class="text-[11px] font-black text-emerald-700 uppercase tracking-widest ml-1">
                                Nama Lengkap
                            </Label>
                            <div class="relative group">
                                <IconId class="absolute left-3 top-3 size-4 text-emerald-300 group-focus-within:text-emerald-600 transition-colors" />
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    placeholder="Nama Lengkap Karyawan"
                                    class="h-11 pl-10 border-slate-200 focus-visible:ring-emerald-500 bg-slate-50/30"
                                    :class="{ 'border-destructive': form.errors.name }"
                                    required
                                />
                            </div>
                            <p v-if="form.errors.name" class="text-[10px] font-bold text-destructive ml-1">
                                * {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Row 2: Role & Whatsapp -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="grid gap-2.5">
                                <Label for="role" class="text-[11px] font-black text-emerald-700 uppercase tracking-widest ml-1">
                                    Hak Akses (Role)
                                </Label>
                                <div class="relative group">
                                    <IconShieldCheck class="absolute left-3 top-3 size-4 text-emerald-300 group-focus-within:text-emerald-600 z-10 transition-colors" />
                                    <select
                                        id="role"
                                        v-model="form.role"
                                        class="flex h-11 w-full rounded-md border border-slate-200 bg-slate-50/30 pl-10 pr-10 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 appearance-none transition-all shadow-sm font-medium"
                                        :class="{ 'border-destructive': form.errors.role }"
                                        required
                                    >
                                        <option value="" disabled>-- Pilih Hak Akses --</option>
                                        <option v-for="role in roles" :key="role.id" :value="role.name">
                                            {{ role.name.toUpperCase() }}
                                        </option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-emerald-300">
                                        <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" /></svg>
                                    </div>
                                </div>
                                <p v-if="form.errors.role" class="text-[10px] font-bold text-destructive ml-1">
                                    * {{ form.errors.role }}
                                </p>
                            </div>

                            <div class="grid gap-2.5">
                                <Label for="whatsapp" class="text-[11px] font-black text-emerald-700 uppercase tracking-widest ml-1">
                                    WhatsApp
                                </Label>
                                <div class="relative group">
                                    <IconBrandWhatsapp class="absolute left-3 top-3 size-4 text-emerald-300 group-focus-within:text-emerald-600 transition-colors" />
                                    <Input
                                        id="whatsapp"
                                        v-model="form.whatsapp"
                                        type="text"
                                        placeholder="62812345678"
                                        class="h-11 pl-10 border-slate-200 focus-visible:ring-emerald-500 bg-slate-50/30"
                                        :class="{ 'border-destructive': form.errors.whatsapp }"
                                        required
                                    />
                                </div>
                                <p v-if="form.errors.whatsapp" class="text-[10px] font-bold text-destructive ml-1">
                                    * {{ form.errors.whatsapp }}
                                </p>
                            </div>
                        </div>

                        <!-- Row 3: Username & Email -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="grid gap-2.5">
                                <Label for="username" class="text-[11px] font-black text-emerald-700 uppercase tracking-widest ml-1">
                                    Username
                                </Label>
                                <div class="relative group">
                                    <IconUserPlus class="absolute left-3 top-3 size-4 text-emerald-300 group-focus-within:text-emerald-600 transition-colors" />
                                    <Input
                                        id="username"
                                        v-model="form.username"
                                        type="text"
                                        placeholder="ID Karyawan"
                                        class="h-11 pl-10 border-slate-200 focus-visible:ring-emerald-500 bg-slate-50/30"
                                        :class="{ 'border-destructive': form.errors.username }"
                                        required
                                    />
                                </div>
                                <p v-if="form.errors.username" class="text-[10px] font-bold text-destructive ml-1">
                                    * {{ form.errors.username }}
                                </p>
                            </div>

                            <div class="grid gap-2.5">
                                <Label for="email" class="text-[11px] font-black text-emerald-700 uppercase tracking-widest ml-1">
                                    Email
                                </Label>
                                <div class="relative group">
                                    <IconMail class="absolute left-3 top-3 size-4 text-emerald-300 group-focus-within:text-emerald-600 transition-colors" />
                                    <Input
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        placeholder="personil@markdynamics.co.id"
                                        class="h-11 pl-10 border-slate-200 focus-visible:ring-emerald-500 bg-slate-50/30"
                                        :class="{ 'border-destructive': form.errors.email }"
                                        required
                                    />
                                </div>
                                <p v-if="form.errors.email" class="text-[10px] font-bold text-destructive ml-1">
                                    * {{ form.errors.email }}
                                </p>
                            </div>
                        </div>

                        <!-- Row 4: Password -->
                        <div class="pt-6 mt-2 border-t border-dashed border-emerald-100">
                            <div class="flex items-center gap-2 mb-6">
                                <div class="h-4 w-1 bg-emerald-500 rounded-full"></div>
                                <h3 class="text-[11px] font-black text-emerald-800 tracking-widest uppercase">
                                    Keamanan Akun
                                </h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="grid gap-2.5">
                                    <Label for="password" class="text-[10px] font-black text-emerald-700 uppercase tracking-widest ml-1">
                                        Password
                                    </Label>
                                    <div class="relative group">
                                        <IconLock class="absolute left-3 top-3 size-4 text-emerald-300 group-focus-within:text-emerald-600 transition-colors" />
                                        <Input
                                            id="password"
                                            v-model="form.password"
                                            type="password"
                                            placeholder="••••••••"
                                            class="h-11 pl-10 border-slate-200 focus-visible:ring-emerald-500 bg-slate-50/30"
                                            required
                                        />
                                    </div>
                                </div>
                                <div class="grid gap-2.5">
                                    <Label for="password_confirmation" class="text-[10px] font-black text-emerald-700 uppercase tracking-widest ml-1">
                                        Konfirmasi Password
                                    </Label>
                                    <div class="relative group">
                                        <IconLock class="absolute left-3 top-3 size-4 text-emerald-300 group-focus-within:text-emerald-600 transition-colors" />
                                        <Input
                                            id="password_confirmation"
                                            v-model="form.password_confirmation"
                                            type="password"
                                            placeholder="••••••••"
                                            class="h-11 pl-10 border-slate-200 focus-visible:ring-emerald-500 bg-slate-50/30"
                                            required
                                        />
                                    </div>
                                </div>
                            </div>
                            <p v-if="form.errors.password" class="text-[10px] font-bold text-destructive mt-3 ml-1">
                                * {{ form.errors.password }}
                            </p>
                        </div>
                    </CardContent>

                    <CardFooter class="flex justify-end gap-3 border-t bg-emerald-50/30 px-6 py-6 rounded-b-lg mt-4">
                        <Button
                            variant="outline"
                            type="button"
                            as-child
                            :disabled="form.processing"
                            class="h-10 px-6 border-emerald-200 text-emerald-700 hover:bg-emerald-50 uppercase font-black text-[10px] tracking-widest"
                        >
                            <Link :href="route('users.index')">Batal</Link>
                        </Button>
                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="h-10 px-8 bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-200 transition-all active:scale-95 uppercase font-black text-[10px] tracking-widest"
                        >
                            <IconLoader2 v-if="form.processing" class="mr-2 size-4 animate-spin" />
                            <IconDeviceFloppy v-else class="mr-2 size-4" />
                            Daftarkan User
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </div>
</template>

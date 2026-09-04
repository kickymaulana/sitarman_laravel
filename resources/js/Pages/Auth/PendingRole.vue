<script setup lang="ts">
import { Head, useForm } from "@inertiajs/vue3"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from "@/components/ui/card"
import { IconFlask, IconShieldCheck, IconLoader2 } from "@tabler/icons-vue"

defineProps<{
    user: { name: string; nik: string }
    roles: string[]
}>()

const form = useForm({
    role: "",
})

const submit = () => {
    form.post(route("sso.pending.submit"))
}
</script>

<template>
    <Head title="Aktivasi Akun - SITARMAN" />

    <div
        class="relative flex min-h-svh w-full items-center justify-center p-4 md:p-10 bg-cover bg-center bg-no-repeat transition-all duration-500 overflow-hidden"
        style="background-image: url('https://images.unsplash.com/photo-1581093588401-fbb62a02f120?q=80&w=2070&auto=format&fit=crop');"
    >
        <div class="absolute inset-0 bg-gradient-to-tr from-emerald-600/40 via-white/20 to-teal-500/30 backdrop-blur-[2px]"></div>

        <div class="relative z-10 w-full max-w-md">
            <div class="flex flex-col items-center mb-6">
                <div class="p-3 bg-white rounded-2xl shadow-xl border border-emerald-100 mb-3">
                    <IconFlask class="size-8 text-emerald-600" />
                </div>
                <h1 class="text-3xl font-black tracking-tighter text-slate-800 drop-shadow-sm">SITARMAN</h1>
                <p class="text-[10px] font-bold text-emerald-800 uppercase tracking-[0.3em]">Aktivasi Akun</p>
            </div>

            <Card class="border-none shadow-xl ring-1 ring-emerald-100 bg-white/95 backdrop-blur-sm overflow-hidden">
                <div class="h-1.5 w-full bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600"></div>

                <CardHeader class="text-center">
                    <CardTitle class="text-lg font-black text-slate-800">
                        Pilih Hak Akses Awal
                    </CardTitle>
                    <CardDescription class="text-sm font-medium">
                        Akun <span class="font-black text-emerald-700">{{ user.name }}</span>
                        <span v-if="user.nik" class="font-mono text-xs">(NIK {{ user.nik }})</span>
                        <br />belum aktif. Pilih jabatan yang sesuai, lalu Admin akan mengaktifkan akun Anda.
                    </CardDescription>
                </CardHeader>

                <form @submit.prevent="submit">
                    <CardContent class="grid gap-4">
                        <div class="grid gap-2.5">
                            <Label for="role" class="text-[10px] font-black text-emerald-700 uppercase tracking-widest ml-1">
                                Jabatan
                            </Label>
                            <div class="relative">
                                <IconShieldCheck class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-emerald-300 z-10" />
                                <select
                                    id="role"
                                    v-model="form.role"
                                    class="flex h-11 w-full rounded-md border border-slate-200 bg-slate-50/50 pl-10 pr-10 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 appearance-none transition-all font-medium"
                                    :class="{ 'border-destructive': form.errors.role }"
                                >
                                    <option value="" disabled>-- Pilih Jabatan --</option>
                                    <option v-for="role in roles" :key="role" :value="role">
                                        {{ role }}
                                    </option>
                                </select>
                            </div>
                            <p v-if="form.errors.role" class="text-[10px] font-bold text-destructive ml-1">
                                * {{ form.errors.role }}
                            </p>
                        </div>
                    </CardContent>

                    <CardFooter class="flex flex-col gap-2 border-t bg-emerald-50/30 px-6 py-4">
                        <Button
                            type="submit"
                            :disabled="form.processing || roles.length === 0"
                            class="w-full h-11 bg-emerald-600 hover:bg-emerald-700 font-black text-[10px] uppercase tracking-widest shadow-lg shadow-emerald-200 transition-all active:scale-[0.98]"
                        >
                            <IconLoader2 v-if="form.processing" class="mr-2 size-4 animate-spin" />
                            Kirim Permintaan
                        </Button>
                        <p v-if="roles.length === 0" class="text-[10px] text-amber-700 font-bold text-center">
                            Belum ada jabatan yang tersedia. Hubungi Admin.
                        </p>
                    </CardFooter>
                </form>
            </Card>

            <div class="absolute bottom-4 left-0 right-0 text-[10px] text-emerald-900 font-bold tracking-widest uppercase text-center z-10">
                &copy; 2026 PT MARK DYNAMICS INDONESIA TBK.
            </div>
        </div>
    </div>
</template>
